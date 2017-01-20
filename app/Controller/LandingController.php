<?php

/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */

class LandingController extends AppController{
    public function index(){
        $this->loadModel('Page.Page');
        $result = $this->Page->find('first',array(
            'fields' => 'uri',
            'conditions' => array('Page.alias'=>'landing_index')
        ));
        $this->set('uri',$result);
        return $result;
    }
}