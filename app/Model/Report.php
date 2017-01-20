<?php

/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */

class Report extends AppModel {
	
	public $belongsTo = array( 'User' );
						
	public $validate = array(	
							'reason' => 	array( 	 
								'rule' => 'notBlank',
								'message' => 'Reason is required'
							)
	);
	
	public $order = 'Report.id desc';
}
