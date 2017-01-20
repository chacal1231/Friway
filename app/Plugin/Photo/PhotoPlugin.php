<?php 
App::uses('MooPlugin','Lib');
class PhotoPlugin implements MooPlugin{
    public function install(){}
    public function uninstall(){}
    public function settingGuide(){}
    public function menu()
    {
        return array(
            __('General') => array('plugin' => 'photo', 'controller' => 'photo_plugins', 'action' => 'admin_index'),
            __('Settings') => array('plugin' => 'photo', 'controller' => 'photo_settings', 'action' => 'admin_index'),
            __('Categories') => array('plugin' => 'photo', 'controller' => 'album_categories', 'action' => 'admin_index'),
        );
    }
}