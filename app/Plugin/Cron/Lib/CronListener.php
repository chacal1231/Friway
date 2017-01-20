<?php
App::uses('CakeEventListener', 'Event');

class CronListener implements CakeEventListener
{
    public function implementedEvents()
    {
        return array(
            'MooView.afterLoadMooCore' => 'afterLoadMooCore',
        );
    }
    
    public function afterLoadMooCore($event)
    {
    	$v = $event->subject();
    	$url = $v->request->base.'/cron/task/run?key='.Configure::read('Cron.cron_key');
    	if (Configure::read('Cron.cron_javascript'))
    	{
	        if ($v instanceof MooView) {
	        	 $v->addInitJs('$(function() { mooAjax("'.$url.'", "get", "", function(data) { }); });');
	        }
    	}
    }
}