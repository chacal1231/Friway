<?php

/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */
//App::uses('AppController','Controller');
class UsersController extends AppController {
	
        
	public $paginate = array( 'order' => 'User.id desc', 'limit' => RESULTS_LIMIT );

	public function index($type = 'home') {
            $uid = $this->Auth->user('id');
            $data = '';
            if ($this->request->is('post')) {
                $data = $this->request->data;
            }
            
            $this->getEventManager()->dispatch(new CakeEvent('User.beforeQuerySearch', $this, $data));
            
            $this->loadmodel('Friend');
            $this->loadModel('FriendRequest');
            
            $page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;
            
            switch ($type) {
                case 'home':
                case 'friends':
                    if($type == 'friends')
                        $this->_checkPermission();
                    if ($this->conditions != '') {
                        $users = $this->User->getUsers(1, $this->conditions, USERS_BROWSE_LIMIT);
                        $more_users = $this->User->getUsers(2, $this->conditions, USERS_BROWSE_LIMIT);
                    } else {
                        $users = $this->User->getUsers(1, null, USERS_BROWSE_LIMIT);
                        $more_users = $this->User->getUsers(2, null, USERS_BROWSE_LIMIT);
                    }
                    if (!empty($more_users))
                        $more_result = 1;
                    break;

                case 'search':

                    if (!Configure::read('core.guest_search') && empty($uid))
                        $this->_checkPermission();

                    $params = array('User.active' => 1);
                    $profile_params = array();
                    $joins = array();
                    $user_ids = array();
                    $i = '';
                    $this->getEventManager()->dispatch(new CakeEvent('User.changeBeforeQuerySearch', $this, $this->request->query['data']));

                    if (!empty($param)) // ajax url search
                        $this->request->query['data']['name'] = $param;

                    if (!empty($this->request->query['data']['gender']))
                        $params['User.gender'] = $this->request->query['data']['gender'];

                    if (!empty($this->request->query['data']['email']))
                        $params['User.email'] = $this->request->query['data']['email'];

                    if (!empty($this->request->query['data']['picture']))
                        $params['User.avatar <> ?'] = '';

                    if (!empty($this->request->query['data']['name']))
                        $params['MATCH(User.name) AGAINST(? IN BOOLEAN MODE)'] = urldecode($this->request->query['data']['name']);

                    // custom fields
                    foreach ($this->request->query['data'] as $field => $value) {
                        if (strpos($field, 'field_') === 0 && !empty($value)) {
                            $field_id = explode('_', $field);
                            $field_id = $field_id[1];

                            $profile_params['ProfileFieldValue' . $i . '.profile_field_id'] = $field_id;
                            if (is_array($value)) {
                                
                                foreach ($value as $item){
                                    $profile_params['OR'][]['MATCH(ProfileFieldValue' . $i . '.value) AGAINST("\"?\"" IN BOOLEAN MODE)'] = urldecode($item);
                                }
                                
                            } else
                                $profile_params['ProfileFieldValue' . $i . '.value'] = $value;

                            if ($i >= 1)
                                $joins[] = array('table' => 'profile_field_values',
                                    'alias' => 'ProfileFieldValue' . $i,
                                    'type' => 'INNER',
                                    'conditions' => array('ProfileFieldValue.user_id = ProfileFieldValue' . $i . '.user_id')
                                );

                            $i = (int) $i + 1;
                        }
                    }
                    
                    if (!empty($profile_params)) {
                        $this->loadModel('ProfileFieldValue');
                        $user_ids = $this->ProfileFieldValue->find('list', array('conditions' => $profile_params,
                            'joins' => $joins,
                            'fields' => array('user_id')
                                ));

                        $params['User.id'] = $user_ids;
                    }
                    if (!empty($this->request->query['data']['online'])) {
                        $online = $this->User->getOnlineUsers();

                        if (!empty($user_ids))
                            $params['User.id'] = array_intersect($user_ids, $online['userids']);
                        else
                            $params['User.id'] = $online['userids'];

                        // hide invisible users
                        $params['User.hide_online'] = 0;
                    }
                    
                    $users = $this->User->getUsers($page, $params, USERS_BROWSE_LIMIT);
                    $more_users = $this->User->getUsers($page + 1, $params, USERS_BROWSE_LIMIT);
                    if (!empty($more_users))
                        $more_result = 1;
                    $this->set('params', $this->request->query['data']);

                    break;

                default:
                    $users = $this->User->getUsers($page, null, USERS_BROWSE_LIMIT);
                    $more_users = $this->User->getUsers($page + 1, null, USERS_BROWSE_LIMIT);
                    if (!empty($more_users))
                        $more_result = 1;
            }

            $this->loadModel('ProfileField');
            $custom_fields = $this->ProfileField->find('all', array('conditions' => array('active' => 1,
                    'searchable' => 1
                )));

            // search value
            $values = array();
            foreach ($this->request->named as $field => $value) {
                if (strpos($field, 'field_') === 0 && !empty($value)) {
                    $field_id = explode('_', $field);
                    $field_id = $field_id[1];
                    $values[$field_id] = array('id' => $field_id, 'value' => urldecode($value));
                }

                if ($field == 'online')
                    $this->set('online_filter', true);
            }
            
            if (!empty($uid)) {
                $friends = $this->Friend->getFriends($uid);
                $friends_request = $this->FriendRequest->getRequestsList($uid);
                $respond = $this->FriendRequest->getRequests($uid);
                $request_id = Hash::combine($respond, '{n}.FriendRequest.sender_id', '{n}.FriendRequest.id');
                $respond = Hash::extract($respond, '{n}.FriendRequest.sender_id');
                $friends_requests = array_merge($friends, $friends_request);
                $this->set(compact('friends', 'respond', 'request_id', 'friends_request'));
            }
            
            $this->set(compact('custom_fields', 'values', 'users', 'more_result'));
            $this->set('title_for_layout', '');
        }

        /*
	 * Browse users based on $type
	 * @param string $type - possible value: all (default), friends, search, home
	 */
	public function ajax_browse($type = null, $param = null) {
           // $this->autoLayout = false;
            $uid = $this->Auth->user('id');
            $this->loadmodel('Friend');

            $page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;
            $users = array();
            $more_result = 0;

            switch ($type) {
                case 'home':
                case 'friends':
                    $this->_checkPermission();
                    $users = $this->Friend->getUserFriends($uid, $page);
                    $more_users = $this->Friend->getUserFriends($uid, $page + 1);
                    if (!empty($more_users))
                        $more_result = 1;
                    break;

                case 'search':

                    if (!Configure::read('core.guest_search') && empty($uid))
                        $this->_checkPermission();

                    $params = array('User.active' => 1);
                    $profile_params = array();
                    $joins = array();
                    $user_ids = array();
                    $i = '';
                    $this->getEventManager()->dispatch(new CakeEvent('User.changeBeforeQuerySearch', $this, $this->request->data));

                    if (!empty($param)) // ajax url search
                        $this->request->data['name'] = $param;

                    if (!empty($this->request->data['gender']))
                        $params['User.gender'] = $this->request->data['gender'];

                    if (!empty($this->request->data['email']))
                        $params['User.email'] = $this->request->data['email'];

                    if (!empty($this->request->data['picture']))
                        $params['User.avatar <> ?'] = '';

                    if (!empty($this->request->data['name']))
                        $params['MATCH(User.name) AGAINST(? IN BOOLEAN MODE)'] = urldecode($this->request->data['name']);

                    // custom fields
                    foreach ($this->request->data as $field => $value) {
                        if (strpos($field, 'field_') === 0 && !empty($value)) {
                            $field_id = explode('_', $field);
                            $field_id = $field_id[1];

                            $profile_params['ProfileFieldValue' . $i . '.profile_field_id'] = $field_id;
                            if (is_array($value)) {
                                
                                foreach ($value as $item){
                                    $profile_params['OR'][]['MATCH(ProfileFieldValue' . $i . '.value) AGAINST("\"?\"" IN BOOLEAN MODE)'] = urldecode($item);
                                }
                                
                            } else
                                $profile_params['ProfileFieldValue' . $i . '.value'] = $value;

                            if ($i >= 1)
                                $joins[] = array('table' => 'profile_field_values',
                                    'alias' => 'ProfileFieldValue' . $i,
                                    'type' => 'INNER',
                                    'conditions' => array('ProfileFieldValue.user_id = ProfileFieldValue' . $i . '.user_id')
                                );

                            $i = (int) $i + 1;
                        }
                    }

                    if (!empty($profile_params)) {
                        $this->loadModel('ProfileFieldValue');
                        $user_ids = $this->ProfileFieldValue->find('list', array('conditions' => $profile_params,
                            'joins' => $joins,
                            'fields' => array('user_id')
                                ));

                        $params['User.id'] = $user_ids;
                    }

                    if (!empty($this->request->data['online'])) {
                        $online = $this->User->getOnlineUsers();

                        if (!empty($user_ids))
                            $params['User.id'] = array_intersect($user_ids, $online['userids']);
                        else
                            $params['User.id'] = $online['userids'];

                        // hide invisible users
                        $params['User.hide_online'] = 0;
                    }
                    $users = $this->User->getUsers($page, $params, USERS_BROWSE_LIMIT);
                    $more_users = $this->User->getUsers($page + 1, $params, USERS_BROWSE_LIMIT);
                    if (!empty($more_users))
                        $more_result = 1;
                    $this->set('params', $this->request->data);

                    break;

                default:
                    $users = $this->User->getUsers($page, null, USERS_BROWSE_LIMIT);
                    $more_users = $this->User->getUsers($page + 1, null, USERS_BROWSE_LIMIT);
                    if (!empty($more_users))
                        $more_result = 1;
            }

            // get current user friends and requests
            if (!empty($uid) && in_array($type, array('search', 'all', 'friends', 'home'))) {
                $this->loadModel('FriendRequest');

                $friends = $this->Friend->getFriends($uid);
                $requests = $this->FriendRequest->getRequestsList($uid);

                $friends_requests = array_merge($friends, $requests);

                $this->set('friends', $friends);

                $this->set('friends_request', $requests);
            }

            $this->set('users', $users);
            $this->set('more_result', $more_result);
            $this->set('type', $type);
            $this->set('more_url', '/users/ajax_browse/' . h($type) . '/page:' . ( $page + 1 ));

            if ($page == 1 && $type == 'home'){
                $this->render('/Elements/ajax/home_user');
            }
            else {
                if ($this->request->is('ajax')){ 
                    $this->render('/Elements/lists/users_list');
                }
                else{
                    $this->render('/Elements/lists/users_list_m');
                }
            }
        }

