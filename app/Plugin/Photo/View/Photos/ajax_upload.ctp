<?php if($this->request->is('ajax')) $this->setCurrentStyle(4);?>

<?php
if ($target_id):
?>
<?php if($this->request->is('ajax')): ?>
<script>
    <?php else: ?>
    <?php  $this->Html->scriptStart(array('inline' => false));   ?>
    <?php endif; ?>
    var newPhotos = new Array();

    $(document).ready(function(){
        var errorHandler = function(event, id, fileName, reason) {
            if ($('.qq-upload-list .errorUploadMsg').length > 0){
        $('.qq-upload-list .errorUploadMsg').html('<?php echo addslashes(__('Can not upload file more than ')) . $file_max_upload?>');
    }else {
        $('.qq-upload-list').prepend('<div class="errorUploadMsg"><?php echo addslashes(__('Can not upload file more than ')) . $file_max_upload?></div>');
    }
    $('.qq-upload-fail').remove();
        };
        var uploader2 = new qq.FineUploader({
            element: $('#photos_upload')[0],
            autoUpload: false,
            text: {
                uploadButton: '<div class="upload-section"><i class="icon-camera"></i><?php echo addslashes(__( 'Drag or click here to upload photo'))?></div>'
            },
            validation: {
                allowedExtensions: ['jpg', 'jpeg', 'gif', 'png'],
                <?php if(!empty($sizeLimit)): ?>
                sizeLimit: <?php echo $sizeLimit ?>
                <?php endif; ?>
            },
            request: {
                endpoint: "<?php echo $this->request->base?>/photo/photo_upload/album/<?php echo $type?>/<?php echo $target_id?>/<?php echo Configure::read('core.save_original_image')?>"
            },
            callbacks: {
                onError: errorHandler,
                onComplete: function(id, fileName, response) {
                    newPhotos.push( response.photo );
                    
                    $('#new_photos').val( newPhotos.join(',') );
                    $('#nextStep').show();
                }
            }
        });

        $('#triggerUpload').click(function() {
            uploader2.uploadStoredFiles();
        });
        
        $('#nextStep').click(function(){
            $('#loadingSpin').spin('tiny');
            $('#uploadPhotoForm').submit();
            $(this).addClass('disabled');
        });

        
    });

    function setNewPhotos()
    {
        jQuery('#new_photos').val( newPhotos.join(',') );
    }
    <?php if($this->request->is('ajax')): ?>
</script>
<?php else: ?>
    <?php $this->Html->scriptEnd();  ?>
<?php endif; ?>

<div class="share-video-section ">
    <div class="title-modal">
        <?php echo __( 'Upload Photos')?>
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
    </div>
    <div class="modal-body">
        <div class="bar-content full_content p_m_10">
            <div class="content_center">
                <form id="uploadPhotoForm" action="<?php echo $this->request->base?>/photos/do_activity/<?php echo $type?>" method="post">
                    <div id="photos_upload"></div>
                    <div id="photo_review"></div>
                    
                    <a href="#" class="btn btn-action" id="triggerUpload"><?php echo __( 'Upload Queued Files')?></a>
                    <input type="hidden" name="new_photos" id="new_photos">
                    <input type="hidden" name="target_id" value="<?php echo $target_id?>">
                    <input type="button" class="btn btn-action" id="nextStep" value="<?php echo __( 'Save Photos')?>" style="display:none">
                    <div id="loadingSpin" style="display: inline-block; padding: 0 10px;"></div>
                </form>
                <?php
                endif;
                ?>
            </div>
        </div>
    </div>