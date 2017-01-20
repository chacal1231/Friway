<?php $this->setCurrentStyle(4);?>
<script>
$(document).ready(function(){
	
	$("#friends").tokenInput("<?php echo $this->request->base?>/friends/do_get_json",
        { preventDuplicates: true, 
          hintText: "<?php echo addslashes(__('Enter a friend\'s name'))?>",
          noResultsText: "<?php echo addslashes(__('No results'))?>",
          tokenLimit: 20,
          resultsFormatter: function(item)
          { 
            return '<li>' + item.avatar + item.name + '</li>';
          } 
        }
    );

	$('#sendButton').click(function(){
		disableButton('sendButton');    
		$.post("<?php echo $this->request->base?>/conversations/ajax_doAdd", $("#sendMessage").serialize(), function(data){
			enableButton('sendButton');
            var json = $.parseJSON(data);
                
            if ( json.result == 1 )
                window.location.reload();
            else
            {
                $(".error-message").show();
                $(".error-message").html(json.message);
            }   
		});
		return false;
	});
});
</script>
<div class="title-modal">
    <?php echo __('Send New Message')?>
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
</div>
<div class="modal-body">
<div class="create_form">
<form id="sendMessage">
<?php echo $this->Form->hidden('msg_id', array('value' => $msg_id)) ?>
<ul class="list6 list6sm2" style="position:relative;">
	<li>
            <div class="col-md-2">
                <?php echo __('Friends')?>
            </div>
            <div class="col-md-10">
                <?php echo $this->Form->text('friends'); ?>
                <div class="text-description">
                    <?php echo __('People you add will see all previous messages in this conversation')?>
                </div>
            </div>
            <div class="clear"></div>
       </li>
	<li>
            <div class="col-md-2">
                &nbsp;
            </div>
            <div class="col-md-10">
                <a href="#" class="btn btn-action" id="sendButton"><?php echo __('Add People')?></a>
            </div>
            <div class="clear"></div>
	</li>
</ul>
</form>
<div class="error-message" style="display:none;margin-top:10px;"></div>
</div>
</div>