        public function login()
    {           
            $this->autoRender = false;

		    
		    $this->member_login();
            $url = $this->referer();
            // only root admin can login when site offline enabled
            $uid = $this->Auth->user('id');

            if (Configure::read('core.site_offline') && $uid != ROOT_ADMIN_ID){
                $this->do_logout();
                $this->Session->setFlash( __('Only Root Admin can login when site is offline'), 'default', array('class' => 'error-message'));
                $this->redirect( $this->referer() );
            }

            // redirect to the previous page
            if ( !empty( $this->request->data['return_url'] ) )
            {
                $this->redirect( base64_decode( $this->request->data['return_url'] ) );
            }
            elseif ( strpos( $url, 'no-permission' ) === false && strpos( $url, 'error' ) === false && 
                             strpos( $url, 'recover' ) === false && strpos( $url, 'resetpass' ) === false )
            {
                $this->redirect( '/' );
            }
            else

                $this->redirect($this->referer());
	}

	public function do_logout()
	{   
            $this->logout();
            return $this->redirect($this->Auth->logout());
	}
        
        

    public function register()
	{			              
		$uid = $this->Auth->user('id');

		if ( empty( $uid ) )
		{
			// check if registration is disabled
            $site_offline = Configure::read('core.site_offline');
            if ( !empty($site_offline) )
                return;
			
			if ( Configure::read('core.disable_registration') )
                $this->_showError( __('The admin has disabled registration on this site') );
            
            // load spam challenge if enabled
            if ( Configure::read('core.enable_spam_challenge') )
            {
                $this->loadModel('SpamChallenge');                
                $challenges = $this->SpamChallenge->findAllByActive(1);
                
                if ( !empty( $challenges ) )
                {
                    $rand = array_rand( $challenges );
                    
                    $this->Session->write('spam_challenge_id', $challenges[$rand]['SpamChallenge']['id']);
                    $this->set('challenge', $challenges[$rand]);
                }
            }
	    
            $this->set('no_right_column', true);
			$this->set('title_for_layout', __('Registration'));
			
			$this->render('/Elements/registration');
		}
		else{
                    $this->Session->setFlash( __('You have logged in, so you can not view that page.'), 'default', array('class' => 'error-message') );
                    $this->redirect( '/' );
                }
	}

	public function ajax_signup_step1()
	{
		// check registration code		
		if ( Configure::read('core.enable_registration_code') && $this->request->data['registration_code'] != Configure::read('core.registration_code') )
		{
			$this->autoRender = false;
			echo '<span id="mooError">' . __('Invalid registration code') . '</span>';
			return;
		}	
		list($packages,$compare) = MooCore::getInstance()->getHelper('Subscription_Subscription')->getPackageSelect(1);            
		
		$this->User->set( $this->request->data );
		$currency = Configure::read('Config.currency');
		$isGatewayEnabled = MooCore::getInstance()->getHelper('Subscription_Subscription')->checkEnableSubscription();
		$this->set(compact('isGatewayEnabled', 'currency', 'packages', 'compare'));

                if ( $this->isBanned($this->request->data['email']))
                {
                    $this->autoRender = false;
                    echo '<span id="mooError">' . __('You are not allowed to register with this email') . '</span>';
                    exit;
                }

	    if ( $this->User->validates() )
	    {
	    	$this->loadModel('ProfileField');		
	    	$custom_fields = $this->ProfileField->getRegistrationFields();		
                $this->set('custom_fields', $custom_fields);            
	    }
	    else
	    {
	    	$this->autoRender = false;
	    	$errors = $this->User->invalidFields();
	    	
	    	echo '<span id="mooError">' . current( current( $errors ) ) . '</span>';
	    }
	}

	public function ajax_signup_step2()
	{			
		$this->autoRender = false;
        
        // check spam challenge
        if ( Configure::read('core.enable_spam_challenge') )
        {
            $this->loadModel('SpamChallenge');

            $challenge = $this->SpamChallenge->findById( $this->Session->read('spam_challenge_id') );
            $answers = explode("\n", $challenge['SpamChallenge']['answers']);

            $found = false;
            foreach ( $answers as $answer )
            {
                if ( strtolower( trim($answer) ) == strtolower( $this->request->data['spam_challenge'] ) )
                    $found = true;
            }

            if ( !$found )
            {
                echo __('Invalid security question');
                return;
            }
        }

        // check captcha
        $checkRecaptcha = MooCore::getInstance()->isRecaptchaEnabled();
        $recaptcha_privatekey = Configure::read('core.recaptcha_privatekey');
        if ($checkRecaptcha)
        {
            App::import('Vendor', 'recaptchalib');
            $reCaptcha = new ReCaptcha($recaptcha_privatekey);
            $resp = $reCaptcha->verifyResponse(
                    $_SERVER["REMOTE_ADDR"], $_POST["g-recaptcha-response"]
            );

            if ($resp != null && !$resp->success) {
                echo __('Invalid security code');
                return;
            }

        }

        if ($this->isBanned($this->request->data['email']))
        {
            $this->autoRender = false;
            echo '<span id="mooError">' . __('You are not allowed to register with this email') . '</span>';
            exit;
        }

		$this->_saveRegistration( $this->request->data );
	}

	private function _saveRegistration( $data )
	{
		// check if registration is disabled			
		if ( Configure::read('core.disable_registration') )
		{
			echo '<span id="mooError">' . __('The admin has disabled registration on this site') . '</span>';
			return;
		}

		// check registration code			
		if ( Configure::read('core.enable_registration_code') && $data['registration_code'] != Configure::read('core.registration_code') )
		{
			echo '<span id="mooError">' . __('Invalid registration code') . '</span>';
			return;
		}
			
		$data['role_id']    = ROLE_MEMBER;
        $clientIP = getenv('HTTP_X_FORWARDED_FOR') ? getenv('HTTP_X_FORWARDED_FOR') : $_SERVER['REMOTE_ADDR'];
		$data['ip_address'] = $clientIP;
		$data['code'] 	    = md5( $data['email'] . microtime() );
		$data['confirmed']  = ( Configure::read('core.email_validation') ) ? 0 : 1;
		$data['last_login'] = date("Y-m-d H:i:s");
		$data['privacy']    = Configure::read('core.profile_privacy');
        $data['featured']   = 0;
        $data['username']   = '';
        
            if (!Configure::read('core.approve_users')){
                $data['approved'] = 1;
            }
		
		$this->User->set( $data );
		
		if ( !$this->User->validates() )
	    {
	    	$errors = $this->User->invalidFields();	    	
	    	echo '<span id="mooError">' . current( current( $errors ) ) . '</span>';
           
			return;
	    }
        $require_upload_avatar = Configure::read('core.require_upload_avatar');
        if(!empty($require_upload_avatar) && empty($data['avatar']))
        {
            echo '<span id="mooError">'.__('Avatar is required').'</span>';

            return;
        }

		// check custom required fields
		$this->loadModel('ProfileField');
		$custom_fields = $this->ProfileField->getRegistrationFields( true );

		foreach ($custom_fields as $field)
		{
			$value = $data['field_' . $field['ProfileField']['id']];
			
			if ( $field['ProfileField']['required'] && empty( $value ) && !is_numeric( $value ) )
			{
				echo $field['ProfileField']['name'] . __(' is required');
                
				return;
			}
		}
                
                // keep a copy of avatar for Profile Album picture, because after uploaded, behavior deleted original file
                $newTmpAvatar = '';
                if(!empty($data['avatar']))
                {
                    $file = $data['avatar'];
                    $epl = explode('.', $file);
                    $extension = $epl[count($epl) - 1];
                    $tmp_name = md5(uniqid());
                    $newTmpAvatar = WWW_ROOT . 'uploads' . DS . 'tmp' . DS . $tmp_name . '.' . $extension;
                    copy(WWW_ROOT . $file, $newTmpAvatar);
                }
                
		if ( $this->User->save() ) // successfully saved
		{	
			$this->getEventManager()->dispatch(new CakeEvent('UserController.doSaveUser', $this, array('data'=>$data,'custom_fields'=>$custom_fields)));		
			// Log user in
			$user = $this->User->read();
                        $cuser = $user['User'];
                        $cuser['Role'] = $user['Role'];
                        $this->Auth->login($cuser);
			
			if ( Configure::read('core.email_validation'))
				$this->Session->setFlash( __('An email has been sent to your email address<br />Please click the validation link to confirm your email') );

            if ( Configure::read('core.approve_users'))
                $this->Session->setFlash( __('Your account is pending approval.') );

            //custom: upload avatar after sign up
            if(!empty($newTmpAvatar))
            {
                $uid = $this->User->id;
                $this->loadModel('Photo.Album');
                $album = $this->Album->getUserAlbumByType($uid, 'profile');
                $title = __('Profile Pictures');
                if (empty($album)) {
                    $this->Album->save(array('user_id' => $uid, 'type' => 'profile', 'title' => $title), false);
                    $album_id = $this->Album->id;
                } else {
                    $album_id = $album['Album']['id'];
                }
                $tmp_photo_url = 'uploads' . DS . 'tmp' . DS . $tmp_name. '.' . $extension;
                // save to db
                $this->loadModel('Photo.Photo');
                $this->Photo->create();
                $this->Photo->set(array('user_id' => $uid,
                    'target_id' => $album_id,
                    'type' => 'Photo_Album',
                    'thumbnail' => $tmp_photo_url,
                ));
               
                $this->Photo->save();
                $this->Album->id = $album_id;
                $filename = explode('/', $tmp_photo_url);
                $filename1 = $filename[count($filename) - 1];
                $this->Album->save(array('cover' => $filename1));

            }
            
            //check redirect to gateway if select package
            $this->getEventManager()->dispatch(new CakeEvent('UserController.doAfterRegister', $this));
            
            return $this->User->id;
		}
		else
			echo __('Something went wrong. Please contact the administrators');
	}
	
