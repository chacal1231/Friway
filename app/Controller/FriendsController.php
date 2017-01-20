<?php

/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */

class FriendsController extends AppController
{
    public function ajax_sendRequest() {
        $this->autoRender = false;
        $this->_checkPermission(array('confirm' => true));

        $uid = $this->Auth->user('id');
        $cuser = $this->_getUser();
        $requestdata = $this->request->data;

        if ($uid == $requestdata['user_id']) {
            echo __('You cannot send friend request to yourself');
            return;
        }

        // check if users are already friends
        if ($this->Friend->areFriends($uid, $requestdata['user_id'])) {
            echo __('You are already a friend of this user');
            return;
        }

        $this->loadModel('FriendRequest');
        if ($this->FriendRequest->existRequest($uid, $requestdata['user_id'])) {
            echo __('You have already sent a friend request to this user');
            return;
        }

        $requestdata['sender_id'] = $uid;

        if ($this->FriendRequest->save($requestdata)) {
            echo __('Your request has been successfully sent');

            // add notification
            $this->loadModel('Notification');
            $this->Notification->record(array('recipients' => $requestdata['user_id'],
                'sender_id' => $uid,
                'action' => 'friend_add',
                'url' => '/home/index/tab:friend-requests'
            ));

            $this->loadModel('User');
            $user = $this->User->findById($requestdata['user_id']);

            if ($user['User']['notification_email']) {
                $ssl_mode = Configure::read('core.ssl_mode');
                $http = (!empty($ssl_mode)) ? 'https' : 'http';

                $this->MooMail->send($user, 'friend_request', array(
                    'recipient_title' => $user['User']['moo_title'],
                    'recipient_link' => $http . '://' . $_SERVER['SERVER_NAME'] . $user['User']['moo_href'],
                    'sender_title' => $cuser['moo_title'],
                    'sender_link' => $http . '://' . $_SERVER['SERVER_NAME'] . $cuser['moo_href'],
                    'message' => h($requestdata['message']),
                	'request_link' => $http . '://' . $_SERVER['SERVER_NAME'] . $this->request->base .'/home/index/tab:friend-requests',
                        )
                );
            }
        }
    }

    public function ajax_add($id = null)
	{
		$id = intval($id);
		$this->_checkPermission( array( 'confirm' => true ) );		
		$uid = $this->Auth->user('id');
		$warning_msg = '';
		if ( $uid == $id )
		{
            $warning_msg = __('You cannot send friend request to yourself');
		}

		// check if users are already friends
		if ( $this->Friend->areFriends( $uid, $id ) )
		{
            $warning_msg = __('You are already a friend of this user');
		}

		// check if this user has already sent a request
		$this->loadModel( 'FriendRequest' );
		if ( $this->FriendRequest->existRequest( $uid, $id ) )
		{
            $warning_msg = __('You have already sent this user a friend request');
		}
		
		// check if the other user has already sent a request
		if ( $this->FriendRequest->existRequest( $id, $uid ) )
		{
            $warning_msg = __('This user has already sent you a friend request');
		}
		
		// nothing? display the form
		$this->loadModel( 'User' );				
		$user = $this->User->findById($id);
		$this->set('user', $user);
        $this->set('warning_msg', $warning_msg);
	}

    public function ajax_cancel($id)
    {
        $this->autoRender = false;
        $id = intval($id);
        $uid = $this->Auth->user('id');
        $this->loadModel('FriendRequest');    
        $this->FriendRequest->deleteAll(array('FriendRequest.sender_id' => $uid, 'FriendRequest.user_id' => $id));
        
        // Issue: counterCache not working when using deleteAll, have to using updateCounterCache
        $this->FriendRequest->updateCounterCache(array('user_id' => $id));
        
        $this->redirect($this->referer());
    }

	public function ajax_requests()
	{
		$this->_checkPermission();		
		$uid = $this->Auth->user('id');
		
		$this->loadModel( 'FriendRequest' );
		$requests = $this->FriendRequest->getRequests( $uid );

		$this->set('requests', $requests);
	}

