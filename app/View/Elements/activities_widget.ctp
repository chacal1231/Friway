<?php
echo $this->Html->script(array('jquery.mp.min'), array('inline' => false));
echo $this->Html->css(array('jquery.mp'), null, array('inline' => false));
if ( !empty($uid) )
{
    echo $this->Html->css(array('token-input', 'fineuploader', 'jquery.mp'), null, array('inline' => false));
    echo $this->Html->script(array('jquery.tokeninput', 'jquery.fileuploader', 'jquery.mp.min'), array('inline' => false));
}
?>
<div class="<?php echo $class_feed?>">
    <?php if ( $check_post_status): ?>
	    <div id="status_box" class="statusHome">
			<?php echo $this->element( 'activity_form',array('video_categories' => $video_categories, 'type'=>$subject_type,'text'=>$text,'target_id'=>$target_id)); ?>
			<div class="clear"></div>
	    </div>
    <?php endif; ?>

    <ul class="list6 comment_wrapper" id="list-content">
            <?php echo $this->element('activities', array('bIsACtivityloadMore' => $bIsACtivityloadMore, 'more_url' => $url_more,'activity_likes'=>$activity_likes,'activities'=>$activities, 'admins' => $admins)); ?>
    </ul>
</div>
