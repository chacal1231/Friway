<?php $this->setCurrentStyle(4) ?>
<?php
echo $this->Html->script(array('jquery.fileuploader'), array('inline' => false));
echo $this->Html->css(array( 'fineuploader' ));

$tags_value = '';
$blogHelper = MooCore::getInstance()->getHelper('Blog_Blog');
if (!empty($tags)) $tags_value = implode(', ', $tags);
?>

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
        element: $('#photos_upload')[0],
        autoUpload: false,
        text: {
            uploadButton: '<div class="upload-section"><i class="icon-camera"></i><?php echo addslashes(__( "Drag or click here to upload photo"))?></div>'
        },
        validation: {
            allowedExtensions: ['jpg', 'jpeg', 'png'],
            <?php if(!empty($sizeLimit)): ?>
            sizeLimit: <?php echo $sizeLimit ?>
            <?php endif; ?>
        },
        request: {
            endpoint: "<?php echo $this->request->base?>/blog/blog_upload/images"
        },
        callbacks: {
            onError: errorHandler,
            onComplete: function(id, fileName, response) {
                tinyMCE.activeEditor.insertContent('<p align="center"><a href="' + response.large + '" class="attached-image"><img src="' + response.thumb + '"></a></p><br>');
            }
        }
    });

    $('#triggerUpload').click(function() {
        uploader.uploadStoredFiles();
    });
    
    $('#saveBtn').click(function(){
        $(this).addClass('disabled');
        if(tinyMCE.activeEditor !== null){
            $('#editor').val(tinyMCE.activeEditor.getContent());
        }
        createItem('blogs', true);
    });
    
    

    var uploader1 = new qq.FineUploader({
        element: $('#blog_thumnail')[0],
        multiple: false,
        text: {
            uploadButton: '<div class="upload-section"><i class="icon-camera"></i><?php echo addslashes(__( "Drag or click here to upload photo"))?></div>'
        },
        validation: {
            allowedExtensions: ['jpg', 'jpeg', 'gif', 'png'],
            <?php if(!empty($sizeLimit)): ?>
            sizeLimit: <?php echo $sizeLimit ?>
            <?php endif; ?>
        },
        request: {
            endpoint: "<?php echo $this->request->base?>/blog/blog_upload/avatar"
        },
        callbacks: {
            onError: errorHandler,
            onComplete: function(id, fileName, response) {
                $('#blog_thumnail_preview > img').attr('src', response.thumb);
                $('#blog_thumnail_preview > img').show();
                $('#thumbnail').val(response.file);
            }
        }
    });
    
});

function toggleUploader()
{
    $('#images-uploader').slideToggle();
}
<?php $this->Html->scriptEnd(); ?>
<div class="create_form">
<div class="bar-content">
<div class="content_center">
<div class="box3">
	<form id='createForm' action="<?php echo  $this->request->base; ?>/blogs/save" method="post">
	<?php
	if (!empty($blog['Blog']['id']))
		echo $this->Form->hidden('id', array('value' => $blog['Blog']['id']));
        echo $this->Form->hidden('thumbnail', array('value' => $blog['Blog']['thumbnail']));
	?>
	<div class="mo_breadcrumb">
            <h1><?php if (empty($blog['Blog']['id'])) echo __( 'Write New Entry'); else echo __( 'Edit Entry');?></h1>
        </div>
        <div class="full_content p_m_10">
            <div class="form_content">
                <ul >
                        <li>
                            <div class="col-md-2">
                                <label><?php echo __( 'Title')?></label>
                            </div>
                            <div class="col-md-10">
                                <?php echo $this->Form->text('title', array('value' => $blog['Blog']['title'])); ?>
                            </div>
                            <div class="clear"></div>
                        </li>
                        <li>
                            <div class="col-md-2">
                                <label><?php echo __( 'Body')?></label>
                            </div>
                            <div class="col-md-10">
                                <?php echo $this->Form->tinyMCE('body', array('value' => $blog['Blog']['body'], 'id' => 'editor')); ?>
                            </div>
                            <div class="clear"></div>
                        </li>
                        <li>
                            <div class="col-md-2">

                                <label><?php echo __( 'Thumbnail')?> (<a original-title="<?php echo __( 'Thumbnail only display on blog listing and share blog to facebook')?>" class="tip" href="javascript:void(0);">?</a>)</label>
                            </div>
                            <div class="col-md-10">
                                <div id="blog_thumnail"></div>
                                <div id="blog_thumnail_preview">
                                    <?php if (!empty($blog['Blog']['thumbnail'])): ?>
                                        <img width="150" src="<?php echo  $blogHelper->getImage($blog, array('prefix' => '150_square')) ?>" />
                                    <?php else: ?>
                                        <img width="150" style="display: none;" src="" />
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="clear"></div>
                        </li>
                        <li>
                            <div class="col-md-2">
                                <label><?php echo __( 'Tags')?></label>
                            </div>
                            <div class="col-md-10">
                                <?php echo $this->Form->text('tags', array('value' => $tags_value)); ?> <a href="javascript:void(0)" class="tip profile-tip" title="<?php echo __( 'Separated by commas or space')?>">(?)</a>
                            </div>
                            <div class="clear"></div>
                        </li>
                        
                        <li>
                            <div class="col-md-2">
                                <label><?php echo __( 'Privacy')?></label>
                            </div>
                            <div class="col-md-10">
                                <?php echo $this->Form->select( 'privacy', 
                                                                                                array( PRIVACY_EVERYONE => __( 'Everyone'), 
                                                                                                           PRIVACY_FRIENDS  => __( 'Friends Only'), 
                                                                                                           PRIVACY_ME 		=> __( 'Only Me') ), 
                                                                                                array( 'value' => $blog['Blog']['privacy'],
                                                                                                           'empty' => false
                                                                                         ) ); 
                                ?>
                            </div>
                            <div class="clear"></div>
                        </li>
                        
                </ul>

                <div class="col-md-2">&nbsp;</div> 
    
                <div class="col-md-10">
                    <div id="images-uploader" style="display:none;margin:10px 0;">
                        <div id="photos_upload"></div>
                        <a href="#" class="button button-primary" id="triggerUpload"><?php echo __( 'Upload Queued Files')?></a>
                    </div>
                    <?php if(empty($isMobile)): ?>
                        <a id="toggleUploader" href="javascript:void(0)" onclick="toggleUploader()"><?php echo __( 'Toggle Images Uploader')?></a>
                    <?php endif; ?>
                        <div style="margin:20px 0">
                            <button type='button' id='saveBtn' class='btn btn-action'><?php echo __( 'Save'); ?></button>
                            
                                <?php if ( !empty( $blog['Blog']['id'] ) ): ?>
                                <a href="<?php echo $this->request->base?>/blogs/view/<?php echo $blog['Blog']['id']?>" class="button"><?php echo __( 'Cancel')?></a>
                                <?php endif; ?>
                                <?php if ( ($blog['Blog']['user_id'] == $uid ) || ( !empty( $blog['Blog']['id'] ) && $cuser['Role']['is_admin'] ) ): ?>
                                <a href="javascript:void(0)" onclick="mooConfirm( '<?php echo addslashes(__( 'Are you sure you want to remove this entry?'))?>', '<?php echo $this->request->base?>/blogs/delete/<?php echo $blog['Blog']['id']?>' )" class="button"><?php echo __( 'Delete')?></a>
                                <?php endif; ?>
                        </div>
                        <div class="error-message" id="errorMessage" style="display: none;"></div>
                </div>
        </form>
                <div class="clear"></div>

        </div>
    </div>
</div>
</div>
</div>
</div>