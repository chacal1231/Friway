<?php if($this->request->is('ajax')): ?>
<script>
<?php else: ?>
<?php  $this->Html->scriptStart(array('inline' => false));   ?>
<?php endif; ?>

function deleteTopic(topic_id)
{
	$.fn.SimpleModal({
        btn_ok: '<?php echo addslashes(__( 'OK'))?>',        
        callback: function(){
            $.post( '<?php echo $this->request->base?>/topics/ajax_delete/' + topic_id, function(data){ 
                loadPage('topics', '<?php echo $this->request->base?>/topics/browse/group/<?php echo $group_id?>');
                
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
<?php
if(Configure::read('Topic.topic_enabled') == 1):
$topicHelper = MooCore::getInstance()->getHelper('Topic_Topic');
if (!empty($topics) && count($topics) > 0) {
    $i = 1;
    foreach ($topics as $topic):
        ?>
        <li class="full_content p_m_10" <?php if ($i == count($topics)) echo 'style="border-bottom:0"'; ?>>
            <a href=<?php if (!empty($ajax_view)): ?>"javascript:void(0)" onclick="loadPage('topics', '<?php echo  $this->request->base ?>/topics/ajax_view/<?php echo  $topic['Topic']['id'] ?>')"<?php else: ?>"<?php echo  $this->request->base ?>/topics/view/<?php echo  $topic['Topic']['id'] ?>/<?php echo  seoUrl($topic['Topic']['title']) ?>"<?php endif; ?>>
            <img width="140" src="<?php echo $topicHelper->getImage($topic, array('prefix' => '150_square'))?>" class="topic-thumb" />
            </a>
        <?php if(!empty($uid) && (($topic['Topic']['user_id'] == $uid ) ||  (!empty($cuser) && $cuser['Role']['is_admin']) || in_array($uid, $topic['Topic']['admins']) ) ): ?>

            <div class="list_option">
                <div class="dropdown">
                    <button id="dLabel" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="icon-edit"></i>
                    </button>

                    <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                        <?php if (!empty($cuser['Role']['is_admin']) || in_array($uid, $topic['Topic']['admins']) ): ?>
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
                                
                        <?php if ($uid == $topic['Topic']['user_id'] || ( !empty($cuser) && $cuser['Role']['is_admin'] ) || in_array($uid, $topic['Topic']['admins']) ): ?>
                        <li><a href="javascript:void(0);" onclick="loadPage('topics', '<?php echo $this->request->webroot?>topics/group_create/<?php echo $topic['Topic']['id']?>')"><?php echo __( 'Edit Topic'); ?></a></li>
                        <li><a href="javascript:void(0);" onclick="deleteTopic(<?php echo $topic['Topic']['id']?>)"><?php echo  __( 'Delete') ?></a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>
            <div class="topic-info">
                <a class="title" href=<?php if (!empty($ajax_view)): ?>"javascript:void(0)" onclick="loadPage('topics', '<?php echo  $this->request->base ?>/topics/ajax_view/<?php echo  $topic['Topic']['id'] ?>')"<?php else: ?>"<?php echo  $this->request->base ?>/topics/view/<?php echo  $topic['Topic']['id'] ?>/<?php echo  seoUrl($topic['Topic']['title']) ?>"<?php endif; ?>><?php echo  h($topic['Topic']['title']) ?></a>
                &nbsp;
                <?php if ($topic['Topic']['pinned']): ?>
                    <i class="icon-pin icon-small tip" title="<?php echo  __( 'Pinned') ?>"></i>
                <?php endif; ?>
                <?php if ($topic['Topic']['attachment']): ?>
                    <i class="icon-paper-clip icon-small tip" title="<?php echo  __( 'Attached files') ?>"></i>
                <?php endif; ?>
                <?php if ($topic['Topic']['locked']): ?>
                    <i class="icon-lock icon-small tip" title="<?php echo  __( 'Locked') ?>"></i>
                    <?php endif; ?>
                <div class="extra_info">
                    <?php echo  __( 'Last posted by %s', $this->Moo->getName($topic['LastPoster'], false)) ?>
        <?php echo  $this->Moo->getTime($topic['Topic']['last_post'], Configure::read('core.date_format'), $utz) ?>
                </div>
                <div class="topic-description-truncate">
                    <div>
                    <?php echo  $this->Text->convert_clickable_links_for_hashtags($this->Text->truncate(strip_tags(str_replace(array('<br>', '&nbsp;'), array(' ', ''), $topic['Topic']['body'])), 85, array('exact' => false)), Configure::read('Topic.topic_hashtag_enabled')) ?>
                    </div>
                    <div class="like-section">
                        <div class="like-action">

                            <a href="<?php echo  $this->request->base ?>/topics/view/<?php echo  $topic['Topic']['id'] ?>/#comments">
                                <i class='icon-comments'></i>&nbsp;<span><?php echo $topic['Topic']['comment_count']?></span>
                            </a>
                            <a href="javascript:void(0)" class="<?php if (!empty($uid) && !empty($like['Like']['thumb_up'])): ?>active<?php endif; ?>">
                                <i class="icon-thumbs-up-alt"></i>
                            </a>
                            <a href="javascript:void(0)" class="" title="<?php echo  __('People Who Like This') ?>">
                                <span id="like_count"><?php echo  $topic['Topic']['like_count'] ?></span>
                            </a>

                            <?php if(empty($hide_dislike)): ?>
                            <a href="javascript:void(0)" class="<?php if (!empty($uid) && isset($like['Like']['thumb_up']) && $like['Like']['thumb_up'] == 0): ?>active<?php endif; ?>">
                                <i class="icon-thumbs-down-alt"></i>
                            </a>
                            <a href="javascript:void(0)" class="" title="<?php echo  __('People Who DisLike This') ?>">
                                <span id="dislike_count"><?php echo  $topic['Topic']['dislike_count'] ?></span>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </li>
        <?php
        $i++;
    endforeach;
} else
    echo '<div class="clear text-center">' . __( 'No more results found') . '</div>';
?>

<?php if (!empty($more_result)): ?>
    <?php $this->Html->viewMore($more_url) ?>
<?php endif; endif; ?>