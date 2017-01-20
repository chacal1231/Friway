<?php
App::uses('BillingAppModel','Billing.Model');
class Currency extends BillingAppModel 
{
    public $validate = array(   
        'name' =>   array(   
            'notEmpty' => array(
                'rule'     => 'notBlank',
                'message'  => 'Name is required'
            ),
        ),     
        'currency_code' => array(
            'rule' => array('notBlank'),
            'message'  => 'Code is not valid'
        ),
        'symbol' => array(
            'rule' => array('notBlank'),
            'message'  => 'Symbol is not valid'
        ),
    );
}
