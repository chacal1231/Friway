<?php

/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */

class ActivityComment extends AppModel 
{
	public $actsAs = array(
            'Notification',
        'MooUpload.Upload' => array(
            'thumbnail' => array(
               'path' => '{ROOT}webroot{DS}uploads{DS}activitycomments{DS}{field}{DS}',
                'thumbnailSizes' => array(
                    'size' => array('200')
                )
            )
        ),
        'Hashtag' => array(
            'table' => 'activities_comments',
            'field_created_get_hashtag'=>'comment',
            'field_updated_get_hashtag'=>'comment',
        )
    );
    
    public $mooFields = array('thumb');
	
	public $hasMany = array( 'Like' => 	array( 'className' => 'Like',	
											   'foreignKey' => 'target_id',
											   'conditions' => array('Like.type' => 'core_activity_comment'),						
											   'dependent'=> true
											 ),
							); 
		
	public $validate = array( 'user_id' => array( 'rule' => 'notBlank'),
							  'activity_id' => array( 'rule' => 'notBlank'),
							  'comment' => array( 'rule' => 'notBlank'),							
						 );
						 
	public $belongsTo = array( 'Activity'  => array('counterCache' => true), 
							   'User' 
	);
	
	public function getThumb($row){
        return 'thumbnail';
    }
	
	public $order = 'ActivityComment.id asc';
	
	public function beforeValidate($options = array()) {
		if (!empty($this->data[$this->alias]['thumbnail'])) {
	        unset($this->validate['comment']);
	    }
	
	    return true;
	}

    public function getActivityCommentHashtags($qid, $limit = RESULTS_LIMIT,$page = 1){
        $cond = array(
            'ActivityComment.id' => $qid,
        );

        $activity_comments = $this->find( 'all', array( 'conditions' => $cond, 'limit' => $limit, 'page' => $page ) );
        return $activity_comments;
    }
}
