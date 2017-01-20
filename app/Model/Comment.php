<?php

/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */

class Comment extends AppModel 
{
	public $actsAs = array(
        'MooUpload.Upload' => array(
            'thumbnail' => array(
               'path' => '{ROOT}webroot{DS}uploads{DS}comments{DS}{field}{DS}',
                'thumbnailSizes' => array(
                    'size' => array('200')
                )
            )
        ),
        'Hashtag'=>array(
            'field_created_get_hashtag'=>'message',
            'field_updated_get_hashtag'=>'message',
        ),
    );

	public $hasMany = array( 'Like' => 	array( 'className' => 'Like',	
											   'foreignKey' => 'target_id',
											   'conditions' => array('Like.type' => 'comment'),						
											   'dependent'=> true
											 ),
							);	
						 
	public $belongsTo = array('User');
	
	public $mooFields = array('thumb');
	
	public $validate = array( 'message' => array( 'rule' => 'notBlank') );
	
	public $order = 'Comment.id desc';
	
	/*
	 * Get comments based on $id and $type
	 * @param int $id - item id
	 * @param string $tyoe - item type
	 * @param int $page
	 * @return array $comments
	 */
	
	public function getComments($id, $type, $page = 1) {
        $comment_count = $this->getCommentsCount($id, $type);

        $offset = 0;
        $comments = array();

        if ($comment_count >= $page * RESULTS_LIMIT) {
            
        }
        if ($page > 1){
            $offset = ($page - 1) * RESULTS_LIMIT;
        }
        //$offset = $comment_count - (RESULTS_LIMIT * intval($page));
        $comments = $this->find('all', array('conditions' => array('Comment.type' => $type,
                'Comment.target_id' => $id
            ),
            'limit' => RESULTS_LIMIT,
            'offset' => $offset
        ));
        return $comments;
    }

    /*
	 * Get comments count based on $id and $type
	 * @param int $id - item id
	 * @param string $tyoe - item type
	 * @return int $comment_count
	 */
	
	public function getCommentsCount( $id, $type )
	{
		$comment_count = $this->find( 'count', array( 'conditions' => array( 'Comment.type' => $type, 
																	  		 'Comment.target_id' => $id
									) ) );
		return $comment_count;
	}

    public function getCommentHashtags($qid, $limit = RESULTS_LIMIT,$page = 1){
        $cond = array(
            'Comment.id' => $qid,
        );

        $comments = $this->find( 'all', array( 'conditions' => $cond, 'limit' => $limit, 'page' => $page ) );
        return $comments;
    }

	public function beforeValidate($options = array()) {
		if (!empty($this->data[$this->alias]['thumbnail'])) {
	        unset($this->validate['message']);
	    }
	
	    return true;
	}
	
	public function getThumb($row){
        return 'thumbnail';
    }
}
 