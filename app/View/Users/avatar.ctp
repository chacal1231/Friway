<?php
if ( empty( $activity ) ){
    echo $this->Html->script(array('jquery.fileuploader', 'jquery.Jcrop.min', 'jquery.mp.min'), array('inline' => false));
    echo $this->Html->css(array( 'fineuploader', 'jquery.Jcrop', 'jquery.mp' ));
}
?>

<?php $this->Html->scriptStart(array('inline'=>false)); ?>
    var jcrop_api;
    var x = 0
    y = 0
    w = 0
    h = 0;

    $(document).ready(function () {
        $('#save-avatar').click(function () {
            if (x == 0 && y == 0 && w == 0 && h == 0){
                mooAlert('Please select area for cropping');
            }
            else{
                $('#avatar_wrapper').spin('large');
                var modal = $('#portlet-config');

                $.post('<?php echo $this->request->base?>/upload/thumb', {x: x, y: y, w: w, h: h}, function (data) {
                    $('#avatar_wrapper').spin(false);
                    if (data != '') {
                        var json = $.parseJSON(data);
                        $('#member-avatar').attr('src', json.thumb);
                        window.location = "<?php echo $this->Moo->getProfileUrl( $cuser )?>";
                    }
                });
            }
        });
    });

    function storeCoords(c) {
        x = c.x;
        y = c.y;
        w = c.w;
        h = c.h;
    }
    $(document).ready(function () {
        if( !isMobile.any() ) {
            $('#av-img2').Jcrop({
                aspectRatio: 1,
                onSelect: storeCoords,
                minSize: [180, 180]
            }, function () {
                jcrop_api = this;
            });
        }
        else
        {
            $('#save-avatar').addClass('hide');
            $('#submit-avatar').removeClass('hide');
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
                    
                    $('#av-img').attr('src', response.avatar);
                    $('#av-img2').attr('src', response.avatar);
                    $('#member-avatar').attr('src', response.thumb);
                    jcrop_api.setImage(response.avatar);
                }
            }
        });

    });
<?php $this->Html->scriptEnd(); ?>
<div class="bar-content full_content p_m_10">
    <div class="content_center">
        <div class="mo_breadcrumb">
            <h1> <?php echo  __('Profile Picture') ?></h1>
        </div>
        <div class="ava_content">
            <div id="avatar_wrapper" style="vertical-align: top;margin: 0 10px 10px 0">
                <img src="<?php echo $this->Moo->getImageUrl(array('User' => $cuser), array('prefix' => '600'))?>"  id="av-img2">
            </div>

            <div class="Metronic-alerts alert alert-warning fade in ava-upload" style="margin-bottom: 20px;"><?php echo __("Optimal size 250x250px"); ?></div>

            <div id="select-0" class="ava-upload"></div>
            <div class="">
                <button id="save-avatar" type="button" class="btn btn-action save-avatar"><span aria-hidden="true"><?php echo  __('Save Thumbnail') ?></span>
                </button>
                <a id="submit-avatar" href="<?php echo $this->request->base; ?>/users/view/<?php echo $cuser['id']; ?>"; type="button" class="btn btn-action submit-avatar hide"><span aria-hidden="true"><?php echo  __('Submit') ?></span>
                </a>
            </div>
        </div>
        
    </div>
</div>
