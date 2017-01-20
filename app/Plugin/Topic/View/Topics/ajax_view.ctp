<?php if($this->request->is('ajax')): ?>
<script>
<?php else: ?>
<?php  $this->Html->scriptStart(array('inline' => false));   ?>
<?php endif; ?>

function deleteTopic()
{
	$.fn.SimpleModal({
        btn_ok: '<?php echo addslashes(__( 'OK'))?>',        
        callback: function(){
            $.post( '<?php echo $this->request->base?>/topics/ajax_delete/<?php echo $topic['Topic']['id']?>', function(data){ 
                loadPage('topics', '<?php echo $this->request->base?>/topics/browse/group/<?php echo $this->request->data['group_id']?>');
                
                if ( $("#group_topics_count").html() != '0' )
                    $("#group_topics_count").html( parseInt($("#group_topics_count").html()) - 1 );
            });     
        },
        title: '<?php echo addslashes(__( 'Please Confirm'))?>',
        contents: '<?php echo addslashes(__( 'Are you sure you want to remove this topic?'))?>',
        model: 'confirm', hideFooter: false, closeButton: false        
    }).showModal();
}

<?php if($this->request->is('ajax')): ?>
</script>
<?php else: ?>
<?php $this->Html->scriptEnd();  ?>
<?php endif; ?>

<div class="bar-content full_content p_m_10">
    <div class="content_center">
        <div class="title_center">
             <h2><?php echo h($topic['Topic']['title']); ?></h2>
        </div>
        <div class="date"><?php echo __( 'Posted by %s', $this->Moo->getName($topic['User']))?> <?php echo $this->Moo->getTime($topic['Topic']['created'], Configure::read('core.date_format'), $utz)?></div>
         <div class="bottom_options likes">
            <?php if (!empty($uid)): ?>
             <span class="dropdown" data-buttons="dropdown">
                <a data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" href="#" class="button button-tiny"><?php echo __( 'Actions')?> <i class="icon-caret-down"></i></a>
                <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                    <?php if ($uid == $topic['Topic']['user_id'] || ( !empty($cuser) && $cuser['Role']['is_admin'] ) || in_array($uid, $admins) ): ?>
                    <li><a href='javascript:void(0)' onclick="loadPage('topics', '<?php echo $this->request->base?>/topics/group_create/<?php echo $topic['Topic']['id']?>')"><?php echo __( 'Edit Topic')?></a></li>
                    <li><a href="javascript:void(0);" onclick="deleteTopic()"><?php echo  __( 'Delete') ?></a></li>
                    <?php endif; ?>
                    <?php if (!empty($cuser['Role']['is_admin']) || in_array($uid, $admins) ): ?>
                        <?php if ( !$topic['Topic']['pinned'] ): ?>
                        <li><a href="<?php echo $this->request->base?>/topics/do_pin/<?php echo $topic['Topic']['id']?>"><?php echo __( 'Pin Topic')?></a></li>
                        <?php else: ?>
                        <li><a href="<?php echo $this->request->base?>/topics/do_unpin/<?php echo $topic['Topic']['id']?>"><?php echo __( 'Unpin Topic')?></a></li>
                        <?php endif; ?>

                        <?php if ( !$topic['Topic']['locked'] ): ?>
                        <li><a href="<?php echo $this->request->base?>/topics/do_lock/<?php echo $topic['Topic']['id']?>"><?php echo __( 'Lock Topic')?></a></li>
                        <?php else: ?>
                        <li><a href="<?php echo $this->request->base?>/topics/do_unlock/<?php echo $topic['Topic']['id']?>"><?php echo __( 'Unlock Topic')?></a></li>
                        <?php endif; ?>     
                    <?php endif; ?>
                    <li>
                        <?php
      $this->MooPopup->tag(array(
             'href'=>$this->Html->url(array("controller" => "reports",
                                            "action" => "ajax_create",
                                            "plugin" => false,
                                            'Topic_Topic',
                                            $topic['Topic']['id']
                                        )),
             'title' => __( 'Report Topic'),
             'innerHtml'=> __( 'Report Topic'),
     ));
 ?>
                          </li>
                          
                          <?php if ($topic['Group']['moo_privacy'] == PRIVACY_PUBLIC): ?>
                          <li>
                               <a href="javascript:void(0);" share-url="<?php echo $this->Html->url(array(
                                    'plugin' => false,
                                    'controller' => 'share',
                                    'action' => 'ajax_share',
                                    'Topic_Topic',
                                    'id' => $topic['Topic']['id'],
                                    'type' => 'topic_item_detail'
                                ), true); ?>" class="shareFeedBtn"><?php echo __('Share'); ?></a>
                           </li>
                           <?php endif; ?>
                </ul>   
            </span>   
            <?php endif; ?>  

        </div>
    <div class="clear"></div>
    <div class="comment_message" style="margin:5px 0">
        <?php echo $this->Moo->cleanHtml($this->Text->convert_clickable_links_for_hashtags( $topic['Topic']['body'] , Configure::read('Topic.topic_hashtag_enabled')))?>

        <?php if ( !empty( $pictures ) ): ?>
            <div class='topic_attached_file'>
                <div class="date"><?php echo __( 'Attached Images')?></div>
                <ul class="list4 p_photos ">
                    <?php foreach ($pictures as $p): ?>
                        <li class='col-xs-6 col-ms-4 col-md-3' >
                            <div class="p_2">
                                <a style="background-image:url(<?php echo $this->request->webroot?>uploads/attachments/t_<?php echo $p['Attachment']['filename']?>)" href="<?php echo $this->request->webroot?>uploads/attachments/<?php echo $p['Attachment']['filename']?>" class="attached-image layer_square"></a>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div class='clear'></div>
            </div>
        <?php endif; ?>

        <?php if ( !empty( $files ) ): ?>
        <div style="margin:10px 0">
            <div class="date"><?php echo __( 'Attached Files')?></div>
            <ul class="list6 list6sm">
            <?php foreach ($files as $attachment): ?>     
                <li><i class="icon-attach"></i><a href="<?php echo $this->request->base?>/attachments/download/<?php echo $attachment['Attachment']['id']?>"><?php echo $attachment['Attachment']['original_filename']?></a> <span class="date">(<?php echo __n('%s download', '%s downloads', $attachment['Attachment']['downloads'], $attachment['Attachment']['downloads'] )?>)</span></li>
            <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
    </div>
    

   
</div>
</div>
<div class="bar-content full_content p_m_10">
    <div class="content_center">
        <?php echo $this->element('likes', array('likes' => $likes,'item' => $topic['Topic'], 'type' => 'Topic_Topic', 'hide_container' => false)); ?>
    </div>
</div>
<div class="bar-content full_content p_m_10">
    <div class="content_center">
        <h2><?php echo __( 'Replies (%s)', $topic['Topic']['comment_count'])?></h2>
		<?php 
        if ( !isset( $is_member ) || $is_member  )
            if ( $topic['Topic']['locked'] )
                echo '<i class="icon icon-lock icon-small"></i> ' . __( 'This topic has been locked');
            else
                   echo $this->element( 'comment_form', array( 'target_id' => $topic['Topic']['id'], 'type' => 'Topic_Topic' ) ); 
        else
                echo __( 'This a group topic. Only group members can leave comment');		
        ?>
        <ul class="list6 comment_wrapper" id="comments">
        <?php echo $this->element('comments');?>
        </ul>
    </div>
</div>