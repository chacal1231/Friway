<?php

/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */
App::uses('PhotoAppModel','Photo.Model');
class PhotoTag extends PhotoAppModel {

    public $belongsTo = array('Photo'=>array(
            'className' => 'Photo.Photo',            
        ), 
    	'User');

    public function getPhotos($user_id = null, $page = 1) {
        $photos = $this->find('all', array('conditions' => array('PhotoTag.user_id' => $user_id),
                'order' => 'PhotoTag.id desc',
                'limit' => Configure::read('Photo.photo_item_per_pages'),
                'page' => $page
                    ));
        return $photos;
    }
    
    public function getPhotosCount($uid = null){
        if (empty($uid)){
            exit;
        }
        $cond = array('PhotoTag.user_id' => $uid);
        $count = $this->find('count', array('conditions' => $cond));
        
        return $count;
    }

}
