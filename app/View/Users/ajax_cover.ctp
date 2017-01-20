<?php $this->setCurrentStyle(4); ?>
<script>
$(document).ready(function()
{
    var JCropper;
    if( !isMobile.any() ) {
        $('#cover-img').Jcrop({
            aspectRatio: 4,
            onSelect: storeCoords,
            minSize: [ 400, 200 ],
            boxWidth: 570
        }, function(){
            JCropper = this;
        });
    }
    
    var errorHandler = function(event, id, fileName, reason) {
        if ($('.qq-upload-list .errorUploadMsg').length > 0){
        $('.qq-upload-list .errorUploadMsg').html('<?php echo addslashes(__('Can not upload file more than ')) . $file_max_upload?>');
    }else {
        $('.qq-upload-list').prepend('<div class="errorUploadMsg"><?php echo addslashes(__('Can not upload file more than ')) . $file_max_upload?></div>');
    }
    $('.qq-upload-fail').remove();
    };
    var uploader = new qq.FineUploader({
        element: $('#select-1')[0],
        multiple: false,
        autoUpload: false,
        text: {
            uploadButton: '<div class="upload-section"><i class="icon-camera"></i><?php echo addslashes(__('Drag or click here to upload photo'))?></div>'
        },
        validation: {
            allowedExtensions: ['jpg', 'jpeg', 'gif', 'png']

        },
        request: {
            endpoint: "<?php echo $this->request->base?>/upload/cover"
        },
        callbacks: {
            onSubmit: function(id, fileName){
                var promise = validateFileDimensions(id, [400, 150],this);
                return promise;
            },
            onError: errorHandler,
            onComplete: function(id, fileName, response) {
                $('#cover_img_display').attr("src",response.cover);

                if( !isMobile.any() ) {
                    JCropper.setImage(response.photo);
                }else{
                    $('#cover_wrapper img').attr('src', response.photo);
                }
            }
        }
    });
    function validateFileDimensions(id, dimensionsLimits,obj)
    {
        window.URL = window.URL || window.webkitURL;
        var file = obj.getFile(id);

        var image = new Image();
        var status = false;
        var sizeDetermination = {};

        image.onerror = function(e) {
            //log("Cannot determine dimensions for image.  May be too large.", "error");
            sizeDetermination['error'] = "Cannot determine dimensions for image.  May be too large.";
        };

        image.onload = function() {
            sizeDetermination = { width: this.width, height: this.height };

            var minWidth = sizeDetermination.width >= dimensionsLimits[0],
                minHeight = sizeDetermination.height >= dimensionsLimits[1];

            // if min-width or min-height satisfied the limits, then approve the image
//console.log(sizeDetermination.width,dimensionsLimits[0],sizeDetermination.height,dimensionsLimits[1])
            if( minWidth && minHeight )
                uploader.uploadStoredFiles();
            else{
                uploader.clearStoredFiles();
                alert('Please choose an image that\'s at least 400 pixels wide and at least 150 pixels tall');
            }
        };
        image.src = window.URL.createObjectURL(file);
    }
});
</script>
<div class="title-modal">
    <?php echo __('Cover Picture')?>
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
</div>
<div class="modal-body">
    <div id="cover_wrapper">
        <?php if ( !empty( $photo['Photo']['thumbnail'] ) ): ?>
        <?php echo $this->Moo->getImage($photo, array('prefix' => '1500', 'id' => 'cover-img'));?>
        <?php else: ?>
        <img src="<?php echo $this->Moo->defaultCoverUrl() ?>"  id="cover-img">
        <?php endif; ?>
    </div>

    <div class="Metronic-alerts alert alert-warning fade in"><?php echo __("Optimal size 1164x266px"); ?></div>

    <div id="select-1" class="ava-upload" style="margin-top:10px;"></div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-action save-cover"><span aria-hidden="true"><?php echo __('Save Cover Picture')?></span></button>
</div>
