<?php

/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */
App::uses('CakeEventListener', 'Event');

class TopicListener implements CakeEventListener {

    public function implementedEvents() {
        return array(
            'Controller.groupDetailMenu' => 'getGroupMenu',
            'UserController.deleteUserContent' => 'deleteUserContent',
            'Controller.Search.search' => 'search',
            'Controller.Search.suggestion' => 'suggestion',
            'Controller.Search.hashtags' => 'hashtags',
            'Controller.Search.hashtags_filter' => 'hashtags_filter',
            'Controller.Widgets.tagCoreWidget' => 'hashtagEnable',
            'Plugin.Controller.Group.beforeDelete' => 'processGroupBeforeDelete',
            'Plugin.Controller.Topic.afterSaveTopic' => 'processEventAfterSave',
            'Controller.Comment.afterComment' => 'afterComment',
            'Controller.User.deactivate' => 'deactivate',
            'Controller.Share.afterShare' => 'afterShare'
        );
    }
    
    public function afterShare($event){
        $data = $event->data['data'];
        if (isset($data['item_type']) && $data['item_type'] == 'Topic_Topic'){
            $blog_id = isset($data['parent_id']) ? $data['parent_id'] : 0;
            $blogModel = MooCore::getInstance()->getModel('Topic.Topic');
            $blogModel->updateAll(array('Topic.share_count' => 'Topic.share_count + 1'), array('Topic.id' => $blog_id));
        }
    }
    
    public function processEventAfterSave($event) {
        $v = $event->subject();
        
        // load feed model
        $this->Activity = ClassRegistry::init('Activity');
        
        // find activity which belong to event just created
        $activity = $this->Activity->find('first', array('conditions' => array(
            'Activity.item_type' => 'Topic_Topic',
            'Activity.item_id' => $event->data['id'],
        )));
        
        if (!empty($activity)){
            $share = false;
            
            if (!empty($activity['Activity']['type']) && $activity['Activity']['type'] != 'Group_Group'){
                $share = true;
            }
            
            if (!empty($activity['Activity']['type']) && $activity['Activity']['type'] == 'Group_Group'){
                $groupModel = MooCore::getInstance()->getModel('Group.Group');
                $group = $groupModel->findById($activity['Activity']['target_id']);
                if (!empty($group) && $group['Group']['type'] == PRIVACY_PUBLIC){
                    $share = true;
                }
            }
            
            $this->Activity->clear();
            $this->Activity->updateAll(array('Activity.share' => $share), array('Activity.id' => $activity['Activity']['id']));
        }
    }
    
    public function afterComment($event){
        $data = $event->data['data'];
        $target_id = isset($data['target_id']) ? $data['target_id'] : null;
        $type = isset($data['type']) ? $data['type'] : '';
        if ($type == 'Topic_Topic' && !empty($target_id)){
            $uid = MooCore::getInstance()->getViewer(true);
            Cache::clearGroup('topic', 'topic');
            $topicModel = MooCore::getInstance()->getModel('Topic.Topic');
            // update last poster id, last post date and wall count					
            $topicModel->id = $target_id;
            $topic = $topicModel->findById($target_id);
            if (!empty($topic)){
                $topicModel->save( array( 
                    'lastposter_id' => $uid, 
                    'last_post' 	   => date("Y-m-d H:i:s"), 
                    //'comment_count' => $topic['Topic']['comment_count'] + 1
                    ));
            }
            $topicModel->updateCounter($target_id);
        }
    }


    // delete all topic belong to group is deleted
    public function processGroupBeforeDelete($event){
        $group_id = isset($event->data['aGroup']['Group']['id']) ? $event->data['aGroup']['Group']['id'] : '';
        if (!empty($group_id)){
            $this->Topic = ClassRegistry::init('Topic.Topic');
            $topics = $this->Topic->getTopics('group', $group_id, null);
            foreach ($topics as $topic){
                $this->Topic->deleteTopic($topic);
            }
        }
    }

    public function getGroupMenu($event) {
        $event->result['menu'][] = array(
            'dataUrl' => Router::url('/', true) . 'topics/browse/group/' . $event->data['aGroup']['Group']['id'],
            'id' => 'topics',
            'href' => Router::url('/', true) . 'groups/view/' . $event->data['aGroup']['Group']['id'] . '/tab:topics',
            'icon-class' => 'icon-topic',
            'name' => __( 'Topics'),
            'id_count' => 'group_topics_count',
            'item_count' => $event->data['aGroup']['Group']['topic_count']
        );
    }

