<?php

/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */
class PhotosController extends PhotoAppController {

    
    public function beforeFilter(){
        parent::beforeFilter();
        $this->loadModel('Photo.Photo');
        $this->loadModel('Photo.Album');
        $this->loadModel('Photo.PhotoTag');
    }
    public function index($cat_id = null) {
        $this->loadModel('Photo.Album');
        $this->loadModel('Tag');

        $cat_id = intval($cat_id);
        $role_id = $this->_getUserRoleId();

        $tags = $this->Tag->getTags('Photo_Album', Configure::read('core.popular_interval'));
        //get friend list
        $this->loadModel('Friend');
        $sFriendsList = '';
        $aFriendListId =  array_keys($this->Friend->getFriendsList($this->Auth->user('id')));
        $sFriendsList = implode(',',$aFriendListId);
        $album_more_result = 0;
        if (!empty($cat_id)){
            $albums = $this->Album->getAlbums('category', $cat_id, 1, RESULTS_LIMIT, '', $role_id);
            $more_albums = $this->Album->getAlbums('category', $cat_id, 2, RESULTS_LIMIT, '', $role_id);
            if(!empty($more_albums))
                $album_more_result = 1;
        }else{
            $albums = $this->Album->getAlbums(null,$this->Auth->user('id'),1,RESULTS_LIMIT,$sFriendsList, $role_id);
            $more_albums = $this->Album->getAlbums(null,$this->Auth->user('id'),2,RESULTS_LIMIT,$sFriendsList, $role_id);
            if(!empty($more_albums))
                $album_more_result = 1;
        }

        $albums = Hash::sort($albums,'{n}.Album.id','desc');
        $this->set('tags', $tags);
        $this->set('albums', $albums);
        $this->set('cat_id', $cat_id);
        $this->set('title_for_layout', '');
        $this->set('album_more_result', $album_more_result);
    }
    
    public function profile_user_photo($uid = null) {
        $uid = intval($uid);
        $page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;

        $photos = $this->PhotoTag->getPhotos($uid, $page);

        $role_id = $this->_getUserRoleId();

        $addition_param = null;
        //check if current user is this profile's owner
        if($this->Auth->user('id') == $uid)
            $role_id = ROLE_ADMIN;
        else{
            //check if current user is a friend of this profile's owner
            $this->loadModel('Friend');
            $are_friend = $this->Friend->areFriends($this->Auth->user('id'), $uid);
            if(!empty($are_friend))
                $addition_param['are_friend'] = true;
        }
        $this->set('photos', $photos);
        $this->set('more_url', '/photos/profile_user_photo/' . $uid . '/page:' . ( $page + 1 ));
        $this->set('album_more_url', '/photos/profile_user_album/' . $uid . '/page:' . ( $page + 1 ));
        $this->set('tag_uid', $uid);
        $albums = $this->Album->getAlbums('user', $uid,$page,RESULTS_LIMIT,$addition_param, $role_id);
        $more_albums = $this->Album->getAlbums('user', $uid,$page + 1,RESULTS_LIMIT,$addition_param, $role_id);
        $album_more_result = 0;
        if(!empty($more_albums))
            $album_more_result = 1;
        $this->set('albums', $albums);
        $this->set('page', $page);
        $this->set('profileUserPhoto', true);
        $this->set('type', APP_USER);
        $this->set('photosAlbumCount', $this->PhotoTag->getPhotosCount($uid));
        $this->set('album_more_result', $album_more_result);
        if ($page > 1)
            $this->render('/Elements/lists/photos_list');
        else
            $this->render('Photo.Photos/profile_user_photo');
        
    }
    
