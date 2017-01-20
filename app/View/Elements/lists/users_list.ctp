

<?php if ( isset($type) && $type == 'home' ): ?>

<?php if($this->request->is('ajax')): ?>
<script>
<?php else: ?>
<?php  $this->Html->scriptStart(array('inline' => false));   ?>
<?php endif; ?>
jQuery(document).ready(function(){

	jQuery("#list-content li").hover(
		function () {
		jQuery(this).contents().find('.delete-icon').show();
	  },
	  function () {
		jQuery(this).contents().find('.delete-icon').hide();
	  }
	);
});
<?php if($this->request->is('ajax')): ?>
</script>
<?php else: ?>
<?php $this->Html->scriptEnd();  ?>
<?php endif; ?>

<?php endif; ?>

<?php
echo $this->element('lists/users_list_bit');
?>

<?php if (!empty($more_result)):?>

    <?php if ( !empty($type) && $type == 'search' ): ?>
    <script> var searchParams = <?php echo (isset($params))? json_encode($params) : 'false'; ?></script>
    <?php endif; ?>
	<?php $this->Html->viewMore($more_url); ?>
<?php endif; ?>