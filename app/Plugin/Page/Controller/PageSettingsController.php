<?php 
class PageSettingsController extends PageAppController{
    public $components = array('QuickSettings');

    public function admin_index()
    {
        $this->QuickSettings->run($this, array("Page"));
        $this->set('title_for_layout', __('Pages Setting'));
    }
}