<?php
App::uses('Widget','Controller/Widgets');

class birthdayBlockCoreWidget extends Widget {
    public function beforeRender(Controller $controller) {
    	$uid = MooCore::getInstance()->getViewer(true);
    	$birthday = null;
    	if ($uid && !Configure::read('core.force_login'))
    	{
    		$controller->loadModel('User');
    		$birthday = $controller->User->getTodayBirthdayFriend($uid,$controller->viewVars['utz']);
    	}
    	$this->setData('birthday', $birthday);
    }
}