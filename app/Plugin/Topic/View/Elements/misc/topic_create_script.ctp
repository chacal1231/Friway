<?php if($this->request->is('ajax')): ?>
<script>
<?php else: ?>
<?php  $this->Html->scriptStart(array('inline' => false));   ?>
<?php endif; ?>
<?php if(empty($isMobile)): ?>
$(document).ready(function(){
    if(typeof window.orientation === 'undefined' ||window.innerWidth > 600)
    {
        if(tinyMCE.editors.length > 0)
            tinymce.editors = [];

        tinymce.init({
            selector: "textarea",
            mode:'none',
            theme: "modern",
            skin: 'light',
            <?php
				if(!empty($site_rtl)):
			?>
            directionality: "rtl",
            <?php endif; ?>
            plugins: [
                "emoticons link image"
            ],
            toolbar1: "bold italic underline strikethrough | bullist numlist | link unlink image emoticons blockquote",
            image_advtab: true,
            width: 580,
            height: 400,
            menubar: false,
            forced_root_block : 'div',
            relative_urls : false,
            remove_script_host : true,
            document_base_url : '<?php echo FULL_BASE_URL . $this->request->root?>'
        });

    }
});
<?php endif; ?>

$(document).ready(function(){
    var errorHandler = function(event, id, fileName, reason) {
        if ($('#attachments_upload .qq-upload-list .errorUploadMsg').length > 0){
            $('#attachments_upload .qq-upload-list .errorUploadMsg').html('<?php echo addslashes(__('Can not upload file more than ')) . $file_max_upload?>');
        }else {
            $('#attachments_upload .qq-upload-list').prepend('<div class="errorUploadMsg"><?php echo addslashes(__('Can not upload file more than ')) . $file_max_upload?></div>');
        }
        $('#attachments_upload .qq-upload-fail').remove();
    };
    
    var errorHandler1 = function(event, id, fileName, reason) {
        if ($('#topic_thumnail .qq-upload-list .errorUploadMsg').length > 0){
            $('#topic_thumnail .qq-upload-list .errorUploadMsg').html('<?php echo addslashes(__('Can not upload file more than ')) . $file_max_upload?>');
        }else {
            $('#topic_thumnail .qq-upload-list').prepend('<div class="errorUploadMsg"><?php echo addslashes(__('Can not upload file more than ')) . $file_max_upload?></div>');
        }
        $('#topic_thumnail .qq-upload-fail').remove();
    };
    
    var uploader = new qq.FineUploader({
        element: $('#attachments_upload')[0],
        autoUpload: false,
        text: {
            uploadButton: '<div class="upload-section"><i class="fa fa-file-text-o"></i> <?php echo addslashes(__('<span>Drag or </span>click here to upload files'));?></div>'
        },
        validation: {
            allowedExtensions: ['jpg', 'jpeg', 'gif', 'png', 'txt', 'zip', 'pdf', 'doc', 'docx'],
            <?php if(!empty($sizeLimit)): ?>
            sizeLimit: <?php echo $sizeLimit ?>
            <?php endif; ?>
        },
        request: {
            endpoint: "<?php echo $this->request->base?>/topic/topic_upload/attachments/<?php echo PLUGIN_TOPIC_ID?>"
        },
        callbacks: {
            onError: errorHandler,
            onComplete: function(id, fileName, response) {
                var attachs = $('#attachments').val();
              
                if (response.attachment_id){
                    tinyMCE.activeEditor.insertContent('<p><a href="<?php echo $this->request->webroot?>attachments/download/' + response.attachment_id + '" class="attached-file">' + response.original_filename + '</a></p><br>');
                    if ( attachs == '' ){
                        $('#attachments').val( response.attachment_id );
                    }
                    else{
                        $('#attachments').val(attachs + ',' + response.attachment_id);
                    }
                }else if(id || response.thumb){
                    console.log(66);
                    tinyMCE.activeEditor.insertContent('<p align="center"><a href="' + response.large + '" class="attached-image"><img src="' + response.thumb + '"></a></p><br>');
                }
            }
        }
    });

    $('#triggerUpload').click(function() {
        uploader.uploadStoredFiles();
    });

    var uploader1 = new qq.FineUploader({
        element: $('#topic_thumnail')[0],
        multiple: false,
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
            endpoint: "<?php echo $this->request->base?>/topic/topic_upload/avatar"
        },
        callbacks: {
            onError: errorHandler1,
            onComplete: function(id, fileName, response) {
                $('#topic_thumnail_preview > img').attr('src', response.thumb);
                $('#topic_thumnail_preview > img').show();
                $('#thumbnail').val(response.file_path);
            }
        }
    });

    $('.attach_remove').click(function(){
		var obj = $(this);
		$.post('<?php echo $this->request->base?>/attachments/ajax_remove/' + $(this).attr('data-id'), function(data){
			obj.parent().fadeOut();
			var arr = $('#attachments').val().split(',');
			var pos = arr.indexOf(obj.attr('data-id'));
			arr.splice(pos, 1);
			$('#attachments').val(arr.join(','));	
		});
		
		return false;
	});
	
});

function toggleUploader()
{
    $('#images-uploader').slideToggle();
}
<?php if($this->request->is('ajax')): ?>
</script>
<?php else: ?>
<?php $this->Html->scriptEnd();  ?>
<?php endif; ?>