	public function fb_register()
	{
		$this->loadModel('ProfileField');					
		$custom_fields = $this->ProfileField->getRegistrationFields( true );
		
		$fields = array( array( 'name' => 'name' ), 
						 array( 'name' => 'email' ),
						 array( 'name' => 'gender' ), 
						 array( 'name' => 'birthday' ),
						 array( 'name' => 'password' ),   
		);
		
		foreach ( $custom_fields as $field )
		{
			$options = array();
			
			if ( $field['ProfileField']['type'] == 'list' || $field['ProfileField']['type'] == 'multilist' )
			{
				$type = 'select';
				$values = explode("\n", $field['ProfileField']['values']);
				
				foreach ( $values as $val )
					$options[$val] = $val;
			}
			else				
				$type = 'text';			
			
			$tmp = array( 'name' 		=> 'field_' . $field['ProfileField']['id'], 
						  'description' => $field['ProfileField']['name'],
						  'type' 		=> $type							   
			);	
			
			if ( !empty( $options ) )
				$tmp['options'] = $options;				
			
			$fields[] = $tmp;
		}
		
		// handle registration code
		if ( Configure::read('core.enable_registration_code') )
			$fields[] = array( 'name' 		 => 'registration_code', 
							   'description' => __('Registration Code'),
							   'type' 		 => 'text'
							 );
		
		$fields[] = array( 'name' => 'captcha' );
		
		$this->set( 'fields', json_encode( $fields ) );
		$this->set( 'title_for_layout', __('Register with your Facebook account') );
	}

	public function do_fb_register()
	{
		$signed_request = $_REQUEST['signed_request'];
			
		list($encoded_sig, $payload) = explode('.', $signed_request, 2); 

		// decode the data
		$sig = $this->_base64_url_decode( $encoded_sig );
		$data = json_decode( $this->_base64_url_decode($payload), true );
		
		if (strtoupper($data['algorithm']) !== 'HMAC-SHA256') {
			$this->Session->setFlash(__('An error has occurred (01)'));
			$this->redirect( $this->referer() );
		}
		
		// check sig
		$expected_sig = hash_hmac('sha256', $payload, Configure::read('core.fb_app_secret'), $raw = true);
		if ($sig !== $expected_sig) {
			$this->Session->setFlash(__('An error has occurred (02)'));
			$this->redirect( $this->referer() );
		}
		
		$reg_data = $data['registration'];
		
		// check to see if user already has an account here
		$user = $this->User->findByEmail( $reg_data['email'] );
		
		if ( empty( $user ) )
		{
			$tmp = explode('/', $reg_data['birthday']);
			
			$reg_data['birthday']  = array( 'year' => $tmp[2], 'month' => $tmp[0], 'day' => $tmp[1] );				
			$reg_data['timezone']  = 0;
			$reg_data['password2'] = $reg_data['password'];
			$reg_data['gender']	   = ucfirst( $reg_data['gender'] );
			
			$uid = $this->_saveRegistration( $reg_data );
			
			$this->redirect('/users/view/' . $uid);
		}
		else
		{
			// log in	
			$this->Session->write('uid', $user['User']['id']);
			$this->redirect('/');
		}
	}

	private function _base64_url_decode($input) {
	    return base64_decode(strtr($input, '-_', '+/'));
	}

	public function picture()
	{
		$this->_checkPermission();
        $uid = $this->Auth->user('id');
        
        $this->redirect('/users/view/' . $uid);
	}	

	/*
	 * Display user's profile
	 * @mixed $param - userid or username
	 */	
	public function view( $param = null )
	{
        $this->set('is_profile_page',true);
		if ( is_numeric( $param ) ) // userid
		{
			$id   = $param;
			$user = $this->User->findById($id);

			// redirect to SEO url if username exists
			if ( !empty( $user['User']['username'] ) && empty( $this->request->named['activity_id'] ) )
			{
				$this->redirect('/-' . $user['User']['username']);
				exit;
			}
		}
		else // username
		{
			$user = $this->User->findByUsername($param);
			$id   = $user['User']['id'];
		}

		$this->_checkExistence( $user );

		if ( !$user['User']['active'] )
		{
			$this->Session->setFlash( __('The user\'s account you were trying to view has been disabled') );
			$this->redirect( '/pages/error' );
			exit;
		}
		
		MooCore::getInstance()->setSubject($user);

		$uid = $this->Auth->user('id');
		$this->loadModel('Friend');
		$areFriends = false;

		if ( !empty( $uid ) ) //  check if user is a friend
		{
			$areFriends = $this->Friend->areFriends( $uid, $user['User']['id'] );

			if ( $uid != $user['User']['id'] )
			{
				$mutual_friends = $this->Friend->getMutualFriends( $uid, $user['User']['id'], 5 );
				$this->set('mutual_friends', $mutual_friends);
			}
		}

		$friends = $this->Friend->getUserFriends( $id, null, 10 );

		// check if a friend request exists
		if ( !empty( $uid ) )
		{
			$this->loadModel( 'FriendRequest' );

			$request_sent = $this->FriendRequest->existRequest( $uid, $id );
			$this->set('request_sent', $request_sent);
		}

        //check if this user already sent you a friend request
        if(!empty($uid))
        {
            $this->loadModel( 'FriendRequest' );

            $respond = $this->FriendRequest->existRequest( $id, $uid );
            $this->set('respond', $respond);
            $request = $this->FriendRequest->findBySenderIdAndUserId($id,$uid);
            if(!empty($request))
                $this->set('request_id',$request['FriendRequest']['id']);
        }

        // get profile and cover album
        $this->loadModel('Photo.Album');
        
        //Album count
		$addition_param = null;
		$role_id = $this->_getUserRoleId();
     	if($this->Auth->user('id') == $user['User']['id'])
            $role_id = ROLE_ADMIN;
        else{
            if($areFriends)
                $addition_param['are_friend'] = $areFriends;
        }

        $albums_count = $this->Album->getAlbums('user', $user['User']['id'], 1,RESULTS_LIMIT, $addition_param, $role_id, true);
        $this->set('albums_count', $albums_count);

        if ( !empty( $user['User']['avatar'] ) )
        {
            $profile_album = $this->Album->find('first', array( 'conditions' => array( 'Album.user_id' => $user['User']['id'],
                                                                                       'Album.type'    => 'profile'
                                               ) ) );
            $profile_album_id = isset($profile_album['Album']['id']) ? $profile_album['Album']['id'] : '';
            $this->set('profile_album_id', $profile_album_id);
        }

        if ( !empty( $user['User']['cover'] ) )
        {
            $cover_album = $this->Album->find('first', array( 'conditions' => array( 'Album.user_id' => $user['User']['id'],
                                                                                     'Album.type'    => 'cover'
                                             ) ) );
            if($cover_album)
                $this->set('cover_album_id', $cover_album['Album']['id']);
        }

        // check online status
        $online = $this->User->getOnlineUsers();

        if ( in_array( $id, $online['userids'] ) && !$user['User']['hide_online'])
            $this->set('is_online', true);

		// check privacy
		$canView = $this->_canViewProfile( $user['User'] );

		if ( $canView )
		{
			$this->loadModel('Blog.Blog');
			$blogs = $this->Blog->getBlogs( 'user', $id, null, 3 );

			$this->loadModel('Group.GroupUser');
			$groups = $this->GroupUser->getGroups('user', $id);

			$this->loadModel('Video.Video');
			$videos = $this->Video->getVideos( 'user', $id, null, 2 );

			$this->set('blogs', $blogs);
			$this->set('groups', $groups);
			$this->set('videos', $videos);
		}

		if ( !empty( $this->request->named['activity_id'] ) ) // show the requested activity
		{
			$this->loadModel('Activity');
			$activity = $this->Activity->findById( $this->request->named['activity_id'] );
            $this->_getProfileDetail( $user );
			$this->_checkExistence( $activity );
			$activities = $this->Activity->getActivities( 'detail', $this->request->named['activity_id'] );
			$activity = $activities[0];
                        
                        // check group permission
                        if (isset($activity['Activity']['type']) && $activity['Activity']['type'] == 'Group_Group'){
                            $this->loadModel('Group.Group');
                            $target_id = $activity['Activity']['target_id'];
                            $group = $this->Group->find('first', array(
                                'conditions' => array(
                                    'Group.id' => $target_id
                                )
                            ));
                            $is_member = $this->GroupUser->isMember($uid, $target_id);
                            $group['Group']['is_member'] = $is_member;
                            $this->set('groupTypeItem', $group['Group']);
                        }

            // check event permission
            if (isset($activity['Activity']['type']) && $activity['Activity']['type'] == 'Event_Event'){
                $this->loadModel('Event.Event');
                $target_id = $activity['Activity']['target_id'];
                $event = $this->Event->findById($activity['Activity']['target_id']);
                if($event['Event']['type'] == PRIVACY_EVERYONE)
                    $is_invited = 1;
                else
                    $is_invited = $this->Event->EventRsvp->getMyRsvp($uid, $target_id);
                $this->set('eventTypeItem', $is_invited);
            }
                        
			// get activity likes
			if ( !empty( $uid ) )
			{
				$this->loadModel('Like');
				$activity_likes = $this->Like->getActivityLikes( $activities, $uid );
				$this->set('activity_likes', $activity_likes);
			}

			$this->set('activity', $activity);
		}
		elseif ( $canView )
			$this->_getProfileDetail( $user );

		$this->set('user', $user);
		$this->set('friends', $friends);
		$this->set('areFriends', $areFriends);
		$this->set('canView', $canView);
		$this->set('title_for_layout', $user['User']['name']);
                if ($user['User']['privacy'] == PRIVACY_EVERYONE || ($user['User']['privacy'] == PRIVACY_FRIENDS && $areFriends)){
                    $this->set('description_for_layout', String::truncate(strip_tags($user['User']['about']), 160, array('ellipsis' => '...', 'html' => false, 'exact' => false)));
                }
        $this->set('profileActivities',$this->Feeds->get());
	}