    public function deleteUserContent($event) {
        App::import('Topic.Model', 'Topic');

        $this->Topic = new Topic();

        $topics = $this->Topic->findAllByUserId($event->data['aUser']['User']['id']);
        foreach ($topics as $topic) {
            $this->Topic->deleteTopic($topic);
        }
    }

    public function search($event)
    {
        $e = $event->subject();
        App::import('Model', 'Topic.Topic');
        $this->Topic = new Topic();
        $results = $this->Topic->getTopics( 'search', $e->keyword, 1);
        if(count($results) > 5)
            $results = array_slice($results,0,5);
        if(empty($results))
            $results = $this->Topic->getTopicSuggestion($e->keyword,5,1);
        if(isset($e->plugin) && $e->plugin == 'Topic')
        {
            $e->set('topics', $results);
            $e->render("Topic.Elements/lists/topics_list");
        }
        else 
        {
            $event->result['Topic']['header'] = "Topics";
            $event->result['Topic']['icon_class'] = "icon-comments";
            $event->result['Topic']['view'] = "lists/topics_list";
            if(!empty($results))
                $event->result['Topic']['notEmpty'] = 1;
            $e->set('topics', $results);
        }
    }

    public function suggestion($event)
    {
        $e = $event->subject();
        App::import('Model', 'Topic.Topic');
        $this->Topic = new Topic();

        $event->result['topic']['header'] = 'Topics';
        $event->result['topic']['icon_class'] = 'icon-comments';

        if(isset($event->data['type']) && $event->data['type'] == 'topic')
        {
            $page = (!empty($e->request->named['page'])) ? $e->request->named['page'] : 1;
            $topics = $this->Topic->getTopics( 'search', $event->data['searchVal'], $page );
            if(empty($topics))
                $topics = $this->Topic->getTopicSuggestion($event->data['searchVal'],RESULTS_LIMIT,$page);
            $e->set('topics', $topics);
            $e->set('result',1);
            $e->set('more_url','/search/suggestion/topic/'.$e->params['pass'][1]. '/page:' . ( $page + 1 ));
            $e->set('element_list_path',"Topic.lists/topics_list");
        }
        if(isset($event->data['type']) && $event->data['type'] == 'all')
        {
            $event->result['topic'] = null;
            $topics = $this->Topic->getTopics( 'search', $event->data['searchVal'], 1,2);
            if(count($topics) >2){
                $topics = array_slice($topics,0,2);
            }
            if(empty($topics))
                $topics = $this->Topic->getTopicSuggestion($event->data['searchVal'],2);

            if(!empty($topics)){
                foreach($topics as $index=>&$detail){
                    $event->result['topic'][$index]['id'] = $detail['Topic']['id'];
                    if(!empty($detail['Topic']['thumbnail']))
                    {
                        //$thumb = explode('/',$detail['Topic']['thumbnail']);
                        $event->result['topic'][$index]['img'] = 'topics/thumbnail/'.$detail['Topic']['id'].'/75_square_'.$detail['Topic']['thumbnail'];

                    }
                    $event->result['topic'][$index]['title'] = $detail['Topic']['title'];
                    $event->result['topic'][$index]['find_name'] = 'Find Topics';
                    $event->result['topic'][$index]['icon_class'] = 'icon-comments';
                    $event->result['topic'][$index]['view_link'] = 'topics/view/';
                    
                    $mooHelper = MooCore::getInstance()->getHelper('Core_Moo');
                    $utz = ( !is_numeric(Configure::read('core.timezone')) ) ? Configure::read('core.timezone') : 'UTC';
                    $cuser = MooCore::getInstance()->getViewer();
                    // user timezone
                    if ( !empty( $cuser['User']['timezone'] ) ){
                        $utz = $cuser['User']['timezone'];
                    }
                    $event->result['topic'][$index]['more_info'] = __( 'Last posted by %s', $mooHelper->getNameWithoutUrl($detail['LastPoster'], false)) .
                        ' ' . $mooHelper->getTime( $detail['Topic']['last_post'], Configure::read('core.date_format'), $utz );
                }
            }
        }
    }

