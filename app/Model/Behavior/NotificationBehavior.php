<?php

/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */
App::uses('ModelBehavior', 'Model');
App::uses('CakeSession', 'Model/Datasource');

class NotificationBehavior extends ModelBehavior {

    public $runtime = array();

    protected $_joinTable;

    protected $_runtimeModel;

    public function setup(Model $Model, $settings = array()) {
        $this->settings[$Model->alias] = (array) $settings;
    }

    public function getRuntimeModel() {
        if (!$this->_runtimeModel)
            $this->_runtimeModel = ClassRegistry::init('Notification');
        return $this->_runtimeModel;
    }

    public function afterSave(Model $Model, $created, $options = array()) {
        $field_created = "users_taggings";
        $status_field = 'item_id';
        $cuid = MooCore::getInstance()->getViewer(true);
        $RuntimeModel = $this->getRuntimeModel();
        $userTaggingModel = ClassRegistry::init('UserTagging');
        $stopNotificationModel = MooCore::getInstance()->getModel('NotificationStop');
        $activityModel = ClassRegistry::init('Activity');
        $groupModel = ClassRegistry::init('Group.Group');
        if (isset($Model->data['Like']) && !empty($Model->data['Like']['thumb_up']) ){ // like item
            // send notification to tagged user
            $type = isset($Model->data['Like']['type']) ? $Model->data['Like']['type'] : '';
            $target_id =  isset($Model->data['Like']['target_id']) ? $Model->data['Like']['target_id'] : '';
            
            if (empty($type) || empty($target_id)){
                return true;
            }
            
            $itemTagged = $userTaggingModel->getTaggedItem($target_id, Inflector::pluralize($type));

            $notified_url = '';
            $notified_action = '';
            switch ($type){
                case 'activity':
                    $notified_url = "/users/view/$cuid/activity_id:$target_id";
                    $notified_action = 'like_tagged_status';
                    break;
                default :
                    break;
            }
            $userTaggings = empty($itemTagged) ? false : $itemTagged['UserTagging']['users_taggings'];

            $listUserTaggings = explode(',', $userTaggings);
            $listUserTaggings[] = $cuid;
            foreach ($listUserTaggings as $tagged_uid){
                if ($cuid != $tagged_uid){ // dont send notification to liker
                    
                    // dont send notification to user who setting it stop
                	$notificationStop = $stopNotificationModel->isNotificationStop($target_id,$type,$tagged_uid);
                	
                    if ($notificationStop){
                        continue;
                    }
                    $RuntimeModel->clear();
                    $RuntimeModel->record( array(
                        'recipients'  => $tagged_uid,
                        'sender_id'   => $cuid,
                        'action'      => $notified_action,
                        'url'         => $notified_url,
                    ) );
                }

                //user mention


                        if($type == 'activity'){
                            $action = 'like_mentioned_post';
                            $isActivity = true;
                            $mentionUrl = $notified_url;
                            $activityModel = ClassRegistry::init('Activity');
                            $activity = $activityModel->findById($Model->data['Like']['target_id']);
                            if(!empty($activity)){
                                preg_match_all(REGEX_MENTION,$activity['Activity']['content'],$matches);
                            }
                        }else{
                            $action = 'like_mentioned_comment';
                            if($type == 'comment'){
                                $commentModel = ClassRegistry::init('Comment');
                                $comment = $commentModel->findById($Model->data['Like']['target_id']);
                                if(!empty($comment)){
                                    $content = $comment['Comment']['message'];
                                    list($plugin, $model) = mooPluginSplit($comment['Comment']['type']);
                                    $mentionUrl = "/".lcfirst(Inflector::pluralize($model))."/view/".$comment['Comment']['target_id'];
                                }
                            }elseif($type == 'core_activity_comment'){
                                $activityComment = ClassRegistry::init('ActivityComment');
                                $comment = $activityComment->findById($Model->data['Like']['target_id']);
                                if(!empty($comment)){
                                    $content = $comment['ActivityComment']['comment'];
                                    $mentionUrl = "/users/view/$cuid/activity_id:".$comment['ActivityComment']['activity_id'];
                                }
                            }
                            if(!empty($comment) && isset($content)){
                                preg_match_all(REGEX_MENTION,$content,$matches);
                            }
                        }

                        if(!empty($matches) && !empty($mentionUrl)){
                            foreach($matches[0] as $key => $value){
                                if($matches[1][$key] != $cuid){
                                    if(!empty($isActivity)){
                                        // dont send notification to user who setting it stop
                                    	$notificationStop = $stopNotificationModel->isNotificationStop($target_id,$type,$matches[1][$key]);                                    	
                                        if ($notificationStop){
                                            continue;
                                        }
                                    }

                                    $RuntimeModel->clear();
                                    $RuntimeModel->record(array('recipients' => $matches[1][$key],
                                            'sender_id' => $cuid,
                                            'action' => $action,
                                            'url' => $mentionUrl
                                        ));
                                }
                            }
                        }


            }
        }else if (isset($Model->data['ActivityComment'])){ // comment on activity
            // send notification to tagged user
            $activity_id = isset($Model->data['ActivityComment']['activity_id']) ? $Model->data['ActivityComment']['activity_id'] : '';
            
            if (empty($activity_id)){
                return true;
            }
            
            $activity_type = 'activity';
            $itemTagged = $userTaggingModel->getTaggedItem($activity_id, Inflector::pluralize($activity_type));

            $notified_url = '';
            $notified_action = '';
            switch ($activity_type){
                case 'activity':
                    $notified_url = "/users/view/$cuid/activity_id:$activity_id";
                    $notified_action = 'comment_tagged_status';
                    break;
                default :
                    break;

            }
            $userTaggings = empty($itemTagged) ? false : $itemTagged['UserTagging']['users_taggings'];

            $listUserTaggings = explode(',', $userTaggings);
            $listUserTaggings[] = $cuid;
            foreach ($listUserTaggings as $tagged_uid){
                if ($cuid != $tagged_uid){ // dont send notification to liker
                    
                    // dont send notification to user who setting it stop
                	$notificationStop = $stopNotificationModel->isNotificationStop($activity_id,$activity_type,$tagged_uid);
                    if ($notificationStop){
                        continue;
                    }
                    $RuntimeModel->clear();
                    $RuntimeModel->record( array(
                        'recipients'  => $tagged_uid,
                        'sender_id'   => $cuid,
                        'action'      => $notified_action,
                        'url'         => $notified_url,
                    ) );
                }
            }

            //user mention
            if($activity_type == 'activity'){
                $activityModel = ClassRegistry::init('Activity');
                $activity = $activityModel->findById($Model->data['ActivityComment']['activity_id']);
                if(!empty($activity)){
                    preg_match_all(REGEX_MENTION,$activity['Activity']['content'],$matches);
                    if(!empty($matches)){
                        foreach($matches[0] as $key => $value){
                            if($matches[1][$key] != $cuid){
                                // dont send notification to user who setting it stop
                            	$notificationStop = $stopNotificationModel->isNotificationStop($activity_id,$activity_type,$matches[1][$key]);
                                if ($notificationStop){
                                    continue;
                                }

                                $RuntimeModel->clear();
                                $RuntimeModel->record(array('recipients' => $matches[1][$key],
                                        'sender_id' => $cuid,
                                        'action' => 'comment_mentioned_post',
                                        'url' => $notified_url
                                    ));
                            }
                        }
                    }
                }
            }

        }else if (isset($Model->data['UserTagging'])) { // tagged user on a status
            $userIds = $this->filterUsers($Model->data[$Model->alias][$field_created]);
            $activity_id = $Model->data[$Model->alias][$status_field];
            $listUserIds = explode(',', $userIds);
            $action = 'tagged_status';
            $params = '';
            
            // check status group
            $activity = $activityModel->find('first', array(
                'conditions' => array(
                    'Activity.id' => $activity_id
                )
            ));
            if (isset($activity['Activity']['type']) && $activity['Activity']['type'] == 'Group_Group'){
                $action = 'tagged_group_status';
                $group = $groupModel->find('first', array(
                    'conditions' => array(
                        'Group.id' => $activity['Activity']['target_id']
                    )
                ));
                $params = isset($group['Group']['name']) ? $group['Group']['name'] : '';
            }
            
            foreach ($listUserIds as $uid){
                $RuntimeModel->clear();
                $RuntimeModel->record(array(
                    'recipients' => $uid,
                    'sender_id' => $cuid,
                    'action' => $action,
                    'url' => "/users/view/$cuid/activity_id:$activity_id",
                    'params' => $params
                ));
            }
        }
        
    }

    public function afterDelete(Model $Model) {
        
    }

    public function beforeFind(Model $Model, $query) {
        
    }

    public function afterFind(Model $Model, $results, $primary = false) {
        
    }

    private function filterUsers($ids = null) {
        if (empty($ids))
            return false;
        $ids = explode(",", $ids);
        if (empty($ids))
            return false;
        $in_SQL = '';
        foreach ($ids as $id) {
            $in_SQL.=(is_int((int) $id) ? ((int) $id) . "," : "");
        }
        $in_SQL = trim($in_SQL, ',');
        if (empty($in_SQL))
            return false;
        $db = ConnectionManager::getDataSource('default');
        $prefix = (!empty($db->config['prefix']) ? $db->config['prefix'] : '');

        $sql = "SELECT id FROM " . $prefix . "users WHERE id IN($in_SQL)";
        $users = $db->fetchAll($sql);

        $results = Hash::extract($users, '{n}.' . $prefix . 'users.id');
        return implode(',', $results);
    }

}