    // check privacy
    private function _canViewProfile( $user )
    {
        $canView = false;
        $uid = $this->Auth->user('id');
        $cuser = $this->_getUser();
        
        if ( $uid == $user['id'] || !empty($cuser['Role']['is_super']) )
            $canView = true;
        else        
        {
            switch ( $user['privacy'] )
            {
                case PRIVACY_EVERYONE:
                    $canView = true;
                    break;
                        
                case PRIVACY_FRIENDS:  
                    $this->loadModel('Friend'); 
                    $areFriends = $this->Friend->areFriends( $uid, $user['id'] );
                                 
                    if ( $areFriends )
                        $canView = true;
                    
                    break;
                    
                case PRIVACY_ME:
                    if ( $uid == $user['id'] )
                        $canView = true;
                        
                    break;
            }           
        }   
        
        return $canView;
    }

	public function ajax_profile($id = null)
	{
		$id = intval($id);	
		$user = $this->User->findById($id);		
        $canView = $this->_canViewProfile( $user['User'] );
        
        if ( $canView )
        {
    		$this->_getProfileDetail( $user );
    		
    		$this->set('user', $user);
            $this->set('profileActivities',$this->Feeds->get());
    		$this->render('/Elements/ajax/profile_detail');
        }
        else
        {
            $this->autoRender = false;
            echo __('Access denied');
        }
	}
	
	private function _getProfileDetail( $user )
	{
		$uid = $this->Auth->user('id');
		
		$this->loadModel('ProfileFieldValue');
		$this->loadModel('Activity');
		$this->loadModel('Photo.Album');
		
		$fields = $this->ProfileFieldValue->getValues( $user['User']['id'], true );
		
		MooCore::getInstance()->setSubject($user);
		
		$this->loadModel('Friend');
		
		$uid = $this->Auth->user('id');
		$this->loadModel('Friend');
		$areFriends = false;
		if ( !empty( $uid ) ) //  check if user is a friend
		{
			$areFriends = $this->Friend->areFriends( $uid, $user['User']['id'] );
		}
		
		//Album count
		$addition_param = null;
		$role_id = $this->_getUserRoleId();
     	if($this->Auth->user('id') == $user['User']['id'])
            $role_id = ROLE_ADMIN;
        else{
            if($areFriends)
                $addition_param['are_friend'] = $areFriends;
        }

        $albums = $this->Album->getAlbums('user', $user['User']['id'], null,4 , $addition_param, $role_id);
		
		$this->set('fields', $fields);
		$this->set('albums', $albums);
		$this->set('admins', array( $user['User']['id'] ) );
	}
	
	/*
	 * Display user's information
	 */	
	public function ajax_info( $uid = null )
	{
		$uid = intval($uid);	
		$user   = $this->User->findById( $uid );
        $canView = $this->_canViewProfile( $user['User'] );
        
        if ( $canView )
        {
    		$this->loadModel('ProfileFieldValue');
            $this->loadModel('Like');
                
    		$fields = $this->ProfileFieldValue->getValues( $uid, false, true );
    		$items  = $this->Like->getAllUserLikes( $uid );
    		
    		$this->set('user', $user);
    		$this->set('fields', $fields);
    		$this->set('items', $items);
    		$this->set('unions', count($items));
        }
        else
        {
            $this->autoRender = false;
            echo __('Access denied');
        }
	}
	
	public function profile_user_friends($uid = null) {
            $uid = intval($uid);
            $this->loadModel('Friend');
            $page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;
            
            $friends = $this->Friend->getUserFriends($uid, $page);
            $more_users = $this->Friend->getUserFriends($uid, $page + 1);
            $more_result = 0;
            if (!empty($more_users)){
                $more_result = 1;
            }

            $this->set('users', $friends);
            $this->set('more_result', $more_result);
            $this->set('more_url', '/users/profile_user_friends/' . $uid . '/page:' . ( $page + 1 ));
            $data = array(
                'page' => $page
            );
            $this->set('data', $data);
            if ($page > 1)
                $this->render('/Elements/lists/users_list');
            else
                $this->render('/Users/profile_user_friends');
            
        }

    public function ajax_albums( $uid = null )
	{
		$uid = intval($uid);
		$this->loadModel('Photo.Album');
		$page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;	
		
		$albums = $this->Album->getAlbums( 'user', $uid, $page );		
		
		$this->set('albums', $albums);
		$this->set('more_url', '/users/ajax_albums/' . $uid . '/page:' . ( $page + 1 ) );
		$this->set('user_id', $uid);
		
		if ( $page > 1 )
			$this->render('/Elements/lists/albums_list');		
	}
	
	
	
	public function ajax_avatar()
	{
		
	}

    public function avatar(){

    }
    
    public function ajax_cover()
    {
        $uid = $this->Auth->user('id');
        $this->loadModel('Photo.Photo');
        
        $photo = $this->Photo->find( 'first', array( 'conditions' => array(  'Album.type' => 'cover', 
                                                                             'Album.user_id' => $uid ),
                                                     'limit' => 1,
                                                     'order' => 'Photo.id desc'
                                   ) );
                                   
        $this->set('photo', $photo);
    }	
	
	public function profile()
	{
		$this->_checkPermission();
		$uid = $this->Auth->user('id');
		$this->_editProfile( $uid );
		
		$this->set('title_for_layout', __('Edit Profile'));
	}
	
	private function _editProfile( $uid = null , $adminEdit = false)
	{		
		$this->loadModel('ProfileFieldValue');
		$this->loadModel('ProfileField');	

		$values = array();
		if ( empty( $uid ) )
			$uid = $this->request->data['id'];
			
		if ( empty( $uid ) )
		{
			$this->Session->setFlash(__('Invalid user id'), 'default', array('class' => 'error-message'));
			$this->redirect( $this->referer() );
			exit;
		}

		// get all the profile field values
		$vals = $this->ProfileFieldValue->getValues( $uid );
		
		// format the profile field values array
		foreach ($vals as $val)
		{
			$values[$val['ProfileFieldValue']['profile_field_id']] = array( 'id' 	=> $val['ProfileFieldValue']['id'],
																			'value' => $val['ProfileFieldValue']['value'] );
		}

		if (!empty($this->request->data))
		{
			// get all the custom fields EXCLUDING headings
            $custom_fields = $this->ProfileField->find( 'all', array( 'conditions' => array( 'active' => 1, 'type <> ?' => 'heading' ) ) );
                				
			$this->User->id = $uid;
			$errors = array();

            $this->loadModel('Friend');
            $friends = $this->Friend->find('all', array('conditions' => array('Friend.user_id' => $uid)));

            //check activate user
            if(!empty( $this->request->data['active'] )){
                //this user has been deactivate before
                if(!$this->User->field('active')){
                    //update others friends count again
                    foreach ($friends as $item){
                        $this->User->updateAll(array(
                                'User.friend_count' => 'User.friend_count + 1'
                            ), array(
                                'User.id' => $item['Friend']['friend_id']
                            ));
                    }
                    $event = new CakeEvent('Controller.User.deactivate',$this,array('uid' => $uid));
                    $this->getEventManager()->dispatch($event);
                    if(!empty($event->result)){
                        $this->loadModel('Category');
                        foreach($event->result as $key => $value){
                            if (!empty($value)){
                                $category_id = key($value);
                                //$this->Category->updateAll(array('Category.item_count' => 'Category.item_count + '.$value[$category_id]),array('Category.id' => $category_id) );
                            }
                        }
                    }
                }
            }

			// check username
			if ( !empty( $this->request->data['username'] ) )
			{
			    if ( is_numeric( $this->request->data['username'] ) ) 
                {
				    $this->Session->setFlash( __('Username must not be a numeric value'), 'default', array('class' => 'error-message') );
    				$this->redirect( $this->referer() );
    				exit;
                }
                
                // check restricted usernames
                $restricted_usernames = Configure::read('core.restricted_usernames');
                if ( !empty($restricted_usernames) )
                {
                    $usernames = explode( "\n", $restricted_usernames);
    
                    foreach ( $usernames as $un )
                    {
                        if ( !empty( $un ) && ( trim($un) == $this->request->data['username'] ) )
                        {
                            $this->Session->setFlash( __('Username is restricted'), 'default', array('class' => 'error-message') );
                            $this->redirect( $this->referer() );
                            exit;
                        }
                    }
                }
			}

            $cuser = $this->_getUser();

            if ($cuser['role_id'] != ROLE_ADMIN)
                unset($this->request->data['role_id']);

            unset($this->request->data['ip_address']);
            unset($this->request->data['code']);
            //unset($this->request->data['featured']);
			
			if ( !$this->User->save( $this->request->data ) ) // save basic info				
				$errors = $this->User->invalidFields();
			
			/* Save custom fields */
			
			foreach ($custom_fields as $field)
			{
				$value = $this->request->data['field_' . $field['ProfileField']['id']];
				
				if ( $field['ProfileField']['required'] && empty( $value ) && !is_numeric( $value ) ) // check if field is required
					$errors[0][0] = $field['ProfileField']['name'] . __(' is required');                
				else
				{
					$value = ( is_array( $value ) ) ? implode( ', ', $value ) : $value;
					
					if ( !isset( $values[$field['ProfileField']['id']] ) ) // save new value
					{							
						$this->ProfileFieldValue->create();
						$this->ProfileFieldValue->save( array( 'user_id' 		  => $uid,
															   'profile_field_id' => $field['ProfileField']['id'],
															   'value' 			  => $value
													) 	);
					}						
					else if ( $value != $values[$field['ProfileField']['id']]['value'] ) // update current value
					{
						$this->ProfileFieldValue->id = $values[$field['ProfileField']['id']]['id'];
						$this->ProfileFieldValue->save( array( 'value' => $value ) );
					}
				}
			}

			if ( !empty( $errors ) )
				$this->Session->setFlash( current( current( $errors ) ), 'default', array('class' => 'error-message') );
			else
				$this->Session->setFlash( __('Your changes have been saved') ,'default',
                    array('class' => 'Metronic-alerts alert alert-success fade in' ));

            //delete friend list cache of friends of this users
            foreach($friends as $friend){
                Cache::delete('user_friend_prefetch_'.$friend['Friend']['friend_id']);
            }

            $user = $this->User->findById($uid);
            if ($adminEdit){
                $this->redirect('/admin/users');
            }else{
                $this->redirect( $this->referer() );
            }

		}
		else
		{
			// get all the custom fields INCLUDING headings
            $custom_fields = $this->ProfileField->find( 'all', array( 'conditions' => array( 'active' => 1 ) ) );
                
			$this->set('custom_fields', $custom_fields);
			$this->set('values', $values);
		}
	}

