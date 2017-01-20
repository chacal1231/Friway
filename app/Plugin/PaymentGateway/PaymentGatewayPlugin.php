<?php 
App::uses('MooPlugin','Lib');
class PaymentGatewayPlugin implements MooPlugin{
    public function install(){}
    public function uninstall(){}
    public function settingGuide(){}
	public function menu()
    {
        return array(
            __('Manage Gateways') => array('plugin' => 'payment_gateway', 'controller' => 'manages', 'action' => 'admin_index'),
        );
    }
}