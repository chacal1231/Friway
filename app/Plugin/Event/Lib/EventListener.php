<?php

App::uses('CakeEventListener', 'Event');

class EventListener implements CakeEventListener {

    public function implementedEvents() {
        return array(
            'Plugin.Controller.Event.index' => 'processEventIndex',
            'Plugin.Controller.Event.create' => 'processEventCreate',
            'Plugin.Controller.Event.afterSaveEvent' => 'processEventAfterSave',
            'Plugin.Controller.Event.view' => 'processEventView',
            'Plugin.Controller.Event.changeRsvpFromAttending' => 'processEventChangeRsvp',
            'Plugin.Controller.Event.firstTimeRsvp' => 'processEventFirstTimeRsvp',
            'Plugin.Controller.Event.sentInvite' => 'processEventSentInvite',
            'UserController.deleteUserContent' => 'deleteUserContent',
            'Controller.Search.search' => 'search',
        	'Model.Activity.afterSetParamsConditionsOr' => 'afterSetParamsConditionsOr',
            'Controller.Search.suggestion' => 'suggestion',
            'Controller.Search.hashtags' => 'hashtags',
            'Controller.Search.hashtags_filter' => 'hashtags_filter',
            'Controller.Widgets.tagCoreWidget' => 'hashtagEnable',
            'Controller.User.deactivate' => 'deactivate'
        );
    }
    
	function afterSetParamsConditionsOr($event)
    {    	    	    	
    	App::import('Event.Model', 'EventRsvp');
		$event_rsvp = new EventRsvp();
		$events = $event_rsvp->getMyEventsList( $event->data['param']);
    	
    	$data = array(array( 'Activity.type' => 'Event_Event', 'Activity.target_id' => $events ));
    	$event->result[] = $data;
    }

    public function processEventIndex($event) {
        $v = $event->subject();
        
    }

    public function processEventCreate($event) {
        $v = $event->subject();
        $this->Category = ClassRegistry::init('Category');
        $role_id = $v->_getUserRoleId();
        $categories = $this->Category->getCategoriesList('Event', $role_id);
        $v->set('categories', $categories);
    }

    public function processEventAfterSave($event) {
        // load feed model
        $this->Activity = ClassRegistry::init('Activity');
        
        // find activity which belong to event just created
        $activity = $this->Activity->find('first', array('conditions' => array(
            'Activity.item_type' => 'Event_Event',
            'Activity.item_id' => $event->data['id'],
        )));
        
        if (!empty($activity)){
            $share = false;
            // only enable share feature for public event
            if ($event->data['type'] == PRIVACY_PUBLIC) {
                $share = true;
            }
            $this->Activity->clear();
            $this->Activity->updateAll(array('Activity.share' => $share), array('Activity.id' => $activity['Activity']['id']));
        }
        
    }

    public function processEventView($event) {
        $v = $event->subject();       
    }

    public function processEventChangeRsvp($event) {
        $v = $event->subject();
        // remove associated activity
        $this->Activity = ClassRegistry::init('Activity');
        
        $activity = $this->Activity->getRecentActivity('event_attend', $event->data['uid']);
        
        if ($activity) {
            $items = array_filter(explode(',',$activity['Activity']['items']));
        	$items = array_diff($items,array($event->data['event_id']));
        	
        	if (!count($items))
        	{
        		$this->Activity->delete($activity['Activity']['id']);
        	}
        	else
        	{
        		$this->Activity->id = $activity['Activity']['id'];
                    $this->Activity->save(
                    array('items' => implode(',',$items))                        
                    );
        	}
		}        
    }

