<?php
App::uses('Widget','Controller/Widgets');

class popularTopicWidget extends Widget {
    public function beforeRender(Controller $controller) {
    	$num_item_show = $this->params['num_item_show'];
    	$controller->loadModel('Topic.Topic');
    	$popular_topics = Cache::read('topic.popular_topics.'.$num_item_show,'topic');
    	if (!$popular_topics)
    	{
    		$popular_topics = $controller->Topic->getPopularTopics( $num_item_show, Configure::read('core.popular_interval') );
    		Cache::write('topic.popular_topics.'.$num_item_show, $popular_topics,'topic');
    	}
    	
    	$this->setData('popular_topics', $popular_topics);
    }
}