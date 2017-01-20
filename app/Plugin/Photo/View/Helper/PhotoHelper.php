<?php
App::uses('AppHelper', 'View/Helper');
class PhotoHelper extends AppHelper {	
	public function getTagUnionsAlbum($albumids)
	{
		return "SELECT i.id, i.title, i.description as body, i.like_count, i.created, 'Photo_Album' as moo_type, i.privacy, i.user_id
						 FROM " . Configure::read('core.prefix') . "albums i						 
						 WHERE i.id IN (" . implode(',', $albumids) . ")";
	}
	
	public function getEnable()
	{
		return Configure::read('Photo.photo_enabled');
	}
	
	public function checkPostStatus($album,$uid)
	{
        //admin always have post permission
        $viewer = MooCore::getInstance()->getViewer();
        if (!empty($viewer) && $viewer['Role']['is_admin']) {
            return true;
        }
		if (!$uid)
			return false;		
		$friendModel = MooCore::getInstance()->getModel('Friend');
		if ($uid == $album['Album']['user_id'] )
			return true;
			
		if ($album['Album']['privacy'] == PRIVACY_EVERYONE)
		{
			return true;
		}
                
                if (empty($album['Album']['privacy'])){
                    return true;
                }
		
		if ($album['Album']['privacy'] == PRIVACY_FRIENDS)
		{
			$areFriends = $friendModel->areFriends( $uid, $album['Album']['user_id'] );
			if ($areFriends)
				return true;
		}
		
		
		return false;
	}
	
	public function checkSeeComment($album,$uid)
	{
		if ($album['Album']['privacy'] == PRIVACY_EVERYONE)
		{
			return true;
		}
		
		return $this->checkPostStatus($album,$uid);
	}
	
	public function getImage($item, $options) {
            $request = Router::getRequest();
            $view = MooCore::getInstance()->getMooView();
            $prefix = '';
            if (isset($options['prefix'])) {
                $prefix = $options['prefix'] . '_';
            }

            if ($item['Photo']['thumbnail']) {
                $url = FULL_BASE_URL . $request->webroot . 'uploads/photos/thumbnail/' . $item['Photo']['id'] . '/' . $prefix . $item['Photo']['thumbnail'];
            } else {
                $url = $this->assetUrl('Photo.noimage/album.png', $options + array('pathPrefix' => Configure::read('App.imageBaseUrl')));
            }

            return $url;
        }
        
        public function getAlbumCover($cover, $options){
            $request = Router::getRequest();
            $photoModel = MooCore::getInstance()->getModel('Photo.Photo');
            $prefix = '';
            if (isset($options['prefix'])) {
                $prefix = $options['prefix'] . '_';
            }
            $photo = $photoModel->find('first', array('conditions' => array('Photo.thumbnail' => $cover)));
            $url = '';
            if (!empty($photo['Photo']['thumbnail'])){
                $url = FULL_BASE_URL . $request->webroot . 'uploads/photos/thumbnail/' . $photo['Photo']['id'] . '/' . $prefix . $photo['Photo']['thumbnail'];
            }
            else {
                $url = FULL_BASE_URL . $this->assetUrl('Photo.noimage/photo.png', $options + array('pathPrefix' => Configure::read('App.imageBaseUrl')));
            }
            return $url;
        }

}
