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
<?php $this->Html->scriptStart(array('inline' => false)); ?>
$(document).ready(function()
{
    $(".multi").multiSelect({
        selectAll: false,
        noneSelected: '',
        oneOrMoreSelected: '<?php echo __('% selected')?>',
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
<?php $this->Html->scriptEnd(); ?>

<div class="edit-profile-section">
<h2><?php echo __('Required Information')?></h2>
<ul class="">
	<li>
            <div class="col-sm-3">
            <label><?php echo __('Full Name')?></label>
            </div>
            <div class="col-sm-9">
	    <?php 
	    if ( Configure::read('core.name_change') )
	        echo $this->Form->text('name', array('value' => $cuser['name']));
        else
        {
           echo $this->Form->hidden('name', array('value' => $cuser['name']));
           echo $cuser['name'];
        }
	    ?>
            </div>
            <div class="clear"></div>
	</li>
	<li>
            <div class="col-sm-3">
                <label><?php echo __('Email Address')?></label>
            </div>
            <div class="col-sm-9">
                <?php echo $this->Form->text('email', array('value' => $cuser['email'])); ?>
            </div>
            <div class="clear"></div>
        </li>
	<?php $require_birthday = Configure::read('core.require_birthday');
        if ( !empty( $require_birthday ) ): ?> 
	<li>
            <div class="col-sm-3">
                <label><?php echo __('Birthday')?></label>
            </div>
            <div class="col-sm-9">
                <div class="col-xs-4">
                <?php echo $this->Form->month('birthday', array('value' => $bday_month))?>
                </div>
                <div class="col-xs-4">
                    <div class='p_l_2'>
                <?php echo $this->Form->day('birthday', array('value' => $bday_day))?>
                    </div>
                </div>
                <div class="col-xs-4">
                <?php echo $this->Form->year('birthday', 1930, date('Y'), array('value' => $bday_year))?>
                </div>
                <a href="javascript:void(0)" class="tip profile-tip" title="<?php echo __('Only month and date will be shown on your profile')?>">(?)</a>
            </div>
            <div class="clear"></div>
        </li>
	<?php endif; ?>    
	<li>
            <div class="col-sm-3">
            <label><?php echo __('Gender')?></label>
            </div>
            <div class="col-sm-9">
                <?php echo $this->Form->select('gender', array('Male' => __('Male'), 'Female' => __('Female')), array('value' => $cuser['gender'])); ?>
            </div>
            <div class="clear"></div>
        </li>
	<?php $enable_timezone_selection = Configure::read('core.enable_timezone_selection');
        if ( !empty( $enable_timezone_selection ) ): ?> 
	<li>
            <div class="col-sm-3">
            <label><?php echo __('Timezone')?></label>
            </div>
            <div class="col-sm-9">
            <?php echo $this->Form->select('timezone', $this->Moo->getTimeZones(), array('value' => $cuser['timezone'])); ?>
            </div>
            <div class="clear"></div>
    </li>    
	<?php endif; ?>	
</ul>
</div>
<div class="edit-profile-section">
<h2><?php echo __('Optional Information')?></h2>
<ul >
    <?php if ( in_array('user_username', $uacos) && ( Configure::read('core.username_change') || empty($cuser['username']) ) ): ?>
	<li>
            <div class="col-sm-3">
            <label><?php echo __('Username')?></label>
            </div>
            <div class="col-sm-9">
		<?php echo $this->Form->text('username', array('value' => $cuser['username'])); ?> 
		<a href="javascript:void(0)" onclick="checkUsername()" class="button button-primary" style="margin-top: 5px;" id="checkButton"><i class="icon-ok"></i> <?php echo __('Check Availability')?></a>
		<div style="display:none;margin:5px 0 0" id="message"></div>
            </div>
            <div class="clear"></div>
	</li>
	<?php endif; ?>
	<li>
            <div class="col-sm-3">
                <label><?php echo __('About')?></label>
            </div>
            <div class="col-sm-9">
                <?php echo $this->Form->textarea('about', array('value' => $cuser['about'])); ?>
            </div>
            <div class="clear"></div>
        </li>
</ul>
</div>

<div class="edit-profile-section">
<?php if ( !empty( $custom_fields ) ): ?>
<h2><?php echo __('Additional Information')?></h2>
<ul class="custom-field">
	<?php
	echo $this->element( 'custom_fields', array( 'show_require' => true, 'show_heading' => true ) );
	?>
</ul>
<?php endif; ?>

</div>
<div class="edit-profile-section">
<h2><?php echo __('User Settings')?></h2>
<ul class="">
	<li>
            <div class="col-sm-3">
            <label><?php echo __('Profile Privacy')?></label>
            </div>
            <div class="col-sm-9">
		<?php echo $this->Form->select('privacy', array( PRIVACY_EVERYONE => __('Everyone'), 
														 PRIVACY_FRIENDS => __('Friends Only'), 
														 PRIVACY_ME => __('Only Me')), 
												  array('value' => $cuser['privacy'], 'empty' => false)); ?>
            </div>
            <div class="clear"></div>
        </li>
	<li>
            <?php echo $this->Form->checkbox('notification_email', array('checked' => $cuser['notification_email'])); ?>
            <?php echo __('Receive Site Emails (including daily notification summary email)')?>
        </li>		
	<li>
            <?php echo $this->Form->checkbox('hide_online', array('checked' => $cuser['hide_online'])); ?>
            <?php echo __('Do not show my online status')?>
        </li>
    <?php
        $send_message_to_non_friend = Configure::read('core.send_message_to_non_friend');
    ?>
    <?php if($send_message_to_non_friend): ?>
    <li>
        <?php echo $this->Form->checkbox('receive_message_from_non_friend', array('checked' => $cuser['receive_message_from_non_friend'])); ?>
        <?php echo __('Receive message from non-friend')?>
    </li>
    <?php endif; ?>
    <?php  ?>
</ul>
</div>
<div class="edit-profile-section" style="border:none">
<div class='col-sm-3 hidden-xs hidden-sm'>&nbsp;</div>
<div class='col-sm-9'>
    <div style="margin-top:10px"><input type="submit" class="btn btn-action" value="<?php echo __('Save Changes')?>"></div>
</div>
<div class='clear'></div>
    </div>

