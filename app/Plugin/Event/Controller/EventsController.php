<?php

/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */
App::uses('CakeEvent', 'Event');

class EventsController extends EventAppController
{
    
    public $paginate = array(
        'order' => array(
            'Event.id' => 'desc'
        ),
        'findType' => 'translated',
    );
	public function beforeFilter(){
        parent::beforeFilter();
        $this->loadModel('Event.Event');
    }
	public function index($cat_id = null)
	{		
            $cat_id = intval($cat_id);

            $event = new CakeEvent('Plugin.Controller.Event.index', $this);
            $this->getEventManager()->dispatch($event);

            $this->loadModel('Event.EventRsvp');
            $eventId = $this->EventRsvp->findAllByUserId($this->Auth->user('id'), array('event_id'));
            if (!empty($eventId)) {
                $eventId = implode(',', Hash::extract($eventId, '{n}.EventRsvp.event_id'));
            } else{
                $eventId = '';
            }
            
            $role_id = $this->_getUserRoleId();
            $more_result = 0;
            if (!empty($cat_id)){
                $events = $this->Event->getEvents('category', $cat_id);
                $more_events = $this->Event->getEvents('category', $cat_id,2);
            }
            else{
                $events = $this->Event->getEvents('upcoming', $this->Auth->user('id'), 1, $role_id, $eventId);
                $more_events = $this->Event->getEvents('upcoming', $this->Auth->user('id'), 2, $role_id, $eventId);
            }

            if(!empty($more_events)) $more_result = 1;
            $events = Hash::sort($events, '{n}.Event.from', ' asc');
            $this->set('events', $events);
            $this->set('cat_id', $cat_id);
            $this->set('title_for_layout', '');
            $this->set('more_result', $more_result);
        }

    /*
	 * Browse events based on $type
	 * @param string $type - possible value: all (default), my, home, friends, past
	 */
	public function browse( $type = null, $param = null )
	{		
		$page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;
        $url = ( !empty( $param ) ) ? $type . '/' . $param : $type;
        $role_id = $this->_getUserRoleId();
        $more_result = 0;

		switch ( $type )
		{
			case 'home': 
			case 'my':
			case 'mypast':
			case 'friends':
				$this->_checkPermission();
				$uid = $this->Auth->user('id');
				
				$this->loadModel( 'Event.EventRsvp' );
				$events = $this->EventRsvp->getEvents( $type, $uid, $page, $role_id );
				$more_events = $this->EventRsvp->getEvents( $type, $uid, $page + 1, $role_id );

				break;
			default: // all, past, category
                $this->loadModel('Event.EventRsvp');
                $eventId = $this->EventRsvp->findAllByUserId($this->Auth->user('id'),array('event_id'));
                if(!empty($eventId)){
                    $eventId = implode(',',Hash::extract($eventId,'{n}.EventRsvp.event_id'));
                }
                else
                    $eventId = '';

                
                $events = $this->Event->getEvents( $type, $param, $page,$role_id,$eventId );
                $events = Hash::sort($events,'{n}.Event.from',' asc');
                $more_events = $this->Event->getEvents( $type, $param, $page + 1,$role_id,$eventId );
        }

        if(!empty($more_events)) $more_result = 1;
		$this->set('events', $events);
		$this->set('more_url', '/events/browse/' . h($url) . '/page:' . ( $page + 1 ) ) ;
        $this->set('more_result', $more_result);

        
            if ( $page == 1 && $type == 'home' ){
                $this->render('/Elements/ajax/home_event');
            }
            else{
                if ($this->request->is('ajax')){
                    $this->render('/Elements/lists/events_list');
                }
                else{
                    $this->render('/Elements/lists/events_list_m');
                }
            }
	}
	
	/*
	 * Show add/edit event form
	 * @param int $id - event id to edit
	 */
	public function create($id = null)
	{
		$id = intval($id);	
		$this->_checkPermission( array( 'confirm' => true ) );
        $this->_checkPermission( array('aco' => 'event_create') );    

        $event = new CakeEvent('Plugin.Controller.Event.create', $this);
        $this->getEventManager()->dispatch($event);

		if (!empty($id)) // editing
		{
			$event = $this->Event->findById($id);
			$this->_checkExistence( $event );
			$this->_checkPermission( array( 'admins' => array( $event['User']['id'] ) ) );
		
			$this->set( 'title_for_layout', __('Edit Event') );
		}
		else // adding new event
		{
			$event = $this->Event->initFields();
			$this->set( 'title_for_layout', __('Add New Event') );
		}
		
		$this->set('event', $event);
	}
	