    public function processEventFirstTimeRsvp($cakeEvent) {
        $v = $cakeEvent->subject();
        $this->Activity = ClassRegistry::init('Activity');
        $activity = $this->Activity->getRecentActivity('event_attend', $cakeEvent->data['uid']);

        // insert into activity feed if it's a public event
        if (!empty($activity)) {
            // aggregate activities
            $event_ids = explode(',', $activity['Activity']['items']);
            if (!in_array($cakeEvent->data['event']['Event']['id'], $event_ids))
                $event_ids[] = $cakeEvent->data['event']['Event']['id'];

            $this->Activity->id = $activity['Activity']['id'];
            $this->Activity->save(array('items' => implode(',', $event_ids)
            ));
        }
        else {
            $this->Activity->save(array('type' => 'user',
                'action' => 'event_attend',
                'user_id' => $cakeEvent->data['uid'],
                'item_type' => 'Event_Event',               
                'items' => $cakeEvent->data['event']['Event']['id'],
            	'plugin' => 'Event'
            ));
        }
    }

    public function processEventSentInvite($event) {
        $this->Notification = ClassRegistry::init('Notification');
        $this->Notification->record(array('recipients' => $event->data['friends'],
            'sender_id' => $event->data['cuser']['id'],
            'action' => 'event_invite',
            'url' => '/events/view/' . $event->data['event_id'],
            'params' => h($event->data['event']['Event']['title'])
        ));
    }
    
    public function deleteUserContent($event) {
        App::import('Event.Model', 'Event');
        App::import('Event.Model', 'EventRsvp');

        $this->Event = new Event();
        $this->EventRsvp = new EventRsvp();

        $events = $this->Event->findAllByUserId($event->data['aUser']['User']['id']);
        foreach ($events as $event) {
            $this->Event->deleteEvent($event);
        }

        $this->EventRsvp->deleteAll(array('EventRsvp.user_id' => $event->data['aUser']['User']['id']), true, true);
    }

    public function search($event)
    {
        $e = $event->subject();
        App::import('Model', 'Event.Event');
        $this->Event = new Event();
        $results = $this->Event->getEvents( 'search', $e->keyword, 1);
        if(count($results) > 5)
            $results = array_slice($results,0,5);
        if(isset($e->plugin) && $e->plugin == 'Event')
        {
            $e->set('events', $results);
            $e->render("Event.Elements/lists/events_list");
        }
        else 
        {
            $event->result['Event']['header'] = "Events";
            $event->result['Event']['icon_class'] = "icon-calendar";
            $event->result['Event']['view'] = "lists/events_list";
            if(!empty($results))
                $event->result['Event']['notEmpty'] = 1;
            $e->set('events', $results);
        }
    }

    public function suggestion($event)
    {
        $e = $event->subject();
        App::import('Model', 'Event.Event');
        $this->Event = new Event();

        $event->result['event']['header'] = 'Events';
        $event->result['event']['icon_class'] = 'icon-calendar';

        if(isset($event->data['type']) && $event->data['type'] == 'event')
        {
            $page = (!empty($e->request->named['page'])) ? $e->request->named['page'] : 1;
            $events = $this->Event->getEvents( 'search', $event->data['searchVal'], $page);

            $e->set('events', $events);
            $e->set('result',1);
            $e->set('more_url','/search/suggestion/event/'.$e->params['pass'][1]. '/page:' . ( $page + 1 ));
            $e->set('element_list_path',"Event.lists/events_list");
        }
        if(isset($event->data['type']) && $event->data['type'] == 'all')
        {
            $event->result['event'] = null;
            $events = $this->Event->getEvents( 'search', $event->data['searchVal'], 1);
            if(count($events) >2){
                $events = array_slice($events,0,2);
            }
            if(!empty($events)){
                foreach($events as $index=>&$detail){
                    $event->result['event'][$index]['id'] = $detail['Event']['id'];
                    if(!empty($detail['Event']['photo']))
                        $event->result['event'][$index]['img'] = 'events/photo/'.$detail['Event']['id'].'/75_square_'.$detail['Event']['photo'];
                    $event->result['event'][$index]['title'] = $detail['Event']['title'];
                    $event->result['event'][$index]['find_name'] = 'Find Event';
                    $event->result['event'][$index]['icon_class'] = 'icon-calendar';
                    $event->result['event'][$index]['view_link'] = 'events/view/';
                    
                    $event->result['event'][$index]['more_info'] = h($detail['Event']['location']) . ' ' . __('%s attending', $detail['Event']['event_rsvp_count']);
                }
            }
        }
    }

