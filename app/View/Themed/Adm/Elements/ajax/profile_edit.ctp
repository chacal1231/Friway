<?php

$bday_month = '';
$bday_day = '';
$bday_year = '';
if (!empty($cuser['birthday']))
{
	$birthday = explode('-', $cuser['birthday']);
	$bday_year = $birthday[0];
	$bday_month = $birthday[1];
	$bday_day = $birthday[2];
}
?>

<script>
$(document).ready(function()
{
    $(".multi").multiSelect({
        selectAll: false,
        noneSelected: '',
        oneOrMoreSelected: '<?php echo __('% selected')?>'
    });
});    
    
function checkUsername()
{
	disableButton('checkButton');
	$.post(baseUrl + "/users/ajax_username", {username: $('#username').val()}, function(data){
		enableButton('checkButton');
		var res = $.parseJSON(data);
		
		$('#message').html( res.message );
		
		if ( res.result == 1 )
			$('#message').css('color', 'green');
		else
			$('#message').css('color', 'red');
		
		$('#message').show();			
	});
}
</script>
<hr>
<h4><?php echo __('Required Information')?></h4>
<div class="form-group">
    <label class="col-md-3 control-label"><?php echo __('Full Name')?></label>
    <div class="col-md-9">
        <?php
        if ( Configure::read('core.name_change') )
            echo $this->Form->text('name', array('class'=>'form-control','value' => $cuser['name']));
        else
        {
            echo $this->Form->hidden('name', array('class'=>'form-control','value' => $cuser['name']));
            echo $cuser['name'];
        }
        ?>
    </div>
</div>
<div class="form-group">
    <label class="col-md-3 control-label"><?php echo __('Email Address')?></label>
    <div class="col-md-9">
        <?php echo $this->Form->text('email', array('class'=>'form-control','value' => $cuser['email'])); ?>
    </div>
</div>
<?php $require_birthday = Configure::read('core.require_birthday');
    if ( !empty($require_birthday) ): ?>
<div class="form-group">
    <label class="col-md-3 control-label"><?php echo __('Birthday')?></label>
    <div class="col-md-9">
        <div class="row">
            <div class="col-md-3">
                <?php echo $this->Form->month('birthday', array('class' => 'form-control','value' => $bday_month))?>
            </div>
            <div class="col-md-3">
                <?php echo $this->Form->day('birthday', array('class' => 'form-control','value' => $bday_day))?>
            </div>
            <div class="col-md-3">
                <?php echo $this->Form->year('birthday', 1930, date('Y'), array('class' => 'form-control','value' => $bday_year))?>
            </div>
        </div>
        <div class="clear"></div>
        <span class="help-block"><?php echo __('Only month and date will be shown on your profile');?></span>
    </div>
</div>
<?php endif; ?>
<div class="form-group">
    <label class="col-md-3 control-label"><?php echo __('Gender')?></label>
    <div class="col-md-9">
        <?php echo $this->Form->select('gender', array('Male' => __('Male'), 'Female' => __('Female')), array('value' => $cuser['gender'],'class'=>'form-control')); ?>
    </div>
</div>

<hr>
<h4><?php echo __('Optional Information')?></h4>
<?php if ( in_array('user_username', $uacos) && ( Configure::read('core.username_change') || empty($cuser['username']) ) ): ?>
    <div class="form-group">
        <label class="col-md-3 control-label"><?php echo __('Username')?></label>
        <div class="col-md-9">
            <?php echo $this->Form->text('username', array('class'=>'form-control','value' => $cuser['username'])); ?>
            <span class="help-block">
                <a href="javascript:void(0)" onclick="checkUsername()" class="button button-primary" style="margin-top: -4px;" id="checkButton"><i class="icon-ok"></i> <?php echo __('Check Availability')?></a>
                <div style="display:none;margin:5px 0 0 145px" id="message"></div>
            </span>
        </div>
    </div>
<?php endif; ?>
<div class="form-group">
    <label class="col-md-3 control-label"><?php echo __('About')?></label>
    <div class="col-md-9">
        <?php echo $this->Form->textarea('about', array('class' => 'form-control','value' => $cuser['about'])); ?>
    </div>
</div>
<?php if ( !empty( $custom_fields ) ): ?>
    <hr>
    <h4><?php echo __('Additional Information')?></h4>

        <?php
        echo $this->element( 'custom_fields', array( 'show_require' => true, 'show_heading' => true ) );
        ?>

<?php endif; ?>

<hr>
<h4><?php echo __('User Settings')?></h4>
<div class="system-setting">
<div class="form-group">
    <label class="col-md-3 control-label"><?php echo __('Profile Privacy')?></label>
    <div class="col-md-9">
        <?php echo $this->Form->select('privacy', array( PRIVACY_EVERYONE => __('Everyone'),
                PRIVACY_FRIENDS => __('Friends Only'),
                PRIVACY_ME => __('Only Me')),
            array('value' => $cuser['privacy'], 'empty' => false,'class'=>'form-control')); ?>
    </div>
</div>
<div class="form-group">
    <label class="col-md-3 control-label"><?php echo __('Receive Site Emails (including daily notification summary email)')?></label>
    <div class="col-md-9">
        <?php echo $this->Form->checkbox('notification_email', array('checked' => $cuser['notification_email'])); ?>
    </div>
</div>
<div class="form-group">
    <label class="col-md-3 control-label"><?php echo __('Do not show my online status')?></label>
    <div class="col-md-9">
        <?php echo $this->Form->checkbox('hide_online', array('checked' => $cuser['hide_online'])); ?>
    </div>
</div>
</div>

<div class="form-actions ">
    <div class="row">
        <div class="col-md-offset-3 col-md-9">
            <input type="submit" class="btn btn-action" value="<?php echo __('Save Changes')?>">
            <button data-id="<?php echo $cuser['id']; ?>" id="login_as_user" class="btn btn-default"><?php echo __('Login as user'); ?></button>
        </div>
    </div>
</div>

