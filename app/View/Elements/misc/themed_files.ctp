<ul class="list2">
<?php foreach ($files as $file): ?>
	<?php if ( strpos($file, '.') === false ): ?>
	<li><a href="javascript:void(0)" onclick="openFolder('<?php if ( !empty($path) ) echo $path . '/'; ?><?php echo $file?>', '<?php echo $type?>', this)"><i class="icon-folder-open icon-small"></i> <?php echo $file?></a></li>
	<?php else: ?>
	<li><a href="javascript:void(0)" onclick="openFile('<?php if ( !empty($path) ) echo $path . '/'; ?><?php echo $file?>', '<?php echo $type?>', this)"><i class="icon-file-alt icon-small"></i> <?php echo $file?></a></li>
	<?php endif; ?>
<?php endforeach; ?>
</ul>