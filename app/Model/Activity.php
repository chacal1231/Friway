<?php

/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */
App::uses('CakeEvent', 'Event');
class Activity extends AppModel 
{
	public $mooFields = array('type');
	
	public $validate = array('user_id' => array( 'rule' => 'notBlank'));								 
						 
	public $belongsTo = array( 'User' );
	
	public $hasMany = array( 'ActivityComment' => array( 
														 'className' => 'ActivityComment',							
													  	 'dependent' => true,
													  	 'order' => 'ActivityComment.id desc'
													),
							 'Like' => 			  array( 
										 				 'className' => 'Like',	
														 'foreignKey' => 'target_id',
														 'conditions' => array('Like.type' => 'activity'),						
														 'dependent'=> true
													)
						); 
	
	public $order = 'Activity.modified desc';
	
	public $_conditions = array('Activity.status' => 'ok');
    public $actsAs = array(
        'Hashtag' => array(

            'field_created_get_hashtag'=>'message',
            'field_updated_get_hashtag'=>'content',
           // 'restricted_fields' =>array('name'=>'action','value'=>'wall_post'),
        ),
        'UserTagging' => array(
           
        ),
        /*
        'UserMention' => array(
            'field_created'=>'message',
            'field_updated'=>'content',
            'restricted_fields' =>array('name'=>'action','value'=>'wall_post'),
        ),
        */
    );
	/*
	 * Get the activities based on $type
	 * @param string $type
	 * @param mixed $param
     * @param mixed #param2
	 * @param int $page
	 * @return array $activities - formated array of activities
	 */
	
	public function getActivities( $type = null, $param = null, $param2 = null, $page = 1 )
	{

		$this->recursive = 2;
		$this->cacheQueries = true;
		$this->ActivityComment->cacheQueries = true;
        $this->User->cacheQueries = true;
		$cond = $this->getConditon($type , $param, $param2);
		
		$this->unbindModel(
			array('hasMany' => array('Like'))
		);

		$this->ActivityComment->unbindModel(
			array('belongsTo' => array('Activity'))
		);

        $this->User->unbindModel(
            array('belongsTo' => array('Role'))
        );
        
		$plugins = MooCore::getInstance()->getListPluginEnable();
		$plugins[] = '';
		$cond['Activity.Plugin'] = $plugins;

		$activities = $this->find('all', array( 'conditions' => $cond, 
												'limit' => RESULTS_LIMIT,
												'page' => $page,
								)	);
								
		App::import('Model', 'Comment');
        $comment = new Comment();
	
		App::import('Model', 'Like');
        $like = new Like();

		// save the items to activities array			
		foreach ( $activities as $key => &$activity )
		{	
			// item activity
			if ( $activity['Activity']['params'] == 'item' )
			{
				$item_type = $activity['Activity']['item_type'];
				list($plugin, $name) = mooPluginSplit($item_type);
				$object = MooCore::getInstance()->getItemByType($item_type,$activity['Activity']['item_id']);
				if (isset($object[$name]['comment_count']))
				{                    
					// get item's comments
					$activity['ItemComment'] = $comment->find('all', array(  'conditions' => array( 
																					 'Comment.target_id' => $activity['Activity']['item_id'],
																					 'Comment.type'      => $item_type ),
																				 'order' => 'Comment.id desc',
																				 'limit' => 2
					)  );				
					
					
					// get items' likes
					$activity['Likes'] = $like->find('list', array( 'conditions' => array( 
														'Like.target_id' => $activity['Activity']['item_id'],
														'Like.type'      => $item_type ),
														'fields' => array( 'Like.user_id', 'Like.thumb_up' )                                                    
					) );
				}
            }
            //photo comment
            if (($activity['Activity']['item_type'] == 'Photo_Album' && $activity['Activity']['action'] == 'wall_post') ||
              ($activity['Activity']['item_type'] == 'Photo_Photo' && $activity['Activity']['action'] == 'photos_add')
             ) {
                $photo_id = explode(',', $activity['Activity']['items']);
                if (count($photo_id) == 1) {
                    $photo_id = $photo_id[0];
                    $activity['PhotoComment'] = $comment->find('all', array(  'conditions' => array(
                        'Comment.target_id' => $photo_id,
                        'Comment.type'      => 'Photo_Photo' ),
                        'order' => 'Comment.id desc',
                        ));
                        
                    $activity['Likes'] = $like->find('list', array( 'conditions' => array( 
														'Like.target_id' => $photo_id,
														'Like.type'      => 'Photo_Photo' ),
														'fields' => array( 'Like.user_id', 'Like.thumb_up' )                                                    
					) );					
                    //$activity['PhotoComment'] = array_reverse( $activity['PhotoComment'] );
                }
            }
		}

		return $activities;
	}
	