    public function profile_user_album($uid = null) {
        $uid = intval($uid);
        $this->loadModel('Photo.Album');
        $page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;
        $role_id = $this->_getUserRoleId();
        
        $addition_param = null;
     	if($this->Auth->user('id') == $uid)
            $role_id = ROLE_ADMIN;
        else{
            //check if current user is a friend of this profile's owner
            $this->loadModel('Friend');
            $are_friend = $this->Friend->areFriends($this->Auth->user('id'), $uid);
            if(!empty($are_friend))
                $addition_param['are_friend'] = true;
        }

        $albums = $this->Album->getAlbums('user', $uid, $page,RESULTS_LIMIT, $addition_param, $role_id);
        $album_more_result = 0;
        $more_albums = $this->Album->getAlbums('user', $uid, $page,RESULTS_LIMIT, $addition_param, $role_id);
        if(!empty($more_albums))
            $album_more_result = 1;

        $this->set('albums', $albums);
        $this->set('album_more_url', '/photos/profile_user_album/' . $uid . '/page:' . ( $page + 1 ));
        $this->set('user_id', $uid);
        $this->set('album_more_result', $album_more_result);

        if ($page > 1)
            $this->render('/Elements/lists/albums_list');
        else
            $this->render('Photo.Photos/profile_user_album');
        
    }

    public function ajax_browse($type = null, $target_id = null) {
        $page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;
        $uid = $this->Auth->user('id');

        if($type == 'group_group'){
            // check permission if group is private
            $this->loadModel('Group.Group');
            $group = $this->Group->findById($target_id);

            $this->loadModel('Group.GroupUser');
            $is_member = $this->GroupUser->isMember($uid, $target_id);

            if ($group['Group']['type'] == PRIVACY_PRIVATE) {
                $cuser = $this->_getUser();

                if (!$cuser['Role']['is_admin'] && !$is_member)
                {
                    $this->autoRender = false;
                    echo 'Only group members can view photos';
                    return;
                }
            }
        }
        if ($type != 'Photo_Album')
        {
        	//Load helper for third plugin
        	list($plugin, $name) = mooPluginSplit($type);
        	$this->set('plugin',$plugin);
        	$this->set('name',$name);
        	if ($plugin)
        		$this->helpers[] = $plugin.'.'.$plugin;        	
        }

        $limit = Configure::read('Photo.photo_item_per_pages');
   		$params = array();
   		if ($type == 'Photo_Album')
   		{
   			$album = MooCore::getInstance()->getItemByType($type,$target_id);
	        if ($album['Album']['type'] == 'newsfeed')
	        {
	        	$this->loadModel('Friend');
	        	$params['newsfeed'] = true;
	        	if ($uid == $album['User']['id'] || $this->_getUserRoleId() == ROLE_ADMIN || ($uid && $this->Friend->areFriends($uid,$album['User']['id'])))
	        	{
	        		$params['is_friend'] = true;
	        	}
	        }
   		}
        $photos = $this->Photo->getPhotos($type, $target_id, $page, $limit,$params);

        $this->set('photosAlbumCount', $this->Photo->getPhotosCount($type, $target_id,$params));
        $this->set('photos', $photos);
        $this->set('target_id', $target_id);
        $this->set('page', $page);
        $this->set('type', $type);
        $this->set('more_url', '/photos/ajax_browse/' . h($type) . '/' . intval($target_id) . '/page:' . ( $page + 1 ));
        if ($page == 1 && $type != 'Photo_Album')
            $this->render(($plugin ? $plugin.'.' : '').'/Elements/ajax/'.strtolower($name).'_photo');
        else
            $this->render('/Elements/lists/photos_list');
    }

    public function upload($aid = null) {
        $this->_checkPermission(array('confirm' => true));
        $this->loadModel('Photo.Album');

        $uid = $this->Auth->user('id');

        $albums = $this->Album->find('list', array('conditions' => array('Album.user_id' => $uid, 'Album.photo_count <= ' . MAX_PHOTOS, 'Album.type' => '')));
        $this->set('albums', $albums);

        $this->set('aid', $aid);
        $this->set('title_for_layout', __('Upload Photos'));
    }

