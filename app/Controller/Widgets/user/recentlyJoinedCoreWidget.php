<?php
App::uses('Widget','Controller/Widgets');

class recentlyJoinedCoreWidget extends Widget {
    public function beforeRender(Controller $controller) {
        $controller->loadModel('User');
        $users = $controller->User->getLatestUsers( $this->params['num_item_show']);
        $this->setData('recentlyJoinedCoreWidget',array('users'=>$users));
    }
}