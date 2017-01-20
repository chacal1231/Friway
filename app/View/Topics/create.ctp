<?php $this->setCurrentStyle(4) ?>
<?php
echo $this->Html->script(array('tinymce/tinymce.min', 'jquery.fileuploader'), array('inline' => false));
echo $this->Html->css(array( 'fineuploader' ));
echo $this->element('misc/topic_create_script');

$tags_value = '';
if (!empty($tags)) $tags_value = implode(', ', $tags);
?>

<style>
.attach_remove {display:none;}
#attachments_list li:hover .attach_remove {display:inline-block;}
</style>
<?php $this->Html->scriptStart(array('inline' => false)); ?>
$(document).ready(function(){ 

    var errorHandler = function(event, id, fileName, reason) {
        if ($('.qq-upload-list .errorUploadMsg').length > 0){
        $('.qq-upload-list .errorUploadMsg').html('<?php echo addslashes(__('Can not upload file more than ')) . $file_max_upload?>');
    }else {
        $('.qq-upload-list').prepend('<div class="errorUploadMsg"><?php echo addslashes(__('Can not upload file more than ')) . $file_max_upload?></div>');
    }
    $('.qq-upload-fail').remove();
    };
    var uploader = new qq.FineUploader({
        element: $('#topic_thumnail')[0],
        multiple: false,
        text: {
            uploadButton: '<div class="upload-section"><i class="icon-camera"></i><?php echo addslashes(__('Drag or click here to upload photo'))?></div>'
        },
        validation: {
            allowedExtensions: ['jpg', 'jpeg', 'gif', 'png'],
            
        },
        request: {
            endpoint: "<?php echo $this->request->base?>/upload/topic_thumbnail"
        },
        callbacks: {
            onError: errorHandler,
            onComplete: function(id, fileName, response) {
                $('#topic_thumnail_preview > img').attr('src', response.thumb);
                $('#topic_thumnail_preview > img').show();
                $('#thumbnail').val(response.thumb_value);
            }
        }
    });
});

<?php $this->Html->scriptEnd(); ?>
<div class="create_form">
<div class="bar-content">
<div class="content_center">
<div class="box3">
    <form id="createForm">
	<?php
	echo $this->Form->hidden( 'attachments', array( 'value' => $attachments_list ) );
    
	if (!empty($topic['Topic']['id']))
		echo $this->Form->hidden('id', array('value' => $topic['Topic']['id']));
	?>
        <div class="mo_breadcrumb">
            <h1><?php if (empty($topic['Topic']['id'])) echo __('Create New Topic'); else echo __('Edit Topic');?></h1>	
        </div>
        <div class="full_content p_m_10">
                <div class="form_content">
                    <ul>
                        <li>
                            <div class="col-md-2">
                                <label><?php echo __('Topic Title')?></label>
                            </div>
                            <div class="col-md-10">
                                <?php echo $this->Form->text( 'title', array( 'value' => $topic['Topic']['title'] ) ); ?>
                            </div>
                            <div class="clear"></div>
                        </li>
                   
                        <li>
                            <div class="col-md-2">
                            <label><?php echo __('Category')?></label>
                            </div>
                            <div class="col-md-10">
                            <?php echo $this->Form->select( 'category_id', $cats, array( 'value' => $topic['Topic']['category_id'] ) ); ?>
                            </div>
                            <div class="clear"></div>
                        </li>
                        <li>
                            <div class="col-md-2">
                            <label><?php echo __('Topic')?></label>
                            </div>
                            <div class="col-md-10">
                                <?php echo $this->Form->textarea( 'body', array( 'value' => $topic['Topic']['body'], 'id' => 'editor' ) ); ?>
                            </div>
                            <div class="clear"></div>
                        </li>
                        <li>
                            <div class="col-md-2">
                                <label><?php echo __('Thumbnail')?>(<a original-title="<?php echo __('Thumbnail only display on topic listing and share topic to facebook')?>" class="tip" href="javascript:void(0);">?</a>)</label>
                            </div>
                            <div class="col-md-10">
                                <div id="topic_thumnail"></div>
                                <div id="topic_thumnail_preview">
                                    <?php if (!empty($topic['Topic']['thumbnail'])): ?>
                                        <img src="<?php echo $this->request->base . "/" .$topic['Topic']['thumbnail']; ?>" />
                                        <input type="hidden" id="thumbnail" name="thumbnail" value="<?php echo $topic['Topic']['thumbnail']?>" />
                                    <?php else: ?>
                                        <img src="" style="display: none;" />
                                        <input type="hidden" id="thumbnail" name="thumbnail" />
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="clear"></div>
                        </li>
                        <li>
                            <div class="col-md-2">
                            <label><?php echo __('Tags')?></label>
                            </div>
                            <div class="col-md-10">
                                <?php echo $this->Form->text( 'tags', array( 'value' => $tags_value ) ); ?> <a href="javascript:void(0)" class="tip profile-tip" title="<?php echo __('Separated by commas')?>">(?)</a>
                            </div>
                            <div class="clear"></div>
                       </li>
                            
                        <?php if (!empty($attachments)): ?>
                        <li>
                            <div class="col-md-2">
                            <label><?php echo __('Attachments')?></label>
                            </div>
                            <div class="col-md-10">
                            <ul class="list6 list6sm" id="attachments_list" style="overflow: hidden;">
                                <?php foreach ($attachments as $attachment): ?>
                                <li><i class="icon-attach "></i><a href="<?php echo $this->request->base?>/attachments/download/<?php echo $attachment['Attachment']['id']?>" target="_blank"><?php echo $attachment['Attachment']['original_filename']?></a>
                                        &nbsp;<a href="#" data-id="<?php echo $attachment['Attachment']['id']?>" class="attach_remove tip" title="<?php echo __('Delete')?>"><i class="icon-trash icon-small"></i></a>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                            </div>
                            <div class="clear"></div>
                        </li>
                        <?php endif; ?>
                    </ul>
                    <div class="col-md-2">&nbsp;</div>
                    <div class="col-md-10">
                        <div id="images-uploader" style="display:none;margin:10px 0;">
                            <div id="attachments_upload"></div>
                            <a href="#" class="button button-primary" id="triggerUpload"><?php echo __('Upload Queued Files')?></a>
                        </div>
                        <a href="javascript:void(0)" onclick="toggleUploader()"><?php echo __('Toggle Attachments Uploader')?></a>

                        <div style="margin:20px 0">           
                            <a href="#" class="btn btn-action" id="createButton"><?php echo __('Save')?></a>
                            <?php if ( !empty( $topic['Topic']['id'] ) ): ?>
                            <a href="<?php echo $this->request->base?>/topics/view/<?php echo $topic['Topic']['id']?>" class="button"><?php echo __('Cancel')?></a>
                            <?php endif; ?>
                            <?php if ( ($topic['Topic']['user_id'] == $uid ) || ( !empty( $topic['Topic']['id'] ) && $cuser['Role']['is_admin'] ) ): ?>
                            <a href="javascript:void(0)" onclick="mooConfirm( '<?php echo addslashes(__('Are you sure you want to remove this topic?'))?>', '<?php echo $this->request->base?>/topics/do_delete/<?php echo $topic['Topic']['id']?>' )" class="button"><?php echo __('Delete')?></a>
                            <?php endif; ?> 
                        </div>
                        <div class="error-message" id="errorMessage" style="display:none"></div>
                    </div>
                <div class="clear"></div>
            </div>
        </div>
            
    </form>
    
</div>
    
</div>
</div>
</div>