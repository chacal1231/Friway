<?php
App::uses('Widget','Controller/Widgets');

class popularVideoWidget extends Widget {
    public function beforeRender(Controller $controller) {
        $num_item_show = $this->params['num_item_show'];
        $controller->loadModel('Video.Video');
        $popular_videos = Cache::read('video.popular_video.'.$num_item_show,'video');
        if (!$popular_videos)
        {
            $popular_videos = $controller->Video->getPopularVideos( $num_item_show, Configure::read('core.popular_interval') );
            Cache::write('video.popular_video.'.$num_item_show,$popular_videos,'video');
        }

        $this->setData('popular_videos', $popular_videos);

    }
}