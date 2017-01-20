<?php if($this->request->is('ajax')): ?>
<script>
<?php else: ?>
<?php  $this->Html->scriptStart(array('inline' => false));   ?>
<?php endif; ?>
$(document).ready(function(){
    if(typeof window.orientation === 'undefined' || window.innerWidth > 600)
    {
        tinymce.init({
            selector: "textarea",
            theme: "modern",
            skin: 'light',
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
        element: $('#attachments_upload')[0],
        autoUpload: false,
        text: {
            uploadButton: '<div class="upload-section"><i class="fa fa-file-text-o"></i> <span>Drag or </span>click here to upload files</div>'
        },
        validation: {
            allowedExtensions: ['jpg', 'jpeg', 'gif', 'png', 'txt', 'zip', 'pdf'],
            <?php if(!empty($sizeLimit)): ?>
            sizeLimit: <?php echo $sizeLimit ?>
            <?php endif; ?>
        },
        request: {
            endpoint: "<?php echo $this->request->base?>/upload/attachments/<?php echo PLUGIN_TOPIC_ID?>"
        },
        callbacks: {
            onError: errorHandler,
            onComplete: function(id, fileName, response) {
                var attachs = $('#attachments').val();
    	
                if ( attachs == '' )
                        $('#attachments').val( response.attachment_id );
                else
                        $('#attachments').val( attachs + ',' + response.attachment_id );
            }
        }
    });

    $('#triggerUpload').click(function() {
        uploader.uploadStoredFiles();
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
	
	$('#createButton').click(function(){
        $('#editor').val(tinyMCE.activeEditor.getContent());
        createItem('topics');
        
        return false;
    })
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