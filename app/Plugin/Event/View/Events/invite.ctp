<?php //if($this->request->is('ajax')) $this->setCurrentStyle(4) ?>

<?php if($this->request->is('ajax')): ?>
<script>
<?php else: ?>
<?php  $this->Html->scriptStart(array('inline' => false));   ?>
<?php endif; ?>

function inviteScript(){
    $("#friends").tokenInput("<?php echo $this->request->base?>/friends/do_get_json", 
        { preventDuplicates: true, 
          hintText: "<?php echo __( 'Enter a friend\'s name')?>",
          noResultsText: "<?php echo __( 'No results')?>",
          tokenLimit: 20,
          resultsFormatter: function(item)
          { 
            return '<li>' + item.avatar + item.name + '</li>';
          } 
        }
    );

	$('#sendButton').click(function(){
        $('#sendButton').spin('small');
		disableButton('sendButton');
        $(".error-message").hide();
        MooAjax.post({
            url : '<?php echo $this->request->base?>/events/sendInvite',
            data: $("#sendInvite").serialize()
        }, function(data){
            enableButton('sendButton');
            $('#sendButton').spin(false);
            var json = $.parseJSON(data);
            if ( json.result == 1 )
            {
                $('#simple-modal-body').html(json.msg);
            }
            else
            {
                $(".error-message").show();
                $(".error-message").html(json.message);
            }
        });
        return false;

	});
}

$(document).ready(function(){
	inviteScript();
});
<?php if($this->request->is('ajax')): ?>
</script>
<?php else: ?>
<?php $this->Html->scriptEnd();  ?>
<?php endif; ?>
<div class="title-modal">
    <?php echo __( 'Invite Friends')?>
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
</div>
<div class="modal-body" id="simple-modal-body">
<div class="message" style="display:none;"></div>
<div class='create_form'>
<form id="sendInvite">
<?php echo $this->Form->hidden('event_id', array('value' => $event_id)); ?>
<ul class="list6" style="position:relative">
	<li>
            <div class='col-md-2'>
                <?php echo __( 'Friend')?>
            </div>
            <div class='col-md-10'>
                <?php echo $this->Form->text('friends'); ?>
            </div>
            <div class='clear'></div>
        </li>	
	<li>
            <div class='col-md-2'>
                 <?php echo __( 'Emails')?>
            </div>
            <div class='col-md-10'>
                <?php echo $this->Form->textarea('emails'); ?>
                 <div class='text-description'>
                    <?php echo __( 'Not on your friends list? Enter their emails below (separated by commas)<br />Limit 10 email addresses per request')?>
                </div>
            </div>
            
            <div class='clear'></div>
	</li>
	<li>
            <div class='col-md-2'>&nbsp;</div>
            <div class='col-md-10'>
                <a href="#" class="button button-action" class="sendButton" id="sendButton"><?php echo __( 'Send Invitations')?></a>
            </div>
        <div class='clear'></div> 
        </li>
</ul>
</form>
</div>
    <div class="error-message" style="display:none;"></div>
</div>