	/*
	 * Save add/edit form
	 */
	public function save()
	{
            $this->_checkPermission( array( 'confirm' => true ) );
            
            $this->autoRender = false;		
            $uid = $this->Auth->user('id');
            if ( !empty( $this->request->data['id'] ) )
            {
                    // check edit permission
                    $event = $this->Event->findById( $this->request->data['id'] );
                    $this->_checkPermission( array( 'admins' => array( $event['User']['id'] ) ) );
                    $this->Event->id = $this->request->data['id'];
            }
            else
                    $this->request->data['user_id'] = $uid;

            $this->Event->set( $this->request->data );

            $this->_validateData( $this->Event );

            if ( $this->Event->save() ) // successfully saved	
            {
                //update field 'type' again because conflict with upload behavior
                $this->Event->id;
                $this->Event->save(array('type' => $this->request->data['type'],'id' => $this->Event->id));

                if ( empty( $this->request->data['id'] ) ) // add event
                {
                    // rsvp the creator
                    $this->loadModel( 'Event.EventRsvp' );
                    $this->EventRsvp->save( array( 'user_id' => $uid, 'event_id' => $this->Event->id, 'rsvp' => RSVP_ATTENDING ) );

                    $event = new CakeEvent('Plugin.Controller.Event.afterSaveEvent', $this, array(
                        'uid' => $uid, 
                        'id' => $this->Event->id, 
                        'type' =>$this->request->data['type']));
                    
                    $this->getEventManager()->dispatch($event);

                }

                $response['result'] = 1;
                $response['id'] = $this->Event->id;

                echo json_encode($response);
            }
	}
	
	/*
	 * View Event
	 * @param int $id - event id to view
	 */
	public function view($id = null)
	{
		$id = intval($id);		
		$uid  = $this->Auth->user('id');
		
             $event = $this->Event->findById($id);
		$this->_checkExistence( $event );
        $role_id = $this->_getUserRoleId();
        $this->_checkPermission( array('aco' => 'event_view') );

		$this->loadModel('Event.EventRsvp');
		
		if ( $uid )
		{
            $my_rsvp = Cache::read('eventrsvp.myrsvp.'.$uid.'.event.'.$id, 'event');
            if(empty($my_rsvp))
            {
			    $my_rsvp = $this->EventRsvp->getMyRsvp( $uid, $id );
                Cache::write('eventrsvp.myrsvp.'.$uid.'.event.'.$id, $my_rsvp,'event');
            }
			$this->set('my_rsvp', $my_rsvp);
		}

		// check if user can view this event
		if ( empty( $my_rsvp ) && $event['Event']['type'] == PRIVACY_PRIVATE && $role_id != ROLE_ADMIN )
			$this->redirect( '/pages/no-permission' );

		$attending 	   = array();
		$maybe 		   = array();
		$not_attending = array();
		$awaiting 	   = array();		
		
		// get rsvp
		$awaiting 		= $this->EventRsvp->getRsvp( $id, RSVP_AWAITING, null, 6 );
		$attending 		= $this->EventRsvp->getRsvp( $id, RSVP_ATTENDING, null, 5 );
		$not_attending  = $this->EventRsvp->getRsvp( $id, RSVP_NOT_ATTENDING, null, 6 );
		$maybe 			= $this->EventRsvp->getRsvp( $id, RSVP_MAYBE, null, 6 );
		
		$maybe_count 		 = $this->EventRsvp->getRsvpCount( $id, RSVP_MAYBE );
		$not_attending_count = $this->EventRsvp->getRsvpCount( $id, RSVP_NOT_ATTENDING );
		$awaiting_count		 = $this->EventRsvp->getRsvpCount( $id, RSVP_AWAITING );
		
		MooCore::getInstance()->setSubject($event);

        $cakeEvent = new CakeEvent('Plugin.Controller.Event.view', $this, array('id' => $id, 'uid' => $uid));
        $this->getEventManager()->dispatch($cakeEvent);
                
		$this->set('attending', $attending);
		$this->set('maybe', $maybe);
		$this->set('not_attending', $not_attending);
		$this->set('awaiting', $awaiting);
		
		$this->set('maybe_count', $maybe_count);
		$this->set('not_attending_count', $not_attending_count);
		$this->set('awaiting_count', $awaiting_count);
		
		$this->set('event', $event);
		$this->set('title_for_layout', htmlspecialchars($event['Event']['title']));
        $this->set('description_for_layout', String::truncate(strip_tags($event['Event']['description']), 160, array('ellipsis' => '...', 'html' => false, 'exact' => false)));

        // set og:image
        if ($event['Event']['photo']){
            $mooHelper = MooCore::getInstance()->getHelper('Core_Moo');
            $this->set('og_image', $mooHelper->getImageUrl($event, array('prefix' => '850')));
        }
        $this->set('eventActivities',$this->Feeds->get());
	}

