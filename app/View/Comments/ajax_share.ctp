<?php $this->setCurrentStyle(4);?>
<?php if (!empty($comment)): ?>
<li class="slide" id="itemcomment_<?php echo $comment['Comment']['id']?>" style="position: relative">
	<?php if ( $comment['Comment']['type'] != APP_CONVERSATION ): ?>
	<div class="dropdown edit-post-icon comment-option">
		<a href="javascript:void(0)" data-toggle="dropdown" class="cross-icon">
			<i class="fa fa-angle-down"></i>
		</a>
		<ul class="dropdown-menu">
			<?php if ($comment['Comment']['user_id'] == $uid):?>
			<li>
				<a href="javascript:void(0)" onclick="return editItemComment(<?php echo $comment['Comment']['id']?>)">
					<?php echo __('Edit Comment'); ?>
				</a>	
			</li>
			<?php endif;?>
			
			<li>
				<a href="javascript:void(0)" onclick="return removeItemComment(<?php echo $comment['Comment']['id']?>)" >
					<?php echo __('Delete Comment'); ?>
				</a>
			</li>
			
			
		</ul>
	</div>
	<?php endif; ?>
	<?php
	if ( !empty( $activity ) )
		echo $this->Moo->getItemPhoto(array('User' => $comment['User']),array( 'prefix' => '50_square'), array('class' => 'img_wrapper2 user_avatar_small'));
	else
		echo $this->Moo->getItemPhoto(array('User' => $comment['User']),array( 'prefix' => '100_square'), array('class' => 'img_wrapper2 user_avatar_large'));
	?>
	<div class="comment">

		<div class="comment_message">
                    <?php echo $this->Moo->getName($comment['User'])?>
            <span id="item_feed_comment_text_<?php echo $comment['Comment']['id']?>">
			    <?php
			    if ( !empty( $activity ) )
	                echo $this->Moo->formatText( h($comment['Comment']['message']), false, true ,array('no_replace_ssl' => 1));
	            else
	                echo $this->Moo->formatText( h($comment['Comment']['message']), false, true, array('no_replace_ssl' => 1) );
	            ?>
	            
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
			<?php echo __('Just now')?>
                    <?php
      $this->MooPopup->tag(array(
             'href'=>$this->Html->url(array("controller" => "histories",
                                            "action" => "ajax_show",
                                            "plugin" => false,
                                            'comment',
                                            $comment['Comment']['id']
                                        )),
             'title' => __('Show edit history'),
             'innerHtml'=> __('Edited'),
          'style' => empty($comment['Comment']['edited']) ? 'display:none;' : '',
          'class' => 'edit-btn',
          'id' => 'history_item_comment_'.$comment['Comment']['id'],
          'data-dismiss'=>'modal'
     ));
 ?>
            
			<?php if ( $comment['Comment']['type'] != APP_CONVERSATION ): ?>
                <a href="javascript:void(0)" onclick="likeActivity('comment', <?php echo $comment['Comment']['id']?>, 1)" id="comment_l_<?php echo $comment['Comment']['id']?>" class="comment-thumb"><i class="icon-thumbs-up-alt"></i></a> <span id="comment_like_<?php echo $comment['Comment']['id']?>">0</span>
                <?php if(empty($hide_dislike)): ?>
                    <a href="javascript:void(0)" onclick="likeActivity('comment', <?php echo $comment['Comment']['id']?>, 0)" id="comment_d_<?php echo $comment['Comment']['id']?>" class="comment-thumb"><i class="icon-thumbs-down-alt"></i></a> <span id="comment_dislike_<?php echo $comment['Comment']['id']?>">0</span>
                <?php  endif;?>
            <?php endif; ?>
		</span>
	</div>
</li>
<?php endif;?>