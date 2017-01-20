<?php $upload_video = Configure::read('UploadVideo.uploadvideo_enabled'); ?>
<?php if ($upload_video): ?>
    <?php
    echo $this->Html->script(array('jquery.fileuploader'), array('inline' => false));
    echo $this->Html->css(array('fineuploader'));
    ?>
<?php endif; ?>
<div class="bar-content">
    <div class="content_center">
        <div class="mo_breadcrumb">
            <h1 class="visible-xs visible-sm"><?php echo h($groupname); ?></h1>
            <?php if (!empty($is_member)): ?>
                <a id="share-new" data-target="#videoModal" data-toggle="modal" rel="<?php echo $this->request->base ?>/videos/group_create" class="topButton button button-action button-mobi-top"><?php echo __('Share New Video') ?></a>

                <?php if ($upload_video): ?>
                    <!-- check enabled upload video from pc -->
                    <?php
                    $this->MooPopup->tag(array(
                        'href' => $this->Html->url(array("controller" => "upload_videos",
                            "action" => "ajax_upload_group",
                            "plugin" => 'upload_video',
                            $group_id
                        )),
                        'title' => __('Upload Video'),
                        'innerHtml' => __('Upload Video'),
                        'data-backdrop' => 'static',
                        'class' => 'button button-action topButton button-mobi-top'
                    ));
                    ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <div class="clear"></div>
        <ul class="video-content-list <?php if (!empty($is_member)): ?>p_top_15<?php endif; ?>" id="list-content">
            <?php echo $this->element('lists/videos_list', array('type' => 'Group_Group')); ?>
        </ul>
    </div>
</div>
<section aria-hidden="true" aria-labelledby="myModalLabel" role="basic" id="videoModal" class="modal fade in" >
    <div class="modal-dialog">
        <div class="modal-content">
        </div>
    </div>
</section>