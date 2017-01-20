<?php echo $this->Html->script(array('jquery.mp.min'), array('inline' => false));?>
<?php echo $this->Html->css(array('jquery.mp'), null, array('inline' => false));?>
<?php  $this->Html->scriptStart(array('inline' => false));   ?>
jQuery(document).ready(function(){
	$("#comments li").hover(
		function () {
		$(this).contents('.cross-icon').show();
	  }, 
	  function () {
		$(this).contents('.cross-icon').hide();
	  }
	);
});
<?php $this->Html->scriptEnd();  ?>
<?php
$subject = isset($data['subject']) ? $data['subject'] : MooCore::getInstance()->getSubject();
$historyModel = MooCore::getInstance()->getModel('CommentHistory');
if ( !empty( $data['comments'] ) ):
	foreach ($data['comments'] as $comment):
?>
	<li id="itemcomment_<?php echo $comment['Comment']['id']?>" style="position: relative">
		<?php 
                
		// delete link available for commenter, site admin and item author (except convesation)
		if ( ( $this->request->controller != Inflector::pluralize(APP_CONVERSATION) ) && ((!empty($subject) && $subject[key($subject)]['user_id'] == $uid) ||  $comment['Comment']['user_id'] == $uid || ( $uid && $cuser['Role']['is_admin'] ) || ( !empty( $data['admins'] ) && in_array( $uid, $data['admins'] ) ) ) ):
		?>		
			<div class="dropdown edit-post-icon comment-option">
			<a href="javascript:void(0)" data-toggle="dropdown" class="cross-icon">
				<i class="fa fa-angle-down"></i>
			</a>
			<ul class="dropdown-menu">
                                <?php if ($comment['Comment']['user_id'] == $uid || $cuser['Role']['is_admin'] ):?>
				<li><a href="javascript:void(0)" onclick="return editItemComment(<?php echo $comment['Comment']['id']?>)"><?php echo __('Edit Comment'); ?>
                                    </a>	
				</li>
                                <?php endif;?>
				<li>
                                    <?php $isTheaterMode = (!empty($blockCommentId) && $blockCommentId == 'theaterComments')? 1 : 0; ?>
                                    <a href="javascript:void(0)" onclick="return removeItemComment(<?php echo $comment['Comment']['id']?>,<?php echo $isTheaterMode; ?>)" >
                                        <?php echo __('Delete Comment'); ?></a>
				</li>
				
				
			</ul>
		</div>
		<?php endif; ?>
		    
		<?php echo $this->Moo->getItemPhoto(array('User' => $comment['User']), array('prefix' => '100_square'), array('class' => 'img_wrapper2 user_avatar_large'))?>
		<div class="comment hasDelLink">
			<div class="comment_message">
				<?php echo $this->Moo->getName($comment['User'])?>
				<span id="item_feed_comment_text_<?php echo $comment['Comment']['id']?>">
					<?php echo $this->viewMore( h($comment['Comment']['message']),null, null, null, true, array('no_replace_ssl' => 1))?>
					<?php if ($comment['Comment']['thumbnail']):?>
						<div class="comment_thumb">
							<a data-dismiss="modal" href="<?php echo $this->Moo->getImageUrl($comment,array());?>">
								<?php echo $this->Moo->getImage($comment,array('prefix'=>'200'));?>
							</a>
						</div>
					<?php endif;?>
				</span>
			</div>
			<span class="feed-time date">
				<?php echo $this->Moo->getTime( $comment['Comment']['created'], Configure::read('core.date_format'), $utz )?>
			<?php
                            $this->MooPopup->tag(array(
                                   'href'=>$this->Html->url(array("controller" => "histories",
                                                                  "action" => "ajax_show",
                                                                  "plugin" => false,
                                                                  'comment',
                                                                  $comment['Comment']['id'],
                                                              )),
                                   'title' => __('Show edit history'),
                                   'innerHtml'=> $historyModel->getText('comment',$comment['Comment']['id']),
                                'id' => 'history_item_comment_'.$comment['Comment']['id'],
                                'class'=>'edit-btn',
                                'style' => empty($comment['Comment']['edited']) ? 'display:none' : '',
								'data-dismiss'=>'modal'
                           ));
                       ?>	
                            
				<?php if (empty($comment_type)): ?> 
	            <a href="javascript:void(0)" onclick="likeActivity('comment', <?php echo $comment['Comment']['id']?>, 1)" id="comment_l_<?php echo $comment['Comment']['id']?>" class="comment-thumb <?php if ( !empty( $uid ) && !empty( $data['comment_likes'][$comment['Comment']['id']] ) ): ?>active<?php endif; ?>"><i class="icon-thumbs-up-alt"></i></a>
	            <?php
      $this->MooPopup->tag(array(
             'href'=>$this->Html->url(array("controller" => "likes",
                                            "action" => "ajax_show",
                                            "plugin" => false,
                                            'comment',
                                            $comment['Comment']['id'],
                                        )),
             'title' => __('People Who Like This'),
             'innerHtml'=> '<span id="comment_like_' . $comment['Comment']['id'] . '">' . $comment['Comment']['like_count'] . '</span>',
          'data-dismiss' => 'modal'
     ));
 ?>
                    
	                <?php if(empty($hide_dislike)): ?>
	            <a href="javascript:void(0)" onclick="likeActivity('comment', <?php echo $comment['Comment']['id']?>, 0)" id="comment_d_<?php echo $comment['Comment']['id']?>" class="comment-thumb <?php if ( !empty( $uid ) && isset( $data['comment_likes'][$comment['Comment']['id']] ) && $data['comment_likes'][$comment['Comment']['id']] == 0 ): ?>active<?php endif; ?>"><i class="icon-thumbs-down-alt"></i></a>
	            <?php
      $this->MooPopup->tag(array(
             'href'=>$this->Html->url(array("controller" => "likes",
                                            "action" => "ajax_show",
                                            "plugin" => false,
                                            'comment',
                                            $comment['Comment']['id'],1
                                        )),
             'title' => __('People Who Dislike This'),
             'innerHtml'=> '<span id="comment_dislike_' . $comment['Comment']['id'] . '">' .  $comment['Comment']['dislike_count'] . '</span>',
          'data-dismiss' => 'modal'
     ));
 ?>
                     <?php endif; ?>
	            <?php endif; ?> 
            </span>
            
		</div>
	</li>
<?php
	endforeach;
endif;
?>

<?php if ($data['bIsCommentloadMore'] > 0): ?>

        <?php if (empty($blockCommentId)): ?>
            <?php $this->Html->viewMore($data['more_comments'],'comments') ?>
        <?php else: ?>
            <?php $this->Html->viewMore($data['more_comments'].'/id_content:'.$blockCommentId,$blockCommentId) ?>
        <?php endif; ?>

<?php endif; ?>