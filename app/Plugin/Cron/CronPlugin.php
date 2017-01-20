<?php 
App::uses('MooPlugin','Lib');
class CronPlugin implements MooPlugin{
	public function install(){}
	public function uninstall(){}
	public function settingGuide(){
		ob_start();
		require_once APP.'Plugin'.DS.'Cron'.DS.'View'.DS.'Task'.DS.'help.ctp';
		return ob_get_clean();
	}
	public function menu()
    {
        return array(            
            __('Manage Tasks') => array('plugin' => 'cron', 'controller' => 'task', 'action' => 'admin_index'),
        	__('Manage Settings') => array('plugin' => 'cron', 'controller' => 'task', 'action' => 'admin_settings'),
        );
    }
};