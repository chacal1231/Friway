<?php
App::uses('Widget','Controller/Widgets');

class featuredCoreWidget extends Widget {
    public function beforeRender(Controller $controller) {
		$num_item_show = $this->params['num_item_show'];
		$controller->loadModel('User');
		$featured_users = $controller->User->getFeaturedUsers($num_item_show);
		$this->setData('featured_users', $featured_users);
    }
}