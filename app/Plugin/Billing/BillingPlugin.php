<?php 
App::uses('MooPlugin','Lib');
class BillingPlugin implements MooPlugin{
    public function install(){}
    public function uninstall(){}
    public function settingGuide(){}
    public function menu()
    {
        return array(
            __('Manage Currencies') => array('plugin' => 'billing', 'controller' => 'currencies', 'action' => 'admin_index')            
        );
    }
}