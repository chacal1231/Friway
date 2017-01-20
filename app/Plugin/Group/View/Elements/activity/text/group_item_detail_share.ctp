<?php if (isset($activity['Activity']['parent_id']) && $activity['Activity']['parent_id']): ?><!-- shared feed -->
    <?php
    $groupModel = MooCore::getInstance()->getModel('Group_Group');
    $group = $groupModel->findById($activity['Activity']['parent_id']);
    echo __("shared");
    ?><a href="<?php echo $group['User']['moo_href'] ?>"> <?php echo $group['User']['name'] ?></a>'s <a href="<?php echo $group['Group']['moo_href']; ?>"><?php echo __('group'); ?></a>
<?php endif; ?>

    <?php if ($activity['Activity']['target_id']): ?>
    <?php
    $subject = MooCore::getInstance()->getItemByType($activity['Activity']['type'], $activity['Activity']['target_id']);

    list($plugin, $name) = mooPluginSplit($activity['Activity']['type']);
    $show_subject = MooCore::getInstance()->checkShowSubjectActivity($subject);

    if ($show_subject):
        ?>
        &rsaquo; <a href="<?php echo $subject[$name]['moo_href'] ?>"><?php echo h($subject[$name]['moo_title']) ?></a>
    <?php else: ?>
        <?php if (!empty($activity['Activity']['parent_id'])): ?>
            <?php //echo __('to your timeline'); ?>
        <?php endif; ?>
    <?php endif; ?>

<?php endif; ?>