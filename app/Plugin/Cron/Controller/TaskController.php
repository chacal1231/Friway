<?php
class TaskController extends CronAppController {
	public $components = array('Paginator','QuickSettings');
	public $paginate = array(
        'limit' => 10,
        'order' => array(
            'id' => 'DESC'
        )
    );
    public $check_subscription = false;
    
	public function beforeFilter()
	{
		parent::beforeFilter();
		if(isset($this->params['prefix']) && $this->params['prefix'] == 'admin')
		{
			$this->_checkPermission(array('super_admin' => 1));
		}
	}
	
    public function run()
    {
    	$time_start = microtime(true);
    	$this->autoRender = false;    	
    	$this->Task->run($this->request->params);    	
    	$time_end = microtime(true);
		$time = $time_end - $time_start;
    	
    	echo 'Cron successfull on :'.$time;
    }
    
 	public function __construct($request = null, $response = null) 
    {
        parent::__construct($request, $response);        
        $this->url = '/admin/cron/task/';
        $this->set('url', $this->url);
    }
    
	public function admin_index()
    {
    	$this->loadModel('Cron.Task');
        $this->loadModel('Cron.Processes');
        
        $this->Paginator->settings = $this->paginate;
        $tasks = $this->Paginator->paginate('Task');
     	foreach($tasks as $k => $v)
        {
            $tasks[$k]['Task']['processes_info'] = $this->Processes->find('first', array(
                'conditions' => array('name' => $v['Task']['class'])
            ));
        }
        $this->set('tasks', $tasks);
    }
    
    public function admin_settings($id = null)
    {
    	$this->QuickSettings->run($this, array("Cron"), $id);
    }
    
    public function admin_do_disable($id)
    {
    	$this->do_active($id, 0, 'enable');
    }
    
	public function admin_do_enable($id)
    {
    	$this->do_active($id, 1, 'enable');
    }
    
	private function do_active($id, $value = 1, $task)
    {
        if(!$this->Task->isIdExist($id))
        {
            $this->Session->setFlash(__('This task does not exist'), 'default', array('class' => 'Metronic-alerts alert alert-danger fade in' ));
        }
        else 
        {
            $this->Task->id = $id;
            $this->Task->save(array($task => $value));

            $this->Session->setFlash(__('Successfully updated'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in' ));
        }
        $this->redirect($this->referer());
    }
}
