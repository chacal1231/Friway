<div id="leftnav">
	<div class="box2 box_style1 menu">
		<h3>System Admin</h3>
		<ul class="list2">
			<li <?php if ($cmenu == 'dashboard') echo 'class="current"'; ?>>
				<a href="<?php echo $this->request->base?>/admin/home"><i class="icon-home"></i> Admin Dashboard</a>
			</li>
			<?php if ( $cuser['Role']['is_super'] ): ?>		
			<li <?php if ($cmenu == 'settings') echo 'class="current"'; ?>>
				<a href="<?php echo $this->request->base?>/admin/settings"><i class="icon-gear"></i> System Settings</a>
			</li>	
			<li <?php if ($cmenu == 'profile_fields') echo 'class="current"'; ?>>
				<a href="<?php echo $this->request->base?>/admin/profile_fields"><i class="icon-file-text"></i> Profile Fields</a>
			</li>			
			<li <?php if ($cmenu == 'bulkmail') echo 'class="current"'; ?>>
				<a href="<?php echo $this->request->base?>/admin/tools/bulkmail"><i class="icon-envelope"></i> Bulk Mail</a>
			</li>
			<li <?php if ($cmenu == 'spam_challenges') echo 'class="current"'; ?>>
                <a href="<?php echo $this->request->base?>/admin/spam_challenges"><i class="icon-warning-sign"></i> Spam Challenges</a>
            </li> 
			<?php endif; ?>
			<li <?php if ($cmenu == 'users') echo 'class="current"'; ?>>
				<a href="<?php echo $this->request->base?>/admin/users"><i class="icon-user"></i> Users Manager</a>
			</li>
		</ul>
	</div> 
	
	<?php if ( $cuser['Role']['is_super'] ): ?>        
	<div class="box2 menu">
		<h3>Site Manager</h3>
		<ul class="list2">
		    <li <?php if ($cmenu == 'roles') echo 'class="current"'; ?>>
                <a href="<?php echo $this->request->base?>/admin/roles"><i class="icon-briefcase"></i> User Roles</a>
            </li>
            <li <?php if ($cmenu == 'pages') echo 'class="current"'; ?>>
                <a href="<?php echo $this->request->base?>/admin/pages"><i class="icon-file-alt"></i> Pages Manager</a>
            </li>    
			<li <?php if ($cmenu == 'categories') echo 'class="current"'; ?>>
				<a href="<?php echo $this->request->base?>/admin/categories"><i class="icon-folder-open"></i> Categories Manager</a>
			</li>				
            <li <?php if ($cmenu == 'hooks') echo 'class="current"'; ?>>
                <a href="<?php echo $this->request->base?>/admin/hooks"><i class="icon-code-fork"></i> Hooks Manager</a>
            </li>             
            <li <?php if ($cmenu == 'plugins') echo 'class="current"'; ?>>
                <a href="<?php echo $this->request->base?>/admin/plugins"><i class="icon-puzzle-piece"></i> Plugins Manager</a>
            </li>
            <li <?php if ($cmenu == 'themes') echo 'class="current"'; ?>>
                <a href="<?php echo $this->request->base?>/admin/themes"><i class="icon-desktop"></i> Themes Manager</a>
            </li>     
            <li <?php if ($cmenu == 'languages') echo 'class="current"'; ?>>
                <a href="<?php echo $this->request->base?>/admin/languages"><i class="icon-comment"></i> Languages Manager</a>
            </li> 
		</ul>
	</div>
	<?php endif; ?>
	
	<div class="box2 menu">
		<h3>Content Manager</h3>
		<ul class="list2">
			<li <?php if ($cmenu == 'blogs') echo 'class="current"'; ?>>
				<a href="<?php echo $this->request->base?>/admin/blogs"><i class="icon-edit"></i> Blogs Manager</a>
			</li>	
			<li <?php if ($cmenu == 'albums') echo 'class="current"'; ?>>
				<a href="<?php echo $this->request->base?>/admin/albums"><i class="icon-picture"></i> Albums Manager</a>
			</li>	
			<li <?php if ($cmenu == 'videos') echo 'class="current"'; ?>>
				<a href="<?php echo $this->request->base?>/admin/videos"><i class="icon-facetime-video"></i> Videos Manager</a>
			</li>
			<li <?php if ($cmenu == 'topics') echo 'class="current"'; ?>>
				<a href="<?php echo $this->request->base?>/admin/topics"><i class="icon-comments"></i> Topics Manager</a>
			</li>	
			<li <?php if ($cmenu == 'groups') echo 'class="current"'; ?>>
				<a href="<?php echo $this->request->base?>/admin/groups"><i class="icon-group"></i> Groups Manager</a>
			</li>	
			<li <?php if ($cmenu == 'events') echo 'class="current"'; ?>>
				<a href="<?php echo $this->request->base?>/admin/events"><i class="icon-calendar"></i> Events Manager</a>
			</li>	
			<li <?php if ($cmenu == 'tags') echo 'class="current"'; ?>>
				<a href="<?php echo $this->request->base?>/admin/tags"><i class="icon-tag"></i> Tags Manager</a>
			</li>		
		</ul>
	</div>
	
	<?php
    if ( $this->elementExists('menu/admin') )
        echo $this->element('menu/admin');
    ?>
</div>