<?php
App::uses('Widget','Controller/Widgets');

class myJoinedGroupWidget extends Widget {
    public function beforeRender(Controller $controller) {
    	$uid = MooCore::getInstance()->getViewer(true);
    	$aMyJoinedGroup = null;
    	if ( !( empty($uid) && Configure::read('core.force_login') ) ):
		    $num_item_show = $this->params['num_item_show'];
		    $aMyJoinedGroup = Cache::read('my_joined_group_'.$uid, 'group');
		    if(empty($aMyJoinedGroup))
		    {
	            $controller->loadModel('Group.GroupUser');
	            $aMyJoinedGroup = $controller->GroupUser->getJoinedGroups($uid, $num_item_show);
		        
		        Cache::write('my_joined_group_'.$uid, $aMyJoinedGroup , 'group');
		    }
    	endif;
    	
    	$this->setData('myJoinedGroupWidget',$aMyJoinedGroup);
    }
}