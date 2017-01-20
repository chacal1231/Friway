<?php if($this->request->is('ajax')) $this->setCurrentStyle(4) ?>

<style>
.delete-icon {
	top: 16px;
	right: 15px;
}
</style>
<?php if($this->request->is('ajax')): ?>
<script>
<?php else: ?>
<?php  $this->Html->scriptStart(array('inline' => false));   ?>
<?php endif; ?>
$(document).ready(function(){

	$("#notifications_list li").hover(
		function () {
		$(this).contents().find('.delete-icon').show();
	  },
	  function () {
		$(this).contents().find('.delete-icon').hide();
	  }
	);
});

function removeNotification(id)
{
    mooAjax(baseUrl + '/notifications/ajax_remove/'+id, 'get', '', function(data) {
        $("#ajax_noti_"+id).slideUp();

        if ( $('#ajax_noti_' + id + ' a:first').hasClass('unread') && jQuery("#notification_count").html() != '0' )
        {
            var noti_count = parseInt($(".notification_count").html()) - 1;
            if(noti_count == 0)
            {
                $(".notification_count").remove();
            }
            else
            {
                $(".notification_count").html( noti_count );
            }
            $("#notification_count").html( noti_count );

            Tinycon.setBubble( noti_count );
        }
    });
}

function clearNotifications()
{
	$.get('<?php echo $this->request->base?>/notifications/ajax_clear');
	$(".notification_list").slideUp();
	$("#new_notifications").fadeOut();
	$("#notification_count").html('0');
        $('.notification_count').html('0');
	Tinycon.setBubble( 0 );
	return false;
}
<?php if($this->request->is('ajax')): ?>
</script>
<?php else: ?>
<?php $this->Html->scriptEnd();  ?>
<?php endif; ?>

<div class="content_center_home">
<?php if ( $type == 'home' ): ?>
	<?php if ( !empty($notifications) ): ?>
	<a href="#" onclick="clearNotifications()" class="button topButton"><?php echo __('Clear All Notifications')?></a>
	<?php endif; ?>
	<h1 class="m_b_10"><?php echo __('Notifications')?></h1>
<?php endif; ?>

<?php 
if (count($notifications) == 0):
	echo __('No new notifications');
else:
?>
<ul class="list2 notification_list" id="notifications_list" >
<?php 
	foreach ($notifications as $noti):
?>
	<li id="ajax_noti_<?php echo $noti['Notification']['id']?>">
		<a href="<?php echo $this->request->base?>/notifications/ajax_view/<?php echo $noti['Notification']['id']?>" <?php if (!$noti['Notification']['read']) echo 'class="unread"';?>>
                    <?php echo $this->Moo->getImage(array('User' => $noti['Sender']), array('prefix' => '50_square', 'width' => 45, 'class' => 'img_wrapper2', 'alt' => h($noti['Sender']['name'])))?>
			<b><?php echo h($noti['Sender']['name'])?></b>
			<?php echo $this->element('misc/notification_texts', array( 'noti' => $noti ));	?>
			<br />
			<span class="date"><?php echo $this->Moo->getTime( $noti['Notification']['created'], Configure::read('core.date_format'), $utz )?></span>
		</a>

		<a href="javascript:void(0)" onclick="return removeNotification(<?php echo $noti['Notification']['id']?>)" style="padding:0"><i class="icon-delete delete-icon"></i></a>
	</li>
<?php
	endforeach;
?>
</ul>
<?php
endif;
?>
</div>