	public function ajax_respond() {
            $this->autoRender = false;
            $this->loadModel('FriendRequest');

            $requestdata = $this->request->data;
            $uid = $this->Auth->user('id');
            $cuser = $this->_getUser();

            $request = $this->FriendRequest->getRequest($requestdata['id']);

            if (!empty($request)) {
                $status = $requestdata['status'];
                $this->FriendRequest->id = $requestdata['id'];

                if (!empty($status)) {
                    // insert to friends table
                    $this->Friend->create();
                    $this->Friend->save(array('user_id' => $uid, 'friend_id' => $request['Sender']['id']));
                    $this->Friend->create();
                    $this->Friend->save(array('user_id' => $request['Sender']['id'], 'friend_id' => $uid));

                    // insert into activity feed
                    $this->loadModel('Activity');
                    $activity = $this->Activity->getRecentActivity('friend_add', $uid);

                    if (!empty($activity)) {
                        // aggregate activities
                        $user_ids = explode(',', $activity['Activity']['items']);

                        if (!in_array($request['Sender']['id'], $user_ids))
                            $user_ids[] = $request['Sender']['id'];

                        $this->Activity->id = $activity['Activity']['id'];
                        $this->Activity->save(array('items' => implode(',', $user_ids),
                            'params' => '',
                            'privacy' => 1,
                            'query' => 1
                        ));
                    }
                    else {
                        $this->Activity->save(array('type' => 'user',
                            'action' => 'friend_add',
                            'user_id' => $uid,
                            'item_type' => APP_USER,
                            'items' => $request['Sender']['id']
                        ));
                    }

                    // send a notification to the sender				
                    $this->loadModel('Notification');
                    $this->Notification->record(array('recipients' => $request['Sender']['id'],
                        'sender_id' => $uid,
                        'action' => 'friend_accept',
                        'url' => '/users/view/' . $uid
                    ));

                    //mark notification as read
                    $notifyId = $this->Notification->find('first', array(
                        'conditions' => array(
                            'Notification.user_id' => $uid,
                            'Notification.sender_id' => $request['Sender']['id'],
                            'Notification.action' => 'friend_add',
                            'Notification.read' => 0)
                            )
                    );
                    if (!empty($notifyId['Notification']['id'])) {
                        $this->Notification->id = $notifyId['Notification']['id'];
                        $this->Notification->save(array('read' => 1));
                    }
                    // delete notification
                    $this->Notification->deleteAll(array('Notification.user_id' => $uid, 'Notification.sender_id' => $request['Sender']['id'], 'Notification.action' => 'friend_add'), false);

                    // add private activity to sender's wall
                    $this->Activity->create();
                    $this->Activity->save(array('type' => 'user',
                        'action' => 'friend_add',
                        'user_id' => $request['Sender']['id'],
                        'item_type' => APP_USER,
                        'items' => $uid,
                        'privacy' => 3
                    ));

                    echo __('You and %s are now friends', '<a href="' . $this->request->base . '/users/view/' . $request['Sender']['id'] . '">' . h($request['Sender']['name']) . '</a>');
                } else
                    echo __('You have deleted the request. The sender will not be notified');

                $this->FriendRequest->delete($requestdata['id']);
            }
        }

        public function ajax_removeRequest()
	{
        $requestdata = $this->request->data;
		$this->autoRender = false;
		$this->_checkPermission();
		
		$uid = $this->Auth->user('id');
		$friend_id = $requestdata['user_id'];

		$this->Friend->deleteAll(array('Friend.user_id' => $uid, 'Friend.friend_id' => $friend_id), true, true);
		$this->Friend->deleteAll(array('Friend.user_id' => $friend_id, 'Friend.friend_id' => $uid), true, true);
                
                // remove feed
                $this->loadModel('Activity');
                $activities = $this->Activity->find('all', array('conditions' => array(
                    'OR' => array(
                        array(
                            'Activity.action' => 'friend_add',
                            'Activity.user_id' => $uid,
                            
                        ),
                        array(
                            'Activity.action' => 'friend_add',
                            'Activity.user_id' => $friend_id,
                            
                        )
                    ),
                )));
                foreach ($activities as $item){
                    $friendsid = explode(',', $item['Activity']['items']);
                    
                    if ($item['Activity']['user_id'] == $uid){
                        if(($key = array_search($friend_id, $friendsid)) !== false) {
                            unset($friendsid[$key]);
                        }
                    }
                    else {
                        if(($key = array_search($uid, $friendsid)) !== false) {
                            unset($friendsid[$key]);
                        }
                    }
                    
                    if (empty($friendsid)){ // delete
                        $this->Activity->delete($item['Activity']['id']);
                    }else { // update
                        $this->Activity->id = $item['Activity']['id'];
                        $this->Activity->set(array(
                            'items' => implode(',', $friendsid),
                            'modified' => false
                        ));
                        $this->Activity->save();
                    }
                }
                
                
                

        echo __('Friend removed successful.');
        
	}

