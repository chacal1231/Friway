<?php if($this->request->is('ajax')) $this->setCurrentStyle(4) ?>

<?php if($this->request->is('ajax')): ?>
<script>
<?php else: ?>
<?php  $this->Html->scriptStart(array('inline' => false));   ?>
<?php endif; ?>
function removeFriend(id)
{
	$.fn.SimpleModal({
        btn_ok: 'OK',
        model: 'confirm',
        callback: function(){
            $.post('<?php echo $this->request->base?>/friends/ajax_remove', {id: id}, function() {
                $('#friend_'+id).fadeOut(function(){
                    $('#friend_'+id).remove();    
                });
                
                if ( $("#friend_count").html() != '0' )
                    $("#friend_count").html( parseInt($("#friend_count").html()) - 1 );
            });
        },
        title: '<?php echo addslashes(__('Please Confirm'))?>',
        contents: '<?php echo addslashes(__('Are you sure you want to remove this friend?'))?>',
        hideFooter: false, 
        closeButton: false
    }).showModal();

	return false;
}
<?php if($this->request->is('ajax')): ?>
</script>
<?php else: ?>
<?php $this->Html->scriptEnd();  ?>
<?php endif; ?>

<style>
#list-content li {
	position: relative;
}
</style>
<div class="content_center_home">
    <a href="<?php echo $this->request->base?>/home/index/tab:invite-friends" class="topButton button button-action"><?php echo __('Invite Friends')?></a>
    <h1><?php echo __('Friends')?></h1>
    <ul class="users_list" id="list-content">
            <?php echo $this->element( 'lists/users_list' ); ?>
    </ul> 
</div>