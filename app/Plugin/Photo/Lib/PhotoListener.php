<?php

/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */
App::uses('CakeEventListener', 'Event');

class PhotoListener implements CakeEventListener {

    public function implementedEvents() {
        return array( 
            'UserController.deleteUserContent' => 'deleteUserContent',
            'Controller.Search.search' => 'search',
            'Controller.Search.suggestion' => 'suggestion',
            'Controller.Search.hashtags' => 'hashtags',
            'Controller.Search.hashtags_filter' => 'hashtags_filter',
            'Controller.Widgets.tagCoreWidget' => 'hashtagEnable',
        	'MooView.beforeRender' => 'beforeRender',
            'Plugin.Controller.Group.beforeDelete' => 'processGroupBeforeDelete',
            'Controller.Like.afterLike' => 'afterLike',
            'Controller.Comment.afterComment' => 'afterComment',
            'Controller.User.deactivate' => 'deactivate',
            'Plugin.Controller.Album.afterSaveAlbum' => 'processAlbumAfterSave'
        );
    }
    
    public function processAlbumAfterSave($event){
        $v = $event->subject();
        
        // load feed model
        $this->Activity = ClassRegistry::init('Activity');
        
        // find activity which belong to event just created
        $activity = $this->Activity->find('first', array('conditions' => array(
            'Activity.item_type' => 'Photo_Album',
            'Activity.item_id' => $event->data['id'],
        )));
        
        if (!empty($activity)){
            $share = false;
            // only enable share feature for public event
            if ($event->data['privacy'] == PRIVACY_EVERYONE || $event->data['privacy'] == PRIVACY_FRIENDS) {
                $share = true;
            }
            $this->Activity->clear();
            $this->Activity->updateAll(array('Activity.share' => $share), array('Activity.id' => $activity['Activity']['id']));
        }
    }
    
    public function afterLike($event) {
        $aLike = $event->data['aLike'];
        if (isset($aLike['Like']['type']) && $aLike['Like']['type'] == 'Photo_Photo') {
            $photo_id = $aLike['Like']['target_id'];
            // get list user tagged
            $photoTagModel = MooCore::getInstance()->getModel('Photo.PhotoTag');
            $notificationModel = MooCore::getInstance()->getModel('Notification');
            $notificationStopModel = MooCore::getInstance()->getModel('NotificationStop');
            $uid = MooCore::getInstance()->getViewer(true);
            $photo_tag = $photoTagModel->find('all', array('conditions' => array('PhotoTag.photo_id' => $photo_id)));
            foreach ($photo_tag as $item) {
                $aUser = $item['User'];
                $aPhoto = $item['Photo'];
                if (!$notificationStopModel->isNotificationStop($aLike['Like']['target_id'], $aLike['Like']['type'], $aUser['id'])) {
                    $notificationModel->record(array('recipients' => $aUser['id'],
                        'sender_id' => $uid,
                        'action' => 'like_photo_user_tagged_in',
                        'url' => $aPhoto['moo_url'],
                    ));
                }
            }
        }
    }

    public function afterComment($event){
        $aData = $event->data['data'];
        if (isset($aData['type']) && $aData['type'] == 'Photo_Photo') {
            $photo_id = $aData['target_id'];
            // get list user tagged
            $photoTagModel = MooCore::getInstance()->getModel('Photo.PhotoTag');
            $notificationModel = MooCore::getInstance()->getModel('Notification');
            $notificationStopModel = MooCore::getInstance()->getModel('NotificationStop');
            $uid = MooCore::getInstance()->getViewer(true);
            $photo_tag = $photoTagModel->find('all', array('conditions' => array('PhotoTag.photo_id' => $photo_id)));
            foreach ($photo_tag as $item) {
                $aUser = $item['User'];
                $aPhoto = $item['Photo'];
                if (!$notificationStopModel->isNotificationStop($aData['target_id'], $aData['type'], $aUser['id'])) {
                    $notificationModel->record(array('recipients' => $aUser['id'],
                        'sender_id' => $uid,
                        'action' => 'comment_photo_user_tagged_in',
                        'url' => $aPhoto['moo_url'],
                    ));
                }
            }

            $photoModel = MooCore::getInstance()->getModel('Photo.Photo');
            Cache::clearGroup('photo', 'photo');
            $photoModel->updateCounter($photo_id);

        }

        $target_id = isset($aData['target_id']) ? $aData['target_id'] : null;
        if (isset($aData['type']) && $aData['type'] == 'Photo_Album' && !empty($target_id)){
            $albumModel = MooCore::getInstance()->getModel('Photo.Album');
            Cache::clearGroup('photo', 'photo');
            $albumModel->updateCounter($target_id);
        }
    }


