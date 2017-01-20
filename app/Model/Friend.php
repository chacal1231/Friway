<?php

/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */

class Friend extends AppModel {
		
	public $belongsTo = array( 'User'  => array('counterCache' => true	));

	/*
	 * Return a list of friends for dropdown list
	 * @param int $uid
	 * @param array $excludes an array of user ids to exclude
	 */
	public function getFriendsList( $uid, $excludes = array() )
	{
		$this->unbindModel(
			array('belongsTo' => array('User'))
		);

		$this->bindModel(
			array('belongsTo' => array(
					'User' => array(
						'className' => 'User',
						'foreignKey' => 'friend_id'
					)
				)
			)
		);

		$cond = array( 'Friend.user_id' => $uid, 'User.active' => 1 );
		
		if ( !empty( $excludes ) )
			$cond['NOT'] = array( 'Friend.friend_id' => $excludes );
		
		$friends = $this->find( 'all', array( 'conditions' => $cond, 
											  'fields' 	   => array( 'User.id', 'User.name', 'User.avatar' ),
											  'order'	   => 'User.name asc'
							) 	); // have to do this because find(list) does not work with bindModel
		$friend_options = array();

		foreach ($friends as $friend)
			$friend_options[$friend['User']['id']] = $friend['User']['name'];

		return $friend_options;
	}
        
        public function getFriendListAsString($uid){
            $sFriendsList = '';
            $aFriendListId =  array_keys($this->getFriendsList($uid));
            $sFriendsList = implode(',',$aFriendListId);
            return $sFriendsList;
        }

        /*
	 * Return an array of friend ids
	 */
	public function getFriends( $uid )
	{
		$friends = $this->find( 'list' , array( 'conditions' => array( 'Friend.user_id' => $uid ), 
												'fields' => array( 'friend_id' ) 
							) );	
		return $friends;
	}
	
	/*
	 * Return a list of friends for displaying
	 */
	public function getUserFriends( $uid, $page = 1, $limit = RESULTS_LIMIT )
	{
		$this->unbindModel(
			array('belongsTo' => array('User'))
		);

		$this->bindModel(
			array('belongsTo' => array(
					'User' => array(
						'className' => 'User',
						'foreignKey' => 'friend_id'
					)
				)
			)
		);

		$friends = $this->find('all', array( 'conditions' => array( 'Friend.user_id' => $uid, 'User.active' => 1 ), 
		                                     'order' => 'Friend.id desc',
											 'limit' => $limit, 
											 'page' => $page)
		);

		return $friends;
	}
    
    /*
     * Return a list of friends for searching
     */
    public function searchFriends( $uid, $q )
    {
        $this->unbindModel(
            array('belongsTo' => array('User'))
        );

        $this->bindModel(
            array('belongsTo' => array(
                    'User' => array(
                        'className' => 'User',
                        'foreignKey' => 'friend_id'
                    )
                )
            )
        );
        
        $friends = $this->find( 'all', array( 'conditions' =>  array( 'Friend.user_id' => $uid, 
                                                                      'User.active' => 1,
                                                                      'User.name LIKE "' . $q . '%"' ), 
                                              //'fields'     => array( 'User.id', 'User.name', 'User.avatar' ),
                                              'order'      => 'User.name asc'
                            )   ); 

        return $friends;
    }
	
	/*
	 * Get friend suggestions of $uid (mutual friends)
	 * @param int $uid
	 * @param boolean $bigList - view all list or not (right column block)
	 * @return array $suggestions
	 */
	
	public function getFriendSuggestions($uid, $bigList = false, $limit = 2) {
            // get friends of current user
            $friends = $this->getFriends($uid);
            $suggestions = array();

            if (!empty($friends)) {
                App::import('Model', 'FriendRequest');

                // get friend requests of current users
                $req = new FriendRequest();
                $requests = $req->find('list', array('conditions' => array('FriendRequest.sender_id' => $uid),
                    'fields' => array('FriendRequest.user_id')
                        ));
                $be_requests = $req->find('list', array('conditions' => array('FriendRequest.user_id' => $uid),
                    'fields' => array('FriendRequest.sender_id')
                        ));
                
                // merge with friends list
                $not_in = array_merge($friends, $requests, $be_requests);
                $not_in[] = $uid;
                
                $this->unbindModel(
                        array('belongsTo' => array('User'))
                );

                $this->bindModel(
                        array('belongsTo' => array(
                                'User' => array(
                                    'className' => 'User',
                                    'foreignKey' => 'friend_id'
                                )
                            )
                        )
                );

                if ($bigList) {
                    $suggestions = $this->find('all', array('conditions' => array('Friend.user_id' => $friends,
                            'User.active' => 1,
                            'NOT' => array('Friend.friend_id' => $not_in)
                        ),
                        'fields' => array('DISTINCT User.id', 'User.*',
                            '(SELECT count(*) FROM ' . $this->tablePrefix . 'friends WHERE user_id = User.id AND friend_id IN (' . implode(',', $friends) . ') ) as count'
                        ),
                        'order' => 'count desc',
                        'limit' => RESULTS_LIMIT * 2));
                } else {
                    $suggestions = $this->find('all', array('conditions' => array('Friend.user_id' => $friends,
                            'User.active' => 1,
                            'NOT' => array('Friend.friend_id' => $not_in),
                        ),
                        'fields' => array('DISTINCT User.id', 'User.*',
                            '(SELECT count(*) FROM ' . $this->tablePrefix . 'friends WHERE user_id = User.id AND friend_id IN (' . implode(',', $friends) . ') ) as count'
                        ),
                        'limit' => $limit,
                        'order' => 'rand()'
                    ));
                }
            }

            return $suggestions;
        }

