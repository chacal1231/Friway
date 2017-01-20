<?php
/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */

class MakeWebFasterController extends AppController{
    public $components = array('QuickSettings');

    public function admin_minifyjs(){
        $this->set('title_for_layout', __('Make the Web Faster - Minify Resources ( JavaScript )'));
        $this->QuickSettings->run($this, array("FacebookIntegration"), $id);
        $this->set('url', '/admin/make_web_faster/minifyjs');
    }
    public function admin_minifycss(){

    }
    public function admin_minifyhtml(){

    }
}