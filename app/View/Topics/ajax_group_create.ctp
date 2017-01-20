<?php if($this->request->is('ajax')) $this->setCurrentStyle(4) ?>
<?php echo $this->element('misc/topic_create_script'); ?>

<?php if($this->request->is('ajax')): ?>
<script>
<?php else: ?>
<?php  $this->Html->scriptStart(array('inline' => false));   ?>
<?php endif; ?>
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

function deleteTopic()
{
	$.fn.SimpleModal({
        btn_ok: '<?php echo addslashes(__('OK'))?>',
        callback: function(){
            $.post( '<?php echo $this->request->base?>/topics/ajax_delete/<?php echo $topic['Topic']['id']?>', function(data){
                loadPage('topics', '<?php echo $this->request->base?>/topics/ajax_browse/group/<?php echo $this->request->data['group_id']?>');
                
                if ( $("#topics_count").html() != '0' )
                    $("#topics_count").html( parseInt($("#topics_count").html()) - 1 );
            });     
        },
        title: '<?php echo addslashes(__('Please Confirm'))?>',
        contents: '<?php echo addslashes(__('Are you sure you want to remove this topic?'))?>',
        model: 'confirm', hideFooter: false, closeButton: false        
    }).showModal();
}
<?php if($this->request->is('ajax')): ?>
</script>
<?php else: ?>
<?php $this->Html->scriptEnd();  ?>
<?php endif; ?>


<style>
.list6 .mce-tinymce { margin-left: 0; }
.attach_remove {display:none;}
#attachments_list li:hover .attach_remove {display:inline-block;}
</style>
<div class="create_form_ajax">
<div class="bar-content full_content p_m_10">
    <div class="content_center">
<form id="createForm">
<?php
echo $this->Form->hidden( 'attachments', array( 'value' => $attachments_list ) );
echo $this->Form->hidden( 'tags' );

if (!empty($topic['Topic']['id']))
	echo $this->Form->hidden('id', array('value' => $topic['Topic']['id']));

echo $this->Form->hidden('group_id', array('value' => $this->request->data['group_id']));
echo $this->Form->hidden('category_id', array('value' => 0));
?>	
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
                        <label> <?php echo __('Topic')?></label>
                    </div>
                    <div class="col-md-10">
                        <?php echo $this->Form->textarea( 'body', array( 'value' => $topic['Topic']['body'], 'id' => 'editor' ) ); ?>
                    </div>
                    <div class="clear"></div>
                </li>
                <li>
                    <div class="col-md-2">
                        <label><?php echo __('Thumbnail')?>(<a original-title="Thumbnail only display on topic listing and share topic to facebook" class="tip" href="javascript:void(0);">?</a>)</label>
                    </div>
                    <div class="col-md-10">
                        <div id="topic_thumnail"></div>
                        <div id="topic_thumnail_preview">
                            <?php if (!empty($topic['Topic']['thumbnail'])): ?>
                                <img src="<?php echo $this->request->base . "/" .$topic['Topic']['thumbnail']; ?>" />
                            <?php else: ?>
                                <img src="" style="display: none;" />
                            <?php endif; ?>
                        </div>
                        <input type="hidden" id="thumbnail" name="thumbnail" />

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
                            <li><i class="icon-attach"></i><a href="<?php echo $this->request->base?>/attachments/download/<?php echo $attachment['Attachment']['id']?>" target="_blank"><?php echo $attachment['Attachment']['original_filename']?></a>
                                &nbsp;<a href="#" data-id="<?php echo $attachment['Attachment']['id']?>" class="attach_remove tip" title="<?php echo __('Delete')?>"><i class="icon-trash icon-small"></i></a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="clear"></div>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</form>
        <div class="col-md-2">&nbsp;</div>
        <div class="col-md-10">
            <div id="images-uploader" style="display:none;">
                <div id="attachments_upload"></div>
                <a href="#" class="button button-primary" id="triggerUpload"><?php echo __('Upload Queued Files')?></a>
            </div>
             <a href="javascript:void(0)" onclick="toggleUploader()"><?php echo __('Toggle Attachments Uploader')?></a>

             <div style="margin:20px 0">           
                <a href="javascript:void(0)" class="btn btn-action" id="ajaxCreateButton" onclick="ajaxCreateItem('topics')"><?php echo __('Save')?></a>

                <?php if ( !empty( $topic['Topic']['id'] ) ): ?>
                <a href="javascript:void(0)" class="button" onclick="loadPage('topics', '<?php echo $this->request->base?>/topics/ajax_view/<?php echo $topic['Topic']['id']?>')"><?php echo __('Cancel')?></a>

                <?php if ( ($topic['Topic']['user_id'] == $uid ) || ( !empty($my_status) && $my_status['GroupUser']['status'] == GROUP_USER_ADMIN ) || ( !empty($cuser) && $cuser['Role']['is_admin'] ) ): ?>
                <a href="javascript:void(0)" onclick="deleteTopic()" class="button button-caution"><?php echo __('Delete')?></a>
                <?php endif; ?> 

                <?php else: ?>
                <a href="javascript:void(0)" class="button" onclick="loadPage('topics', '<?php echo $this->request->base?>/topics/ajax_browse/group/<?php echo $this->request->data['group_id']?>')"><?php echo __('Cancel')?></a>
                <?php endif; ?>     
            </div>
            <div class="error-message" id="errorMessage" style="display:none"></div>
         </div>
        <div class="clear"></div>
</div>
</div>

</div>