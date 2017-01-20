<?php 
App::uses('MooPlugin','Lib');
class BlogPlugin implements MooPlugin{
    public function install(){}
    public function uninstall(){}
    public function settingGuide(){}
    public function menu()
    {
        return array(
            __('General') => array('plugin' => 'blog', 'controller' => 'blog_plugins', 'action' => 'admin_index'),
            __('Settings') => array('plugin' => 'blog', 'controller' => 'blog_settings', 'action' => 'admin_index'),
        );
    }
}