    public function password()
    {
        $this->_checkPermission();
        $uid = $this->Auth->user('id');
        
        if (!empty($this->request->data))
        {
            $this->User->id = $uid;
            $errors = array();
            $user = $this->User->read();
                
            if ( md5( $this->request->data['old_password'] . Configure::read('Security.salt') ) != $user['User']['password'] )
            {
                $this->Session->setFlash( __('Incorrect current password'), 'default', array('class' => 'error-message') );
                $this->redirect( $this->referer() );
                exit;
            }

            unset($this->request->data['role_id']);
            unset($this->request->data['ip_address']);
            unset($this->request->data['code']);
            unset($this->request->data['featured']);
            unset($this->request->data['username']);
            
            if ( !$this->User->save( $this->request->data ) )             
                $errors = $this->User->invalidFields();
            
            if ( !empty( $errors ) )
                $this->Session->setFlash( current( current( $errors ) ), 'default', array('class' => 'error-message') );
            else
                $this->Session->setFlash( __('Your password has been changed') );
                
            $this->redirect( $this->referer() );
        }
        
        $this->set('title_for_layout', __('Change Password'));
    }

	public function recover($state = null)
	{
            
            if ($this->Auth->user('id')){
                $this->Session->setFlash( __('You have logged in, so you can not view that page.'), 'default', array('class' => 'error-message') );
                $this->redirect('/');
            }
            
		if (!empty($this->request->data))
		{
			if ( empty( $this->request->data['email'] ) )
			{
				$this->Session->setFlash( __('Please enter an email address'), 'default', array('class' => 'error-message') );
				$this->redirect( '/users/recover' );
				exit;
			}
			
			$user = $this->User->findByEmail($this->request->data['email']);
			
			if (!empty($user))
			{			
				$this->loadModel('PasswordRequest');	
				$code = md5( Configure::read('Security.salt') . time() );

				if ( $this->PasswordRequest->save( array('user_id' => $user['User']['id'], 'code' => $code) ) )
				{
					//$this->_sendEmail( $this->request->data['email'], __('Password Change Request'), 'password_request', array('code' => $code) );
					$ssl_mode = Configure::read('core.ssl_mode');
        			$http = (!empty($ssl_mode)) ? 'https' :  'http';
					$this->MooMail->send($this->request->data['email'],'reset_password',
	    				array(	    					
	    					'recipient_title' => $user['User']['name'],
	    					'recipient_link' => $http.'://'.$_SERVER['SERVER_NAME'].$user['User']['moo_href'],
	    					'reset_link'=> $http.'://'.$_SERVER['SERVER_NAME'].$this->request->base.'/users/resetpass/'.$code,
	    				)
	    			);
					
					$this->redirect( '/users/recover/sent' );
				}
			}
			else
			{
				$this->Session->setFlash( __('Email does not exist'), 'default', array('class' => 'error-message') );
				$this->redirect( '/users/recover' );
			}
		}

		$this->set('state', $state);
	}
	
	public function resetpass( $code = null )
	{
		$this->loadModel('PasswordRequest');
			
		if ( !empty( $this->request->data ) )
		{		
			$request = $this->PasswordRequest->findByCode( $this->request->data['code'] );
			$this->_checkExistence( $request );
		
			$this->User->id = $request['PasswordRequest']['user_id'];
			$user = $this->User->read();
			
			$this->User->set( $this->request->data );		
			
			if ( !$this->User->validates() )
		    {
				$errors = $this->User->invalidFields();
				
		    	$this->Session->setFlash( current( current( $errors ) ), 'default', array('class' => 'error-message') );
				$this->redirect( $this->referer() );
		    }
			
			$this->User->save( array( 'password' => $this->request->data['password'] ) );
			$this->PasswordRequest->delete( $request['PasswordRequest']['id'] );
			
			$this->Session->setFlash( __('Your password has been reset') );
			$this->redirect( '/' );
		}
		else
		{
			$request = $this->PasswordRequest->findByCode( $code );
			$this->_checkExistence( $request );		
			$this->set('code', $code);
		}
	}
	
	public function do_confirm( $code = null )
	{
		$this->autoRender = false;
		$user = $this->User->findByCode( $code );
		
		if ( !empty(  $user ) )
		{
			$this->User->id = $user['User']['id'];
			$this->User->save( array( 'confirmed' => 1 ) );
			$this->Session->setFlash( __('Your account has been validated!') );
		}
		else
			$this->Session->setFlash( __('Invalid code!'), 'default', array('class' => 'error-message') );
			
		$this->redirect( '/' );
	}
	
	/*
	 * Check if a username exists or not 
	 */
	public function ajax_username()
	{
		$this->autoRender = false;		
		$username = $this->request->data['username'];
		$res = array( 'result' => 0 );
		
		if ( strlen( $username ) < 5 || strlen( $username ) > 50 )
			$res['message'] = __('Username must be between 5 and 50 characters long');
		elseif ( is_numeric( $username ) )
			$res['message'] = __('Username must not be a numeric value');
		elseif ( !ctype_alnum( $username ) )
			$res['message'] = __('Username must only contain alphanumeric characters (no special chars)');
		else		
		{
			// check restricted usernames
			$restricted_usernames = Configure::read('core.restricted_usernames');
			if ( !empty($restricted_usernames) )
			{
				$usernames = explode( "\n", $restricted_usernames );

				foreach ( $usernames as $un )
				{
					if ( !empty( $un ) && ( trim($un) == $username ) )
					{
						$res['message'] = __('Username is restricted');
						echo json_encode($res);						
						return;
					}
				}
			}
				
			// check available username
			$count = $this->User->find( 'count', array( 'conditions' => array( 'User.username' => $username ) ) );
		
			if ( $count )
				$res['message'] = __('Username is already taken');
			else
			{
				$res['result'] = 1;
				$res['message'] = __('Username is available');
			}
		}
		
		echo json_encode($res);
	}
	
	/*
	 * Deactivate user account
	 */
	public function deactivate()
	{
		$this->_checkPermission();
		$uid = $this->Auth->user('id');
        $cuser = $this->_getUser();
		
		if ( $cuser['Role']['is_super'] )
		{
			$this->Session->setFlash( __('Root admin account cannot be deactivated') , 'default', array('class' => 'error-message'));
			$this->redirect( $this->referer() );
		}
		else 
		{
			$this->User->id = $uid;
			$this->User->save( array( 'active' => 0 ) );
            $this->loadModel('Friend');
            $this->loadModel('FriendRequest');
            // update friend count
            $friends = $this->Friend->find('all', array('conditions' => array('Friend.user_id' => $uid)));
            foreach ($friends as $item){
                $this->User->updateAll(array(
                    'User.friend_count' => 'User.friend_count - 1'
                ), array(
                    'User.id' => $item['Friend']['friend_id']
                ));
            }
            //delete all friend request of this user
            $friendRequests = $this->FriendRequest->findAllBySenderId($uid);
            if(!empty($friendRequests)){
                foreach($friendRequests as $request){
                    $this->FriendRequest->delete($request['FriendRequest']['id']);
                }
            }
            //delete all notification of this user
            $this->loadModel('Notification');
            $userNotifications = $this->Notification->findAllBySenderId($uid);
            if(!empty($userNotifications)){
                foreach($userNotifications as $notification){
                    $this->Notification->delete($notification['Notification']['id']);
                }
            }
            //delete all feed of this user
            $this->loadModel('Activity');
            $userActivities = $this->Activity->findAllByUserId($uid);
            if(!empty($userActivities)){
                foreach($userActivities as &$activity){
                    $this->Activity->delete($activity['Activity']['id']);
                }
            }
            $event = new CakeEvent('Controller.User.deactivate',$this,array('uid' => $uid));
            $this->getEventManager()->dispatch($event);
            if(!empty($event->result)){
                $this->loadModel('Category');
                foreach($event->result as $key => $value){
                    if (!empty($value)){
                        $category_id = key($value);
                        //$this->Category->updateAll(array('Category.item_count' => 'Category.item_count - '.$value[$category_id]),array('Category.id' => $category_id) );
                    }             
                }
            }
            //update category count


			$this->Session->setFlash( __('Your account has been successfully deactivated') );
			$this->do_logout();
		}
                
                // event
                $cakeEvent = new CakeEvent('Controller.User.afterDeactive', $this, array('item' => $cuser));
                $this->getEventManager()->dispatch($cakeEvent);
	}
	
