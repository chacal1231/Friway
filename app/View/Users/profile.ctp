<?php $this->setNotEmpty('west');?>
<?php $this->start('west'); ?>
<div class="bar-content">
    <div class="profile-info-menu">
        <?php echo $this->element('profilenav', array("cmenu" => "profile"));?>
    </div>
</div>
<?php $this->end(); ?>
<div class="bar-content ">
    <div class="content_center profile-info-edit">
        <form action="<?php echo $this->request->base?>/users/profile" method="post">
        <div id="center" class="post_body">
            <div class="mo_breadcrumb">
                 <h1><?php echo __('Profile Information')?></h1>
                 <a href="<?php echo $this->request->base?>/users/view/<?php echo $uid?>" class="topButton button button-action button-mobi-top"><?php echo __('View Profile')?></a>
            </div>
            <div class="full_content">
                <div class="content_center">
                <?php echo $this->element('ajax/profile_edit');?>
                <div class="edit-profile-section" style="border:none">
                    <?php if ( !$cuser['Role']['is_super'] ): ?>
                        <ul class="list6 list6sm" style="margin:10px 0">
                            <li><a href="javascript:void(0)" onclick="mooConfirm('<?php echo addslashes(__('Are you sure you want to deactivate your account? Your profile will not be accessible to anyone and you will not be able to login again!'))?>', '<?php echo $this->request->base?>/users/deactivate')"><?php echo __('Deactivate my account')?></a></li>
                            <li><a href="javascript:void(0)" onclick="mooConfirm('<?php echo addslashes(__('Are you sure you want to permanently delete your account? All your contents (including groups, topics, events...) will also be permanently deleted!'))?>', '<?php echo $this->request->base?>/users/request_deletion')"><?php echo __('Request to permanently delete my account')?></a></li>
                        </ul>
                    <?php endif; ?>
                </div>

                </div>
            </div>
        </div>
        </form>
    </div>
</div>