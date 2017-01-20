<?php if ( !empty( $users ) ): ?>
<ul class="list_block">	
	<?php foreach ($users as $u): ?>
	<li><?php echo $this->Moo->getItemPhoto(array('User' => $u['User']),array('prefix' => '50_square'),array('class' => 'tip'))?></li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>