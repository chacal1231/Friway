
<?php if($this->request->is('ajax')) $this->setCurrentStyle(4);?>
<?php if($this->request->is('ajax')): ?>
<script>
    <?php else: ?>
    <?php  $this->Html->scriptStart(array('inline' => false));   ?>
    <?php endif; ?>

    $(document).ready(function(){
        $('#removeFriendButton').click(function(){
            disableButton('removeFriendButton');
            $.post("<?php echo $this->request->base?>/friends/ajax_removeRequest", jQuery("#removeFriendForm").serialize(), function(data){
                enableButton('removeFriendButton');
                $('#themeModal').modal('hide');
                mooAlert(data);
                var liUser = $('#removeFriend_<?php echo $user['User']['id']?>').parents('li:first')
                var liUserParent = liUser.parents('li[id^="activity_"]');
                liUser.remove();

                //remove this out of activity
                liUserParent.remove();
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
    <?php echo  __('Please Confirm')?>
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
</div>
<div class="modal-body">
    <div class><?php echo  __('Are you sure you want to remove this friend ?')?></div>
</div>
<div class="modal-footer">
    <form id="removeFriendForm">
        <input type="hidden" name="user_id" value="<?php echo $user['User']['id']?>">
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td>
                    <a style="float:left" href="javascript:void(0)" id="removeFriendButton" class="button button-caution"><?php echo __('Ok')?></a>
                    <a style="float:left; margin-left:3px" href="javascript:void(0)" data-dismiss="modal" class="button button-action"><?php echo __('Cancel')?></a>
                </td>
            </tr>
        </table>
    </form>
</div>