	/*
	 * Request Deletetion
	 */
	public function request_deletion()
	{
		$this->_checkPermission();
		$uid = $this->Auth->user('id');
		$cuser = $this->_getUser();
		
		$this->loadModel('AdminNotification');					
		$this->AdminNotification->save( array( 'user_id' => $uid,
											   'text' => __('requested to delete account'),
											   'url' => $this->request->base . '/admin/users/index/keyword:' . $cuser['email']
									) );
		
		$this->Session->setFlash( __('Your account deletion request has been submitted') );
		$this->redirect( $this->referer() );
	}
	
	/*
	 * Feature user
	 */
	public function admin_feature( $id = null )
	{
		if ( !empty( $id ) )
		{		
			$this->User->id = $id;		
			$this->User->save( array( 'featured' => 1 ) );
                        			
			$this->Session->setFlash( __('This user has been successfully featured') );
		}
					
		$this->redirect( $this->referer() );
	}
	
	/*
	 * Unfeature user
	 */
	public function admin_unfeature( $id = null )
	{
		if ( !empty( $id ) )
		{		
			$this->User->id = $id;		
			$this->User->save( array( 'featured' => 0 ) );
			
			$this->Session->setFlash( __('This user has been successfully unfeatured') );
		}
					
		$this->redirect( $this->referer() );
	}
	
	public function admin_index()
	{
		if ( !empty( $this->request->data['keyword'] ) )
			$this->redirect( '/admin/users/index/keyword:' . $this->request->data['keyword'] );
			
		$cond = array();
		if ( !empty( $this->request->named['keyword'] ) )
			$cond['MATCH(User.name, User.email) AGAINST(? IN BOOLEAN MODE)'] = $this->request->named['keyword'];
			
		$users = $this->paginate( 'User', $cond );	
		
		$this->set('users', $users);
		$this->set('title_for_layout', __('Users Manager'));
	}
	
	public function admin_edit( $id = null )
	{
		$this->set('title_for_layout', __('Users'));
		if ( empty($this->request->data) )
		{
			if ( empty( $id ) )
			{
				$this->Session->setFlash(__('Invalid user id'), 'default', array('class' => 'error-message'));
				$this->redirect( $this->referer() );
				exit;
			}
				
            $uid = $this->Auth->user('id');
			$user = $this->User->findById( $id );		
			$this->set('user', $user);

            if ( $user['Role']['is_super'] && $uid != $id && $uid != ROOT_ADMIN_ID )
            {
                $this->Session->setFlash(__('You cannot edit other super admins'), 'default', array('class' => 'error-message'));
                $this->redirect( $this->referer() );
                exit;
            }
            
            $this->loadModel('Role');
            $roles = $this->Role->find('list', array('field' => array('name')));
            
            foreach ($roles as $key => $r)
                if ( $key == ROLE_GUEST )
                    unset($roles[$key]);
            
            $this->set('roles', $roles);
		}
		
		$this->_editProfile( $id ,true);
	}

    public function admin_ajax_password( $id = null )
    {
        $this->set('id', $id);        
    }
    
    public function admin_do_password()
    {
        if (!empty($this->request->data))
        {
            $user = $this->User->findById( $this->request->data['id'] );
                
            $this->User->id = $this->request->data['id'];
            $this->User->set( $this->request->data );
            
            $this->_validateData($this->User);            
            $this->User->save();
            
            if ( !empty( $this->request->data['notify'] ) )
            {
            	$ssl_mode = Configure::read('core.ssl_mode');
        		$http = (!empty($ssl_mode)) ? 'https' :  'http';
				$this->MooMail->send($user['User']['email'],'admin_change_password',
    				array(	    					
    					'recipient_title' => $user['User']['name'],
    					'recipient_link' => $http.'://'.$_SERVER['SERVER_NAME'].$user['User']['moo_href'],
    					'password'=> $this->request->data['password'],
    				)
    			);
                
            }
            
            //$this->Session->setFlash( __('Password has been changed') );
            
            $response['result'] = 1;
            echo json_encode($response);
        }      
    }
	
	public function admin_avatar( $id = null )
	{
		if ( empty( $id ) )
			$this->Session->setFlash(__('Invalid user id'), 'default', array('class' => 'error-message'));
		else
		{
			$this->User->id = $id;
			$user = $this->User->findById( $id );
			
			$this->User->removeAvatarFiles( $user['User'] );			
			$this->User->save( array('photo' => '', 'avatar' => '') );	
			
			$this->Session->setFlash(__('User\'s avatar has been removed'));
		}	
			
		$this->redirect( $this->referer() );
	}
    
    public function admin_resend($id = null) {
        $user = $this->User->findById($id);
        
        if (empty($user)){
            $this->Session->setFlash(__('There is error while send email, please try again later.'));
            $this->redirect($this->referer());
        }
        
        $ssl_mode = Configure::read('core.ssl_mode');
        $http = (!empty($ssl_mode)) ? 'https' : 'http';
        if ($user['User']['confirmed']) {
            $this->MooMail->send($user['User']['email'], 'welcome_user', array(
                'email' => $user['User']['email'],
                'recipient_title' => $user['User']['name'],
                'recipient_link' => $http . '://' . $_SERVER['SERVER_NAME'] . $user['User']['moo_href'],
                'site_name' => Configure::read('core.site_name'),
                'login_link' => $http . '://' . $_SERVER['SERVER_NAME'] . $this->request->base . '/users/member_login',
                    )
            );
        } else {
            $this->MooMail->send($user['User']['email'], 'welcome_user_confirm', array(
                'email' => $user['User']['email'],
                'recipient_title' => $user['User']['name'],
                'recipient_link' => $http . '://' . $_SERVER['SERVER_NAME'] . $user['User']['moo_href'],
                'site_name' => Configure::read('core.site_name'),
                'confirm_link' => $http . '://' . $_SERVER['SERVER_NAME'] . $this->request->base . '/users/do_confirm/' . $user['User']['code'],
                    )
            );
        }

        $this->Session->setFlash(__('Validation email has been resent'));
        $this->redirect($this->referer());
    }

    public function admin_delete_content($id)
    {
        $this->_checkPermission(array('super_admin' => 1));
        $user = $this->User->findById($id);
        
        if ( !$user['Role']['is_super'] )
        {
            $this->_delete_user_contents($user);
            
            $this->Session->setFlash( __('All user\'s content has been deleted' ));
        }
        
        $this->redirect( $this->referer() );
    }
	
    public function admin_delete()
    {
            $this->_checkPermission(array('super_admin' => 1));

            if ( !empty( $_POST['users'] ) )
            {
                    $users = $this->User->find( 'all', array( 'conditions' => array( 'User.id' => $_POST['users'] ) ) );

                    foreach ( $users as $user )
                    {
                        $this->_delete_user_contents($user);

                        $this->User->delete( $user['User']['id'] );                      
                        $this->Session->setFlash( __('The selected users have been deleted'),'default',
                            array('class' => 'Metronic-alerts alert alert-success fade in' ));
                    }	
            }

            $this->redirect( $this->referer() );
    }