    public function hashtags($event)
    {
        $enable = Configure::read('Event.event_hashtag_enabled');
        $e = $event->subject();
        App::import('Model', 'Event.Event');
        $this->Event = new Event();
        App::import('Model', 'Tag');
        $this->Tag = new Tag();
        $events = array();
        $uid = CakeSession::read('uid');
        $page = (!empty($e->request->named['page'])) ? $e->request->named['page'] : 1;

        if($enable)
        {
            if(isset($event->data['type']) && $event->data['type'] == 'events')
            {
                $events = $this->Event->getEventHashtags($event->data['item_ids'],RESULTS_LIMIT,$page);
                $events = $this->_filterEvent($events);
            }
            $table_name = $this->Event->table;
            if(isset($event->data['type']) && $event->data['type'] == 'all' && !empty($event->data['item_groups'][$table_name]) )
            {
                $events = $this->Event->getEventHashtags($event->data['item_groups'][$table_name],5);
                $events = $this->_filterEvent($events);
            }
        }

        // get tagged item
        $tag = h(urldecode($event->data['search_keyword']));
        $tags = $this->Tag->find('all', array('conditions' => array(
            'Tag.type' => 'Event_Event',
            'Tag.tag' => $tag
        )));
        $event_ids = Hash::combine($tags,'{n}.Tag.id', '{n}.Tag.target_id');

        $friendModel = MooCore::getInstance()->getModel('Friend');

        $items = $this->Event->find('all', array('conditions' => array(
                'Event.id' => $event_ids
            ),
            'limit' => RESULTS_LIMIT,
            'page' => $page
        ));

        $viewer = MooCore::getInstance()->getViewer();

        foreach ($items as $key => $item){
            $owner_id = $item[key($item)]['user_id'];
            $privacy = isset($item[key($item)]['privacy']) ? $item[key($item)]['privacy'] : 1;
            if (empty($viewer)){ // guest can view only public item
                if ($privacy != PRIVACY_EVERYONE){
                    unset($items[$key]);
                }
            }else{ // viewer
                $aFriendsList = array();
                $aFriendsList = $friendModel->getFriendsList($owner_id);
                if ($privacy == PRIVACY_ME){ // privacy = only_me => only owner and admin can view items
                    if (!$viewer['Role']['is_admin'] && $viewer['User']['id'] != $owner_id){
                        unset($items[$key]);
                    }
                }else if ($privacy == PRIVACY_FRIENDS){ // privacy = friends => only owner and friendlist of owner can view items
                    if (!$viewer['Role']['is_admin'] && $viewer['User']['id'] != $owner_id && !in_array($viewer['User']['id'], array_keys($aFriendsList))){
                        unset($items[$key]);
                    }
                }else {

                }
            }
        }
        $events = array_merge($events, $items);
        //only display 5 items on All Search Result page
        if(isset($event->data['type']) && $event->data['type'] == 'all')
        {
            $events = array_slice($events,0,5);
        }
        $events = array_map("unserialize", array_unique(array_map("serialize", $events)));
        if(!empty($events))
        {
            $event->result['events']['header'] = 'Events';
            $event->result['events']['icon_class'] = 'icon-calendar';
            $event->result['events']['view'] = "Event.lists/events_list";

            if(isset($event->data['type']) && $event->data['type'] == 'events')
            {
                $e->set('result',1);
                $e->set('more_url','/search/hashtags/'.$e->params['pass'][0]. '/events/page:' . ( $page + 1 ));
                $e->set('element_list_path',"Event.lists/events_list");
            }
            $e->set('events', $events);

        }
    }
    
