<span class="arr-notify"></span>
<ul class="initSlimScroll">
    <?php if (empty($notifications)): ?>
        <li class="notify_no_content"><?php echo __('No new notifications')?></li>
    <?php else: ?>
        <?php foreach($notifications as $noti): ?>
            <li id="noti_<?php echo $noti['Notification']['id']?>" <?php echo $noti['Notification']['read'] ? '' : 'class="unread"'?>>
                <a href="<?php echo $this->request->base ?>/notifications/ajax_view/<?php echo $noti['Notification']['id']?>">
                    
                <?php echo $this->Moo->getImage(array('User' => $noti['Sender']), array('alt'=>h($noti['Sender']['name']),'class'=> "img_wrapper2", 'width'=>"45", 'prefix' => '50_square'))?>
                <div class="notification_content">
                    <div>
                    <b><?php echo h($noti['Sender']['name'])?></b>
                    <span><?php echo $this->element('misc/notification_texts', array('noti' => $noti))?></span>
                    <br />
                    </div>
                <span class="date"><?php echo $this->Moo->getTime($noti['Notification']['created'], Configure::read('core.date_format'), $utz)?></span>
                </div></a>
                <a href="javascript:void(0)" onclick="return removeNotification(<?php echo $noti['Notification']['id']?>)" class="p_0 delete-icon">
                    <i class="icon-delete "></i>
                </a>
            </li>
        <?php endforeach; ?>
    <?php endif; ?>
</ul>
<li class="more-notify">
    <a id="notifications" rel="home-content" href="<?php echo $this->request->base ?>/home/index/tab:notifications">
        <?php echo __('View All Notification')?>
    </a>
</li>