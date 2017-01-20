<?php 
echo $this->Html->css(array('jquery.mp'), null, array('inline' => false));
echo $this->Html->script(array('jquery.mp.min'), array('inline' => false)); 
?>

<?php $this->Html->scriptStart(array('inline' => false)); ?>
$(document).ready(function(){
    registerImageOverlay();
    $(".sharethis").hideshare({media: '<?php echo FULL_BASE_URL . $this->request->webroot?>img/og-image.png', linkedin: false});
});
<?php $this->Html->scriptEnd(); ?>


<?php $this->setNotEmpty('west');?>
<?php $this->start('west'); ?>
	<div class="box2">
		<h3><?php echo __('Topic Creator')?></h3>
		<div class="box_content">
		    <?php echo $this->element('misc/user_mini', array('user' => $topic['User'])); ?>
		</div>
	</div>
	
	<?php if ( !empty( $files ) ): ?>
	<div class="box2">
            <div class="box_content">
		<h3><?php echo __('Attachments')?></h3>
		<div class="box_content">
                    <ul class="list6 list6sm">
                    <?php foreach ($files as $attachment): ?>     
                        <li><i class="icon-attach"></i><a href="<?php echo $this->request->base?>/attachments/download/<?php echo $attachment['Attachment']['id']?>"><?php echo $attachment['Attachment']['original_filename']?></a> <span class="date">(<?php echo __n( '%s download', '%s downloads', $attachment['Attachment']['downloads'], $attachment['Attachment']['downloads'] )?>)</span></li>
                    <?php endforeach; ?>
                    </ul>
                </div>
            </div>
	</div>
    <?php endif; ?>
    
    <?php echo $this->element('hooks', array('position' => 'topic_detail_sidebar') ); ?> 
	
	<div class="box2">
		<h3><?php echo __('Tags')?></h3>
		<div class="box_content">
		    <?php echo $this->element( 'blocks/tags_item_block' ); ?>
		</div>
	</div>
	
	
	
	<div class="box2">
            <div class="box_content">
		<ul class="list6 list6sm">
			<?php if ( ( !empty($cuser) && $cuser['Role']['is_admin'] ) ): ?>
				<?php if ( !$topic['Topic']['pinned'] ): ?>
				<li><a href='<?php echo $this->request->base?>/topics/do_pin/<?php echo $topic['Topic']['id']?>'><?php echo __('Pin Topic')?></a></li>
				<?php else: ?>
				<li><a href='<?php echo $this->request->base?>/topics/do_unpin/<?php echo $topic['Topic']['id']?>'><?php echo __('Unpin Topic')?></a></li>
				<?php endif; ?>
				
				<?php if ( !$topic['Topic']['locked'] ): ?>
                <li><a href='<?php echo $this->request->base?>/topics/do_lock/<?php echo $topic['Topic']['id']?>'><?php echo __('Lock Topic')?></a></li>
                <?php else: ?>
                <li><a href='<?php echo $this->request->base?>/topics/do_unlock/<?php echo $topic['Topic']['id']?>'><?php echo __('Unlock Topic')?></a></li>
                <?php endif; ?>
			<?php endif; ?>
			<?php if ($uid == $topic['Topic']['user_id'] || ( !empty($cuser) && $cuser['Role']['is_admin'] ) || ( !empty($admins) && in_array($uid, $admins) ) ): ?>
			<li><a href='<?php echo $this->request->base?>/topics/create/<?php echo $topic['Topic']['id']?>'><?php echo __('Edit Topic')?></a></li>
			<?php endif; ?>
			<li>
                            <?php
      $this->MooPopup->tag(array(
             'href'=>$this->Html->url(array("controller" => "reports",
                                            "action" => "ajax_create",
                                            "plugin" => false,
                                            'topic_topic',
                                            $topic['Topic']['id']
                                        )),
             'title' => __('Report Topic'),
             'innerHtml'=> __('Report Topic'),
     ));
 ?>
                            </li>
			<li><a href="#" class="sharethis"><?php echo __('Share This')?></a></li>
		</ul>	
            </div>
	</div>
<?php $this->end();?>

<!--Begin Center-->
<div class="bar-content full_content p_m_10">
    <div class="content_center">
	<div class="comment_message post_body">
	    <h1><?php echo h($topic['Topic']['title'])?></h1>
	    <?php echo $this->Moo->cleanHtml( $topic['Topic']['body'] )?>
	    <?php if ( !empty( $pictures ) ): ?>
            <div class='topic_attached_file'>
                <div class="date"><?php echo __('Attached Images')?></div>
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
	    <div class="date"><?php echo __('Posted in')?> <a href="<?php echo $this->request->base?>/topics/index/<?php echo $topic['Topic']['category_id']?>/<?php echo seoUrl($topic['Category']['name'])?>"><strong><?php echo $topic['Category']['name']?></strong></a> <?php echo $this->Moo->getTime($topic['Topic']['created'], Configure::read('core.date_format'), $utz)?></div>
	</div>

	
    </div>
</div>
<div class="bar-content full_content p_m_10">
    <div class="content_center">
        <?php echo $this->element('likes', array('item' => $topic['Topic'], 'type' => APP_TOPIC)); ?>
    </div>
</div>
<div class="bar-content full_content p_m_10">
    <div class="content_center">
        <h2><?php echo __('Replies')?> (<span id="comment_count"><?php echo $topic['Topic']['comment_count']?></span>)</h2>
	<ul class="list6 comment_wrapper" id="comments">
	<?php echo $this->element('comments');?>
	</ul>
        <?php 
	if ( $topic['Topic']['locked'] )
        echo '<i class="icon icon-lock icon-small"></i> ' . __('This topic has been locked');
        else
	    echo $this->element( 'comment_form', array( 'target_id' => $topic['Topic']['id'], 'type' => APP_TOPIC ) ); 
	?>
    </div>
</div>
