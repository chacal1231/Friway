<?php
class Job extends AppModel {	
	public function gc()
	{
		$this->updateAll(array(
	      'state' => "'timeout'",
	      'is_complete' => 1,
	      'progress' => 1,
	      'completion_date' => 'NOW()',
	    ), array(
	      'is_complete = ?' => 0,
	      'state = ?' => 'active',
	      'modified_date < DATE_SUB(NOW(),INTERVAL 60 MINUTE)',
	    ));
	}
}