    // delete all photo belong to group is deleted
    public function processGroupBeforeDelete($event){
        $group_id = isset($event->data['aGroup']['Group']['id']) ? $event->data['aGroup']['Group']['id'] : '';
        if (!empty($group_id)){
            $this->Photo = ClassRegistry::init('Photo.Photo');
            $photos = $this->Photo->getPhotos('Group_Group', $group_id, null, null);
            foreach ($photos as $p){
                $this->Photo->delete($p['Photo']['id']);
            }
        }
    }
    
	function beforeRender($event)
    {
    	$view = $event->subject();
    	if ($view instanceof MooView) {
    		$view->addPhraseJs(array(
	            'done_tagging' => __("Done Tagging"),	
    			'tag_photo' => __("Tag Photo"),
    			'are_you_delete' => __('Are you sure you want to delete this photo ?')
	        ));
	        
	        if (Configure::read('core.photo_theater_mode'))
	        {
	        	$view->addInitJs('$(function() { if (typeof MooPhotoTheater != "undefined") MooPhotoTheater.setActive(true); });');
	        }
        }
    }


    function deleteUserContent($event) {
        App::import('Photo.Model', 'Photo');
        App::import('Photo.Model', 'PhotoTag');
        App::import('Photo.Model', 'Album');
        
        $this->Photo = new Photo();
        $this->PhotoTag = new PhotoTag();
        $this->Album = new Album();
        
        $photos = $this->Photo->findAllByUserId($event->data['aUser']['User']['id']);
        foreach ($photos as $photo) {
            $this->Photo->delete($photo['Photo']['id']);
        }

        $this->PhotoTag->deleteAll(array('PhotoTag.user_id' => $event->data['aUser']['User']['id']), true, true);
        $this->PhotoTag->deleteAll(array('PhotoTag.tagger_id' => $event->data['aUser']['User']['id']), true, true);


        $albums = $this->Album->findAllByUserId($event->data['aUser']['User']['id']);
        foreach ($albums as $album) {
            $this->Album->deleteAlbum($album);
        }
    }

    public function search($event)
    {
        $e = $event->subject();
        App::import('Model', 'Photo.Album');
        $this->Album = new Album();
        $results = $this->Album->getAlbums( 'search', $e->keyword, 1);
        if(count($results) > 5)
            $results = array_slice($results,0,5);
        if(empty($results))
            $results = $this->Album->getAlbumSuggestion($e->keyword,5);
        if(isset($e->plugin) && $e->plugin == 'Photo')
        {
            $e->set('albums', $results);
            $e->render("Photo.Elements/lists/albums_list");
        }
        else 
        {
            $event->result['Photo']['header'] = "Albums";
            $event->result['Photo']['icon_class'] = "icon-picture";
            $event->result['Photo']['view'] = "lists/albums_list";
            if(!empty($results))
                $event->result['Photo']['notEmpty'] = 1;
            $e->set('albums', $results);
        }
    }
    public function suggestion($event)
    {
        $e = $event->subject();
        App::import('Model', 'Photo.Album');
        App::import('Model', 'Photo.Photo');
        $this->Album = new Album();
        $this->Photo = new Photo();

        $event->result['album']['header'] = 'Albums';
        $event->result['album']['icon_class'] = 'icon-picture';

        if(isset($event->data['type']) && ($event->data['type'] == 'album' || $event->data['type'] == 'photo'))
        {
            $page = (!empty($e->request->named['page'])) ? $e->request->named['page'] : 1;
            $albums = $this->Album->getAlbums( 'search', $event->data['searchVal'], $page);
            if(empty($albums))
                $albums = $this->Album->getAlbumSuggestion($event->data['searchVal'],RESULTS_LIMIT,$page);
            $e->set('albums', $albums);
            $e->set('result',1);
            $e->set('more_url','/search/suggestion/album/'.$e->params['pass'][1]. '/page:' . ( $page + 1 ));
            $e->set('element_list_path',"Photo.lists/albums_list");
        }
        if(isset($event->data['type']) && $event->data['type'] == 'all')
        {
            $event->result['album'] = null;
            $albums = $this->Album->getAlbums( 'search', $event->data['searchVal'], 1, 2);
            if(count($albums) >2){
                $albums = array_slice($albums,0,2);
            }
            if(empty($albums))
                $albums = $this->Album->getAlbumSuggestion($event->data['searchVal'],2);
            if(!empty($albums)){
                foreach($albums as $index=>&$detail){
                    $event->result['album'][$index]['id'] = $detail['Album']['id'];
                    if(!empty($detail['Album']['cover']))
                    {
                        $photo = $this->Photo->find('first',array('conditions' => array('Photo.target_id' => $detail['Album']['id'], 'Photo.thumbnail' => $detail['Album']['cover'])));
                        //$thumb = explode('/',$detail['Album']['thumbnail']);
                        if(!empty($photo))
                            $event->result['album'][$index]['img'] = 'photos/thumbnail/'.$photo['Photo']['id'].'/75_square_'.$detail['Album']['cover'];

                    }
                    $event->result['album'][$index]['title'] = $detail['Album']['title'];
                    $event->result['album'][$index]['find_name'] = 'Find Album';
                    $event->result['album'][$index]['icon_class'] = 'icon-picture';
                    $event->result['album'][$index]['view_link'] = 'albums/view/';
                    
                    $event->result['album'][$index]['more_info'] = __n('%s photo', '%s photos', $detail['Album']['photo_count'], $detail['Album']['photo_count']);
                }
            }
        }
    }

