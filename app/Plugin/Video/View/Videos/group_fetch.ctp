<script>
function deleteVideo()
{
	$.fn.SimpleModal({
        btn_ok: '<?php echo addslashes(__( 'OK'))?>',
        callback: function(){
            $.post( '<?php echo $this->request->base?>/videos/ajax_delete/<?php echo $video['Video']['id']?>', function(data){
				loadPage('videos', '<?php echo $this->request->base?>/videos/browse/group/<?php echo $video['Video']['group_id']?>');
				if ( $("#group_videos_count").html() != '0' )
					$("#group_videos_count").html( parseInt($("#group_videos_count").html()) - 1 );
			});	
        },
        title: '<?php echo addslashes(__( 'Please Confirm'))?>',
        contents: "<?php echo addslashes(__( 'Are you sure you want to remove this video?'))?>",
        model: 'confirm', hideFooter: false, closeButton: false        
    }).showModal();
    //mooConfirm('Are you sure ?', '<?php echo $this->request->base?>/videos/ajax_delete/<?php echo $video['Video']['id']?>');
}

</script>
<?php if ( !empty( $video['Video']['id'] ) ): ?>
<div class='bar-content'>
<?php endif; ?>
    <div class='content_center'>
        <div class='mo_breadcrumb'>
            <h1><?php echo __( 'Video Details')?></h1>
        </div>
        <div class="error-message" style="display:none"></div>
        <div class='create_form full_content p_m_10'>
        <?php if ( !empty( $video['Video']['id'] ) ): ?>
        <form id="createForm">
        <?php endif; ?>

        <ul class="list6 list6sm2">
                <?php if (!empty($video['Video']['id'])): ?>
                <?php echo $this->Form->hidden('id', array('value' => $video['Video']['id'])); ?>
                <?php endif; ?>
                <?php echo $this->Form->hidden('source_id', array('value' => $video['Video']['source_id'])); ?>
                <?php echo $this->Form->hidden('thumb', array('value' => $video['Video']['thumb'])); ?>
                <?php echo $this->Form->hidden('privacy', array('value' => PRIVACY_EVERYONE)); ?>

                <li>
                    <div class='col-md-2'>
                    <label><?php echo __( 'Video Title')?></label>
                    </div>
                    <div class='col-md-10'>
                        <?php echo $this->Form->text('title', array('value' => $video['Video']['title'])); ?>
                    </div>
                    <div class='clear'></div>  
                </li>
                <li>
                    <div class='col-md-2'>
                        <label><?php echo __( 'Description')?></label>
                    </div>
                    <div class='col-md-10'>
                        <?php echo $this->Form->textarea('description', array('value' => $video['Video']['description'])); ?>
                    </div>
                    <div class='clear'></div>
                </li>
                <li>
                    <div class='col-md-2'>
                    <label>&nbsp;</label>
                    </div>
                    <div class='col-md-10'>
                        <a href="javascript:void(0)" class="btn btn-action" onclick="$('.modal').modal('hide') ;ajaxCreateItem('videos', true)"><?php echo __( 'Save')?></a>
                        <?php if ( !empty($video['Video']['id']) ): ?>
                        <a href="javascript:void(0)" class="button" onclick="loadPage('videos', '<?php echo $this->request->base?>/videos/ajax_view/<?php echo $video['Video']['id']?>')"> <?php echo __( 'Cancel')?></a>
                        <a href="javascript:void(0)" class="button" onclick="$('.modal').hide() ;deleteVideo()"> <?php echo __( 'Delete')?></a>
                        <?php else: ?>
                        <a href="javascript:void(0)" class="button" onclick="$('.modal').modal('hide');"> <?php echo __( 'Cancel')?></a>
                        <?php endif; ?>
                    </div>
                    <div class='clear'></div>  
                </li>
        </ul>

        <?php if ( !empty( $video['Video']['id'] ) ): ?>
        </form>
        <?php endif; ?>
        </div>
    </div>
<?php if ( !empty( $video['Video']['id'] ) ): ?>
</div>
<?php endif; ?>