<?php

/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */

class ConversationsController extends AppController 
{
    
    
    public function show() {
        $this->_checkPermission();
        $uid = $this->Auth->user('id');

        $page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;
        $this->loadModel('ConversationUser');

        $this->Conversation->unbindModel(
                array('belongsTo' => array('User'))
        );

        $this->Conversation->unbindModel(
                array('hasMany' => array('Comment'))
        );

        $this->ConversationUser->recursive = 3;
        $conversations = $this->ConversationUser->find('all', array('conditions' => array('ConversationUser.user_id' => $uid),
            'limit' => 10,
            'page' => $page,
            'order' => 'modified desc'
                ));

        $this->set('conversations', $conversations);
    }

    public function ajax_browse()
	{
		$this->_checkPermission();
		$uid = $this->Auth->user('id');
		
		$page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;
		$this->loadModel( 'ConversationUser' );

		$this->Conversation->unbindModel(
			array('belongsTo' => array('User'))
		);
        
        $this->Conversation->unbindModel(
            array('hasMany' => array('Comment'))
        );
		
	
		
		$this->ConversationUser->recursive = 3;
		$conversations = $this->ConversationUser->find( 'all', array( 'conditions' => array( 'ConversationUser.user_id' => $uid ), 
																	  'limit' => RESULTS_LIMIT, 
												 					  'page' => $page,
																	  'order' => 'modified desc'
													)	);

		$this->set('conversations', $conversations);
		$this->set('more_url', '/conversations/ajax_browse/page:' . ( $page + 1 ) ) ;
		
		if ( $page > 1 )
			$this->render('/Elements/lists/messages_list');
	}
	
	public function ajax_send($recipient = null)
	{
		$this->_checkPermission( array( 'confirm' => true ) );
		$uid = $this->Auth->user('id');

		if ( !empty($recipient) )
		{
			$this->loadModel( 'User' );
            $this->loadModel('Friend');

            $to = $this->User->findById($recipient);
			$this->_checkExistence( $to );
            $allow_send_message_to_non_friend = Configure::read('core.send_message_to_non_friend');
            if ($allow_send_message_to_non_friend) {
                if(empty($to['User']['receive_message_from_non_friend'])){
                    $areFriend = $this->Friend->areFriends($uid, $to['User']['id']);
                    if(!$areFriend)
                        $this->set('notAllow', 1);
                }
            }
            else {
                $areFriend = $this->Friend->areFriends($uid, $to['User']['id']);
                if(!$areFriend)
                    $this->set('notAllow', 1);
            }
			$this->set('to', $to);
		}
	}

	public function ajax_doSend()
	{			
		$this->autoRender = false;
		$this->_checkPermission( array( 'confirm' => true ) );
		
		$uid = $this->Auth->user('id');

		$this->request->data['user_id'] = $uid;
		$this->request->data['lastposter_id'] = $uid;
		
		$this->Conversation->set( $this->request->data );
		$this->_validateData( $this->Conversation );
		
		// @todo: validate recipients
		
		if ( !empty($this->request->data['friends']) )
		{
			$recipients = explode( ',', $this->request->data['friends'] );
				
			if ( $this->Conversation->save() ) // successfully saved	
			{
				// save convo users
				$participants = array();
                $allow_send_message_to_non_friend = Configure::read('core.send_message_to_non_friend');

                $this->loadModel('Friend');
				foreach ( $recipients as $participant ) {
                    if ($allow_send_message_to_non_friend) {
                        $to = $this->User->findById($participant);
                        if(empty($to['User']['receive_message_from_non_friend'])){
                            $areFriend = $this->Friend->areFriends($uid, $participant);
                            if(!$areFriend)
                                continue;
                        }
                    }
                    else {
                        $areFriend = $this->Friend->areFriends($uid, $participant);
                        if(!$areFriend)
                            continue;
                    }

					$participants[] = array('conversation_id' => $this->Conversation->id, 'user_id' => $participant);

                }
				// add sender to convo users array
				$participants[] = array('conversation_id' => $this->Conversation->id, 'user_id' => $uid, 'unread' => 0);

				$this->loadModel( 'ConversationUser' );
				$this->ConversationUser->saveAll( $participants );
				
				$this->loadModel( 'Notification' );
				$this->Notification->record( array( 'recipients' => $recipients,
													'sender_id' => $uid,
													'action' => 'message_send',
													'url' => '/conversations/view/'.$this->Conversation->id
				) );
				
				
				$response['result'] = 1;
                $response['id'] = $this->Conversation->id;
                echo json_encode($response);
			}
		}
		else
			$this->_jsonError(__('Recipient is required'));
	}

