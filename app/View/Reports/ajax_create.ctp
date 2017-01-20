<?php if($this->request->is('ajax')) $this->setCurrentStyle(4) ?>
<?php // $this->Html->scriptStart(array('inline' => false));   ?>
<script>
jQuery(document).ready(function(){
	jQuery('#reportButton').click(function(){
		disableButton('reportButton');
        jQuery('#reportButton').spin('small');
		jQuery.post("<?php echo $this->request->base?>/reports/ajax_save", jQuery("#reportForm").serialize(), function(data){
			enableButton('reportButton');
            $('#reportButton').spin(false);
			var json = $.parseJSON(data);

            if ( json.result == 1 )
            {
                $(".error-message").hide();
                mooAlert(json.message);
                $('#portlet-config').modal('hide');
                $('#themeModal').modal('hide');
            }
            else
            {
                $(".error-message").show();
                $(".error-message").html(json.message);
            }
		});
	});
	return false;
});
</script>
 <?php //$this->Html->scriptEnd();  ?>
<div class="title-modal">
    <?php echo __('Report')?>
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
</div>
<div class="modal-body">
<div class="error-message" style="display:none;"></div>
<div class='create_form'>
<form id="reportForm">
<?php echo $this->Form->hidden('type', array( 'value' => $type ) ); ?>
<?php echo $this->Form->hidden('target_id', array( 'value' => $target_id ) ); ?>
<ul class="list6 list6sm2" style="position:relative">
	<li>
            <div class='col-md-2'>
                <label><?php echo __('Reason')?></label>
            </div>
            <div class='col-md-10'>
                <?php echo $this->Form->textarea('reason'); ?>
            </div>
            <div class='clear'></div>
	</li>
	<li>
            <div class='col-md-2'>
                <label>&nbsp;</label>
            </div>
            <div class='col-md-10'>
                <a href="#" class="button" id="reportButton"><?php echo __('Report')?></a>
            </div>
            <div class='clear'></div>
	</li>
</ul>
</form>
</div>
</div>