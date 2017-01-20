<?php

/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */
class TopicSettingsController extends TopicAppController {

    public $components = array('QuickSettings');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->loadModel('Setting');
        $this->loadModel('SettingGroup');
        $this->loadModel('Plugin');
        $this->loadModel('Menu.CoreMenuItem');
    }

    public function admin_index($id = null) {
        $topic_enabled = Configure::read('Topic.topic_enabled');
        if (!$topic_enabled) {
            $topics_menu = $this->CoreMenuItem->find('first', array(
                'conditions' => array('url' => '/topics', 'type' => 'page')
            ));
            if ($topics_menu['CoreMenuItem']['id']) {
                $this->CoreMenuItem->id = $topics_menu['CoreMenuItem']['id'];
                $this->CoreMenuItem->save(array('is_active' => 0));
            } else {
                $this->CoreMenuItem->set(array(
                    'name' => 'Topics',
                    'url' => '/topics',
                    'is_active' => 0,
                    'menu_id' => 1,
                    'type' => 'page',
                    'menu_order' => 999
                ));
                $this->CoreMenuItem->save();
            }

            //update plugin enable status
            $this->loadModel('Plugin');
            $topicPlugin = $this->Plugin->find('first',array('conditions' => array('Plugin.key'=>'Topic')));
            $this->Plugin->id = $topicPlugin['Plugin']['id'];
            $this->Plugin->save( array( 'enabled' => 0 ) );

        } else {
            $topics_menu = $this->CoreMenuItem->find('first', array(
                'conditions' => array('url' => '/topics', 'type' => 'page')
            ));
            if ($topics_menu['CoreMenuItem']['id']) {
                $this->CoreMenuItem->id = $topics_menu['CoreMenuItem']['id'];
                $this->CoreMenuItem->save(array('is_active' => 1));
            } else {
                $this->CoreMenuItem->set(array(
                    'name' => 'Topics',
                    'url' => '/topics',
                    'is_active' => 1,
                    'menu_id' => 1,
                    'type' => 'page',
                    'menu_order' => 999
                ));
                $this->CoreMenuItem->save();
            }

            //update plugin enable status
            $this->loadModel('Plugin');
            $topicPlugin = $this->Plugin->find('first',array('conditions' => array('Plugin.key'=>'Topic')));
            $this->Plugin->id = $topicPlugin['Plugin']['id'];
            $this->Plugin->save( array( 'enabled' => 1 ) );

        }
        
        // clear cache menu
        Cache::clearGroup('menu', 'menu');
        
        // clear cache topic
        Cache::clearGroup('topic', 'topic');

        $this->QuickSettings->run($this, array("Topic"), $id);
        $this->set('title_for_layout', __('Topics Setting'));
    }

}