    public function view($id = null) {
    	$uid = $this->Auth->user('id');
        $id = intval($id);
        $photo = $this->Photo->findById($id);
        $this->_checkExistence($photo);
        $this->_checkPermission(array('aco' => 'photo_view'));
        MooCore::getInstance()->setSubject($photo);
        if ($this->request->is('ajax')){
        	$limit = 20;
        }
        else
        {
        	$limit = Configure::read('Photo.photo_item_per_pages');
        }
        $params = array();
        switch ($photo['Photo']['type']) {
            case 'Photo_Album':
                $this->_checkPrivacy($photo['Album']['privacy'], $photo['User']['id']);
                $title = $photo['Album']['title'];
                
		        if ($photo['Album']['type'] == 'newsfeed')
		        {
		        	$this->loadModel('Friend');
		        	$params['newsfeed'] = true;
		        	if ($uid == $photo['Album']['user_id'] || $this->_getUserRoleId() == ROLE_ADMIN || ($uid && $this->Friend->areFriends($uid,$photo['Album']['user_id'])))
		        	{
		        		$params['is_friend'] = true;
		        	}
		        }
                
                $photos = $this->Photo->getPhotos('Photo_Album', $photo['Photo']['target_id'], 1, $limit, $params);

                break;

            case 'Group_Group':
            	$group = MooCore::getInstance()->getItemByType($photo['Photo']['type'],$photo['Photo']['target_id']);
                $title = __( 'Photos of %s', $group['Group']['name']);
                $photos = $this->Photo->getPhotos('Group_Group', $photo['Photo']['target_id'], 1, $limit);
               	$this->set('group',$group);

                break;
        }

        $this->_getPhotoDetail($photo);
        
        $this->loadModel('Friend');        
        $friends = $this->Friend->getFriendsList($uid);

        $type = $photo['Photo']['type'];
        $target_id = $photo['Photo']['target_id'];

        if (!empty($this->request->named['uid'])) {
            $this->loadModel('User');
            $user = $this->User->findById($this->request->named['uid']);
            $this->set('user', $user);

            $this->loadModel('Photo.PhotoTag');
            $photos = $this->PhotoTag->getPhotos($this->request->named['uid']);

            $type = APP_USER;
            $target_id = $this->request->named['uid'];
        }

        $can_tag = false;
        if ($uid && ( $uid == $photo['User']['id'] || $this->Friend->areFriends($uid, $photo['User']['id']) ))
            $can_tag = true;
        $total_photos = $this->Photo->getPhotosCount($photo['Photo']['type'], $photo['Photo']['target_id'], $params);
        $this->set('photosAlbumCount', $total_photos);
        $this->set('page', 1);
        $all_photos = $this->Photo->getAllPhotos($photo['Photo']['type'], $photo['Photo']['target_id'], $params);
        $photo_position = $this->findPositionItem($photo, $all_photos);
        $this->set(compact('photos', 'photo', 'type', 'target_id', 'can_tag', 'friends', 'total_photos', 'photo_position'));
        $this->set('no_right_column', true);
        $this->set('title_for_layout', $title);
        $photo_description = !empty($photo['Photo']['caption']) ? htmlspecialchars($photo['Photo']['caption']) : htmlspecialchars($photo['Album']['description']);
        $this->set('description_for_layout', $photo_description);

        // set og:image
        if ($photo['Photo']['thumbnail']) {
            $mooHelper = MooCore::getInstance()->getHelper('Core_Moo');
            $this->set('og_image', $mooHelper->getImageUrl($photo, array('prefix' => '850')));
            
        }
        
        // theater mode MOOSOCIAL-1593
        if ($this->request->is('ajax')){
            $this->render('/Elements/photos/theater');
        }
        
    }
    
    // $needle : Photo need to be find position in $haystack
    // $haystack : list Photo
    protected function findPositionItem($needle, $haystack){
        foreach ($haystack as $key => $item){
            if ($needle['Photo']['id'] == $item['Photo']['id']){
                return $key + 1;
            }
        }
        return false;
    }

