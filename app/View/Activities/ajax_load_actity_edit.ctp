<div id="activity_edit_<?php echo $activity['Activity']['id']?>">
	<textarea name="message_edit_<?php echo $activity['Activity']['id']?>" id="message" ><?php echo $activity['Activity']['content']?></textarea>
	<div>
		<a class="admin-or-owner-confirm-edit-activity" data-activity-id="<?php echo $activity['Activity']['id'];?>" href="javascript:void(0);" ><?php echo __('Cancel');?></a> <a href="javascript:void(0);" onclick="confirmEditActivity(<?php echo $activity['Activity']['id'];?>)"><?php echo __('Done Editing');?></a>
	</div>
</div>