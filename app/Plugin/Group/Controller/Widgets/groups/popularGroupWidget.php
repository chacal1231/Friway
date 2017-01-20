<?php
App::uses('Widget','Controller/Widgets');

class popularGroupWidget extends Widget {
    public function beforeRender(Controller $controller) {
    	$num_item_show = $this->params['num_item_show'];
    	
    	$popular_groups = Cache::read('group.popular_groups.'.$num_item_show,'group');
		if(!$popular_groups){
		    $controller->loadModel('Group.Group');
            $popular_groups = $controller->Group->getPopularGroups($num_item_show, Configure::read('core.popular_interval'));
		    Cache::write('group.popular_groups.'.$num_item_show,$popular_groups);
		}
		
		$this->setData('popularGroupWidget', $popular_groups);
    }
}