<?php
if (Configure::read('UploadVideo.uploadvideo_enabled')) {
    echo $this->Html->css(array('video-js/video-js'), null, array('inline' => false));
    echo $this->Html->script(array('video-js/video-js'), array('inline' => false));
}
$videoHelper = MooCore::getInstance()->getHelper('Video_Video');
?>
<?php $this->Html->scriptStart(array('inline' => false)); ?>
$(document).ready(function(){
    <?php if (!empty($video['Video']['pc_upload'])): ?>
        videojs.options.flash.swf = "<?php echo FULL_BASE_URL . $this->request->webroot ?>js/video-js/video-js.swf"
    <?php endif; ?>
    $(".sharethis").hideshare({media: '<?php echo FULL_BASE_URL . $this->request->webroot . 'uploads/videos/thumb/'. $video['Video']['id']. '/'. $video['Video']['thumb']?>', linkedin: false});
});
<?php $this->Html->scriptEnd(); ?>

<?php $this->setNotEmpty('east');?>
<?php $this->start('east'); ?>
    <?php echo $this->element('hooks', array('position' => 'video_detail_sidebar') ); ?> 
    <?php if(!empty($tags)): ?>
        <div class="box2 ">
            <h3><?php echo __( 'Tags')?></h3>
            <div class="box_content">
                <?php echo $this->element( 'blocks/tags_item_block' ); ?>
            </div>
        </div>
    <?php endif; ?>
	<?php if ( !empty( $similar_videos ) ): ?>
        <div class="box2 box_style2">
            <h3><?php echo __( 'Similar Videos')?></h3>
            <div class="box_content">
                <?php echo $this->element('blocks/videos_block', array('videos' => $similar_videos)); ?>
            </div>
        </div>
	<?php endif; ?>
<?php $this->end(); ?>
<div class="bar-content">
    <div >
        <div class="video-detail">
            <?php echo $this->element('Video./video_snippet', array('video' => $video)); ?>
        
        </div>
    <div class="content_center full_content p_m_10">	
    	<h1 class="video-detail-title"><?php echo h($video['Video']['title'])?></h1>
        <div class="video-detail-action">
            <div class="list_option">
                <div class="dropdown">
                    <button id="dLabel" type="button" data-toggle="dropdown" aria-haspopup="true" role="button" aria-expanded="false">
                        <i class="icon-ellipsis-vert"></i>
                    </button>
                    <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                        <?php if ($video['User']['id'] == $uid || ( !empty($cuser) && $cuser['Role']['is_admin'] ) || ( !empty($admins) && in_array($uid, $admins) )): ?>
                        <li>
                            <?php
      $this->MooPopup->tag(array(
             'href'=>$this->Html->url(array("controller" => "videos",
                                            "action" => "create",
                                            "plugin" => 'video',
                                            $video['Video']['id']
                                            
                                        )),
             'title' => __( 'Edit Video Details'),
             'innerHtml'=> __( 'Edit Video'),
     ));
 ?>
                            </li>
                        <li><a href="javascript:void(0)" onclick="mooConfirm('<?php echo addslashes(__('Are you sure ?'));?>','<?php echo $this->request->base;?>/videos/delete/<?php echo $video["Video"]["id"]?>');"><?php echo __( 'Delete Video')?></a></li>
                        <?php endif; ?>
                        <li>
                            <?php
      $this->MooPopup->tag(array(
             'href'=>$this->Html->url(array("controller" => "reports",
                                            "action" => "ajax_create",
                                            "plugin" => false,
                                            'Video_Video',
                                            $video['Video']['id']
                                        )),
             'title' => __( 'Report Video'),
             'innerHtml'=> __( 'Report Video'),
     ));
 ?>
                            </li>
                            
                            <?php if ($video['Video']['privacy'] != PRIVACY_ME): ?>
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
        </div>	
    	<div class="video-description truncate" data-more-text="<?php echo __( 'Show More')?>" data-less-text="<?php echo __( 'Show Less')?>">
    		<?php echo $this->Moo->formatText( $video['Video']['description'], false, true, array('no_replace_ssl' => 1) )?>
    	</div>
        <div class="extra_info">
    	<?php echo __( 'Posted by %s', $this->Moo->getName($video['User']))?> <?php echo __( 'in')?> <a href="<?php echo $this->request->base?>/videos/index/<?php echo $video['Video']['category_id']?>/<?php echo seoUrl($video['Category']['name'])?>"><?php echo $video['Category']['name']?></a> <?php echo $this->Moo->getTime($video['Video']['created'], Configure::read('core.date_format'), $utz)?>
    	&nbsp;&middot;&nbsp;<?php if ($video['Video']['privacy'] == PRIVACY_PUBLIC): ?>
                        <?php echo __('Public') ?>
                        <?php elseif ($video['Video']['privacy'] == PRIVACY_ME): ?>
                        <?php echo __('Private') ?>
                        <?php elseif ($video['Video']['privacy'] == PRIVACY_FRIENDS): ?>
                        <?php echo __('Friend Only') ?>
                        <?php endif; ?>
       
        </div>
        <?php $this->Html->rating($video['Video']['id'],'videos', 'Video'); ?>

    </div>
    </div>
</div>
<div class="bar-content full_content p_m_10">
    <div class="content_center">
        <?php echo $this->element('likes', array('item' => $video['Video'], 'type' => $video['Video']['moo_type'])); ?>
    </div>
</div>
<div class="bar-content full_content p_m_10">
    <div class="content_center">
		<?php echo $this->renderComment();?>
    </div>
</div>


                