    public function hashtags($event)
    {
        $enable = Configure::read('Topic.topic_hashtag_enabled');
        $e = $event->subject();
        App::import('Model', 'Topic.Topic');
        $this->Topic = new Topic();
        App::import('Model', 'Tag');
        $this->Tag = new Tag();
        $topics = array();
        $uid = CakeSession::read('uid');
        $page = (!empty($e->request->named['page'])) ? $e->request->named['page'] : 1;

        if($enable)
        {
            if(isset($event->data['type']) && $event->data['type'] == 'topics')
            {
                $topics = $this->Topic->getTopicHashtags($event->data['item_ids'],RESULTS_LIMIT,$page);

                $e->set('result',1);
                $e->set('more_url','/search/hashtags/'.$e->params['pass'][0]. '/topics/page:' . ( $page + 1 ));
                $e->set('element_list_path',"Topic.lists/topics_list");
            }
            $table_name = $this->Topic->table;
            if(isset($event->data['type']) && $event->data['type'] == 'all' && !empty($event->data['item_groups'][$table_name]) )
            {
                $event->result['topics'] = null;

                $topics = $this->Topic->getTopicHashtags($event->data['item_groups'][$table_name],5);


            }
        }

        // get tagged item
        $tag = h(urldecode($event->data['search_keyword']));
        $tags = $this->Tag->find('all', array('conditions' => array(
            'Tag.type' => 'Topic_Topic',
            'Tag.tag' => $tag
        )));
        $topic_ids = Hash::combine($tags,'{n}.Tag.id', '{n}.Tag.target_id');

        $friendModel = MooCore::getInstance()->getModel('Friend');

        $items = $this->Topic->find('all', array('conditions' => array(
                'Topic.id' => $topic_ids
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
        $topics = array_merge($topics, $items);
        //only display 5 items on All Search Result page
        if(isset($event->data['type']) && $event->data['type'] == 'all')
        {
            $topics = array_slice($topics,0,5);
        }
        $topics = array_map("unserialize", array_unique(array_map("serialize", $topics)));
        if(!empty($topics))
        {
            $event->result['topics']['header'] = 'Topics';
            $event->result['topics']['icon_class'] = 'icon-comments';
            $event->result['topics']['view'] = "Topic.lists/topics_list";
            if(isset($event->data['type']) && $event->data['type'] == 'topics')
            {
                $e->set('result',1);
                $e->set('more_url','/search/hashtags/'.$e->params['pass'][0]. '/topics/page:' . ( $page + 1 ));
                $e->set('element_list_path',"Topic.lists/topics_list");
            }

            $e->set('topics', $topics);
        }

    }
    
    public function hashtags_filter($event){
        $e = $event->subject();
        App::import('Model', 'Topic.Topic');
        $this->Topic = new Topic();

        if(isset($event->data['type']) && $event->data['type'] == 'topics')
        {
            $page = (!empty($e->request->named['page'])) ? $e->request->named['page'] : 1;
            $topics = $this->Topic->getTopicHashtags($event->data['item_ids'],RESULTS_LIMIT,$page);
            $e->set('topics', $topics);
            $e->set('result',1);
            $e->set('more_url','/search/hashtags/'.$e->params['pass'][0]. '/topics/page:' . ( $page + 1 ));
            $e->set('element_list_path',"Topic.lists/topics_list");
        }
        $table_name = $this->Topic->table;
        if(isset($event->data['type']) && $event->data['type'] == 'all' && !empty($event->data['item_groups'][$table_name]) )
        {
            $event->result['topics'] = null;

            $topics = $this->Topic->getTopicHashtags($event->data['item_groups'][$table_name],5);

            if(!empty($topics))
            {
                $event->result['topics']['header'] = 'Topics';
                $event->result['topics']['icon_class'] = 'icon-comments';
                $event->result['topics']['view'] = "Topic.lists/topics_list";
                $e->set('topics', $topics);
            }
        }
    }

    public function hashtagEnable($event)
    {
        $enable = Configure::read('Topic.topic_hashtag_enabled');
        $event->result['topics']['enable'] = $enable;
    }
    public function deactivate($event){
        $topicModel = MooCore::getInstance()->getModel('Topic.Topic');
        $topicCategory = $topicModel->find('all',array(
                'conditions' => array('Topic.user_id' => $event->data['uid']),
                'group' => array('Topic.category_id'),
                'fields' => array('category_id','(SELECT count(*) FROM '.$topicModel->tablePrefix.'topics WHERE category_id=Topic.category_id AND user_id = '.$event->data['uid'].') as count')
            )
        );
        $topicCategory = Hash::combine($topicCategory,'{n}.Topic.category_id','{n}.{n}.count');
        $event->result['Topic'] = $topicCategory;
    }
}
