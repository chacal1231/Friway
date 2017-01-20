<script>
function contactSubmit()
{
	if ( jQuery('#contact_name').val() == '' || jQuery('#contact_email').val() == '' || jQuery('#subject').val() == '' || jQuery('#message').val() == '' )
	{
		mooAlert('<?php echo addslashes(__('All fields are required'))?>');
		return false;
	}
	
	var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
    if( reg.test( jQuery('#contact_email').val() ) == false ) {
        mooAlert('<?php echo addslashes(__('Invalid email address'))?>');
        return false;
    }
	
	return true;
}
</script>

<?php
if ( !$uid )
{
	$cuser['name']  = '';
	$cuser['email'] = '';
}
?>
<div class="bar-content">
<div class="page-padding content_center full_content p_m_10">
    <div class="create_form">
    <form action="<?php echo $this->request->base?>/home/contact" method="post" onsubmit="return contactSubmit()">
    <h1><?php echo __('Contact Us')?></h1>
    
    <ul class="form_content ">
    	<li>
            <div class="col-md-2">
                <label><?php echo __('Your Name')?></label>
            </div>
            <div class="col-md-10">
                <?php echo $this->Form->text('name', array('value' => $cuser['name'], 'id' => 'contact_name')); ?>
            </div>
            <div class="clear"></div>
    	</li>
    	<li>
            <div class="col-md-2">
                <label><?php echo __('Email Address')?></label>
            </div>
            <div class="col-md-10">
                <?php echo $this->Form->text('sender_email', array('value' => $cuser['email'], 'id' => 'contact_email')); ?>
            </div>
            <div class="clear"></div>
    	</li>
    	<li>
            <div class="col-md-2">
                <label><?php echo __('Subject')?></label>
            </div>
            <div class="col-md-10">
                <?php echo $this->Form->text('subject'); ?>
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
                <?php echo $this->Form->submit('Send', array('class' => 'button button-action')); ?>
            </div>
            <div class="clear"></div>
    	</li>
    </ul>	
    </form>
    </div>
</div>
</div>