<script>
function addFriend( uid )
{
	jQuery.post("<?php echo $this->request->base?>/friends/ajax_sendRequest", {user_id: uid, message: ''}, function() {
		jQuery('#friend_'+uid).fadeOut();
	});
}
</script>
<div class="title-modal">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
    <h4 class="modal-title" id="myModalLabel"><?php echo  __('People You May Know') ?></h4>
</div>
<div class="modal-body">
<ul class="list1" id="list-content">
<?php foreach ($suggestions as $friend): ?>
	<li id="friend_<?php echo $friend['User']['id']?>"><?php echo $this->Moo->getItemPhoto(array('User' => $friend['User']), array( 'prefix' => '50_square'), array('class' => 'img_wrapper2 user_avatar_large'))?>
		<div style="margin-left:50px">
			<?php echo $this->Moo->getName($friend['User'])?><br />
			<span class="date"><?php echo __n( '%s mutual friend', '%s mutual friends', $friend[0]['count'], $friend[0]['count'] )?></span><br />
			<a href="javascript:void(0)" id="addFriend_<?php echo $friend['User']['id']?>" onclick="addFriend(<?php echo $friend['User']['id']?>)"><?php echo __('Add as friend')?></a>
		</div>
	</li>
<?php endforeach; ?>
</ul>
</div>