	public function view($id)
	{
		$id = intval($id);
		$this->_checkPermission();
		$uid = $this->Auth->user('id');

		$conversation = $this->Conversation->findById($id);
		$this->_checkExistence( $conversation );

		// check permission to view
		$this->loadModel('ConversationUser');
		$convo_users = $this->ConversationUser->findAllByConversationId($id);
		$users_array = array();

		foreach ($convo_users as $user)
		{
			$users_array[] = $user['ConversationUser']['user_id'];

			if ( $uid == $user['ConversationUser']['user_id'] )
				$convo_user = $user['ConversationUser'];
		}

		$this->_checkPermission( array( 'admins' => $users_array ) );

		// set to read if unread
		if ( $convo_user['unread'] )
		{
			$this->ConversationUser->id = $convo_user['id'];
			$this->ConversationUser->save( array( 'unread' => 0 ) );
		}

		// get messages
		$this->loadModel('Comment');
		$comments = $this->Comment->getComments( $id, APP_CONVERSATION );
		
		// get friends
		$this->loadModel( 'Friend' );
		$friends = $this->Friend->getFriends($uid);
		
		$this->set('convo_users', $convo_users);
		
		$this->set('friends', $friends);
		$this->set('conversation', $conversation);
                
                $this->set('comment_type', APP_CONVERSATION);
		
		$this->set('title_for_layout', $conversation['Conversation']['subject']);
                $data = array();
                $page = 1 ;
                
                $data = array(
                    'bIsCommentloadMore' => $conversation['Conversation']['message_count'] - $page * RESULTS_LIMIT,
                    'more_comments' => '/comments/browse/conversation/' . $id . '/page:' . ($page + 1),
                    'comments' => $comments
                );
                $this->set('data', $data);
	}
	
	public function mark_all_read()
	{
	        $uid = $this->Auth->user('id');
	        $this->loadModel('ConversationUser');
	        $convo_users = $this->ConversationUser->findAllByUserId($uid);
	        foreach($convo_users as $user){
	            if ( $user['ConversationUser']['unread'] )
	            {
	                $this->ConversationUser->id = $user['ConversationUser']['id'];
	                $this->ConversationUser->save( array( 'unread' => 0 ) );

	            }
	        }
	        $this->redirect($this->referer());
	}
	public function ajax_add($msg_id = null)
	{
		$msg_id = intval($msg_id);
		$this->_checkPermission( array( 'confirm' => true ) );

		$this->set('msg_id', $msg_id);
	}
	
	public function ajax_doAdd()
	{			
		$this->autoRender = false;
		$this->_checkPermission( array( 'confirm' => true ) );
		
		if ( !empty($this->request->data['friends']) )
		{		
			$msg_id = $this->request->data['msg_id'];
			$uid = $this->Auth->user('id');
            $friends = explode(',', $this->request->data['friends']);
			
			$this->loadModel( 'ConversationUser' );
			$users = $this->ConversationUser->getUsersList( $msg_id );
			$this->_checkPermission( array( 'admins' => $users ) ); // check to see if the user is a participant
			
			//$this->ConversationUser->save( array('conversation_id' => $msg_id, 'user_id' => $uid) );
			
			$participants = array();
			foreach ( $friends as $participant )
                if ( !in_array($participant, $users) )
				    $participants[] = array('conversation_id' => $msg_id, 'user_id' => $participant);
	
            if ( !empty($participants) )
            {
    			$this->ConversationUser->saveAll( $participants );
    			
    			$this->loadModel( 'Notification' );
    			$this->Notification->record( array( 'recipients' => $friends,
    												'sender_id' => $uid,
    												'action' => 'conversation_add',
    												'url' => '/conversations/view/'.$msg_id
    			) );
            }
            
            $response['result'] = 1;
		}
		else
        {
            $response['result'] = 0;
            $response['message'] = __('Please select at least one person');        
        }
        
        echo json_encode($response);
	}
	
	public function do_leave( $msg_id = null )
	{
		$msg_id = intval($msg_id);
		$this->_checkPermission( array( 'confirm' => true ) );
		$uid = $this->Auth->user('id');
		
		$this->loadModel( 'ConversationUser' );
		$this->ConversationUser->deleteAll( array( 'conversation_id' => $msg_id, 'ConversationUser.user_id' => $uid ), true, true );
		if (!$this->ConversationUser->hasAny(array('conversation_id' => $msg_id))) {//all users had left the conversation
            $this->Conversation->delete($msg_id);
        }
		$this->redirect( '/home/index/tab:messages' );
	}
}

?>
