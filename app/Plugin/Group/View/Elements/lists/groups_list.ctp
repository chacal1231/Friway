<?php
if(Configure::read('Group.group_enabled') == 1):
$groupHelper = MooCore::getInstance()->getHelper('Group_Group');
?>
<ul class="group-content-list">
<?php
if (!empty($groups) && count($groups) > 0):
    $i = 1;
    foreach ($groups as $group):
        
        ?>
        <li class="full_content p_m_10" <?php if ($i == count($groups)) echo 'style="border-bottom:0"'; ?>>
            <a href="<?php echo  $this->request->base ?>/groups/view/<?php echo  $group['Group']['id'] ?>/<?php echo  seoUrl($group['Group']['name']) ?>">
                <img src="<?php echo $groupHelper->getImage($group, array('prefix' => '150_square'))?>" class="group-thumb" />
            </a>
            <div class="group-info">
                <a class="title" href="<?php echo  $this->request->base ?>/groups/view/<?php echo  $group['Group']['id'] ?>/<?php echo  seoUrl($group['Group']['name']) ?>"><b><?php echo  h($group['Group']['name']) ?></b></a>

                <div class="extra_info">
                    <?php
                    switch ($group['Group']['type']) {
                        case PRIVACY_PUBLIC:
                            echo __( 'Public');
                            break;

                        case PRIVACY_RESTRICTED:
                            echo __( 'Restricted');
                            break;

                        case PRIVACY_PRIVATE:
                            echo __( 'Private');
                            break;
                    }
                    ?> . 
                    <?php echo  __n('%s member', '%s members', $group['Group']['group_user_count'], $group['Group']['group_user_count']) ?>
                </div>
                <div class="list-item-description"><?php echo  $this->Text->convert_clickable_links_for_hashtags(h($this->Text->truncate($group['Group']['description'], 200, array('exact' => false))), Configure::read('Group.group_hashtag_enabled')) ?></div>
                <?php $this->Html->rating($group['Group']['id'],'groups', 'Group'); ?>

            </div>

            <?php if (!empty($uid) && ( ( !empty($group['Group']['my_status']) && $group['Group']['my_status']['GroupUser']['status'] == GROUP_USER_ADMIN ) || !empty($cuser['Role']['is_admin'] )) ): ?>
                <div class="list_option">
                    <?php if ( ( !empty($group['Group']['my_status']) && $group['Group']['my_status']['GroupUser']['status'] == GROUP_USER_ADMIN ) || $group['Group']['type'] != PRIVACY_PRIVATE  || !empty($cuser['Role']['is_admin'] )): ?>
                    <div class="dropdown">
                        <button id="dropdown-edit" data-target="#" data-toggle="dropdown">
                            <i class="icon-edit"></i>
                        </button>
                        <?php //debug( ( !empty($group['Group']['my_status']) && $group['Group']['my_status']['GroupUser']['status'] == GROUP_USER_ADMIN ) || !empty($cuser['Role']['is_admin'] )); ?>
                        <ul role="menu" class="dropdown-menu" aria-labelledby="dropdown-edit">
                            <?php if ( ( !empty($group['Group']['my_status']) && $group['Group']['my_status']['GroupUser']['status'] == GROUP_USER_ADMIN ) || !empty($cuser['Role']['is_admin'] ) ): ?>
                                <li><a href="<?php echo $this->request->base?>/groups/create/<?php echo $group['Group']['id']?>"><?php echo __( 'Edit Group')?></a></li>
                                <li><a href="javascript:void(0)" onclick="mooConfirm( '<?php echo addslashes(__( 'Are you sure you want to remove this group?<br />All group contents will also be deleted!'))?>', '<?php echo  $this->request->base ?>/groups/do_delete/<?php echo  $group['Group']['id'] ?>' )"><?php echo __( 'Delete Group')?></a></li>
                            <?php endif; ?>
                                <li class="seperate"></li>
                            <?php if ( !empty($my_status) && ( $my_status['GroupUser']['status'] == GROUP_USER_MEMBER || $my_status['GroupUser']['status'] == GROUP_USER_ADMIN ) && ( $uid != $group['Group']['user_id'] ) ): ?>
                                <li><a href="javascript:void(0)" onclick="mooConfirm('<?php echo addslashes(__('Are you sure you want to leave this group?'))?>', '<?php echo $this->request->base?>/groups/do_leave/<?php echo $group['Group']['id']?>')"><?php echo __('Leave Group')?></a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </li>
        <?php
        $i++;
    endforeach;
else:
    echo '<div class="clear text-center">' . __( 'No more results found') . '</div>';
endif;
?>

<?php
if (!empty($more_result)):
    ?>

    <?php $this->Html->viewMore($more_url) ?>
    <?php
endif;
endif;
?>
</ul>