    public function hashtags($event)
    {
        $enable = Configure::read('Photo.photo_hashtag_enabled');
        $e = $event->subject();
        App::import('Model', 'Photo.Photo');
        App::import('Model', 'Photo.Album');
        $this->Photo = new Photo();
        $this->Album = new Album();
        App::import('Model', 'Tag');
        $this->Tag = new Tag();
        $albums = array();
        $uid = CakeSession::read('uid');
        $page = (!empty($e->request->named['page'])) ? $e->request->named['page'] : 1;

        if($enable)
        {
            //Album
            if(isset($event->data['type']) && $event->data['type'] == 'albums')
            {
                $albums = $this->Album->getAlbumHashtags($event->data['item_ids'],RESULTS_LIMIT,$page);
                $albums = $this->_filterAlbum($albums);
            }
            $table_name = $this->Album->table;
            if(isset($event->data['type']) && $event->data['type'] == 'all' && !empty($event->data['item_groups'][$table_name]) )
            {
                $albums = $this->Album->getAlbumHashtags($event->data['item_groups'][$table_name],5);
                $albums = $this->_filterAlbum($albums);
            }

            //Photo
            if(isset($event->data['type']) && $event->data['type'] == 'photos')
            {
                $photos = $this->Photo->getPhotoHashtags($event->data['item_ids'],RESULTS_LIMIT,$page);
                $e->set('photos', $photos);
                $e->set('result',1);
                $e->set('more_url','/search/hashtags/'.$e->params['pass'][0]. '/photos/page:' . ( $page + 1 ));
                $e->set('element_list_path',"Photo.lists/photos_list");
                $e->set('photosAlbumCount', count($photos));
                $e->set('page', $page);

            }
            $table_name = $this->Photo->table;
            if(isset($event->data['type']) && $event->data['type'] == 'all' && !empty($event->data['item_groups'][$table_name]) )
            {
                $photos = $this->Photo->getPhotoHashtags($event->data['item_groups'][$table_name],5);
            }
            if(!empty($photos))
            {
                $event->result['photos']['header'] = 'Photos';
                $event->result['photos']['icon_class'] = 'icon-picture';
                $event->result['photos']['view'] = "Photo.lists/photos_list";
                $e->set('photos', $photos);
                $e->set('photosAlbumCount', count($photos));
                $e->set('page', 1);
            }
        }

        // get tagged album item
        $tag = h(urldecode($event->data['search_keyword']));
        $tags = $this->Tag->find('all', array('conditions' => array(
            'Tag.type' => 'Photo_Album',
            'Tag.tag' => $tag
        )));
        $video_ids = Hash::combine($tags,'{n}.Tag.id', '{n}.Tag.target_id');

        $friendModel = MooCore::getInstance()->getModel('Friend');

        $items = $this->Album->find('all', array('conditions' => array(
                'Album.id' => $video_ids
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
        $albums = array_merge($albums, $items);
        //only display 5 items on All Search Result page
        if(isset($event->data['type']) && $event->data['type'] == 'all')
        {
            $albums = array_slice($albums,0,5);
        }
        $albums = array_map("unserialize", array_unique(array_map("serialize", $albums)));
        if(!empty($albums))
        {
            $event->result['albums']['header'] = 'Albums';
            $event->result['albums']['icon_class'] = 'icon-picture';
            $event->result['albums']['view'] = "Photo.lists/albums_list";

            if(isset($event->data['type']) && $event->data['type'] == 'albums')
            {
                $e->set('result',1);
                $e->set('more_url','/search/hashtags/'.$e->params['pass'][0]. '/albums/page:' . ( $page + 1 ));
                $e->set('element_list_path',"Photo.lists/albums_list");
            }

            $e->set('albums', $albums);
        }
    }
    
    public function hashtags_filter($event){
        $e = $event->subject();
        App::import('Model', 'Photo.Photo');
        App::import('Model', 'Photo.Album');
        $this->Photo = new Photo();
        $this->Album = new Album();

        //Album
        if(isset($event->data['type']) && $event->data['type'] == 'albums')
        {
            $page = (!empty($e->request->named['page'])) ? $e->request->named['page'] : 1;
            $albums = $this->Album->getAlbumHashtags($event->data['item_ids'],RESULTS_LIMIT,$page);
            $e->set('albums', $albums);
            $e->set('result',1);
            $e->set('more_url','/search/hashtags/'.$e->params['pass'][0]. '/albums/page:' . ( $page + 1 ));
            $e->set('element_list_path',"Photo.lists/albums_list");
        }
        $table_name = $this->Album->table;
        if(isset($event->data['type']) && $event->data['type'] == 'all' && !empty($event->data['item_groups'][$table_name]) )
        {
            //$event->result['albums'] = null;

            $albums = $this->Album->getAlbumHashtags($event->data['item_groups'][$table_name],5);

            if(!empty($albums))
            {
                $event->result['albums']['header'] = 'Albums';
                $event->result['albums']['icon_class'] = 'icon-picture';
                $event->result['albums']['view'] = "Photo.lists/albums_list";
                $e->set('albums', $albums);
            }
        }

        //Photo
        if(isset($event->data['type']) && $event->data['type'] == 'photos')
        {
            $page = (!empty($e->request->named['page'])) ? $e->request->named['page'] : 1;
            $photos = $this->Photo->getPhotoHashtags($event->data['item_ids'],RESULTS_LIMIT,$page);
            $e->set('photos', $photos);
            $e->set('result',1);
            $e->set('more_url','/search/hashtags/'.$e->params['pass'][0]. '/photos/page:' . ( $page + 1 ));
            $e->set('element_list_path',"Photo.lists/photos_list");
            $e->set('photosAlbumCount', count($photos));
            $e->set('page', $page);

        }
        $table_name = $this->Photo->table;
        if(isset($event->data['type']) && $event->data['type'] == 'all' && !empty($event->data['item_groups'][$table_name]) )
        {
            //$event->result['photos'] = null;

            $photos = $this->Photo->getPhotoHashtags($event->data['item_groups'][$table_name],5);

            if(!empty($photos))
            {
                $event->result['photos']['header'] = 'Photos';
                $event->result['photos']['icon_class'] = 'icon-picture';
                $event->result['photos']['view'] = "Photo.lists/photos_list";
                $e->set('photos', $photos);
                $e->set('photosAlbumCount', count($photos));
                $e->set('page', 1);
            }
        }

    }

    private function _filterAlbum($albums)
    {
        if(!empty($albums))
        {
            $friendModel = MooCore::getInstance()->getModel('Friend');
            $viewer = MooCore::getInstance()->getViewer();
            foreach($albums as $key => &$album)
            {
                $owner_id = $album[key($album)]['user_id'];
                $privacy = isset($album[key($album)]['privacy']) ? $album[key($album)]['privacy'] : 1;
                if (empty($viewer)){ // guest can view only public item
                    if ($privacy != PRIVACY_EVERYONE){
                        unset($albums[$key]);
                    }
                }else{ // viewer
                    $aFriendsList = array();
                    $aFriendsList = $friendModel->getFriendsList($owner_id);
                    if ($privacy == PRIVACY_ME){ // privacy = only_me => only owner and admin can view items
                        if (!$viewer['Role']['is_admin'] && $viewer['User']['id'] != $owner_id){
                            unset($albums[$key]);
                        }
                    }else if ($privacy == PRIVACY_FRIENDS){ // privacy = friends => only owner and friendlist of owner can view items
                        if (!$viewer['Role']['is_admin'] && $viewer['User']['id'] != $owner_id && !in_array($viewer['User']['id'], array_keys($aFriendsList))){
                            unset($albums[$key]);
                        }
                    }else {

                    }
                }
            }
        }
        return $albums;
    }

    public function hashtagEnable($event)
    {
        $enable = Configure::read('Photo.photo_hashtag_enabled');
        $event->result['photos']['enable'] = $enable;
        $event->result['albums']['enable'] = $enable;
    }

    public function deactivate($event){
        $albumModel = MooCore::getInstance()->getModel('Photo.Album');
        $albumCategory = $albumModel->find('all',array(
                'conditions' => array('Album.user_id' => $event->data['uid']),
                'group' => array('Album.category_id'),
                'fields' => array('category_id','(SELECT count(*) FROM '.$albumModel->tablePrefix.'albums WHERE category_id=Album.category_id  AND user_id = 2 AND user_id = '.$event->data['uid'].') as count')
            )
        );
        $albumCategory = Hash::combine($albumCategory,'{n}.Album.category_id','{n}.{n}.count');
        $event->result['Photo'] = $albumCategory;
    }
}
