<?php
App::uses('Widget','Controller/Widgets');

class friendCoreWidget extends Widget {
    public function beforeRender(Controller $controller) {
    	$controller->loadModel('Friend');
    	$num_item_show = $this->params['num_item_show'];
        $id = MooCore::getInstance()->getViewer(true);
        $friends = array();
        if ($id)
        {
        	$friends = $controller->Friend->getUserFriends( $id, null, $num_item_show );
        }
		
        $this->setData('friendCoreWidget',$friends);
    }
}