<?php if($this->request->is('ajax')): ?>
<script>
<?php else: ?>
<?php $this->Html->scriptStart(array('inline' => false,'requires'=>array('jquery','jquery.fileuploader','bootstrap'),'object'=>array('$'))); ?>
<?php endif; ?>
$('[data-toggle="tooltip"]').tooltip();
var errorHandler = function(event, id, fileName, reason) {
    
    if ($('.qq-upload-list .errorUploadMsg').length > 0){
        $('.qq-upload-list .errorUploadMsg').html('<?php echo addslashes(__('Can not upload file more than ')) . $file_max_upload?>');
    }else {
        $('.qq-upload-list').prepend('<div class="errorUploadMsg"><?php echo addslashes(__('Can not upload file more than ')) . $file_max_upload?></div>');
    }
    $('.qq-upload-fail').remove();
};
var uploader = new qq.FineUploader({
    element: $('#select-2')[0],
    text: {
        uploadButton: '<div class="upload-section"><i class="icon-camera"></i></div>'
    },
    validation: {
        allowedExtensions: ['jpg', 'jpeg', 'gif', 'png'],
        <?php if(!empty($sizeLimit)): ?>
        sizeLimit: <?php echo $sizeLimit ?>
        <?php endif; ?>
    },
    multiple: true,
    request: {
        endpoint: "<?php echo $this->request->base?>/upload/wall"
    },
    
    callbacks: {
        onError: errorHandler,
        onSubmit: function(id, fileName){
    		var element = $('<span id="feed_'+id+'" style="background-image:url(<?php echo $this->request->webroot?>img/indicator.gif);background-size:inherit;background-repeat:no-repeat"></span>');
    		element.insertBefore('.addMoreImage');
    		$('#wall_photo_preview').show();
            $('#addMoreImage').show();    
        },
        onComplete: function(id, fileName, response, xhr) {
            if (response.success){
                jQuery(this.getItemByFileId(id)).remove();
                img = $('<img src="'+response.file_path+'">');
                img.load(function() {
                	 element = $('#feed_'+id);
                     element.attr('style','background-image:url(' + response.file_path + ')');
                     var deleteItem = $('<a href="#"><i class="icon-delete"></i></a>');
                     element.append(deleteItem);
                      
                     element.find('.icon-delete').click(function(e){
                         e.preventDefault();
                         jQuery(this).parents('span').remove();
                         $('#wall_photo').val($('#wall_photo').val().replace(response.photo + ',',''));
                     });
                });
                
                var wall_photo = $('#wall_photo').val();
                $('#wall_photo').val(wall_photo+ response.photo + ',');
                //toggleUploadPhoto();               
                
            }         
        }
    }
});

<?php $upload_video = Configure::read('UploadVideo.uploadvideo_enabled'); ?>
<?php if($upload_video): ?>
var uploader2 = new qq.FineUploader({
    element: $('#videoPcFeed')[0],
    multiple: false,
    text: {
        uploadButton: '<div class="upload-section"><i class="icon-videocam"></i></div>'
    },
    validation: {
        allowedExtensions: [
        'flv',
        'mp4',
        'wmv',
        '3gp',
        'mov',
        'avi'
    ],
        <?php if(!empty($videoMaxUpload)): ?>
        sizeLimit: <?php echo $videoMaxUpload ?>
        <?php endif; ?>            
    },
    request: {
        endpoint: "<?php echo $this->Html->url(array(
                                'plugin' => 'upload_video',
                                'controller' => 'upload_videos',
                                'action' => 'process_upload'
                            ));?>"
    },
    callbacks: {
        onError: errorHandler,
        onSubmit: function(id, fileName){
        },
        onComplete: function (id, fileName, response) {
            file_uploading = 1;
            if (response.success){
                $('#video_pc_feed_preview').show();
                $('#video_destination').val(response.filename);
            }
        }
    }
});

$('#triggerUpload').click(function () {
    if (uploader2._storedFileIds.length){
        file_uploading = 1;
        uploader2.uploadStoredFiles();
        $(".error-message").hide();
    }
    else{
        $(".error-message").html('<?php echo addslashes(__('Please select video to upload.')); ?>');
        $(".error-message").show();
    }
});
<?php endif; ?>

$('#addMoreImage').click(function(){
	$('#select-2 input[name=file]').click();
});
<?php if($this->request->is('ajax')): ?>
</script>
<?php else: ?>
<?php $this->Html->scriptEnd(); ?>
<?php endif; ?>

<form id="wallForm">
	<?php
	echo $this->Form->hidden('type', array('value' => $type));
	echo $this->Form->hidden('target_id', array('value' => $target_id));
	echo $this->Form->hidden('action', array('value' => 'wall_post'));
	echo $this->Form->hidden('wall_photo');
	
	?>
	<div class="form-feed-holder">
		<div class="post-status">
		<?php
		   echo $this->Form->textarea('message', array('name' => 'messageText', 'placeholder' => $text, 'onfocus' => 'showCommentButton(0)'),true);
		?>
		</div>
	
	</div>
	<div>
		<div id="wall_photo_preview" style="display:none">
			 <span id="addMoreImage" style="display:none;" class="addMoreImage"><i class="icon-add"></i></span>            
        </div>
            
            <div id="video_pc_feed_preview" style="display: none;">
                <div class="left">
                    <div class="video_thumb">
                        <i class="icon-videocam-1"></i>
                    </div>
                </div>
                <div class="right">
                    <div>
                        <?php echo $this->Form->text('title', array('value' => 'Untitle video', 'placeholder' => 'Untitle video')); ?>
                    </div>
                    <div>
                        <?php echo $this->Form->select('category_id', $video_categories, array('empty' => false, 'value' => '')); ?>
                    </div>
                    <div>
                        <?php echo $this->Form->textarea('description', array('value' => '', 'placeholder' => 'Description')); ?>
                    </div>
                </div>
            </div>
            
        <?php echo $this->Form->userTagging('','userTagging',true);?>  
	</div>
        <div class="stt-action">
            <div style="width: 40px;" data-toggle="tooltip" title="<?php echo __('Add photos to your post');?>" id="select-2"></div>
            <div class="user-tagging-container">
            	<i class="icon-user-add" data-toggle="tooltip" title="<?php echo __('Tag people in your post');?>" onclick="$('.userTagging-userTagging').toggleClass('hidden')"></i>
            </div>
            
            <?php if($upload_video): ?>
            <?php echo $this->Form->hidden('video_destination', array('value' => '')); ?>
            <div id="videoPcFeed"></div>
            <?php endif; ?>
            
            <div id="commentButton_0" class="post-stt-btn">
			<div class="wall-post-action">
				<?php if (strtolower($type) == 'user' && !$target_id):?>
					<?php echo $this->Form->select('privacy', array( PRIVACY_EVERYONE => __('Everyone'), PRIVACY_FRIENDS => __('Friends Only') ), array('empty' => false)); ?>
				<?php else:?>
					<?php echo $this->Form->hidden('privacy', array('value' => PRIVACY_EVERYONE));?>
				<?php endif;?>
				<a href="javascript:void(0)" onclick="postWall()" class="btn btn-action" style="margin-bottom:3px" id="status_btn"> <?php echo __('Share')?></a>
			</div>
            </div>
        </div>
	
	
        
</form>