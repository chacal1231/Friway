<?php 
App::import('Cron.Task/Job','CronTaskJobAbstract');
class CronTaskJobTest extends CronTaskJobAbstract
{
	protected function _execute()
  	{
  		echo 'test';
  		$this->_setIsComplete(true);
  	}
}