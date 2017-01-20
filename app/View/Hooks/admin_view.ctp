<?php
echo $this->element('adminnav', array("cmenu" => "hooks"));
?>

<div id="center">		
    <?php if ( !empty( $hook['Hook']['id'] ) ): ?>
	<h1><?php echo $hook['Hook']['name']?></h1>
	
	<h2>Hook Info</h2>
	<ul class="list6 info">
		<li><label>Name</label><?php echo $hook['Hook']['name']?></li>
		<li><label>Key</label><?php echo $hook['Hook']['key']?></li>
		<li><label>Version</label><?php echo $hook['Hook']['version']?> <?php if ( $info->version > $hook['Hook']['version'] ):?>(<a href="<?php echo $this->request->base?>/admin/hooks/do_upgrade/<?php echo $hook['Hook']['id']?>">Upgrade</a>)<?php endif; ?></li>
		<li><label>Author</label><?php echo $info->author?></li>
		<li><label>Website</label><?php echo $info->website?></li>
		<li><label>Description</label><?php echo $info->description?></li>
		<li><label>Controller</label><?php echo $hook['Hook']['controller']?></li>
		<li><label>Action</label><?php echo $hook['Hook']['action']?></li>
		<li><label>Position</label><?php echo $hook['Hook']['position']?></li>
		<li><label>Enabled</label><?php echo ($hook['Hook']['enabled'])?'Yes':'No';?></li>
		<li><label>Installed</label>Yes</li>       	
	</ul>
	<?php else: ?>
	<h1><?php echo $info->name?></h1>
	
    <h2>Hook Info</h2>
    <ul class="list6 info">
        <li><label>Name</label><?php echo $info->name?></li>
        <li><label>Key</label><?php echo $info->key?></li>
        <li><label>Version</label><?php echo $info->version?></li>
        <li><label>Author</label><?php echo $info->author?></li>
        <li><label>Website</label><?php echo $info->website?></li>
        <li><label>Description</label><?php echo $info->description?></li>
        <li><label>Controller</label><?php echo $info->controller?></li>
        <li><label>Action</label><?php echo $info->action?></li>
        <li><label>Position</label><?php echo $info->position?></li>
        <li><label>Installed</label>No</li>          
    </ul>
	<?php endif; ?>
</div>
