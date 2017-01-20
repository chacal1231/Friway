
<?php

App::uses('Widget', 'Controller/Widgets');

class featuredGroupWidget extends Widget {

    public function beforeRender(Controller $controller) {
        
        $data = array(
            'featuredGroup' => array(),
        );
        
        $controller->loadModel('Group.Group');
        
        // get featured groups
        $featuredGroup = $controller->Group->find('all', array('conditions' => array('Group.featured' => 1)));
        
        $data['featuredGroup'] = $featuredGroup;
        
        $this->setData('data', $data);
    }

}
