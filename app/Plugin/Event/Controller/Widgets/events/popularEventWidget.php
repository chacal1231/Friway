<?php
App::uses('Widget','Controller/Widgets');

class popularEventWidget extends Widget {
    public function beforeRender(Controller $controller) {
    	$controller->loadModel('Event.Event');
    	$num_item_show = $this->params['num_item_show'];        
        $upcomming_events = Cache::read('popular_events.'.$num_item_show, 'event');
        
	    if(!$upcomming_events){
	        $upcomming_events = $controller->Event->getPopularEvents( $num_item_show,Configure::read('core.popular_interval'));
	        Cache::write('popular_events.'.$num_item_show,$upcomming_events, 'event');
	    }
        
        $this->setData('popularEventWidget',$upcomming_events);
    }
}