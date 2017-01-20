<?php
class SubscriptionTransaction extends SubscriptionAppModel 
{
    public $belongsTo = array('User',
    	'Gateway' => array(
    		'className'=> 'PaymentGateway.Gateway',            
        	),
        'Subscribe' => array(
    		'className'=> 'Subscription.Subscribe', 
			'foreignKey' => 'subscribes_id'			
        	),
        'SubscriptionPackagePlan' => array(
        	'className'=> 'Subscription.SubscriptionPackagePlan',
			'foreignKey' => 'plan_id'
        	)
    	);
}