    public function ajax_view($id = null, $mode = null) {
        $id = intval($id);
        $photo = Cache::read('photo.photo_view_'.$id, 'photo');
        if(empty($photo)){
            $photo = $this->Photo->findById($id);
            Cache::write('photo.photo_view_'.$id, $photo, 'photo');
        }
        
        $this->_checkExistence($photo);
        
        if (!$photo){
            return;
        }
        MooCore::getInstance()->setSubject($photo);
        $this->_checkPermission(array('aco' => 'photo_view'));

        $uid = $this->Auth->user('id');
        $this->loadModel('Friend');

        $this->_getPhotoDetail($photo, $mode);
        $this->set('photo', $photo);

        $can_tag = false;
        if ($uid && ( $uid == $photo['User']['id'] || $this->Friend->areFriends($uid, $photo['User']['id']) ))
            $can_tag = true;

        $this->set('can_tag', $can_tag);
        
        $this->render('/Elements/ajax/photo_detail');
        
    }
    
    public function ajax_view_theater($id = null, $mode = null) {
        $id = intval($id);
        $uid = $this->Auth->user('id');
        $photo = Cache::read('photo.photo_view_'.$id, 'photo');
        if(empty($photo)){
            $photo = $this->Photo->findById($id);
            Cache::write('photo.photo_view_'.$id, $photo, 'photo');
        }
        
        $this->_checkExistence($photo);
        
        if (!$photo){
            return;
        }
        
        $this->_checkPermission(array('aco' => 'photo_view'));
        $limit = Configure::read('Photo.photo_item_per_pages');
        $params = array();
        switch ($photo['Photo']['type']) {
            case 'Photo_Album':
                $this->_checkPrivacy($photo['Album']['privacy'], $photo['User']['id']);
                $title = $photo['Album']['title'];
                
                if ($photo['Album']['type'] == 'newsfeed')
                {
                    $this->loadModel('Friend');
                    $params['newsfeed'] = true;
                    if ($uid == $photo['Album']['user_id'] || $this->_getUserRoleId() == ROLE_ADMIN || ($uid && $this->Friend->areFriends($uid,$photo['Album']['user_id'])))
                    {
                        $params['is_friend'] = true;
                    }
                }
                
                

                break;

            case 'Group_Group':
            	$group = MooCore::getInstance()->getItemByType($photo['Photo']['type'],$photo['Photo']['target_id']);
                $title = __( 'Photos of %s', $group['Group']['name']);
                
               	$this->set('group',$group);

                break;
        }

        $uid = $this->Auth->user('id');
        $this->_getPhotoDetail($photo, $mode);
        $this->loadModel('Friend');
        $friends = $this->Friend->getFriendsList($uid);

        $type = $photo['Photo']['type'];
        $target_id = $photo['Photo']['target_id'];

        if (!empty($this->request->named['uid'])) {
            $this->loadModel('User');
            $user = $this->User->findById($this->request->named['uid']);
            $this->set('user', $user);

            $this->loadModel('Photo.PhotoTag');
            $photos = $this->PhotoTag->getPhotos($this->request->named['uid']);

            $type = APP_USER;
            $target_id = $this->request->named['uid'];
        }

        
        
        $can_tag = false;
        if ($uid && ( $uid == $photo['User']['id'] || $this->Friend->areFriends($uid, $photo['User']['id']) ))
            $can_tag = true;
        $total_photos = $this->Photo->getPhotosCount($photo['Photo']['type'], $photo['Photo']['target_id'], $params);
        $this->set('photosAlbumCount', $total_photos);
        $this->set('page', 1);
        $all_photos = $this->Photo->getAllPhotos($photo['Photo']['type'], $photo['Photo']['target_id'], $params);
        $photo_position = $this->findPositionItem($photo, $all_photos);
        $this->set('page', 1);
        $this->set(compact('can_tag','photo', 'friends', 'total_photos', 'photo_position'));
        
        $this->render('/Elements/ajax/photo_detail_theater');
        
    }
    
