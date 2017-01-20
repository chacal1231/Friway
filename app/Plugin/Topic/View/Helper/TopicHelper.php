<?php
App::uses('AppHelper', 'View/Helper');
class TopicHelper extends AppHelper {	
	public function getTagUnionsTopic($topicids)
	{
		return "SELECT i.id, i.title, i.body, i.like_count, i.created, 'Topic_Topic' as moo_type, 0 as privacy, i.user_id
						 FROM " . Configure::read('core.prefix') . "topics i
						 WHERE i.id IN (" . implode(',', $topicids) . ")";
	}
        
	public function getEnable()
	{
		return Configure::read('Topic.topic_enabled');
	}
	
	public function getImage($item, $options) {
            $request = Router::getRequest();
            $view = MooCore::getInstance()->getMooView();
            $prefix = '';
            if (isset($options['prefix'])) {
                $prefix = $options['prefix'] . '_';
            }

            if ($item[key($item)]['thumbnail']) {
                $url = FULL_BASE_URL . $request->webroot . 'uploads/topics/thumbnail/' . $item[key($item)]['id'] . '/' . $prefix . $item[key($item)]['thumbnail'];
            } else {
                $url = FULL_BASE_URL . $this->assetUrl('Topic.noimage/topic.png', $options + array('pathPrefix' => Configure::read('App.imageBaseUrl')));
            }

            return $url;
        }
        
        public function checkPostStatus($topic, $uid) {
            $cuser = MooCore::getInstance()->getViewer();
            
            if (isset($cuser) && $cuser['Role']['is_admin']){
                return true;
            }
            
            if (isset($topic['Topic']['locked']) && $topic['Topic']['locked']){
                return false;
            }
            
            return true;
        }

        public function checkSeeComment($topic, $uid) {
            return true;
        }

}