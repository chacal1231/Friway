<?php
App::uses('Widget','Controller/Widgets');

class popularAlbumsPhotoWidget extends Widget {
    public function beforeRender(Controller $controller) {
    	$num_item_show = $this->params['num_item_show'];
    	$controller->loadModel('Photo.Album');    	
    	$popular_albums = Cache::read('photo.popular_albums.'.$num_item_show, 'photo');
    	if (!$popular_albums)
    	{
        	$popular_albums = $controller->Album->getPopularAlbums($num_item_show, Configure::read('core.popular_interval'));
        	Cache::write('photo.popular_albums.'.$num_item_show, $popular_albums, 'photo');
    	}
        $this->setData('popular_albums', $popular_albums);
    }
}