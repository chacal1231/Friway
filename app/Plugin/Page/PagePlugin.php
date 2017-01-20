<?php 
App::uses('MooPlugin','Lib');
class PagePlugin implements MooPlugin{
    public function install(){}
    public function uninstall(){}
    public function settingGuide(){}
    public function menu()
    {
        return array(
            __('General') => array('plugin' => 'page', 'controller' => 'page_plugins', 'action' => 'admin_index'),
            __('Settings') => array('plugin' => 'page', 'controller' => 'page_settings', 'action' => 'admin_index'),
        );
    }
}