    public function ajax_thumb_theater($id = null , $page = null)
    {
    	$uid = $this->Auth->user('id');
        $id = intval($id);
        $photo = $this->Photo->findById($id);
        $this->_checkExistence($photo);
        $this->_checkPermission(array('aco' => 'photo_view'));
        $limit = 20;

        $params = array();
        switch ($photo['Photo']['type']) {
            case 'Photo_Album':
                $this->_checkPrivacy($photo['Album']['privacy'], $photo['User']['id']);
                
		        if ($photo['Album']['type'] == 'newsfeed')
		        {
		        	$this->loadModel('Friend');
		        	$params['newsfeed'] = true;
		        	if ($uid == $photo['Album']['user_id'] || $this->_getUserRoleId() == ROLE_ADMIN || ($uid && $this->Friend->areFriends($uid,$photo['Album']['user_id'])))
		        	{
		        		$params['is_friend'] = true;
		        	}
		        }
                
                $photos = $this->Photo->getPhotos('Photo_Album', $photo['Photo']['target_id'], $page, $limit, $params);

                break;

            case 'Group_Group':
                $photos = $this->Photo->getPhotos('Group_Group', $photo['Photo']['target_id'], $page, $limit);
                break;
        }
        $this->set('photos',$photos);    
        $this->render('/Elements/theater/photo_thumbs');   
    }

    private function _getPhotoDetail($photo, $mode = null) {
        $uid = $this->Auth->user('id');
        $tag_uid = 0;

        if (!empty($this->request->named['uid'])) { // tagged photos
            $this->loadModel('Photo.PhotoTag');
            $photo_tag = $this->PhotoTag->find('first', array('conditions' => array('photo_id' => $photo['Photo']['id'],
                    'PhotoTag.user_id' => $this->request->named['uid'])
            ));

            $photo_path = 'uploads'. DS . 'photos' . DS . 'thumbnail' . DS .$photo_tag['PhotoTag']['photo_id'] . DS . $photo['Photo']['thumbnail'] ;
            App::import('Vendor', 'phpThumb', array('file' => 'phpThumb/ThumbLib.inc.php'));

            $thumb = PhpThumbFactory::create(WWW_ROOT . DS . $photo_path, array('jpegQuality' => 100));
            $image_real_dimension = $thumb->getCurrentDimensions();

            $neighbors = array();
            
            if ($photo_tag){
                $neighbors = $this->PhotoTag->find('neighbors', array('field' => 'id',
                    'value' => $photo_tag['PhotoTag']['id'],
                    'conditions' => array('PhotoTag.user_id' => $this->request->named['uid']
                )));

                $tag_uid = $this->request->named['uid'];
            }
            
        } else {
            $neighbors = Cache::read('photo.photo_getdetail_neighbors_'.$photo['Photo']['id'], 'photo');
            if(empty($neighbors)){
                $neighbors = $this->Photo->find('neighbors', array('field' => 'id',
                    'value' => $photo['Photo']['id'],
                    'conditions' => array('Photo.type' => $photo['Photo']['type'],
                        'target_id' => $photo['Photo']['target_id']
                )));
                Cache::write('photo.photo_getdetail_neighbors_'.$photo['Photo']['id'], $neighbors, 'photo');
            }
        }

        $this->loadModel('Comment');
        $this->loadModel('Like');

        $comments = $this->Comment->getComments($photo['Photo']['id'], 'Photo_Photo');
        
        $comment_count = $photo['Photo']['comment_count'];

        // get comment likes
        if (!empty($uid)) {
            $comment_likes = $this->Like->getCommentLikes($comments, $uid);
            $this->set('comment_likes', $comment_likes);

            $like = $this->Like->getUserLike($photo['Photo']['id'], $uid, 'Photo_Photo');
            $this->set('like', $like);
        }

        $likes = $this->Like->getLikes($photo['Photo']['id'], 'Photo_Photo');
        $dislikes = $this->Like->getDisLikes($photo['Photo']['id'], 'Photo_Photo');

        $this->loadModel('Photo.PhotoTag');
        $photo_tags = $this->PhotoTag->findAllByPhotoId($photo['Photo']['id']);
        // check to see if user can delete photo
        $admins = array($photo['Photo']['user_id']);

        if ($photo['Photo']['type'] == 'Group_Group') { // if it's a group photo, add group admins to the admins array
            // get group admins
            $this->loadModel('Group.GroupUser');

            $is_member = $this->GroupUser->isMember($uid, $photo['Photo']['target_id']);
            $this->set('is_member', $is_member);

            $group_admins = $this->GroupUser->getUsersList($photo['Photo']['target_id'], GROUP_USER_ADMIN);
            $admins = array_merge($admins, $group_admins);
        }

        $this->set('likes', $likes);
        $this->set('dislikes', $dislikes);
        $this->set('photo_tags', $photo_tags);
        
        $this->set('neighbors', $neighbors);
        $this->set('admins', $admins);
        $this->set('tag_uid', $tag_uid);

        
        $page = 1;
        $data['bIsCommentloadMore'] = $comment_count - $page * RESULTS_LIMIT;
        $data['more_comments'] = '/comments/browse/photo_photo/' . $photo['Photo']['id'] . '/page:' . ($page + 1);
        //$data['admins'] = $admins;
        $data['comments'] = $comments;
        $this->set('data', $data);
    }

