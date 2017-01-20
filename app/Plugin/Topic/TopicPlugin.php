<?php 
App::uses('MooPlugin','Lib');
class TopicPlugin implements MooPlugin{
    public function install(){}
    public function uninstall(){}
    public function settingGuide(){}
    public function menu()
    {
        return array(
            __('General') => array('plugin' => 'topic', 'controller' => 'topic_plugins', 'action' => 'admin_index'),
            __('Settings') => array('plugin' => 'topic', 'controller' => 'topic_settings', 'action' => 'admin_index'),
            __('Categories') => array('plugin' => 'topic', 'controller' => 'topic_categories', 'action' => 'admin_index'),
        );
    }
}