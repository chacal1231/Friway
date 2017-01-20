<?php
App::uses('Widget','Controller/Widgets');

class upcomingEventWidget extends Widget {
    public function beforeRender(Controller $controller) {
    	$controller->loadModel('Event.Event');
    	$num_item_show = $this->params['num_item_show'];        
        $upcomming_events = Cache::read('upcoming_events.'.$num_item_show, 'event');
        
	    if(!$upcomming_events){
	        $upcomming_events = $controller->Event->getUpcoming( $num_item_show);
	        Cache::write('upcoming_events.'.$num_item_show,$upcomming_events, 'event');
	    }
        
        $this->setData('upcomingEventWidget',$upcomming_events);
    }
}