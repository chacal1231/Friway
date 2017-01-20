<div id="activity_edit_<?php echo $activity['Activity']['id']?>">
	<?php echo $this->Form->textarea("message_edit_".$activity['Activity']['id']."",array('name' => "message", 'value' => $activity['Activity']['content'], 'style' => 'width:100%;margin-top:0px;'),true ); ?>
    <div class="edit-post-action">
            <a class="button button-action admin-or-owner-cancel-edit-activity" data-activity-id="<?php echo $activity['Activity']['id'];?>" href="javascript:void(0);" ><?php echo __('Cancel');?></a>
            <a class="btn btn-action admin-or-owner-confirm-edit-activity" data-activity-id="<?php echo $activity['Activity']['id'];?>" href="javascript:void(0);" ><?php echo __('Done Editing');?></a>
	</div>
</div>