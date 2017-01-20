<?php
echo $this->Html->script(array('jquery.fileuploader'));
echo $this->Html->css(array( 'fineuploader' ));
?> 
<form id="<?php echo empty($commentFormId) ? 'commentForm' : $commentFormId; ?>"> 
<?php
echo $this->Form->hidden('target_id', array('value' => $target_id));
echo $this->Form->hidden('type', array('value' => $type));

if ( !empty( $class ) )
    $cls = $class;
else
    $cls = 'commentForm';
?>
<?php echo $this->Moo->getItemPhoto(array('User' => $cuser),array('prefix' => '100_square'), array('class' => 'img_wrapper2 user_avatar_large'))?>
<div class="comment">
    <?php $implementMention = ($type == APP_CONVERSATION)? false : true ?>
    <?php echo $this->Form->textarea('message', array('id'=> empty($commentFormTextId) ? 'postComment' : $commentFormTextId,'class' => $cls, 'placeholder' => __('Write a comment'), 'onfocus' => 'showCommentButton(0)'),$implementMention);?>
    <div style="text-align:right;display:none;margin-top:5px;display:block;" class="commentButton" id="commentButton_0">
        <?php if ( $uid ): ?>
        <input type="hidden" name="thumbnail" id="comment_image_<?php echo $target_id;?>" />
		<div id="comment_button_attach_<?php echo $target_id;?>"></div>
        <a href="javascript:void(0)" class="btn btn-action" onclick="ajax_postComment(<?php echo $target_id;?>)" id="shareButton"><?php echo __('Comment')?></a>
	        <?php if($this->request->is('ajax')): ?>
                    <script type="text/javascript">registerAttachComment(<?php echo $target_id;?>);</script>
                <?php else: ?>
            <?php $this->Html->scriptStart(array('inline' => false,'requires'=>array('jquery','global'))); ?>
				jQuery(document).ready(function(){
					registerAttachComment(<?php echo $target_id;?>);
				});
			<?php $this->Html->scriptEnd(); ?>
		<?php endif; ?>
        <?php else: ?>
        <?php echo __('Login or register to post your comment')?>
        <?php endif; ?>
    </div>
    <div id="comment_preview_image_<?php echo $target_id;?>"></div>
</div>	
</form>