	public function getConditon($type = null, $param = null, $param2 = null)
	{
		$cond = array();
		$viewer = MooCore::getInstance()->getViewer();
		switch ($type) {
			case 'home':
			case 'everyone':
					$friend = MooCore::getInstance()->getModel('Friend');
					$friends = $friend->getFriends( $param );
                                        if ($viewer['Role']['is_admin']){
                                            $cond = array( 
                                                'OR' => array(
                                                    array( 'Activity.action <>' => "friend_add"), 
                                                    array( 'Activity.action' => "friend_add", 'Activity.privacy <>' => PRIVACY_ME )
                                                    )
                                                );
                                        }else {
                                            $cond = array( 
                                                'OR' => array( 
                                                    array( 'Activity.type' => APP_USER, 'Activity.privacy' => PRIVACY_EVERYONE ), 
                                                    array( 'Activity.user_id'=>$param, 'Activity.action <>' => "friend_add"),
                                                    array( 'Activity.type' => APP_USER, 'Activity.user_id'=>$friends ,'Activity.privacy <>' => PRIVACY_ME ), 
                                                )
                                            );
                                        }
					
					
					$event = new CakeEvent('Model.Activity.afterSetParamsConditionsOr', $this, array(		               
						'param'=>$param
					));
					$this->getEventManager()->dispatch($event);		           
					if ($event->result && is_array($event->result))
					{
						foreach ($event->result as $result)
						{   
							$cond['OR'] = array_merge($cond['OR'],$result);
						}
					}
                                            
				break;

			case 'friends':
				$friend = MooCore::getInstance()->getModel('Friend');				
				$friends = $friend->getFriends( $param );
				
				$cond = array( 
					'OR' => array( 
						array( 
							'Activity.type' => APP_USER, 'Activity.user_id' => $friends, 'Activity.privacy <> ' . PRIVACY_ME,						 	
						),
						array ('Activity.user_id' => $param)
				) );
				
				$event = new CakeEvent('Model.Activity.afterSetParamsConditionsOr', $this, array(		               
					'param'=>$param
				));
				$this->getEventManager()->dispatch($event);		           
				if ($event->result && is_array($event->result))
				{
					foreach ($event->result as $result)
					{
						$cond['OR'] = array_merge($cond['OR'],array($result));
					}
				}
				break;

			case 'profile':

                //get activities that this user been tagged
                $userTaggingModel = MooCore::getInstance()->getModel('UserTagging');
                $friend = MooCore::getInstance()->getModel('Friend');

                $taggings = $userTaggingModel->find('all',array('conditions' => array(
                        'UserTagging.users_taggings LIKE "%'.$param.'%"'
                    )
                ));

                if(!empty($taggings))
                {
                    foreach($taggings as $key => $value)
                    {
                        $userIds = explode(',',$value['UserTagging']['users_taggings']);
                        if(!in_array($param,$userIds))
                            unset($taggings[$key]);
                    }
                }
                if(!empty($taggings))
                {
                    $activityIds = Hash::combine($taggings,'{n}.UserTagging.item_id','{n}.UserTagging.item_id');
                    $taggingUserIds = Hash::combine($taggings,'{n}.UserTagging.item_id','{n}.UserTagging.users_taggings');
                    $taggingIds = Hash::combine($taggings,'{n}.UserTagging.item_id','{n}.UserTagging.id');

                    //check privacy of tagging activity
                    $taggingActivities = $this->getActivities('tagging',$activityIds);
                    $deleteId = array();
                    if(!empty($taggingActivities))
                    {
                        foreach($taggingActivities as $index => &$activity)
                        {
                            if($activity['Activity']['type'] == 'Group_Group' && $activity['Activity']['action'] == 'wall_post'){
                                $deleteId[] = $activity['Activity']['id'];
                                continue;
                            }
                            $notTaggedUser = (!in_array($param2,explode(',', $taggingUserIds[$activity['Activity']['id']]) ) )? true: false;
                            $isPostOwner = ($param2 == $activity['Activity']['user_id']) ? true : false;
                            $areFriends = $friend->areFriends($activity['Activity']['user_id'],$param2);

                            $activity['UserTagging']['users_taggings'] = $taggingUserIds[$activity['Activity']['id']];
                            $activity['UserTagging']['id'] = $taggingIds[$activity['Activity']['id']];
                            if($activity['Activity']['target_id'] == $param)
                            {
                                $deleteId[] = $activity['Activity']['id'];
                                continue;
                            }
                            if(!$isPostOwner)
                            {
                                switch($activity['Activity']['privacy'])
                                {
                                    case PRIVACY_FRIENDS:
                                        if(!$areFriends)
                                        {
                                            $deleteId[] = $activity['Activity']['id'];
                                            continue;
                                        }
                                        break;
                                    case PRIVACY_ME:
                                        if($notTaggedUser)
                                        {
                                            $deleteId[] = $activity['Activity']['id'];
                                            continue;
                                        }
                                        break;
                                }
                            }
                        }
                    }
                    $cond3 = array('Activity.id' => array_diff($activityIds,$deleteId) );
                }

				$cond1 = array('Activity.user_id' => $param);
                //remove all group activity in profile page
                //$cond1['Activity.type !='] = 'Group_Group';
                $cond1[] = 'Activity.id NOT IN (Select `ac`.`id` from `'.$this->tablePrefix.'activities` as `ac` where `ac`.`type` = "Group_Group" and `ac`.`action` = "wall_post")';

				if ($param != $param2) // current user != user profile page
                {
					$cond1['Activity.privacy'] = PRIVACY_EVERYONE;

                }
				$cond = array('OR' => array($cond1,
						array('Activity.target_id' => $param,
							'Activity.type' => APP_USER,
							

						)
					)
				);
                                
                $cond2 = array();
                if ($friend->areFriends($param, $param2)){
                    $cond2 = array(
                        'Activity.user_id' => $param,
                        'Activity.privacy' => PRIVACY_FRIENDS
                    );
                }

                if (!empty($cond2)){
                    $cond['OR'] = array_merge($cond['OR'], array($cond2));
                }
                if(!empty($cond3)){
                    $cond['OR'] = array_merge($cond['OR'],array($cond3));
                }
                if(!empty($cond4)){
                    $cond['OR'] = array_merge($cond['OR'],array($cond4));
                }
				break;
			case 'detail':
				$cond = array('Activity.id' => $param);
				break;

            case 'tagging':
                $cond = array('Activity.id' => $param);

                break;
            case 'Group_Group':
                $cond = array(
                    'OR' => array(
                        array('Activity.type' => $type,'Activity.action' => 'group_join', 'Activity.items LIKE "%'.$param.'%"'),
                        array('Activity.type' => $type, 'Activity.target_id' => $param),
                    )
                );
                break;
			default:
				$cond = array('Activity.type' => $type, 'Activity.target_id' => $param);
		}

		$plugins = MooCore::getInstance()->getListPluginEnable();
		$plugins[] = '';
		$cond['Activity.Plugin'] = $plugins;
                $cond['Activity.status'] = ACTIVITY_OK;
                $cond['User.active'] = true;
		return $cond;
	}

