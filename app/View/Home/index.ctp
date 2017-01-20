<?php
echo $this->Html->css(array('token-input'), null, array('inline' => false));
echo $this->Html->script(array('jquery.tokeninput'), array('inline' => false));
?>
<?php $this->Html->scriptStart(array('inline' => false)); ?>
jQuery(document).ready(function(){
	<?php if ( !empty( $tab ) ): ?>
	if ($("#<?php echo $tab?>").length > 0)
	{
		$('#<?php echo $tab?>').spin('tiny');
		$('#<?php echo $tab?>').children('.badge_counter').hide();
		$('#browse .current').removeClass('current');
		$('#<?php echo $tab?>').parent().addClass('current');
                $('#home-content').html('Loading...');
		$('#home-content').load( $('#<?php echo $tab?>').attr('data-url'), function(response){
                    //$('#home-content').html($.parseJSON(response).data);
                    $('#<?php echo $tab?>').spin(false);
			$('#<?php echo $tab?>').children('.badge_counter').fadeIn();

			// reattach events
			$('textarea').autogrow();
			$(".tip").tipsy({ html: true, gravity: 's' });
			registerOverlay();
		});
	}
	else
		$('#home-content').load( '<?php echo $this->request->base?>/activities/ajax_browse/home', {noCache: 1} );
	<?php endif; ?>
});
<?php $this->Html->scriptEnd(); ?>

<?php
if ( empty($uid) && Configure::read('core.force_login') ):
    $guest_message = Configure::read('core.guest_message');
    if ( !empty($guest_message) ): ?>
    <div class="box1 guest_msg"><?php echo nl2br(Configure::read('core.guest_message'))?></div>
<?php
    endif;
else:
?>
<?php $this->setNotEmpty('west');?>
<?php $this->start('west'); ?>


<?php $this->end(); ?>


    
<?php endif; ?>