	/*
	 * RSVP event
	 */
	public function do_rsvp()
	{
		$this->_checkPermission( array( 'confirm' => true ) );
					
		$uid = $this->Auth->user('id');
		$this->request->data['user_id'] = $uid;		
		$event = $this->Event->findById( $this->request->data['event_id'] );
                
		// find existing and update if necessary
		$this->loadModel( 'Event.EventRsvp' );
		$my_rsvp = $this->EventRsvp->getMyRsvp( $uid, $this->request->data['event_id'] );
		
		// check if user was invited
		if ( empty( $my_rsvp ) && $event['Event']['type'] == PRIVACY_PRIVATE )
			$this->redirect( '/pages/no-permission' );

		if ( !empty($my_rsvp) )
		{
                    $this->EventRsvp->id = $my_rsvp['EventRsvp']['id'];

                    // user changed rsvp from attending to something else
                    if ( $my_rsvp['Event']['type'] != PRIVACY_PRIVATE && $my_rsvp['EventRsvp']['rsvp'] == RSVP_ATTENDING && isset($this->request->data['rsvp']) && $this->request->data['rsvp'] != RSVP_ATTENDING )
                    {
                        $cakeEvent = new CakeEvent('Plugin.Controller.Event.changeRsvpFromAttending', $this, array('uid' => $uid, 'event_id' => $this->request->data['event_id']));
                        $this->getEventManager()->dispatch($cakeEvent);
                    }
		}
		else
		{
                    // first time rsvp
                    if ( $event['Event']['type'] == PRIVACY_PUBLIC && isset($this->request->data['rsvp']) && $this->request->data['rsvp'] == RSVP_ATTENDING ) // attending
                    {
                        $cakeEvent = new CakeEvent('Plugin.Controller.Event.firstTimeRsvp', $this, array('uid' => $uid, 'event' => $event));
                        $this->getEventManager()->dispatch($cakeEvent);
                    }
		}

		$this->EventRsvp->save( $this->request->data );

		$this->redirect( '/events/view/'.$this->request->data['event_id'] );
	}
	
	/*
	 * Show invite form
	 * @param int $event_id
	 */
	public function invite( $event_id = null )
	{
		$event_id = intval($event_id);
		$this->_checkPermission( array( 'confirm' => true ) );	

		$this->set('event_id', $event_id);
        $this->render('Event.Events/invite');
    }
	
	/*
	 * Handle invite submission
	 */
	public function sendInvite()
	{
		$this->autoRender = false;
		$this->_checkPermission( array( 'confirm' => true ) );
		$cuser = $this->_getUser();
		
		if ( !empty( $this->request->data['friends'] ) || !empty( $this->request->data['emails'] ) )
		{		
			$event = $this->Event->findById( $this->request->data['event_id'] );

			// check if user can invite
			if ( $event['Event']['type'] == PRIVACY_PRIVATE && ( $cuser['id'] != $event['User']['id'] ) )
				return;
			
			if ( !empty( $this->request->data['friends'] ) )
			{
				$this->loadModel( 'Event.EventRsvp' );
				$data = array();	
                $friends = explode(',', $this->request->data['friends']);
                $rsvp_list = $this->EventRsvp->getRsvpList($this->request->data['event_id']);
				
				foreach ($friends as $friend_id)		
                    if ( !in_array($friend_id, $rsvp_list) )	
					   $data[] = array('event_id' => $this->request->data['event_id'], 'user_id' => $friend_id);
	           
                if ( !empty($data) )
                {
    				$this->EventRsvp->saveAll($data);

                    $cakeEvent = new CakeEvent('Plugin.Controller.Event.sentInvite', $this, array('friends' => $friends, 'cuser' => $cuser, 'event_id' => $this->request->data['event_id'], 'event' => $event));
                    $this->getEventManager()->dispatch($cakeEvent);

                }
			}
			
			if ( !empty( $this->request->data['emails'] ) )
			{
				$emails = explode( ',', $this->request->data['emails'] );
				
				$i = 1;
				foreach ( $emails as $email )
				{
					if ( $i <= 10 )
					{
						if ( Validation::email( trim($email) ) )
						{
							$ssl_mode = Configure::read('core.ssl_mode');
        					$http = (!empty($ssl_mode)) ? 'https' :  'http';
        					$this->MooMail->send(trim($email),'event_invite_none_member',
			    				array(
			    					'event_title' => $event['Event']['moo_title'],
			    					'event_link' => $http.'://'.$_SERVER['SERVER_NAME'].$event['Event']['moo_href'],
			    					'email' => trim($email),
			    					'sender_title' => $cuser['name'],
			    					'sender_link' => $http.'://'.$_SERVER['SERVER_NAME'].$cuser['moo_href'],
			    				)
			    			);
							
							
						}
					}
					$i++;
				}
			}
            $response = array();
            $response['result'] = 1;
            $response['msg'] = __('Your invitations have been sent.') . ' <a href="javascript:void(0)" onclick="inviteMore()">' . __( 'Invite more friends') . '</a>';
            echo json_encode($response);
		}
        else{
            $this->_jsonError(__('Recipient is required'));
        }
	}
	