	public function getActivitiesCount( $type = null, $param = null, $param2 = null )
	{
			$this->recursive = 2;
			$this->cacheQueries = true;
			$this->ActivityComment->cacheQueries = true;
			$this->User->cacheQueries = true;
			$cond = $this->getConditon($type , $param, $param2);

			$this->unbindModel(
					array('hasMany' => array('Like'))
			);
	
			$this->ActivityComment->unbindModel(
					array('belongsTo' => array('Activity'))
			);
	
			$this->User->unbindModel(
					array('belongsTo' => array('Role'))
			);


			return $this->find('count', array(
                    'conditions' => $cond,

                )
            );
	}

/*
	 * Get latest activity of $uid for $action within a day
	 * @param string $action
	 * @param int $uid - user id
     * @param string $item_type
	 * @return array $activity
	 */
	
	public function getRecentActivity( $action = null, $uid = null, $item_type = null )
	{
		$cond = array( 'Activity.user_id' => $uid, 
                       'Activity.action' => $action,
                       'DATE_SUB(CURDATE(),INTERVAL 1 DAY) <= Activity.created'
        );
        
        if ( !empty( $item_type ) )
            $cond['Activity.item_type'] = $item_type;
            
		$activity = $this->find( 'first', array( 'conditions' => $cond	)	);
        
		return $activity;
	}
	
	/*
	 * Get item activity
	 * @param string $item_type
	 * @param int $item_id
	 * @return array $activity
	 */
	
	public function getItemActivity( $item_type = null, $item_id = null )
	{			
		$activity = $this->find( 'first', array( 'conditions' => array( 'Activity.item_type' => $item_type, 
																	    'Activity.item_id' 	 => $item_id,
																	    'Activity.params'	 => 'item',
																	    'Activity.type' 	 => 'user'
								) 	)	);
		return $activity;
	}
	
	/*
	 * Get comment/like activity of an item
	 * @param string $item_type
	 * @param int $item_id
	 * @return array $activity
	 */
	
	public function getCommentLikeActivity( $item_type = null, $item_id = null, $action = 'like_add' )
	{
		$activity = $this->find( 'first', array( 'conditions' => array( 'Activity.action' 	 => $action, 
																	    'Activity.item_type' => $item_type,
																	    'Activity.item_id' 	 => $item_id
								) 	)	);
		return $activity;
	}
    