    public function hashtags_filter($event){
       
        $e = $event->subject();
        App::import('Model', 'Event.Event');
        $this->Event = new Event();

        if(isset($event->data['type']) && $event->data['type'] == 'events')
        {
            $page = (!empty($e->request->named['page'])) ? $e->request->named['page'] : 1;
            $events = $this->Event->getEventHashtags($event->data['item_ids'],RESULTS_LIMIT,$page);
            $e->set('events', $events);
            $e->set('result',1);
            $e->set('more_url','/search/hashtags/'.$e->params['pass'][0]. '/events/page:' . ( $page + 1 ));
            $e->set('element_list_path',"Event.lists/events_list");
        }
        $table_name = $this->Event->table;
        if(isset($event->data['type']) && $event->data['type'] == 'all' && !empty($event->data['item_groups'][$table_name]) )
        {
            $event->result['events'] = null;

            $events = $this->Event->getEventHashtags($event->data['item_groups'][$table_name],5);

            if(!empty($events))
            {
                $event->result['events']['header'] = 'Events';
                $event->result['events']['icon_class'] = 'icon-calendar';
                $event->result['events']['view'] = "Event.lists/events_list";
                $e->set('events', $events);

            }
        }
    }
    private function _filterEvent($events)
    {
        if(!empty($events))
        {
            $eventRsvpModel = MooCore::getInstance()->getModel('Event.EventRsvp');
            $viewer = MooCore::getInstance()->getViewer();
            foreach($events as $key => &$event)
            {
                $owner_id = $event[key($event)]['user_id'];
                $privacy = isset($event[key($event)]['type']) ? $event[key($event)]['type'] : 1;

                if (empty($viewer)){ // guest can view only public item
                    if ($privacy != PRIVACY_EVERYONE){
                        unset($events[$key]);
                    }
                }else{ // viewer
                    $awaiting 		= $eventRsvpModel->getRsvp( $event[key($event)]['id'], RSVP_AWAITING);
                    $attending 		= $eventRsvpModel->getRsvp( $event[key($event)]['id'], RSVP_ATTENDING);
                    $not_attending  = $eventRsvpModel->getRsvp( $event[key($event)]['id'], RSVP_NOT_ATTENDING);
                    $maybe 			= $eventRsvpModel->getRsvp( $event[key($event)]['id'], RSVP_MAYBE);

                    $awaiting = Hash::extract($awaiting,'{n}.User.id');
                    $attending = Hash::extract($attending,'{n}.User.id');
                    $not_attending = Hash::extract($not_attending,'{n}.User.id');
                    $maybe = Hash::extract($maybe,'{n}.User.id');

                    $idList = array_merge($awaiting,$attending,$not_attending,$maybe);
                    if ($privacy == PRIVACY_FRIENDS){ // privacy = private => only owner and admin can view items
                        if (!$viewer['Role']['is_admin'] && $viewer['User']['id'] != $owner_id && !in_array($viewer['User']['id'], $idList)){
                            unset($events[$key]);
                        }
                    }else {

                    }
                }
            }
        }
        return $events;
    }
    public function hashtagEnable($event)
    {
        $enable = Configure::read('Event.event_hashtag_enabled');
        $event->result['events']['enable'] = $enable;
    }

    public function deactivate($event){
        $eventModel = MooCore::getInstance()->getModel('Event.Event');
        $eventCategory = $eventModel->find('all',array(
                'conditions' => array('Event.user_id' => $event->data['uid']),
                'group' => array('Event.category_id'),
                'fields' => array('category_id','(SELECT count(*) FROM '.$eventModel->tablePrefix.'events WHERE category_id=Event.category_id AND user_id = '.$event->data['uid'].') as count')
            )
        );
        $eventCategory = Hash::combine($eventCategory,'{n}.Event.category_id','{n}.{n}.count');
        $event->result['Event'] = $eventCategory;
    }
}