    public function ajax_upload($type = null, $target_id = null) {
        $target_id = intval($target_id);
        $this->_checkPermission(array('aco' => 'photo_upload'));
        $this->set('target_id', $target_id);
        $this->set('type', $type);
    }

    public function do_activity($type) {
        $this->_checkPermission();
        $uid = $this->Auth->user('id');

        if (!empty($this->request->data['new_photos'])) {
            $new_photos = explode(',', $this->request->data['new_photos']);
            
            $this->loadModel('Activity');
            $this->loadModel('Photo.Album');
            $photoList = explode(',', $this->request->data['new_photos']);

            $this->loadModel('Photo.Photo');
            $this->request->data['type'] = $type;
            $this->request->data['user_id'] = $uid;
            $photoId = array();
            foreach ($photoList as $photoItem){
                if(!empty($photoItem))
                {
                    $this->request->data['thumbnail'] = $photoItem;
                    $this->Photo->create();

                    $this->Photo->set($this->request->data);
                    $this->Photo->save();
                    array_push($photoId, $this->Photo->id);
                }
            }
            switch ($type) {
                case 'Photo_Album':
                    $album = $this->Album->findById($this->request->data['target_id']);
                    $url = '/albums/edit/' . $this->request->data['target_id'];
                    $activity = $this->Activity->getItemActivity('Photo_Album', $this->request->data['target_id']);

                    if (!empty($activity)) { // update the existing one
                        $this->Activity->id = $activity['Activity']['id'];
                        $this->Activity->save(array('items' => join(',', $photoId), 'privacy' => $album['Album']['privacy']));
                    } else // insert new
                        $this->Activity->save(array('type' => APP_USER,
                            'action' => 'photos_add',
                            'user_id' => $uid,
                            'items' => join(',', $photoId),
                            'item_type' => 'Photo_Album',
                            'item_id' => $this->request->data['target_id'],
                            'privacy' => $album['Album']['privacy'],
                            'query' => 1,
                            'params' => 'item',
                            'plugin' => 'Photo'
                        ));
                    
                    // update privacy photo album
                    $this->Photo->updateAll(array('Photo.privacy' => $album['Album']['privacy']), array('Photo.id' => $photoId));
                    
                    $event = new CakeEvent('Plugin.Controller.Album.afterSaveAlbum', $this, array(
                        'uid' => $uid, 
                        'id' => $album['Album']['id'], 
                        'privacy' => $album['Album']['privacy']
                    ));

                    $this->getEventManager()->dispatch($event);

                    break;

                default:                    
                    $privacy = PRIVACY_EVERYONE;
                    list($plugin, $name) = mooPluginSplit($type);
                    
                    $item = MooCore::getInstance()->getItemByType($type,$this->request->data['target_id']);                    
                    $url = $item[$name]['moo_url'];

                    if ($item[$name]['type'] == PRIVACY_PRIVATE)
                        $privacy = PRIVACY_ME;
                    
                    $share = 0;
                    if ($privacy = PRIVACY_EVERYONE){
                        $share = 1;
                    }

                    $this->Activity->save(array('type' => $type,
                        'target_id' => $this->request->data['target_id'],
                        'action' => 'photos_add',
                        'user_id' => $uid,
                        'items' => join(',', $photoId),
                        'item_type' => 'Photo_Photo',
                        'privacy' => $privacy,
                        'query' => 1,
                    	'plugin' => 'Photo',
                        'share' => $share
                    ));

                    break;
            }
        }

        $this->redirect($url);
    }

