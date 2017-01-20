<?php if($this->request->is('ajax')) $this->setCurrentStyle(4) ?>

<?php if($this->request->is('ajax')): ?>
<script>
<?php else: ?>
<?php  $this->Html->scriptStart(array('inline' => false));   ?>
<?php endif; ?>
$(document).ready(function(){
	$('#inviteButton').click(function(){
            $('#inviteButton').spin('small');
            disableButton('inviteButton');
            $(".error-message").hide();
            MooAjax.post({
                url : '<?php echo $this->request->base?>/friends/ajax_invite',
                data: $("#inviteForm").serialize()
            }, function(data){
                enableButton('inviteButton');
                $('#inviteButton').spin(false);
                var json = $.parseJSON(data);
                if ( json.result == 1 )
                {
                    $("#to").val('');
                    $("#message").val('');
                    $(".error-message").hide();
                    mooAlert('<?php echo addslashes(__('Your invitation has been sent'))?>');
                }
                else
                {
                    $(".error-message").show();
                    $(".error-message").html(json.message);
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
<div class="content_center_home">
    <div class="post_body">
        <div class="mo_breadcrumb">
        <h1><?php echo __('Invite Your Friends')?></h1>
        </div>
        <div class="full_content p_m_10">
            <?php echo __("Enter your friends' emails below (separated by commas). Limit 10 email addresses per request")?><br /><br />
            <div class="create_form">
                <form id="inviteForm">
                <ul class="list6 list6sm2">
                    <li>
                        <div class="col-md-2">
                            <label><?php echo __('To')?></label>
                        </div>
                        <div class="col-md-10">
                            <?php echo $this->Form->textarea('to'); ?>
                        </div>
                        <div class="clear"></div>
                    </li>
                    <li>
                        <div class="col-md-2">
                            <label><?php echo __('Message')?></label>
                        </div>
                        <div class="col-md-10">
                            <?php echo $this->Form->textarea('message'); ?>
                        </div>
                        <div class="clear"></div>
                    </li>
                    <li>
                        <div class="col-md-2">
                            <label>&nbsp;</label>
                        </div>
                        <div class="col-md-10">
                            <a href="#" class="btn btn-action" id="inviteButton"><?php echo __('Send Invitation')?></a>
                        </div>
                        <div class="clear"></div>
                    </li>
                </ul>
                </form>
            </div>
            <div class="error-message" style="display:none;"></div>
        </div>
    </div>
</div>