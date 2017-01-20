<?php 
class EventSettingsController extends EventAppController{
    public $components = array('QuickSettings');
    public function beforeFilter(){
        parent::beforeFilter();
        $this->loadModel('Setting');
        $this->loadModel('SettingGroup');
    }
    public function admin_index()
    {
        $event_enabled = Configure::read('Event.event_enabled');
        if($event_enabled == 0)
        {
            $this->loadModel('Menu.CoreMenuItem');
            $events_menu = $this->CoreMenuItem->find('first',array(
                'conditions'=>array('url'=>'/events','type'=>'page')
            ));

            if ($events_menu['CoreMenuItem']['id']) {
                $this->CoreMenuItem->id = $events_menu['CoreMenuItem']['id'];
                $this->CoreMenuItem->save(array('is_active' => 0));
            } else {
                $this->CoreMenuItem->set(array(
                    'name' => 'Events',
                    'url' => '/events',
                    'is_active' => 0,
                    'menu_id' => 1,
                    'type' => 'page',
                    'menu_order' => 999
                ));
                $this->CoreMenuItem->save();
            }
            //update plugin enable status
            $this->loadModel('Plugin');
            $eventPlugin = $this->Plugin->find('first',array('conditions' => array('Plugin.key'=>'Event')));
            $this->Plugin->id = $eventPlugin['Plugin']['id'];
            $this->Plugin->save( array( 'enabled' => 0 ) );

        }
        elseif($event_enabled == 1)
        {
            $this->loadModel('Menu.CoreMenuItem');
            $events_menu = $this->CoreMenuItem->find('first',array(
                'conditions'=>array('url'=>'/events','type'=>'page')
            ));

            if ($events_menu['CoreMenuItem']['id']) {
                $this->CoreMenuItem->id = $events_menu['CoreMenuItem']['id'];
                $this->CoreMenuItem->save(array('is_active' => 1));
            } else {
                $this->CoreMenuItem->set(array(
                    'name' => 'Events',
                    'url' => '/events',
                    'is_active' => 1,
                    'menu_id' => 1,
                    'type' => 'page',
                    'menu_order' => 999
                ));
                $this->CoreMenuItem->save();
            }
            //update plugin enable status
            $this->loadModel('Plugin');
            $eventPlugin = $this->Plugin->find('first',array('conditions' => array('Plugin.key'=>'Event')));
            $this->Plugin->id = $eventPlugin['Plugin']['id'];
            $this->Plugin->save( array( 'enabled' => 1 ) );
        }
        // clear cache menu
        Cache::clearGroup('menu', 'menu');
        Cache::clearGroup('event');

        $this->QuickSettings->run($this, array("Event"));

    }

}