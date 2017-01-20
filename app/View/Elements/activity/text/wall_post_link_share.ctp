<?php if (isset($activity['Activity']['parent_id']) && $activity['Activity']['parent_id']): ?><!-- shared feed -->
    <?php
    list($plugin, $name) = mooPluginSplit($activity['Activity']['item_type']);
    $activityModel = MooCore::getInstance()->getModel('Activity');
    $parentFeed = $activityModel->findById($activity['Activity']['parent_id']);
    echo __("shared");
    ?><a href="<?php echo $parentFeed['User']['moo_href'] ?>"> <?php echo $parentFeed['User']['name'] ?></a>'s <a href="<?php
    echo $this->Html->url(array(
        'plugin' => false,
        'controller' => 'users',
        'action' => 'view',
        $parentFeed['User']['id'],
        'activity_id' => $activity['Activity']['parent_id']
    ));
    ?>"><?php echo __('post'); ?></a>
<?php endif; ?>

<?php echo $this->element('activity/text/wall_post', array('activity' => $activity, 'object' => $object)); ?>
