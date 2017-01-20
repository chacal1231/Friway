<?php

/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */
App::uses('AuthComponent', 'Controller/Component');
App::uses('MooPasswordHasher', 'Controller/Component/Auth');
class User extends AppModel
{
    public $belongsTo = array( 'Role' );
    
    public $actsAs = array(
        'MooUpload.Upload' => array(
            'avatar' => array(
                'path' => '{ROOT}webroot{DS}uploads{DS}users{DS}{field}{DS}',
                'thumbnailSizes' => array(
                    'size' => array('50_square','100_square','200_square', '600')
                ),
            ),
            'photo' => array(
                'path' => '{ROOT}webroot{DS}uploads{DS}users{DS}{field}{DS}',
                'thumbnailSizes' => array(
                    'size' => array('50_square','100_square','200_square')
                ),
            )
        )
    );
    
    public $mooFields = array('title','href','plugin','type','url', 'thumb');
    		
	public $validate = array(  
			'name' => 	array( 	 
				'rule' => 'notBlank',
				'message' => 'Name is required'
			),
			'email' => 	array( 	 
				'email' => array(
					  'rule' => 'email',		  
					  'allowEmpty' => false,
					  'message' => 'Email must be a valid email'
				),
				'uniqueEmail' => array(
					  'rule' => 'isUnique',
					  'message' => 'Email already exists'
			    ),
			    'required' => array(
					  'rule' => 'notBlank',
					  'required' => true,		  
					  'on' => 'create',
					  'message' => 'Email is required'
				)
			),
			'password' => array( 
				'rule' => array('minLength', 6),
				'allowEmpty' => false,
				'message' => 'Password must have at least 6 characters'
			),
			'password2' => array( 
				'identicalFieldValues' => array(
					  'rule' => array('identicalFieldValues', 'password' ),
					  'message' => 'Passwords do not match'
				)	
			),
			'birthday' => array( 	 
				'rule' => 'notBlank',
				'message' => 'Birthday is required'
			),
			'gender' =>	array( 	 
				'rule' => 'notBlank',
				'message' => 'Gender is required'
			),
			'username' => 	array( 	 
				'username' => array(
					  'rule' => 'alphaNumeric',
					  'allowEmpty' => true,
					  'message' => 'Username must only contain letters and numbers'
				),
				'length' => array(
					  'rule'    => array('between', 5, 50),
        			  'message' => 'Username must be between 5 and 50 characters long.'
				),
				'uniqueUsername' => array(
					  'rule' => 'isUnique',
					  'message' => 'Username already exists'
			    )
			)
	);
	
	// Identical field validation rule
	public function identicalFieldValues( $field=array(), $compare_field=null ) 
    {
        foreach( $field as $key => $value ){
            $v1 = $value;
            $v2 = $this->data[$this->name][ $compare_field ];                 
            if($v1 !== $v2) {
                return FALSE;
            } else {
                continue;
            }
        }
        return TRUE;
    } 
	
	// Hash the password before saving user data
    public function beforeSave($options = array())
	{
        if ( !empty( $this->data['User']['password'] ) ){
            $passwordHasher = new MooPasswordHasher();
            $this->data['User']['password'] = $passwordHasher->hash(
                $this->data['User']['password']
            );
        }

        return true;
		if ( !empty( $this->data['User']['password'] ) )
			$this->data['User']['password'] = md5( $this->data['User']['password'] . Configure::read('Security.salt') );
			
		return true;
	}
	function unserialize_php($session_data) {
		$return_data = array();
		$offset = 0;
		while ($offset < strlen($session_data)) {
			if (!strstr(substr($session_data, $offset), "|")) {
				throw new Exception("invalid data, remaining: " . substr($session_data, $offset));
			}
			$pos = strpos($session_data, "|", $offset);
			$num = $pos - $offset;
			$varname = substr($session_data, $offset, $num);
			$offset += $num + 1;
			$data = unserialize(substr($session_data, $offset));
			$return_data[$varname] = $data;
			$offset += strlen(serialize($data));
		}
		return $return_data;
	}
	/*
	 * Get current online users
	 * @param int $interval - interval to check
	 * @return array $res
	 */
	public function getOnlineUsers( $limit = 12, $interval = 1200 )
        {
            $userids = array();
            $guests = 0;
            $time = time() - intval($interval);
            $sessions = $this->query('SELECT data FROM ' . $this->tablePrefix . 'cake_sessions WHERE expires > ' . $time);


            foreach ($sessions as $session) {

                $data = $session[$this->tablePrefix.'cake_sessions']['data'];


                $data = $this->unserialize_php($data);

                if (empty($data["Auth"]['User']['id'])){
                    $guests++;
                }else{
                    if (!in_array($data["Auth"]['User']['id'],$userids)){
                        $userids[] = $data["Auth"]['User']['id'];
                    }
                }

            }


            $members = array();
            if (!empty($userids)) {
                $members = $this->find('all', array('conditions' => array('User.id' => $userids, 'User.hide_online' => 0), 'limit' => intval($limit)));
            }

            $total = $guests + count($userids);
            
            $res = array('guests' => $guests,
                'members' => $members,
                'total' => $total,
                'userids' => $userids
            );
            
            return $res;
        }