    public function ajax_tag() {
        $this->autoRender = false;
        $this->_checkPermission(array('confirm' => true));

        $uid = $this->Auth->user('id');
        $this->loadModel('Photo.PhotoTag');
        $this->loadModel('Photo.Photo');

        $user_id = $this->request->data['uid'];
        $photo_id = $this->request->data['photo_id'];

        $photo = $this->Photo->findById($photo_id);
        // if tagging a member then check if that member is already tagged in this photo
        if (!empty($user_id))
            $tag = $this->PhotoTag->find('first', array('conditions' => array('photo_id' => $photo_id, 'PhotoTag.user_id' => $user_id)));
        
        if (empty($tag)) {
            $this->PhotoTag->save(array('photo_id' => $photo_id,
                    'user_id' => $user_id,
                    'tagger_id' => $uid,
                    'value' => $this->request->data['value'],
                    'style' => $this->request->data['style']
                ));

            if ($user_id) {
                // insert into activity
                $this->loadModel('Activity');
                $activity = $this->Activity->getRecentActivity('photos_tag', $user_id);

                if (!empty($activity)) {
                    $photo_ids = explode(',', $activity['Activity']['items']);
                    $photo_ids[] = $photo_id;

                    $this->Activity->id = $activity['Activity']['id'];
                    $this->Activity->save(array('items' => implode(',', $photo_ids)
                    ));
                } else {
                    $this->Activity->save(array('type' => APP_USER,
                        'action' => 'photos_tag',
                        'user_id' => $user_id,
                        'item_type' => 'Photo_Photo',
                        'items' => $photo_id,
                        'query' => 1,
                        'params' => 'no-comments',
                    	'plugin' => 'Photo',
                        'privacy' => $photo['Photo']['moo_privacy']
                    ));
                }

                if ($user_id != $uid) {
                    // add notification
                    $this->loadModel('Notification');
                    $this->Notification->record(array('recipients' => $user_id,
                        'sender_id' => $uid,
                        'action' => 'photo_tag',
                        'url' => '/photos/view/' . $photo_id . '#content'
                    ));
                }
            }

            $response['result'] = 1;
            $response['id'] = $this->PhotoTag->id;
        } else {
            $response['result'] = 0;
            $response['message'] = __( 'Duplicated tag!');
        }

        echo json_encode($response);
    }

