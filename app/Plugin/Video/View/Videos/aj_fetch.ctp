
<script type="text/javascript">
    $(document).ready(function(){
        $('#saveBtn').click(function(){
            $(this).addClass('disabled');
            createItem('videos',true)
        }); 
    });
</script>
<?php
$tags_value = '';
if (!empty($tags)) $tags_value = implode(', ', $tags);
?>
<?php if ( !empty( $video['Video']['id'] ) ): ?>
<div class="title-modal">
    <?php echo __( 'Edit Video')?>
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
</div>
<div class="modal-body">

<script type="text/javascript">
    function confirmDelete(url){
        mooConfirm('<?php echo addslashes(__('Are you sure ?'));?>',url);
    }
</script>
    <div class="bar-content full_content p_m_10">
        <div class="content_center">
            <div class="create_form">
                <form id="createForm">
                    <?php endif; ?>
                    <div class="create_form">
                    <ul class="list6 list6sm2">
                        <?php echo $this->Form->hidden('id', array('value' => $video['Video']['id'])); ?>
                        <?php echo $this->Form->hidden('source_id', array('value' => $video['Video']['source_id'])); ?>
                        <?php echo $this->Form->hidden('thumb', array('value' => $video['Video']['thumb'])); ?>

                        <li>
                                <div class="col-md-2">
                                    <label><?php echo __( 'Video Title')?></label>
                                </div>
                                <div class="col-md-10">
                                    <?php echo $this->Form->text('title', array('value' => $video['Video']['title'])); ?>
                                </div>
                                <div class="clear"></div>
                        </li>

                        <?php if(empty($isGroup)): ?>
                        <li>
                                <div class="col-md-2">
                                    <label><?php echo __( 'Category')?></label>
                                </div>
                                <div class="col-md-10">
                                    <?php echo $this->Form->select( 'category_id', $categories, array( 'value' => $video['Video']['category_id'] ) ); ?>
                                </div>
                                <div class="clear"></div>


                        </li>
                        <?php endif; ?>

                        <li>
                                <div class="col-md-2">
                                    <label><?php echo __( 'Description')?></label>
                                </div>
                                <div class="col-md-10">
                                    <?php echo $this->Form->textarea('description', array('value' => $video['Video']['description'])); ?>
                                </div>
                                <div class="clear"></div>


                        </li>

                        <?php if(empty($isGroup)): ?>
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



                            <?php
                            echo $this->Form->select( 'privacy',
                                                      array( PRIVACY_EVERYONE => __( 'Everyone'),
                                                             PRIVACY_FRIENDS  => __( 'Friends Only'),
                                                             PRIVACY_ME 	  => __( 'Only Me')
                                                            ),
                                                      array( 'value' => $video['Video']['privacy'],
                                                             'empty' => false
                                                            )
                                                    );
                            ?>
                                    </div>
                                <div class="clear"></div>
                        </li>
                        <?php endif; ?>

                        <li>
                                <div class="col-md-2">
                                    <label>&nbsp;</label>
                                </div>
                                <div class="col-md-10">


                                <button type='button' class='btn btn-action' id="saveBtn"><?php echo __( 'Save Video')?></button>
                            
                            <?php if ( !empty( $video['Video']['id'] ) ): ?>
                            <a href="javascript:void(0)" onclick="$('.modal').modal('hide'); confirmDelete('<?php echo $this->request->base?>/videos/delete/<?php echo $video['Video']['id']?>')" class="button button-caution"><i class="icon-trash"></i> <?php echo __( 'Delete Video')?></a>
                            <?php endif; ?>
                             </div>
                                <div class="clear"></div>
                        </li>
                    </ul>
                    </div>
                    <?php if ( !empty( $video['Video']['id'] ) ): ?>
                    </form>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="error-message" style="display:none;margin-top:10px;"></div>