<?php if(Configure::read('Topic.topic_enabled') == 1): ?>
<ul class="topic-content-list">
<?php
$topicHelper = MooCore::getInstance()->getHelper('Topic_Topic');
if (!empty($topics) && count($topics) > 0)
{
    $i = 1;
	foreach ($topics as $topic):
?>
	<li class="full_content p_m_10" <?php if( $i == count($topics) ) echo 'style="border-bottom:0"'; ?>>
            <a href=<?php if ( !empty( $ajax_view ) ): ?>"javascript:void(0)" onclick="loadPage('topics', '<?php echo $this->request->base?>/topics/ajax_view/<?php echo $topic['Topic']['id']?>')"<?php else: ?>"<?php echo $this->request->base?>/topics/view/<?php echo $topic['Topic']['id']?>/<?php echo seoUrl($topic['Topic']['title'])?>"<?php endif; ?>>
               <img width="140" src="<?php echo $topicHelper->getImage($topic, array('prefix' => '150_square'))?>" class="topic-thumb" />
            </a>

        <?php if(!empty($uid) && (($topic['Topic']['user_id'] == $uid ) ||  (!empty($cuser) && $cuser['Role']['is_admin']) ) ): ?>
        <div class="list_option">
                <div class="dropdown">
                    <button id="dLabel" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      <i class="icon-edit"></i>
                    </button>

                    <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                        <?php if ( ( !empty($cuser) && $cuser['Role']['is_admin'] ) ): ?>
                            <?php if ( !$topic['Topic']['pinned'] ): ?>
                            <li><a href='<?php echo $this->request->base?>/topics/do_pin/<?php echo $topic['Topic']['id']?>'><?php echo __( 'Pin Topic')?></a></li>
                            <?php else: ?>
                            <li><a href='<?php echo $this->request->base?>/topics/do_unpin/<?php echo $topic['Topic']['id']?>'><?php echo __( 'Unpin Topic')?></a></li>
                            <?php endif; ?>

                            <?php if ( !$topic['Topic']['locked'] ): ?>
                            <li><a href='<?php echo $this->request->base?>/topics/do_lock/<?php echo $topic['Topic']['id']?>'><?php echo __( 'Lock Topic')?></a></li>
                            <?php else: ?>
                            <li><a href='<?php echo $this->request->base?>/topics/do_unlock/<?php echo $topic['Topic']['id']?>'><?php echo __( 'Unlock Topic')?></a></li>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php if ( ($topic['Topic']['user_id'] == $uid ) || ( !empty($cuser['Role']['is_admin']) ) ): ?>
                        <li><?php echo $this->Html->link(__( 'Edit Topic'), array(
                          'plugin' => 'Topic',
                          'controller' => 'topics',
                          'action' => 'create',
                          $topic['Topic']['id']
                      )); ?></li>
                        <li><a href="javascript:void(0);" onclick="mooConfirm( '<?php echo addslashes(__('Are you sure you want to remove this topic?')) ?>', '<?php echo $this->request->base?>/topics/do_delete/<?php echo $topic['Topic']['id']?>' )"><?php echo __( 'Delete')?></a></li>
                        <li class="seperate"></li>
                        <?php endif; ?>
                        
                         
                    </ul>
                </div>
            </div>
        <?php endif; ?>
		<div class="topic-info">
			<a class="title" href=<?php if ( !empty( $ajax_view ) ): ?>"javascript:void(0)" onclick="loadPage('topics', '<?php echo $this->request->base?>/topics/ajax_view/<?php echo $topic['Topic']['id']?>')"<?php else: ?>"<?php echo $this->request->base?>/topics/view/<?php echo $topic['Topic']['id']?>/<?php echo seoUrl($topic['Topic']['title'])?>"<?php endif; ?>><?php echo h($topic['Topic']['title'])?></a>
			
			<?php if ( $topic['Topic']['pinned'] ): ?>
			<i class="icon-pin icon-small tip" title="<?php echo __( 'Pinned')?>"></i>
			<?php endif; ?>
			<?php if ( $topic['Topic']['attachment'] ): ?>
                        <i class="icon-paper-clip icon-small tip" title="<?php echo __( 'Attached files')?>"></i>
                        <?php endif; ?>
                                    <?php if ( $topic['Topic']['locked'] ): ?>
                        <i class="icon-lock icon-small tip" title="<?php echo __( 'Locked')?>"></i>
                        <?php endif; ?>
                        <div class="extra_info">
                            <?php echo __( 'Last posted by %s', $this->Moo->getName($topic['LastPoster'], false))?>
                            <?php echo $this->Moo->getTime( $topic['Topic']['last_post'], Configure::read('core.date_format'), $utz )?>
    			</div>
			<div class="topic-description-truncate">
                            <div>
                            <?php echo $this->Text->convert_clickable_links_for_hashtags($this->Text->truncate(strip_tags(str_replace(array('<br>','&nbsp;'), array(' ',''), $topic['Topic']['body'])), 200, array('exact' => false)), Configure::read('Topic.topic_hashtag_enabled')) ?>
                            </div>
                            <div class="like-section">
                                <div class="like-action">
                                    
                                    <a href="<?php echo  $this->request->base ?>/topics/view/<?php echo  $topic['Topic']['id'] ?>/<?php echo seoUrl($topic['Topic']['title'])?>">
                                        <i class='icon-comments'></i>&nbsp;<span><?php echo $topic['Topic']['comment_count']?></span>
                                    </a>
                                    <a href="<?php echo  $this->request->base ?>/topics/view/<?php echo  $topic['Topic']['id'] ?>/<?php echo seoUrl($topic['Topic']['title'])?>" class="<?php if (!empty($uid) && !empty($like['Like']['thumb_up'])): ?>active<?php endif; ?>">
                                        <i class="icon-thumbs-up-alt"></i>
                                    </a>
                                    <?php
      $this->MooPopup->tag(array(
             'href'=>$this->Html->url(array("controller" => "likes",
                                            "action" => "ajax_show",
                                            "plugin" => false,
                                            'Topic_Topic',
                                            $topic['Topic']['id'],
                                        )),
             'title' => __('People Who Like This'),
             'innerHtml'=> '<span id="like_count">' . $topic['Topic']['like_count'] . '</span>',
     ));
 ?>

                                    <?php if(empty($hide_dislike)): ?>
                                    <a href="<?php echo  $this->request->base ?>/topics/view/<?php echo  $topic['Topic']['id'] ?>/<?php echo seoUrl($topic['Topic']['title'])?>" class="<?php if (!empty($uid) && isset($like['Like']['thumb_up']) && $like['Like']['thumb_up'] == 0): ?>active<?php endif; ?>">
                                        <i class="icon-thumbs-down-alt"></i>
                                    </a>
                                    
                                    <?php
                                    $this->MooPopup->tag(array(
                                             'href'=>$this->Html->url(array("controller" => "likes",
                                                                            "action" => "ajax_show",
                                                                            "plugin" => false,
                                                                            'Topic_Topic',
                                                                            $topic['Topic']['id'], 1
                                                                        )),
                                             'title' => __('People Who DisLike This'),
                                             'innerHtml'=>  '<span id="dislike_count">' . $topic['Topic']['dislike_count'] . '</span>',
                                    ));
                                    ?>
                                    <?php endif; ?>
 <a href="<?php echo  $this->request->base ?>/topics/view/<?php echo  $topic['Topic']['id'] ?>/<?php echo seoUrl($topic['Topic']['title'])?>">
                                        <i class="icon-share"></i> <span><?php echo  $topic['Topic']['share_count'] ?></span>
                                    </a>
                                   
                                </div>


                            </div>
                        </div>
            <div class="clear"></div>
            <div class="extra_info">
                <?php $this->Html->rating($topic['Topic']['id'],'topics', 'Topic'); ?>
            </div>
		</div>
	</li>
<?php
    $i++;
	endforeach;
}
else
	echo '<div class="clear text-center">' . __( 'No more results found') . '</div>';
?>
<?php if (isset($more_url)&& !empty($more_result)): ?>
    <?php $this->Html->viewMore($more_url) ?>
<?php endif; ?>
</ul>
<?php endif; ?>