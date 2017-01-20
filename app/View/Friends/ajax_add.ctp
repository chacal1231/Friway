

<?php if($this->request->is('ajax')) $this->setCurrentStyle(4);?>
<?php if($this->request->is('ajax')): ?>
<script>
<?php else: ?>
<?php  $this->Html->scriptStart(array('inline' => false));   ?>
<?php endif; ?>

$(document).ready(function(){
	$('#addFriendButton').click(function(){
        $('#addFriendButton').spin('small');
            disableButton('addFriendButton');
            $.post("<?php echo $this->request->base?>/friends/ajax_sendRequest", jQuery("#addFriendForm").serialize(), function(data){
                if ($('.suggestion_block').length){
                    $('.suggestion_block #addFriend_<?php echo $user['User']['id']?>').parents('li:first').remove();
                    if ($('.suggestion_block li').length == 0){
                        $('.suggestion_block').remove();
                    }
                }
                enableButton('addFriendButton');
                $('#themeModal').modal('hide');
                mooAlert(data);
                $('#addFriend_<?php echo $user['User']['id']?>').parents('div.user-idx-item').append('<a href="<?php echo $this->request->base?>/friends/ajax_cancel/<?php echo $user['User']['id']?>" id="cancelFriend_<?php echo $user["User"]["id"]?>" class="add_people" title="<?php addslashes(__("Cancel a friend request"));?>"><i class="icon-pending"></i><?php echo addslashes(__("Cancel Request"))?></a>');
                $('#addFriend_<?php echo $user['User']['id']?>').remove();
                if ($('#blogAddFriend').length){
                    $('#blogAddFriend').parents('.blog_view_leftnav').append('<li><a href="<?php echo $this->request->base?>/friends/ajax_cancel/<?php echo $user['User']['id']?>" id="blogCancelFriend" class="" title="<?php addslashes(__("Cancel a friend request"));?>"><i class="icon-pending icon-small"></i><?php echo addslashes(__("Cancel Request"))?></a></li>');
                    $('#blogAddFriend').parents('li:first').remove();
                }
                if ($('#userAddFriend').length){
                    $('#userAddFriend').parents('.profile-action').append('<a id="userCancelFriend" href="<?php echo $this->request->base?>/friends/ajax_cancel/<?php echo $user['User']['id']?>" class="topButton button button-action" title="<?php addslashes(__("Cancel a friend request"));?>"><i class="visible-xs visible-sm icon-pending"></i><i class="hidden-xs hidden-sm"><?php echo addslashes(__("Cancel Request"))?></i></a>');
                    $('#userAddFriend').remove();
                }
            });
            return false;
	});
});
<?php if($this->request->is('ajax')): ?>
</script>
<?php else: ?>
    <?php $this->Html->scriptEnd();  ?>
<?php endif; ?>
<div class="title-modal">
    <?php printf( __('Send %s a friend request'), h($user['User']['name']) )?>
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
</div>
<div class="modal-body">
    <?php if ($warning_msg): ?>
        <div><?php echo $warning_msg?></div>
    <?php else: ?>
    <div style="margin:0 0 5px 0"><?php printf( __('You can send <b>%s</b> an optional message below'), h($user['User']['name']) ); ?></div>
    <form id="addFriendForm">
    <input type="hidden" name="user_id" value="<?php echo $user['User']['id']?>">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td width="65" valign="top"><?php echo $this->Moo->getImage(array('User' => $user['User']), array("class" => "img_wrapper", 'prefix' => '50_square'))?></td>
            <td><textarea name="message"></textarea></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td><br /><a href="#" id="addFriendButton" class="button button-action"><?php echo __('Send Request')?></a></td>
        </tr>
    </table>
    </form>
    <?php endif; ?>
</div>