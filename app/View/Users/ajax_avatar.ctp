<?php $this->setCurrentStyle(4); ?>
<script>
$(document).ready(function()
{
    var JCropperAvatar;
    if( !isMobile.any() ) {
        $('#av-img2').Jcrop({
            aspectRatio: 1,
            onSelect: storeCoords,
            minSize: [ 180, 180 ],
            boxWidth: 570
        }, function(){
            JCropperAvatar = this;
        });
    }
    else
        $('.modal-footer').addClass('hide');

    var errorHandler = function(event, id, fileName, reason) {
        if ($('.qq-upload-list .errorUploadMsg').length > 0){
        $('.qq-upload-list .errorUploadMsg').html('<?php echo addslashes(__('Can not upload file more than ')) . $file_max_upload?>');
    }else {
        $('.qq-upload-list').prepend('<div class="errorUploadMsg"><?php echo addslashes(__('Can not upload file more than ')) . $file_max_upload?></div>');
    }
    $('.qq-upload-fail').remove();
    };
    var uploader = new qq.FineUploader({
        element: $('#select-0')[0],
        multiple: false,
        text: {
            uploadButton: '<div class="upload-section"><i class="icon-camera"></i><?php echo addslashes(__('Drag or click here to upload photo'))?></div>'
        },
        validation: {
            allowedExtensions: ['jpg', 'jpeg', 'gif', 'png'],
            
        },
        request: {
            endpoint: "<?php echo $this->request->base?>/upload/avatar"
        },
        callbacks: {
            onError: errorHandler,
            onComplete: function(id, fileName, response) {
                $('#av-img').attr('src', response.avatar_mini);
                if( !isMobile.any() ) {
                   JCropperAvatar.setImage(response.avatar);
                }else{
                    $('#avatar_wrapper img').attr('src', response.avatar);
                }
                $('#member-avatar').attr('src', response.thumb);   
            }
        }
    });


});
</script>
<div class="title-modal">
    <?php echo __('Profile Picture')?>
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
</div>
<div class="modal-body">
    <div id="avatar_wrapper" style="vertical-align: top;margin: 0 10px 10px 0">
        <?php echo $this->Moo->getImage(array('User' => $cuser), array("id" => "av-img2", 'prefix' => '600'))?>
    </div>

    <div class="Metronic-alerts alert alert-warning fade in"><?php echo __("Optimal size 250x250px"); ?></div>
    <div id="select-0" class="ava-upload"></div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-action save-avatar"><span aria-hidden="true"><?php echo __('Save Thumbnail')?></span></button>
</div>
