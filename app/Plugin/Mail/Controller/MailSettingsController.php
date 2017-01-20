<?php 
class MailSettingsController extends MailAppController{
	public $components = array('QuickSettings');
	
	public function beforeFilter()
    {
        parent::beforeFilter();
        $this->_checkPermission( array('super_admin' => true) ); 
    } 
    
    public function admin_index($id = null)
    {
    	$this->QuickSettings->run($this, array("Mail"), $id);
    }
}