<div class="box2 menu">
	<h3><?php echo __('User Menu')?></h3>
	<ul class="list2">					
		<li <?php if ($cmenu == 'profile') echo 'class="current"'; ?>>
			<a href="<?php echo $this->request->base?>/users/profile"><i class="icon-file-text-alt"></i> <?php echo __('Profile Information')?></a>
		</li>
		<li <?php if ($cmenu == 'password') echo 'class="current"'; ?>>
                <a href="<?php echo $this->request->base?>/users/password"><i class="icon-lock"></i> <?php echo __('Change Password')?></a>
        </li>
        
		<?php
			$helperSubscription = MooCore::getInstance()->getHelper('Subscription_Subscription');
			if ($helperSubscription->checkEnableSubscription() && $cuser['Role']['is_super'] != 1):
		?>
       
		<li <?php if ($cmenu == 'upgrade_membership') echo 'class="current"'; ?>>
			<?php echo $this->Html->link('<i class="icon-up-circled"></i>' . __('Subscription Management'), array('plugin' => 'subscription', 'controller' => 'subscribes', 'action' => 'upgrade'), array('escape' => false)) ?>
		</li>
		<?php endif;?>
		<?php
		if ( $this->elementExists('menu/profile') )
			echo $this->element('menu/profile');
		?>
	</ul>
</div>
