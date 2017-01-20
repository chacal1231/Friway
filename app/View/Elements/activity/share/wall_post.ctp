<div class="user-info">
   <!--  <div class="activity_feed_image">
        <?php echo $this->Moo->getItemPhoto(array('User' => $activity['User']), array('prefix' => '50_square'), array('class' => 'img_wrapper2 user_avatar_large ')) ?>
    </div> -->
    <div class="activity_feed_content">
       
            <?php echo $this->Moo->getName($activity['User']) ?>
            
            <?php if ($activity['Activity']['target_id']): ?>
                <?php
                $subject = MooCore::getInstance()->getItemByType($activity['Activity']['type'], $activity['Activity']['target_id']);

                list($plugin, $name) = mooPluginSplit($activity['Activity']['type']);
                $show_subject = MooCore::getInstance()->checkShowSubjectActivity($subject);

                if ($show_subject):
                    ?>
                    &rsaquo; <a href="<?php echo $subject[$name]['moo_href'] ?>"><?php echo h($subject[$name]['moo_title']) ?></a>
                <?php endif; ?>
            <?php endif; ?>

            <div class="feed_time">
                <span class="date"><?php echo $this->Moo->getTime($activity['Activity']['created'], Configure::read('core.date_format'), $utz) ?></span>
            </div>
        
    </div>
    <div class="clear"></div>
</div>
<div class="comment_message">
    <?php echo $this->viewMore(h($activity['Activity']['content']), null, null, null, true, array('no_replace_ssl' => 1)); ?>
    <?php
    if (!empty($activity['UserTagging']['users_taggings']))
        $this->MooPeople->with($activity['UserTagging']['id'], $activity['UserTagging']['users_taggings']);
    ?>
</div>
<div class="">
    <?php if ($activity['Activity']['item_type']): ?>
        <?php
        list($plugin, $name) = mooPluginSplit($activity['Activity']['item_type']);
        ?>
        <?php echo $this->element('activity/content/' . strtolower($name) . '_post_feed', array('activity' => $activity, 'object' => $object, 'had_comment_message' => 1 ), array('plugin' => $plugin)); ?>
    <?php endif; ?>
</div>
