<?php
App::uses('PaymentGatewayAppModel', 'PaymentGateway.Model');
class Gateway extends PaymentGatewayAppModel 
{
    public $validate = array(   
        'name' =>   array(   
            'notEmpty' => array(
                'rule'     => 'notBlank',
                'message'  => 'Name is required'
            ),
        ),     
    );
    
	public function beforeSave($options = array())
	{
		if ( !empty( $this->data['Gateway']['config'] ) )
			$this->data['Gateway']['config'] = json_encode($this->data['Gateway']['config']);
		
		return true;
	}
}