    public function ajax_remove_tag() {
        $this->autoRender = false;
        $this->_checkPermission(array('confirm' => true));
        $uid = $this->Auth->user('id');

        $this->loadModel('Photo.PhotoTag');
        $tag = $this->PhotoTag->findById($this->request->data['tag_id']);
        if (!$tag){
            return;
        }
        
        
        // tagger, user was tagged and photo author can delete tag
        $admins = array($tag['PhotoTag']['user_id'], $tag['PhotoTag']['tagger_id'], $tag['Photo']['user_id']);

        $this->_checkPermission(array('admins' => $admins));
        $this->PhotoTag->delete($this->request->data['tag_id']);
                
        $this->loadModel('Activity');             
        $activity = $this->Activity->getRecentActivity('photos_tag', $tag['PhotoTag']['tagger_id']);

        if ($activity) {
            $items = array_filter(explode(',',$activity['Activity']['items']));
        	$items = array_diff($items,array($tag['PhotoTag']['photo_id']));
        	
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

    public function ajax_fetch() {
        $limit = Configure::read('Photo.photo_item_per_pages');
        switch ($this->data['type']) {
            case 'Photo_Album':
                // check the privacy of album
                $this->loadModel('Photo.Album');
                $album = $this->Album->findById($this->data['target_id']);

                $uid = $this->Auth->user('id');
                $this->_checkPrivacy($album['Album']['privacy'], $album['User']['id']);
                $photos = $this->Photo->getPhotos('Photo_Album', $this->data['target_id'], $this->data['page'], $limit);

                break;

            case 'Group_Group':
                // @todo: check the type of group
                $photos = $this->Photo->getPhotos('Group_Group', $this->data['target_id'], $this->data['page'], $limit);

                break;

            case APP_USER:
                $this->loadModel('Photo.PhotoTag');
                $photos = $this->PhotoTag->getPhotos($this->data['target_id'], $this->data['page']);

                break;
        }
        $this->set('photosAlbumCount', $this->Photo->getPhotosCount('Photo_Album', $this->data['target_id']));
        $this->set('page', $this->data['page']);
        $this->set('photos', $photos);
        $this->render('/Elements/ajax/photo_thumbs');
    }

    public function ajax_friends_list() {
        $this->_checkPermission();
        $uid = $this->Auth->user('id');

        $this->loadModel('Friend');
        $friends = $this->Friend->getFriendsList($uid);

        $this->set('friends', $friends);
        $this->render('/Elements/misc/photo_friends_list');
    }

    public function ajax_remove() {

        $photoId = intval($this->request->params['named']['photo_id']);

        $this->autoRender = false;
        $this->_checkPermission(array('confirm' => true));

        $photo = $this->Photo->findById($photoId);
        
        if (!$photo){
            return;
        }
        
        $admins = array($photo['Photo']['user_id']);

        if ($photo['Photo']['type'] == 'Group_Group') { // if it's a group photo, add group admins to the admins array
            // get group admins
            $this->loadModel('Group.GroupUser');

            $group_admins = $this->GroupUser->getUsersList($photo['Photo']['target_id'], GROUP_USER_ADMIN);
            $admins = array_merge($admins, $group_admins);
        }

        // make sure user can delete photo
        $this->_checkPermission(array('admins' => $admins));
        
        // permission ok, delete photo now
        $this->Photo->delete($photo['Photo']['id']);
        
        $cakeEvent = new CakeEvent('Plugin.Controller.Group.afterDeletePhoto', $this, array('item' => $photo));
        $this->getEventManager()->dispatch($cakeEvent);

        // update cover of album
        $nextCoverPhoto = $this->Photo->find('first', array('conditions' => array('Photo.type' => 'Photo_Album', 'Photo.target_id' => $photo['Photo']['target_id'])));
        $currentCoverPhoto = $this->Album->find('first', array('conditions' => array('Album.id' => $photo['Photo']['target_id'])));

        if (!empty($nextCoverPhoto)){
            // cond1: delete item is cover => need to update cover
            // cond2: current album have no cover => need to update cover
            if ($photo['Photo']['thumbnail'] == $currentCoverPhoto['Album']['cover'] || empty($currentCoverPhoto['Album']['cover'])){
                $this->Album->id = $photo['Photo']['target_id'];
                $this->Album->save(array(
                    'cover' => $nextCoverPhoto['Photo']['thumbnail']
                ));
            }
            
        }else{
            $this->Album->id = $photo['Photo']['target_id'];
            $this->Album->save(array(
                'cover' => ''
            ));
        }

        if ($this->request->params['named']['next_photo'] == 0) {
            if ($photo['Photo']['type'] == 'group') {
                $this->redirect('/groups/view/' . $photo['Photo']['target_id'] . '/tab:photos');
            } else {
                $this->redirect(array('controller' => 'photos', 'action' => 'index'));
            }
        }
        
        $this->redirect(array('controller' => 'photos', 'action' => 'view', $this->request->params['named']['next_photo']));
    }
    
    public function categories_list(){
        if ($this->request->is('requested')){
            $this->loadModel('Category');
            $categories = $this->Category->getCategories('Photo');
            return $categories;
        }
    }

}