       /*
	 * Get array of users based on $conditions
	 * @param int $page
	 * @param array $conditions
	 * @return array $users
	 */
	public function getUsers( $page = 1, $conditions = null, $limit = RESULTS_LIMIT )
	{
		if ( empty( $conditions ) )
			$conditions = array( 'User.active' => 1 );
			
		$users = $this->find('all', array( 'conditions' => $conditions, 
										   'limit' 		=> $limit,
										   'page'  		=> $page,
										   'order' 		=> 'User.id desc'
								)	);
		return $users;
	}

    /*
	 * Get all users
	 */

    public function getAllUser($q = '',$notIn = array(),$limit = RESULTS_LIMIT,$page = 1)
    {
        $users = $this->find( 'all', array( 'conditions' =>  array(

                'User.active' => 1,
                'User.name LIKE "' . $q . '%"',
                'NOT' => array('User.id' => $notIn),
            ),
            'limit' => $limit,
            'page' => $page,
            'order'      => 'User.name asc'

        )   );

        return $users;

    }
	
	
	/*
	 * Remove user's avatar files
	 * @param object $user
	 */
	public function removeAvatarFiles( $user )
	{
		$path = WWW_ROOT . 'uploads' . DS . 'avatars';
		
		if ($user['photo'] && file_exists($path . DS .$user['photo']))
			unlink($path . DS . $user['photo']);
			
		if ($user['avatar'] && file_exists($path . DS .$user['avatar']))
			unlink($path . DS . $user['avatar']);
	}
    
    /*
     * Remove user's cover file
     * @param object $user
     */
    public function removeCoverFile( $user )
    {
        $path = WWW_ROOT . 'uploads' . DS . 'covers';
        
        if ($user['cover'] && file_exists($path . DS .$user['cover']))
            unlink($path . DS . $user['cover']);
    }
	
	public function getTodayBirthday()
	{
		$birthday_users = Cache::read('birthday_users');
        
        if ( !is_array( $birthday_users ) ) 
        {
            $today = date('m-d');
            $birthday_users = $this->find( 'all', array( 'conditions' => array( 'active' => 1, 'SUBSTRING(birthday, 6)' => $today ) ) );
        
            Cache::write('birthday_users', $birthday_users);
        }
		
		return $birthday_users;
	}

    public function getTodayBirthdayFriend($userId,$timezone)
    {
        //date_default_timezone_set($timezone);
        $today = new DateTime('now', new DateTimeZone($timezone));
        $today = $today->format('m-d');
        $birthday_users = $this->find('all', array(
            'joins' => array(
                array(
                    'table' => 'friends',
                    'alias' => 'Friend',
                    'type' => 'INNER',
                    'conditions' => array(
                        'User.id = Friend.friend_id'
                    )
                )
            ),
            'conditions' => array(
                'Friend.user_id' => $userId,
                'active' => 1,
                'SUBSTRING(birthday, 6)' => $today,
            ),
            'fields' => array('User.*'),

        ));

        return $birthday_users;
    }
	
	public function getLatestUsers( $limit = 10 )
	{
		$users = $this->find( 'all', array( 'conditions' => array( 'active' => 1 ), 
											'order' => 'User.id desc', 
											'limit' => $limit
							)	);
		
		return $users;
	}
	
	public function getFeaturedUsers($limit = 10)
	{
		$users = $this->find( 'all', array( 'conditions' => array( 'active' => 1, 'featured' => 1 ), 
											'order' => 'User.id desc', 
											'limit' => $limit
							)	);
		
		return $users;
	}

	public function afterSave($created, $options = array()) {
        $cakeEvent = new CakeEvent('Model.User.afterSave', $this, array('id' => $this->field('id'),'created' => $created));
        $this->getEventManager()->dispatch($cakeEvent);
        //delete cache when user update something
        if(!$created){
            Cache::clearGroup('blog');
            Cache::clearGroup('event');
            Cache::clearGroup('group');
            Cache::clearGroup('photo');
            Cache::clearGroup('video');
        }
    }
    
	public function getHref($row)
    {
    	$request = Router::getRequest();
    	if (!empty( $row['username']))
    		return $request->base . '/-' . $row['username'];
    	elseif (!empty($row['id']))
    		return $request->base.'/users/view/'.$row['id'];
    		
    	return false;
    }
    
    public function getTitle($row)
    {
    	if (isset($row['name']))
    		return $row['name'];
    }
    
    public function getThumb($row)
    {
        return 'avatar';
    }
}