    public function ajax_remove($id = null)
    {
        $id = intval($id);
        $this->_checkPermission( array( 'confirm' => true ) );
        $uid = $this->Auth->user('id');

        // check if users are not friends
        if ( !$this->Friend->areFriends( $uid, $id ) )
        {
            $this->autoRender = false;
            echo __('You are not a friend of this user');
            return;
        }

        // nothing? display the form
        $this->loadModel( 'User' );
        $user = $this->User->findById($id);
        $this->set('user', $user);
    }
	
	public function ajax_invite()
	{
        if ($this->request->is('post')){
            if ( !empty( $this->request->data['to'] ) )
            {
                $this->autoRender = false;
                $cuser = $this->_getUser();

                $emails = explode( ',', $this->request->data['to'] );

                $i = 1;
                foreach ($emails as $email)
                {
                    if ( $i <= 10 )
                    {
                        if ( Validation::email( trim($email) ) )
                        {
                        	$ssl_mode = Configure::read('core.ssl_mode');
        					$http = (!empty($ssl_mode)) ? 'https' :  'http';
        					
                        	$this->MooMail->send(trim($email),'site_invite',
			    				array(
			    					'email' => trim($email),
			    					'sender_title' => $cuser['moo_title'],
			    					'sender_link' => $http.'://'.$_SERVER['SERVER_NAME'].$cuser['moo_href'],
			    					'message' => $this->request->data['message'],
			    					'signup_link' => $http.'://'.$_SERVER['SERVER_NAME'].$this->request->base.'/users/register'
			    				)
			    			);
                            
                        }
                    }
                    $i++;
                }
                $response = array();
                $response['result'] = 1;
                echo json_encode($response);
            }else{
                $this->_jsonError(__('Recipient is required'));
            }
        }
	}
	
	public function ajax_suggestions()
	{
		$this->_checkPermission();
		$uid = $this->Auth->user('id');
		
		$suggestions = $this->Friend->getFriendSuggestions( $uid, true );
		$this->set('suggestions', $suggestions);
	}
	
	public function ajax_show_mutual( $user_id )
	{
		$user_id = intval($user_id);
		$this->_checkPermission();
		$uid = $this->Auth->user('id');
		$page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;
		
		$users = $this->Friend->getMutualFriends( $user_id, $uid, RESULTS_LIMIT, $page );
		
		$this->set('users', $users);
		$this->set('page', $page);
		$this->set('more_url', '/friends/ajax_show_mutual/' . $user_id . '/page:' . ( $page + 1 ) );
		
		$this->render('/Elements/ajax/mutual_friend');
	}
    
    public function do_get_json()
    {
        $this->_checkPermission();
        $uid = $this->Auth->user('id');
        
        $friends = $this->Friend->searchFriends( $uid, $this->request->query['q'] );
        // have to do this because find(list) does not work with bindModel
        $friend_options = array();
        
        
        $mooHelper = MooCore::getInstance()->getHelper('Core_Moo');
        foreach ($friends as $friend){
            $avatar = $mooHelper->getImage(array('User' => $friend['User']), array('prefix' => '50_square', 'align' => 'absmiddle', 'style' => 'width: 40px'));
            $friend_options[] = array( 'id' => $friend['User']['id'], 'name' => $friend['User']['name'], 'avatar' => $avatar );
        }
        return json_encode( $friend_options );
    }
    
    public function tagged($activity_id = null){
        if (!empty($activity_id)){
            $uid = $this->Auth->user('id');
            $friendModel = MooCore::getInstance()->getModel('Friend');
            $activityModel = MooCore::getInstance()->getModel('Activity');
            $activity = $activityModel->findById($activity_id);
            $userTagged = isset($activity['UserTagging']['users_taggings']) ? explode(',', $activity['UserTagging']['users_taggings']) : array();
            $friendList = $friendModel->getFriends($uid);
            MooPeople::getInstance()->register($friendList);
            $this->set(compact('friendList', 'userTagged', 'activity_id'));
        }
    }
    
    public function tagged_save(){
        $this->autoRender = false;
        $data = $this->request->data;
        $friends = $data['friends'];
        $activity_id = $data['activity_id'];
        $activityModel = MooCore::getInstance()->getModel('Activity');
        $userTaggingModel = MooCore::getInstance()->getModel('UserTagging');
        $activity = $activityModel->findById($activity_id);
        
        if (empty($friends)){ // remove tagging
            $userTaggingModel->delete($activity['UserTagging']['id']);
        }else {
            $userTaggingModel->id = $activity['UserTagging']['id'];
            $userTaggingModel->set(array(
                'users_taggings' => implode(',', $friends)
            ));
            $userTaggingModel->save();
        }
        
    }
}