	/*
	 * Show RSVP list
	 * @param int $event_id
	 */
	public function showRsvp( $event_id = null, $type = RSVP_ATTENDING )
	{
		$event_id = intval($event_id);
		$this->loadModel('Event.EventRsvp');
		$page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;

        $more_result = 0;
		$users = $this->EventRsvp->getRsvp( $event_id, $type, $page );
		$more_users = $this->EventRsvp->getRsvp( $event_id, $type, $page + 1);
        if(!empty($more_users))
            $more_result = 1;

		$this->set('users', $users);
		$this->set('page', $page);
		$this->set('more_url', '/events/showRsvp/' . $event_id . '/' . $type . '/page:' . ( $page + 1 ) );
        $this->set('rsvp_type', $type);
        $this->set('more_result', $more_result);

        $this->render('/Elements/ajax/user_overlay');
    }
	
	/*
	 * Delete event
	 * @param int $id - event id to delete
	 */
	public function do_delete($id = null)
	{
		$id = intval($id);
		$event = $this->Event->findById($id);
		$this->_checkExistence( $event );
		$this->_checkPermission( array( 'admins' => array( $event['User']['id'] ) ) );
		
		$this->Event->deleteEvent( $event );
                
                $cakeEvent = new CakeEvent('Plugin.Controller.Event.afterDeleteEvent', $this, array('item' => $event));
                $this->getEventManager()->dispatch($cakeEvent);
		
		$this->Session->setFlash( __('Event has been deleted'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in' ) );
		$this->redirect( '/events' );
	}

    public function popular(){
        if ($this->request->is('requested')) {
            $num_item_show = $this->request->named['num_item_show'];
            return $this->Event->getPopularEvents( $num_item_show, Configure::read('core.popular_interval'));
        }
    }
    public function upcomingAll(){
        if ($this->request->is('requested')) {
            $num_item_show = $this->request->named['num_item_show'];
            return $this->Event->getUpcoming( $num_item_show);
        }
    }
    public function upcomming(){
        if ($this->request->is('requested')) {
            $aid = $this->request->named['uid'];
            $page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;
            $this->loadModel('Event.EventRsvp');
            $events = $this->EventRsvp->getEvents( 'my', $aid, $page );
            return $events;
        }
    }
    public function _getUserRoleId(){
        return parent::_getUserRoleId();
    }
    public function show_g_map($id = null)
    {
        if(!empty($id))
        {
            $event = $this->Event->findById($id);
            $this->set('event', $event);
            $this->render('Event.Widgets/events/show_g_map');
        }
    }
    
    public function ajax_event_joined(){
        $activity_id = $this->request->named['activity_id'];
        $this->loadModel('Activity');
        $activity = $this->Activity->findById($activity_id);
        if (!empty($activity)){
            $items = $activity['Activity']['items'];
            $ids = explode(',', $items);
            $this->loadModel('Event.Event');
            $events = $this->Event->find('all', array('conditions' => array(
                'Event.id' => $ids
            )));
            $this->set(compact('events'));
        } 
        $this->render('/Elements/ajax/ajax_event_joined');
    }
    
    public function categories_list(){
        if($this->request->is('requested')){
            $this->loadModel('Category');
            $categories = $this->Category->getCategories('Event');
            return $categories;
        }
    }
    
    public function profile_user_event($uid = null){
        $uid = intval($uid);
            
        $page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;
        
        $events = $this->Event->getEvents('user', $uid, $page);

        $more_events = $this->Event->getEvents('user', $uid, $page+1);
        $more_result = 0;
        if(!empty($more_events))
            $more_result = 1;

        $this->set('events', $events);
        $this->set('more_url', '/events/profile_user_event/' . $uid . '/page:' . ( $page + 1 ));
        $this->set('user_id', $uid);
        $this->set('user_event', true);
        $this->set('more_result',$more_result);


        if ($page > 1)
            $this->render('/Elements/lists/events_list');
        else
            $this->render('Event.Events/profile_user_event');
    }
}

?>
