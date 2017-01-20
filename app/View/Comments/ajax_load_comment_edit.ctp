<script type="text/javascript">
    $(document).ready(function(){
        $('textarea:not(.no-grow)').autogrow();
    });
</script>
<div id="item_comment_edit_<?php echo $comment['Comment']['id']?>">
	<textarea id="message_item_comment_edit_<?php echo $comment['Comment']['id']?>" name="message" ><?php echo $comment['Comment']['message']?></textarea>
	<input type="hidden" value="<?php echo $comment['Comment']['thumbnail'];?>" name="comment_attach" id="item_comment_attach_id_<?php echo $comment['Comment']['id']?>">
	<div <?php if ($comment['Comment']['thumbnail']) echo "style='display:none;'";?> id="item_comment_attach_<?php echo $comment['Comment']['id'];?>"></div>
	<div id="item_comment_preview_attach_<?php echo $comment['Comment']['id'];?>">
		<?php
			if ($comment['Comment']['thumbnail']): 
		?>
			<span style="background-image:url(<?php echo $this->Moo->getImageUrl($comment);?>)"><a onclick="removePhotoComment('item',<?php echo $comment['Comment']['id']?>)" href="javascript:void(0);"><i class="icon-delete"></i></span></a>
		<?php endif;?>
	</div>
	<div class="edit-post-action">
		<a class="button button-action" href="javascript:void(0);" onclick="cancelEditItemComment(<?php echo $comment['Comment']['id'];?>,<?php echo $isPhotoComment; ?>)"><?php echo __('Cancel');?></a>
                <a class="btn btn-action" href="javascript:void(0);" onclick="confirmEditItemComment(<?php echo $comment['Comment']['id'];?>,<?php echo $isPhotoComment; ?>)"><?php echo __('Done Editing');?></a>
	</div>
</div>