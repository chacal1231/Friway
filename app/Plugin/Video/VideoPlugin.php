<?php 
App::uses('MooPlugin','Lib');
class VideoPlugin implements MooPlugin{
    public function install(){}
    public function uninstall(){}
    public function settingGuide(){}
    public function menu()
    {
        return array(
            __('General') => array('plugin' => 'video', 'controller' => 'video_plugins', 'action' => 'admin_index'),
            __('Settings') => array('plugin' => 'video', 'controller' => 'video_settings', 'action' => 'admin_index'),
            __('Categories') => array('plugin' => 'video', 'controller' => 'video_categories', 'action' => 'admin_index'),
        );
    }
}