    public function parseLink( &$data )
    {
        App::uses('Validation', 'Utility');
        $text = trim( $data['content'] );
		
		$reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
		
		// Check if there is a url in the text
		preg_match($reg_exUrl, $text, $url);
		if (!is_array($url))
			return;
		$url = isset($url[0]) ? $url[0] : false;		
        if ( Validation::url( $url ) )
        {   
            if ( strpos( $url, 'http' ) === false )
                $url = 'http://' . $url;
            
            $response = MooCore::getInstance()->getHtmlContent($url);
			
            // get title       
            
            if( preg_match("|<[\s]*title[\s]*>([^<]+)<[\s]*/[\s]*title[\s]*>|Ui", $response, $m) )
                $link['title'] = trim( $m[1] );

            if ( !empty( $link['title'] ) )
            {   
                // get description
                if( preg_match("/<meta name=\"description\" content=\"(.+)\"/i", $response, $m) )
                    $link['description'] = trim( $m[1] );

                if( empty($link['description']) && preg_match("/<meta content=\"(.+)\" name=\"description\"/i", $response, $m) )
                    $link['description'] = trim( $m[1] );

                if( empty($link['description']) && preg_match("/<meta property=\"og:description\" content=\"(.+)\"/i", $response, $m) )
                    $link['description'] = trim( $m[1] );


                // get image
                if( preg_match("/(.+) property=\"og:image\"(.*) content=\"(.+)\"/i", $response, $m) )
                {   
                    $image_url = trim( $m[3] );

                    if ( $image_url )
                    {
                        $tmp = explode('.', $image_url);
                        $ext = strtolower( array_pop($tmp) );

                        if ( in_array($ext, array( 'jpg', 'jpeg', 'gif', 'png' ) ) )
                        {                            
                            $image_name = md5( time() ) . '.' . $ext;

                            $image = MooCore::getInstance()->getHtmlContent($image_url);
                            $image_loc = WWW_ROOT . 'uploads/links/' . $image_name;
                            file_put_contents($image_loc, $image);

                            // resize image
                            App::import('Vendor', 'phpThumb', array('file' => 'phpThumb/ThumbLib.inc.php'));
                            $thumb = PhpThumbFactory::create($image_loc, array('jpegQuality' => 100));
                            $thumb->resize(650, 650)->save($image_loc);

                            $link['image'] = $image_name;
                        }
                    }
                }
				$link['url'] = $url;
                $data['params'] = serialize( $link );
                $data['action'] = 'wall_post_link';
                /*if ( substr($data['content'], 0, 4) != 'http' )
                    $data['content'] = 'http://' . $data['content'];*/
            }
            
        }
        
    }

    public function getActivityHashtags($qid, $limit = RESULTS_LIMIT,$page = 1){

        $this->recursive = 2;
        $this->cacheQueries = true;
        $this->ActivityComment->cacheQueries = true;
        $this->User->cacheQueries = true;
        $cond = $this->getConditon('everyone');

        $this->unbindModel(
            array('hasMany' => array('Like'))
        );

        $this->ActivityComment->unbindModel(
            array('belongsTo' => array('Activity'))
        );

        $this->User->unbindModel(
            array('belongsTo' => array('Role'))
        );

        $plugins = MooCore::getInstance()->getListPluginEnable();
        $plugins[] = '';

        $cond['Activity.id'] = $qid;
        $cond['Activity.privacy'] = PRIVACY_EVERYONE ;
        $cond['Activity.Plugin'] = $plugins;
        $activities = $this->find( 'all', array( 'conditions' => $cond, 'limit' => $limit, 'page' => $page ) );

        App::import('Model', 'Comment');
        $comment = new Comment();

        App::import('Model', 'Like');
        $like = new Like();

        // save the items to activities array
        foreach ( $activities as $key => &$activity )
        {
            // item activity
            if ( $activity['Activity']['params'] == 'item' )
            {
                $item_type = $activity['Activity']['item_type'];

                // get item's comments
                $activity['ItemComment'] = $comment->find('all', array(  'conditions' => array(
                    'Comment.target_id' => $activity['Activity']['item_id'],
                    'Comment.type'      => $item_type ),
                    'order' => 'Comment.id desc',
                    'limit' => 2
                )  );
                

                // get items' likes
                $activity['Likes'] = $like->find('list', array( 'conditions' => array(
                    'Like.target_id' => $activity['Activity']['item_id'],
                    'Like.type'      => $item_type ),
                    'fields' => array( 'Like.user_id', 'Like.thumb_up' )
                ) );
            }
        }

        return $activities;
    }
    public function isMentioned($uid,$activity_id){
        $activity = $this->findById($activity_id);
        preg_match_all(REGEX_MENTION,$activity['Activity']['content'],$matches);
        if(!empty($matches)){
            if(in_array($uid, $matches[1]))
                return true;
        }
        return false;
    }
}
 