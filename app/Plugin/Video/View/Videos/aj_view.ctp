<?php
if (Configure::read('UploadVideo.uploadvideo_enabled')) {
    echo $this->Html->css(array('video-js/video-js'), null, array('inline' => false));
    echo $this->Html->script(array('video-js/video-js'), array('inline' => false));
}

$videoHelper = MooCore::getInstance()->getHelper('Video_Video');
?>
<div class="bar-content">
    <div>
        <div class="video-detail">
            <?php echo $this->element('Video./video_snippet', array('video' => $video)); ?>
            
        </div>

        <div class="content_center full_content p_m_10 video_group_detail">
                <div class="list_option">
                    <div class="dropdown">
                         <button id="dLabel" type="button" data-toggle="dropdown" aria-haspopup="true" role="button" aria-expanded="false">
                            <i class="icon-ellipsis-vert"></i>
                        </button>
                         <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                             <?php if ($uid == $video['Video']['user_id'] || ( !empty($cuser['Role']['is_admin']) ) || in_array($uid, $admins) ): ?>
                                <li>
                            <a href='javascript:void(0)' onclick="loadPage('videos', '<?php echo $this->request->base?>/videos/group_create/<?php echo $video['Video']['id']?>')"><?php echo __( 'Edit')?></a></li>
                            <?php endif; ?>
                             <?php if ($video['Group']['moo_privacy'] == PRIVACY_PUBLIC): ?>
                            <!-- not allow sharing only me item -->
                            <li>
                                <a href="javascript:void(0);" share-url="<?php echo $this->Html->url(array(
                                    'plugin' => false,
                                    'controller' => 'share',
                                    'action' => 'ajax_share',
                                    'Video_Video',
                                    'id' => $video['Video']['id'],
                                    'type' => 'video_item_detail'
                                ), true); ?>" class="shareFeedBtn"><?php echo __('Share'); ?></a>
                            </li>
                            <?php endif; ?>
                         </ul>
                    </div>
                </div>
                <div class="title_center">
                    <h2><?php echo h($video['Video']['title'])?></h2>
                </div>


                <div style="margin:10px 0">
                        <?php echo $this->Moo->formatText( $video['Video']['description'], false, true , array('no_replace_ssl' => 1))?>
                </div>
                <span class="date"><?php echo __( 'Posted by %s', $this->Moo->getName($video['User']))?> <?php echo $this->Moo->getTime($video['Video']['created'], Configure::read('core.date_format'), $utz)?></span><br />
                <div class="likes bottom_options">
                        <?php echo $this->element('likes', array('item' => $video['Video'], 'type' => 'Video_Video', 'hide_container' => true)); ?>    
                       
                        
                       
                </div>
        </div>
    </div>
</div>
<div class="bar-content full_content p_m_10">
    <div class="content_center">
        <?php echo $this->element('likes', array('likes' => $likes, 'item' => $video['Video'], 'type' => 'Video_Video')); ?>
    </div>
</div>
<div class="bar-content full_content p_m_10">
    <div class="content_center">
            <h2><?php echo __( 'Comments (%s)', $video['Video']['comment_count'])?></h2>
			<?php 
            if ( !isset( $is_member ) || $is_member  )
                    echo $this->element( 'comment_form', array( 'target_id' => $video['Video']['id'], 'type' => 'Video_Video' ) ); 
            else
                    echo __( 'This a group video. Only group members can leave comment');
            ?>
            <ul class="list6 comment_wrapper" id="comments">
            <?php echo $this->element('comments');?>
            </ul>            
    </div>
</div>
   