    public function admin_manage() {
        $type = $this->request->data['type'];
        $this->_checkPermission(array('super_admin' => 1));

        if (!empty($_POST['users'])) {
            $users = $this->User->find('all', array('conditions' => array('User.id' => $_POST['users'])));
            
            foreach ($users as $user) {
                switch ($type) {
                    case 'delete':
                        $this->_delete_user_contents($user);
                        $this->User->delete($user['User']['id']);
                        
                        // update friend count
                        $friends = $this->Friend->find('all', array('conditions' => array('Friend.user_id' => $user['User']['id'])));
                        foreach ($friends as $item){
                            $this->User->updateAll(array(
                                'User.friend_count' => 'User.friend_count - 1'
                            ), array(
                                'User.id' => $item['Friend']['friend_id']
                            ));
                        }
                        
                        $this->Session->setFlash(__('The selected users have been deleted'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
                        break;
                    case 'approve':
                        $this->User->id = $user['User']['id'];
                        $this->User->save(array(
                            'approved' => 1
                        ));
                        
                        $this->Session->setFlash(__('Selected user(s) have been approved successfully'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
                        break;
                    default:
                        break;
                }
            }
        }

        $this->redirect($this->referer());
    }

    private function _delete_user_contents($user)
    {
         // hook event : delete all user content
        $event = new CakeEvent('UserController.deleteUserContent', $this, array('passParams' => true, 'aUser' => $user));
        $this->getEventManager()->dispatch($event); 
        
        $this->loadModel('Activity');
        $this->loadModel('ActivityComment');
        $this->loadModel('Comment');
        $this->loadModel('Conversation');
        $this->loadModel('ConversationUser');
        $this->loadModel('Friend');
        $this->loadModel('FriendRequest');
        $this->loadModel('Like');
        $this->loadModel('Notification');
        $this->loadModel('ProfileFieldValue');
        $this->loadModel('Report');

        $this->User->removeAvatarFiles( $user['User'] );
        $this->User->removeCoverFile( $user['User'] );
        
        $this->Activity->deleteAll( array( 'Activity.user_id' => $user['User']['id'] ), true, true );
        $this->Activity->deleteAll( array( 'Activity.target_id' => $user['User']['id'], 'Activity.type' => APP_USER ), true, true );
        $this->ActivityComment->deleteAll( array( 'ActivityComment.user_id' => $user['User']['id'] ), true, true );
        
        $this->Comment->deleteAll( array( 'Comment.user_id' => $user['User']['id'] ), true, true );
        $this->Conversation->deleteAll( array( 'Conversation.user_id' => $user['User']['id'] ), true, true );
        $this->ConversationUser->deleteAll( array( 'ConversationUser.user_id' => $user['User']['id'] ), true, true );
        
        $this->Friend->deleteAll( array( 'Friend.user_id' => $user['User']['id'] ), true, true );
        $this->Friend->deleteAll( array( 'Friend.friend_id' => $user['User']['id'] ), true, true );                 
        $this->FriendRequest->deleteAll( array( 'FriendRequest.user_id' => $user['User']['id'] ), true, true );
        $this->FriendRequest->deleteAll( array( 'FriendRequest.sender_id' => $user['User']['id'] ), true, true );
        
        $this->Like->deleteAll( array( 'Like.user_id' => $user['User']['id'] ), true, true );
        $this->Notification->deleteAll( array( 'Notification.user_id' => $user['User']['id'] ), true, true );
        $this->Notification->deleteAll( array( 'Notification.sender_id' => $user['User']['id'] ), true, true );
        
        $this->ProfileFieldValue->deleteAll( array( 'ProfileFieldValue.user_id' => $user['User']['id'] ), true, true );
        $this->Report->deleteAll( array( 'Report.user_id' => $user['User']['id'] ), true, true );
        
        // event
        $cakeEvent = new CakeEvent('Controller.User.afterDelete', $this, array('item' => $user));
        $this->getEventManager()->dispatch($cakeEvent);
        
    }

    public function online_user(){
        if ($this->request->is('requested')) {
            $num_online_users = $this->request->named['num_online_users'];
            $this->loadModel('User');

            $online = $this->User->getOnlineUsers( $num_online_users);

            return $online;
        }
    }
    public function recently_joined(){
        if ($this->request->is('requested')){
            $num_new_members = $this->request->named['num_new_members'];
            $this->loadModel('User');
            $users = $this->User->getLatestUsers( $num_new_members);
            return $users;
        }
    }
    public function featured_member(){
        if ($this->request->is('requested')) {
            $num_item_show = $this->request->named['num_item_show'];
            $this->loadModel('User');
            $users = $this->User->getFeaturedUsers($num_item_show);
            return $users;
        }
    }
    public function friends(){
        $this->loadModel('Friend');
        if ($this->request->is('requested')) {
            $num_item_show = $this->request->named['num_item_show'];
            $id = $this->request->named['user_id'];

            return $this->Friend->getUserFriends( $id, null, $num_item_show );
        }else{
            $viewer = MooCore::getInstance()->getViewer(true);
            if($viewer){
                $friends = Cache::read('user_friend_prefetch_'.$viewer);
                if(empty($friends))
                {
                    $friends = $this->Friend->getUserFriends($viewer,null,100);
                    Cache::write('user_friend_prefetch_'.$viewer,$friends);
                }
                if($this->request->is('post'))
                {
                    $ids = $this->request->data['ids'];
                    $ids = explode(',',$ids);
                    $query = $this->request->data['q'];
                }
                if(!empty($friends)){
                    $response = array();
                    foreach ($friends as $key=>&$friend){
                        if(!empty($query))
                        {
                            if(strpos($friend['User']['name'],$query) !== 0)
                            {
                                unset($friends[$key]);
                                continue;
                            }
                        }
                        if(!empty($ids))
                        {
                            if(in_array($friend['User']['id'],$ids))
                            {
                                unset($friends[$key]);
                                continue;
                            }
                        }
                        $response[]= array(
                            'id'=>$friend['User']['id'],
                            'name'=>$friend['User']['name'],

                        );
                    }
                }
                $this->set(compact('friends'));

            }


        }

    }
    public function mutual_friends(){
        if ($this->request->is('requested')) {
            $uid = $this->request->named['uid'];
            $viewed_id = $this->request->named['viewed_id'];
            $this->loadModel('Friend');
            return $this->Friend->getMutualFriends( $uid, $viewed_id, 5 );
        }

    }

    public function member_login(){
        $email = isset($this->request->data['User']['email']) ? $this->request->data['User']['email'] : '';
        $passwd = isset($this->request->data['User']['password']) ? $this->request->data['User']['password'] : '';
        $remember = isset($this->request->data['remember']) ? $this->request->data['remember'] : false;

        
        // banned email
        if ($this->isBanned($email)){
            $this->autoRender = false;
            echo __('You are not allowed to view this site');
            exit;
        }
        
		if ($this->request->is('api')) {
			if ($this->request->is('post')) {
				if ($this->Auth->login()) {
					$this->OAuth2 = $this->Components->load('OAuth2');
					$this->OAuth2->setOwnerIdRewsoudRequest($this->Auth->user('id'));
					$this->OAuth2->sendReponse($this->OAuth2->createToken());
                    $this->_logMeIn( $email, $passwd , $remember );
					return true;
				}
			}
                        
			throw new BadRequestException(__('Your username or password was incorrect.'), 'default', array('class' => 'error-message'));
		}else{
			if ($this->request->is('post')) {
				if ($this->Auth->login()) {
                    $this->_logMeIn( $email, $passwd , $remember );
                    return $this->redirect($this->Auth->redirect());
				}
				$this->Session->setFlash(__('Your username or password was incorrect.'), 'default', array('class' => 'error-message'));
			}
		}

    }
    public function member_signup(){

    }
    public function suggestions() {
        if ($this->request->is('requested')) {
            $uid = $this->Auth->user('id');
            $num_item_show = $this->request->named['num_item_show'];
            if (!empty($uid)) {
                $this->loadModel('Friend');
                $friend_suggestions = $this->Friend->getFriendSuggestions($uid, false, $num_item_show);
                return $friend_suggestions;
            }
        }
    }
    public function ajax_register(){
        $this->autoRender = false;

        // check spam challenge
        if ( Configure::read('core.enable_spam_challenge') )
        {
            $this->loadModel('SpamChallenge');

            $challenge = $this->SpamChallenge->findById( $this->Session->read('spam_challenge_id') );
            $answers = explode("\n", $challenge['SpamChallenge']['answers']);

            $found = false;
            foreach ( $answers as $answer )
            {
                if ( strtolower( trim($answer) ) == strtolower( $this->request->data['spam_challenge'] ) )
                    $found = true;
            }

            if ( !$found )
            {
                echo __('Invalid security question');
                return;
            }
        }

        // check captcha
        $checkRecaptcha = MooCore::getInstance()->isRecaptchaEnabled();
        $recaptcha_privatekey = Configure::read('core.recaptcha_privatekey');
        if ( $checkRecaptcha)
        {
            App::import('Vendor', 'recaptchalib');
            $reCaptcha = new ReCaptcha($recaptcha_privatekey);
            $resp = $reCaptcha->verifyResponse(
                    $_SERVER["REMOTE_ADDR"], $_POST["g-recaptcha-response"]
            );

            if ($resp != null && !$resp->success) {
                echo __('Invalid security code');
                return;
            }
        }

        $this->_saveRegistration( $this->request->data );

    }
    public function getCustomField(){
        if($this->request->is('requested')){

            $this->loadModel('ProfileField');
            $custom_fields = $this->ProfileField->getRegistrationFields();
            return $custom_fields;

        }
    }
    public function social_login()
    {
        $this->autoRender = false;

        $email = $this->request->data['email'];
        // find the user
        $user = $this->User->find( 'first', array( 'conditions' => array( 'email' => trim( $email ) ) ) );

        if (!empty($user)) // found
        {

            if ( !$user['User']['active'] )
            {
                $this->Session->setFlash( __('This account has been disabled'), 'default', array( 'class' => 'error-message') );

                return $this->referer();
            }
            else
            {
                // save user id and user data in session
                $this->Session->write('uid', $user['User']['id']);

                // update last login
                $this->User->id = $user['User']['id'];
                $this->User->save( array( 'last_login' => date("Y-m-d H:i:s") ) );
            }
        }
        else{
            $this->Session->setFlash( __('Invalid email or password'), 'default', array('class' => 'error-message'));
            return  $this->referer() ;
        }

        $url = $this->referer();
        // redirect to the previous page

        if ( !empty( $this->request->data['return_url'] ) )
        {
            return base64_decode( $this->request->data['return_url'] ) ;
        }
        elseif ( strpos( $url, 'no-permission' ) === false && strpos( $url, 'error' ) === false &&
            strpos( $url, 'recover' ) === false && strpos( $url, 'resetpass' ) === false )
        {
            return true;
        }
        else

            return true;
    }

    public function getBirthday(){
        if ($this->request->is('requested')) {
            $num_birthday_users = $this->request->named['num_birthday_users'];
            $this->loadModel('User');

            $birthday_users = $this->User->getTodayBirthdayLimit($num_birthday_users);

            return $birthday_users;
        }
    }
    public function get_birthday_friend(){
        if ($this->request->is('requested')) {
            $utz = str_replace('-','/',$this->request->named['utz']);
            $uid = $this->Auth->user('id');

            $birthday_users = $this->User->getTodayBirthdayFriend($uid,$utz);
            return $birthday_users;
        }
    }
    public function ajax_birthday_more(){
        $uid = $this->Auth->user('id');
        $utz = str_replace('-','/',$this->request->named['utz']);
        $birthday_users = $this->User->getTodayBirthdayFriend($uid,$utz);
        $this->loadModel('Activity');
        $users_sent = $this->Activity->find('all',array(
            'conditions' => array(
                'Activity.params' => 'birthday_wish',
                'Activity.user_id' => $uid,
                'Activity.created LIKE' => date('Y-m-d').'%'
            ),
            'fields' => array('target_id')
        ));
        $a = '';
        foreach($users_sent as $u){
            $a[] = $u['Activity']['target_id'];
        }
        $users_sent = $a;
        $this->set('users_sent',$users_sent);
        $this->set('birthday',$birthday_users);
    }

    public function do_get_json()
    {
        $this->_checkPermission();

        $friends = $this->User->getUsers(1, array('User.active' => 1,'User.name LIKE "' . $this->request->query['q'] . '%"') );

        $friend_options = array();
        $mooHelper = MooCore::getInstance()->getHelper('Core_Moo');
        foreach ($friends as $friend){
            $avatar = $mooHelper->getImage(array('User' => $friend['User']), array('prefix' => '50_square', 'align' => 'absmiddle', 'style' => 'width: 40px'));
            $friend_options[] = array( 'id' => $friend['User']['id'], 'name' => $friend['User']['name'], 'avatar' => $avatar );
        }

        return json_encode( $friend_options );
    }
    
    // set a photo as cover
    public function set_photo_as_cover() {

        $this->autoRender = false;

        $uid = $this->Auth->user('id');

        $path = 'uploads' . DS . 'tmp' . DS;
        $url = 'uploads/tmp/';

        if (!$uid) {
            return;
        }

        $photo_id = $this->request->data['photo_id'];

        if (!$photo_id) {
            exit;
        }

        $this->loadModel('Photo.Photo');
        $this->loadModel('Photo.Album');
        $aPhoto = $this->Photo->findById($photo_id);

        $photo_path = WWW_ROOT . 'uploads' . DS . 'photos' . DS . 'thumbnail' . DS . $aPhoto['Photo']['id'] . DS . $aPhoto['Photo']['thumbnail'];

        // copy to tmp path
        $file = $photo_path;
        $newTmpAvatar = WWW_ROOT . $path . $aPhoto['Photo']['thumbnail'];
        copy($file, $newTmpAvatar);
        $newTmpAvatar1 = WWW_ROOT . $path . 'tmp_' . $aPhoto['Photo']['thumbnail'];
        copy($file, $newTmpAvatar1);

        $album = $this->Album->getUserAlbumByType($uid, 'cover');
        $title = __('Cover Pictures');

        if (empty($album)) {
            $this->Album->save(array('user_id' => $uid, 'type' => 'cover', 'title' => $title), false);
            $album_id = $this->Album->id;
            $album = $this->Album->initFields();
        } else{
            $album_id = $album['Album']['id'];
        }

        // resize image
        App::import('Vendor', 'phpThumb', array('file' => 'phpThumb/ThumbLib.inc.php'));

        $photo = PhpThumbFactory::create($path . DS . $aPhoto['Photo']['thumbnail']);

        // save to db
        $this->loadModel('Photo.Photo');
        $this->Photo->create();
        $this->Photo->set(array('user_id' => $uid,
            'target_id' => $album_id,
            'type' => 'Photo_Album',
            'thumbnail' => $path . $aPhoto['Photo']['thumbnail'],
        ));
        $this->Photo->save();

        // save album cover
        if (isset($album['Album']['cover']) && !$album['Album']['cover']){
            $this->Album->id = $album_id;
            $this->Album->save(array('cover' => $aPhoto['Photo']['thumbnail']));
        }

        /* Create and update cover */
        $cover_path = WWW_ROOT . 'uploads' . DS . 'covers';
        $cover_loc = $cover_path . DS . $aPhoto['Photo']['thumbnail'];

        if (!file_exists($cover_path)) {
            mkdir($cover_path, 0755, true);
            file_put_contents(WWW_ROOT . $path . DS . 'index.html', '');
        }

        // resize image
        $cover = PhpThumbFactory::create($path . 'tmp_' . $aPhoto['Photo']['thumbnail'], array('jpegQuality' => PHOTO_QUALITY));
        $cover->adaptiveResize(COVER_WIDTH, COVER_HEIGHT)->save($cover_loc);

        // delete tmp thumbnail
        if (file_exists(WWW_ROOT . $path . 'tmp_' . $aPhoto['Photo']['thumbnail'])){
            unlink(WWW_ROOT . $path . 'tmp_' . $aPhoto['Photo']['thumbnail']);
        }
        
        $this->loadModel('User');
        $user = $this->User->findById($uid);

        // delete old files
        $this->User->removeCoverFile($user['User']);

        // update user cover pic in db
        $this->User->id = $uid;
        $this->User->save(array('cover' => $aPhoto['Photo']['thumbnail']));
        
        echo htmlspecialchars(json_encode(array('url' => $user['User']['moo_href'])), ENT_NOQUOTES);
    }

    // set photo as profile picture
    public function set_photo_as_profile_picture() {
        $this->autoRender = false;

        $uid = $this->Auth->user('id');

        $path = 'uploads' . DS . 'tmp' . DS;
        $url = 'uploads/tmp/';

        if (!$uid) {
            return;
        }

        $photo_id = $this->request->data['photo_id'];

        if (!$photo_id) {
            exit;
        }

        $this->loadModel('Photo.Photo');
        $this->loadModel('Photo.Album');
        $aPhoto = $this->Photo->findById($photo_id);

        $photo_path = WWW_ROOT . 'uploads' . DS . 'photos' . DS . 'thumbnail' . DS . $aPhoto['Photo']['id'] . DS . $aPhoto['Photo']['thumbnail'];

        // copy to tmp path
        $file = $photo_path;
        $newTmpAvatar = WWW_ROOT . $path . $aPhoto['Photo']['thumbnail'];
        $newTmpAvatar1 = WWW_ROOT . $path . 'tmp_' . $aPhoto['Photo']['thumbnail'];
        copy($file, $newTmpAvatar);

        copy($file, $newTmpAvatar1);
        
        $album = $this->Album->getUserAlbumByType($uid, 'profile');
        $title = __('Profile Pictures');

        if (empty($album)) {
            $this->Album->save(array('user_id' => $uid, 'type' => 'profile', 'title' => $title), false);
            $album_id = $this->Album->id;
            $album = $this->Album->initFields();
        } else {
            $album_id = $album['Album']['id'];
        }

        // save to db
        $this->loadModel('Photo.Photo');
        $this->Photo->create();
        $this->Photo->set(array('user_id' => $uid,
            'target_id' => $album_id,
            'type' => 'Photo_Album',
            'thumbnail' => $path . $aPhoto['Photo']['thumbnail'],
        ));
        $this->Photo->save();

        if (isset($album['Album']['cover']) && !$album['Album']['cover']){
            $this->Album->save(array('cover' => $aPhoto['Photo']['thumbnail']));
            $this->Album->id = $album_id;
        }

        $this->loadModel('User');
        $user = $this->User->findById($uid);
        
        $this->User->id = $uid;
        $this->User->set(array('avatar' => $path . 'tmp_' . $aPhoto['Photo']['thumbnail']));
        $this->User->save();
        
        // insert into activity feed
        if ($user['User']['last_login'] != $user['User']['created']) {
            $this->loadModel('Activity');
            $activity = $this->Activity->getRecentActivity('user_avatar', $uid);

            if (empty($activity)) {
                $this->Activity->save(array('type' => 'user',
                    'action' => 'user_avatar',
                    'user_id' => $uid
                ));
            }
        }
        
        echo htmlspecialchars(json_encode(array('url' => $user['User']['moo_href'])), ENT_NOQUOTES);

    }
    
    public function tagging(){
        $tagging_id = isset($this->request->named['tagging_id']) ? $this->request->named['tagging_id'] : '';
        $this->loadModel('UserTagging');
        $tagging = $this->UserTagging->find('first', array('conditions' => array('UserTagging.id' => $tagging_id)));
        $users_taggings = explode(',', $tagging['UserTagging']['users_taggings']);
        $users = $this->User->find('all', array('conditions' => array(
            'User.id' => $users_taggings
        )));
        $this->set(compact('users'));
    }
    
    public function ajax_friend_added(){
        $activity_id = $this->request->named['activity_id'];
        $this->loadModel('Activity');
        $activity = $this->Activity->findById($activity_id);
        if (!empty($activity)){
            $items = $activity['Activity']['items'];
            $ids = explode(',', $items);
            $this->loadModel('User');
            $users = $this->User->find('all', array('conditions' => array(
                'User.id' => $ids
            )));
            $this->set(compact('users'));
        } 
    }

    public function get_user_mention(){
        $viewer = MooCore::getInstance()->getViewer(true);
        if($viewer){
            $users = null;
            if($this->request->is('post'))
            {
                $ids = $this->request->data['ids'];
                $ids = explode(',',$ids);
                $query = $this->request->data['q'];
                $users = $this->User->getAllUser($query,$ids);
            }
            if(!empty($users)){
                $response = array();
                foreach ($users as $key=>&$user){
                    $response[]= array(
                        'id'=>$user['User']['id'],
                        'name'=>$user['User']['name'],
                    );
                }
            }
            $this->set(compact('users'));

        }
    }

    public function admin_login_as_user($id = null){
        $user = $this->User->findById($id);
        if(!empty($user)){
            $cuser = $user['User'];
            $cuser['Role'] = $user['Role'];
            unset($cuser['password']);
            $this->Auth->login($cuser);
            $this->redirect('/');
        }
    }

    public function accept_cookie(){
        $this->autoRender = false;
        $answer = $this->request->data['answer'];
        switch ($answer) {
            case 1://user accepted to store cookies
                $this->Cookie->write('accept_cookie',1,true, 60*60*24*30);
                $msg = array('result' => '1');
                break;
            default:
                $msg = array('result' => 0,'url' => Configure::read('core.deny_url'));
        }
        echo json_encode($msg);
        return;
    }
    
}
 