        public function getMutualFriends( $uid1, $uid2, $limit = RESULTS_LIMIT, $page = 1 )
	{
		// get friends of the first user
		$friends = $this->getFriends( $uid1 );
		$mutual_friends = array();			
		
		if ( !empty( $friends ) )
		{			
			$this->unbindModel(
				array('belongsTo' => array('User'))
			);
	
			$this->bindModel(
				array('belongsTo' => array(
						'User' => array(
							'className' => 'User',
							'foreignKey' => 'friend_id'
						)
					)
				)
			);	

			$mutual_friends = $this->find('all', array('conditions' => array( 'Friend.user_id' => $uid2, 
																		   	  'User.active' => 1, 
																		  	  'Friend.friend_id' => $friends																		   	  
																		 ), 																		 
													   'fields' => array( 'DISTINCT User.id', 'User.name', 'User.avatar', 'User.friend_count', 'User.photo_count', 'User.gender','User.*'),
													   'limit' => $limit,
													   'page' => $page
			)	);
		}
		
		return $mutual_friends;
	}
        
    // auto add friendList to user uid
    public function autoFriends($uid = null, $friendList = array()) {

        if (!$uid || empty($friendList)) {
            return false;
        }

        foreach ($friendList as $friend_id) {
            
            $friendModel = MooCore::getInstance()->getModel('Friend');
            
            // insert to friends table
            $friendModel->create();
            $friendModel->save(array('user_id' => $uid, 'friend_id' => $friend_id));
            $friendModel->create();
            $friendModel->save(array('user_id' => $friend_id, 'friend_id' => $uid));

            // insert into activity feed
            $activityModel = MooCore::getInstance()->getModel('Activity'); 
            $activity = $activityModel->getRecentActivity('friend_add', $uid);

            if (!empty($activity)) {
                // aggregate activities
                $user_ids = explode(',', $activity['Activity']['items']);

                if (!in_array($friend_id, $user_ids)){
                    $user_ids[] = $friend_id;
                }
                    
                $activityModel->id = $activity['Activity']['id'];
                $activityModel->save(array('items' => implode(',', $user_ids),
                    'params' => '',
                    'privacy' => 1,
                    'query' => 1
                ));
            }
            else {
                $activityModel->create();
                $activityModel->save(array('type' => 'user',
                    'action' => 'friend_add',
                    'user_id' => $uid,
                    'item_type' => APP_USER,
                    'items' => $friend_id
                ));
            }
        }
    }

    /*
	 * Are we friends?
	 */
	public function areFriends( $uid1, $uid2 )
	{
		$this->cacheQueries = true;
		
		$count = $this->find( 'count', array( 'conditions' => array( 'Friend.user_id' => $uid1, 'Friend.friend_id' => $uid2 ) ) );
		return $count;		
	}
    public function afterSave($created, $options = array()){
        Cache::delete('user_friends_'.$this->data['Friend']['user_id']);
        Cache::delete('user_friend_prefetch_'.$this->data['Friend']['user_id']);
        $friends = $this->findAllByUserId($this->data['Friend']['user_id']);
        foreach($friends as &$friend)
        {
            Cache::delete('mutual_friends_'.$this->data['Friend']['user_id'].'_'.$friend['Friend']['friend_id']);
            Cache::delete('mutual_friends_'.$friend['Friend']['friend_id'].'_'.$this->data['Friend']['user_id']);
        }
    }
    public function beforeDelete(){
        Cache::delete('user_friends_'.$this->field('user_id'));
        $friends = $this->findAllByUserId($this->field('user_id'));
        foreach($friends as &$friend)
        {
            Cache::delete('mutual_friends_'.$this->field('user_id').'_'.$friend['Friend']['friend_id']);
            Cache::delete('mutual_friends_'.$friend['Friend']['friend_id'].'_'.$this->field('user_id'));
        }
    }
}
 