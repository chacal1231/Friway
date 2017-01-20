

<?php if ($this->request->is('ajax')): ?>
    <script>
<?php else: ?>
    <?php $this->Html->scriptStart(array('inline' => false)); ?>
<?php endif; ?>
    jQuery(document).ready(function() {
        registerCrossIcons();
        $('textarea:not(.no-grow)').autogrow();
    });

    function removeActivity(id)
    {
        $.fn.SimpleModal({
            btn_ok: '<?php echo  addslashes(__('OK')) ?>',
            callback: function() {
                $.post('<?php echo  $this->request->base ?>/activities/ajax_remove', {id: id}, function() {
                    $('#activity_' + id).fadeOut('normal', function() {
                        $('#activity_' + id).remove();
                    });
                });
            },
            title: '<?php echo  addslashes(__('Please Confirm')) ?>',
            contents: "<?php echo  addslashes(__('Are you sure you want to remove this activity?')) ?>",
            model: 'confirm', hideFooter: false, closeButton: false
        }).showModal();
    }

    function removeActivityComment(id)
    {
        $.fn.SimpleModal({
            btn_ok: '<?php echo  addslashes(__('OK')) ?>',
            callback: function() {
                $.post('<?php echo  $this->request->base ?>/activities/ajax_removeComment', {id: id}, function() {
                    $('#comment_' + id).fadeOut('normal', function() {
                        $('#comment_' + id).remove();
                    });
                });
            },
            title: '<?php echo  addslashes(__('Please Confirm')) ?>',
            contents: "<?php echo  addslashes(__('Are you sure you want to remove this comment?')) ?>",
            model: 'confirm', hideFooter: false, closeButton: false
        }).showModal();
    }

<?php if ($this->request->is('ajax')): ?>
    </script>
<?php else: ?>
    <?php $this->Html->scriptEnd(); ?>
<?php endif; ?>

<style>
    #list-content li {
        position: relative;
    }
</style>
<?php
if (!isset($data['activities'])) {
    $allActivities = $this->requestAction('/home/getActivities');
    $data['activities'] = $allActivities[1];
}
?>
<?php
foreach ($data['activities'] as $activity):

    $item_type = ( $activity['Activity']['item_type'] == APP_PHOTO && $activity['Activity']['params'] == 'item' ) ? APP_ALBUM : $activity['Activity']['item_type'];

    if (!empty($activity['Content']))
        if ($activity['Activity']['item_type'] == APP_PHOTO && $activity['Activity']['params'] == 'item')
            $obj = $activity['Content'][0]['Album'];
        elseif (!empty($activity['Content'][ucfirst($item_type)]))
            $obj = $activity['Content'][ucfirst($item_type)];
    ?>
    <li id="activity_<?php echo  $activity['Activity']['id'] ?>">
        <div class="feed_main_info">
            <?php
            // delete link available for activity poster, site admin and item admins
            if ($activity['Activity']['user_id'] == $uid || ( $uid && $cuser['Role']['is_admin'] ) || (!empty($data['admins']) && in_array($uid, $data['admins']) )):
                ?>
                <a href="javascript:void(0)" onclick="return removeActivity(<?php echo  $activity['Activity']['id'] ?>)" class="cross-icon"><i class="icon-delete"></i></a>
            <?php endif; ?>
            <div class="activity_feed_image">
                <?php echo  $this->Moo->getItemPhoto(array('User' => $activity['User']),array( 'prefix' => '100_square'), array('class' => 'img_wrapper2 user_avatar_large')) ?>
            </div>

            <div class="activity_feed_content">
                <div class="comment hasDelLink">
                    <div class="activity_text">
                        <?php echo  $this->Moo->getName($activity['User']) ?>
                        <?php
                        if (!empty($activity['TextContent']))
                            echo $this->element('misc/activity_texts/' . $activity['Activity']['action'], array('activity' => $activity));
                        else
                            echo $this->element('misc/activity_texts/general', array('activity' => $activity));
                        ?>
                    </div>
                    <div class="feed_time">
                        <?php if ($activity['Activity']['params'] != 'no-comments'): ?>
                            <a href="<?php echo  $this->request->base ?>/users/view/<?php echo  $activity['Activity']['user_id'] ?>/activity_id:<?php echo  $activity['Activity']['id'] ?>" class="date"><?php echo  $this->Moo->getTime($activity['Activity']['created'], Configure::read('core.date_format'), $utz) ?></a>
                        <?php else: ?>
                            <span class="date"><?php echo  $this->Moo->getTime($activity['Activity']['created'], Configure::read('core.date_format'), $utz) ?></span>
                        <?php endif; ?>
                    </div>


                </div>
            </div>
            <div class="clear"></div>
            <div class="activity_feed_content_text">
                <?php
                if (!empty($activity['Content'])):
                    //pr($activity);
                    echo $this->element('misc/activity_contents/' . $activity['Activity']['action'], array('activity' => $activity));
                else:
                    ?>
                    <div class="comment_message truncate" data-more-text="<?php echo  __('Show More') ?>" data-less-text="<?php echo  __('Show Less') ?>">
                        <?php
                        if (!empty($activity['Activity']['content']) && !$activity['Activity']['query']) {
                            if ($activity['Activity']['action'] == 'wall_post') { // wall post
                                if (!empty($activity['Activity']['params']) && $activity['Activity']['params'] != 'mobile')
                                    echo $this->element('misc/activity_contents/wall_post_link', array('activity' => $activity));
                                else
                                    echo $this->Moo->formatText($activity['Activity']['content']);
                            } else // everything else
                                echo nl2br($this->Text->autoLink($activity['Activity']['content'], array('target' => '_blank')));
                        }
                        ?>
                    </div>
                    <?php
                    endif;
                    //pr($activity);
                    ?>
            </div>
        </div>
        <div class="feed_comment_info">
    <?php if ($activity['Activity']['params'] != 'no-comments'): ?>
                <div class="date">


        <?php if ($activity['Activity']['params'] == 'mobile') echo __('via mobile'); ?>
        <?php if (!isset($data['is_member']) || $data['is_member']): ?>
                <a href="javascript:void(0)" onclick="showCommentForm(<?php echo  $activity['Activity']['id'] ?>)"><i class='icon-comments'></i>&nbsp;<?php echo  __('Comment') ?></a>
                <?php if ($activity['Activity']['params'] == 'item'): ?>
                    &nbsp;<a href="javascript:void(0)" onclick="likeActivity('<?php echo  $item_type ?>', <?php echo  $activity['Activity']['item_id'] ?>, 1)" id="<?php echo  $item_type ?>_l_<?php echo  $activity['Activity']['item_id'] ?>" class="comment-thumb <?php if (!empty($uid) && !empty($activity['Likes'][$uid])): ?>active<?php endif; ?>"><i class="icon-thumbs-up-alt"></i></a>
                        <?php
                        $this->MooPopup->tag(array(
                                 'href'=>$this->Html->url(array("controller" => "likes",
                                                                "action" => "ajax_show",
                                                                "plugin" => false,
                                                                $item_type,
                                                                $activity['Activity']['item_id']
                                                            )),
                                 'title' => __('People Who Like This'),
                                 'innerHtml'=> '<span id="'. $item_type .'_like_' . $activity['Activity']['item_id'] . '">' . $obj['like_count'] . '</span>',
                              'data-dismiss' => 'modal'
                        ));
                        ?>
                        <?php if(empty($hide_dislike)): ?>
                            <a href="javascript:void(0)" onclick="likeActivity('<?php echo  $item_type ?>', <?php echo  $activity['Activity']['item_id'] ?>, 0)" id="<?php echo  $item_type ?>_d_<?php echo  $activity['Activity']['item_id'] ?>" class="comment-thumb <?php if (!empty($uid) && isset($activity['Likes'][$uid]) && $activity['Likes'][$uid] == 0): ?>active<?php endif; ?>"><i class="icon-thumbs-down-alt"></i></a>
                            <?php
                            $this->MooPopup->tag(array(
                                     'href'=>$this->Html->url(array("controller" => "likes",
                                                                    "action" => "ajax_show",
                                                                    "plugin" => false,
                                                                    $item_type,
                                                                    $activity['Activity']['item_id'],1
                                                                )),
                                     'title' => __('People Who Dislike This'),
                                     'innerHtml'=> '<span id="'.  $item_type .'_dislike_'.  $activity['Activity']['item_id'] . '">'.  $obj['dislike_count'] . '</span>',
                            ));
                            ?>
                        <?php endif; ?>
                <?php else: ?>
                            &nbsp;<a href="javascript:void(0)" onclick="likeActivity('activity', <?php echo  $activity['Activity']['id'] ?>, 1)" id="activity_l_<?php echo  $activity['Activity']['id'] ?>" class="comment-thumb <?php if (!empty($uid) && !empty($data['activity_likes']['activity_likes'][$activity['Activity']['id']])): ?>active<?php endif; ?>"><i class="icon-thumbs-up-alt"></i></a>
                            <?php
      $this->MooPopup->tag(array(
             'href'=>$this->Html->url(array("controller" => "likes",
                                            "action" => "ajax_show",
                                            "plugin" => false,
                                            'activity',
                                            $activity['Activity']['id']
                                        )),
             'title' => __('People Who Like This'),
             'innerHtml'=> '<span id="activity_like_'.  $activity['Activity']['id'] . '">' .  $activity['Activity']['like_count'] . '</span>',
            'data-dismiss' => 'modal'
     ));
 ?>
                   <?php if(empty($hide_dislike)): ?>
                            <a href="javascript:void(0)" onclick="likeActivity('activity', <?php echo  $activity['Activity']['id'] ?>, 0)" id="activity_d_<?php echo  $activity['Activity']['id'] ?>" class="comment-thumb <?php if (!empty($uid) && isset($data['activity_likes']['activity_likes'][$activity['Activity']['id']]) && $data['activity_likes']['activity_likes'][$activity['Activity']['id']] == 0): ?>active<?php endif; ?>"><i class="icon-thumbs-down-alt"></i></a>
                            <?php
      $this->MooPopup->tag(array(
             'href'=>$this->Html->url(array("controller" => "likes",
                                            "action" => "ajax_show",
                                            "plugin" => false,
                                            'activity',
                                            $activity['Activity']['id'],1
                                        )),
             'title' => __('People Who Dislike This'),
             'innerHtml'=> '<span id="activity_dislike_'. $activity['Activity']['id'] . '">' . $activity['Activity']['dislike_count'] . '</span>',
     ));
 ?>
                   <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>


                </div>
    <?php else: ?>
    <?php endif; ?>

            <ul class="activity_comments" id="comments_<?php echo  $activity['Activity']['id'] ?>" <?php if (empty($activity['ActivityComment']) && empty($activity['Activity']['like_count']) && empty($activity['ItemComment']) && ( $activity['Activity']['params'] != 'item' || empty($obj['like_count']) )) echo 'style="display:none"'; ?>>
            <?php
            // item comments
            if (!empty($activity['ItemComment'])):
                if (count($activity['ItemComment']) >= 2):
                    ?>
                        <li><i class="icon-comments icon-small"></i> <a href="<?php echo  $this->request->base ?>/<?php echo  $item_type ?>s/view/<?php echo  $activity['Activity']['item_id'] ?>/<?php echo  seoUrl($obj['title']) ?>"><?php echo  __('View all comments') ?></a></li>
                        <?php
                    endif;
                    foreach ($activity['ItemComment'] as $comment):
                        ?>
                        <li id="itemcomment_<?php echo  $comment['Comment']['id'] ?>"><?php echo  $this->Moo->getItemPhoto(array('User' => $comment['User']),array( 'prefix' => '50_square'), array('class' => 'img_wrapper2 user_avatar_small')) ?>
                        <?php
                        // delete link available for activity poster, site admin and admins array
                        if ($comment['Comment']['user_id'] == $uid || ( $uid && $cuser['Role']['is_admin'] ) || (!empty($data['admins']) && in_array($uid, $data['admins']) )):
                            ?>
                                <a class="admin-or-owner-confirm-delete-item-comment" href="javascript:void(0)" data-comment-id="<?php echo $comment['Comment']['id']?>" class="cross-icon"><i class="icon-delete"></i></a>
                            <?php endif; ?>
                            <div class="comment hasDelLink">
                            <?php echo  $this->Moo->getName($comment['User']) ?>
                                <span class="comment_message truncate" data-more-text="<?php echo  __('Show More') ?>" data-less-text="<?php echo  __('Show Less') ?>"><?php echo  $this->Moo->formatText($comment['Comment']['message']) ?></span>
                                <div class="feed_time date">
                                <?php echo  $this->Moo->getTime($comment['Comment']['created'], Configure::read('core.date_format'), $utz) ?>
                                    &nbsp;<a href="javascript:void(0)" onclick="likeActivity('comment', <?php echo  $comment['Comment']['id'] ?>, 1)" id="comment_l_<?php echo  $comment['Comment']['id'] ?>" class="comment-thumb <?php if (!empty($uid) && !empty($data['activity_likes']['item_comment_likes'][$comment['Comment']['id']])): ?>active<?php endif; ?>"><i class="icon-thumbs-up-alt"></i></a>
                                    <?php
      $this->MooPopup->tag(array(
             'href'=>$this->Html->url(array("controller" => "likes",
                                            "action" => "ajax_show",
                                            "plugin" => false,
                                            'comment',
                                            $comment['Comment']['id'],
                                        )),
             'title' => __('People Who Like This'),
             'innerHtml'=> '<span id="comment_like_' . $comment['Comment']['id'] . '">' . $comment['Comment']['like_count'] . '</span>',
          'data-dismiss' => 'modal'
     ));
 ?>
                                    <?php if(empty($hide_dislike)): ?>
                                    <a href="javascript:void(0)" onclick="likeActivity('comment', <?php echo  $comment['Comment']['id'] ?>, 0)" id="comment_d_<?php echo  $comment['Comment']['id'] ?>" class="comment-thumb <?php if (!empty($uid) && isset($data['activity_likes']['item_comment_likes'][$comment['Comment']['id']]) && $data['activity_likes']['item_comment_likes'][$comment['Comment']['id']] == 0): ?>active<?php endif; ?>"><i class="icon-thumbs-down-alt"></i></a>
                                    
                                    <?php
      $this->MooPopup->tag(array(
             'href'=>$this->Html->url(array("controller" => "likes",
                                            "action" => "ajax_show",
                                            "plugin" => false,
                                            'comment',
                                            $comment['Comment']['id'],1
                                        )),
             'title' => __('People Who Dislike This'),
             'innerHtml'=> '<span id="comment_dislike_' . $comment['Comment']['id'] . '">' . $comment['Comment']['dislike_count'] . '</span>',
     ));
 ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </li>
            <?php
        endforeach;
    endif;
    ?>

                <?php
                // activity comments
                if (!empty($activity['ActivityComment'])):
                    if (count($activity['ActivityComment']) > 2):
                        ?>
                        <li id="all_comments_<?php echo  $activity['Activity']['id'] ?>"><i class="icon-comments icon-small"></i> <a href="javascript:void(0)" onclick="showAllComments(<?php echo  $activity['Activity']['id'] ?>)"><?php echo  __('View all %s comments', count($activity['ActivityComment'])) ?></a></li>
                        <?php
                    endif;
                    foreach ($activity['ActivityComment'] as $key => $comment):
                        $class = '';
                        if (count($activity['ActivityComment']) > 2 && $key < count($activity['ActivityComment']) - 2)
                            $class = 'hidden';
                        ?>
                        <li id="comment_<?php echo  $comment['id'] ?>" class="<?php echo  $class ?>"><?php echo  $this->Moo->getItemPhoto(array('User' => $comment['User']),array('class' => 'user_avatar_small', 'prefix' => '50_square'), array('class' => 'user_avatar_small img_wrapper2')) ?>
                        <?php
                        // delete link available for activity poster, site admin and admins array
                        if ($comment['user_id'] == $uid || ( $uid && $cuser['Role']['is_admin'] ) || (!empty($data['admins']) && in_array($uid, $data['admins']) )):
                            ?>
                                <a href="javascript:void(0)" onclick="return removeActivityComment(<?php echo  $comment['id'] ?>)" class="cross-icon"><i class="icon-delete"></i></a>
                            <?php endif; ?>
                            <div class="comment hasDelLink">
                            <?php echo  $this->Moo->getName($comment['User']) ?>
                                <span class="comment_message truncate" data-more-text="<?php echo  __('Show More') ?>" data-less-text="<?php echo  __('Show Less') ?>"><?php echo  $this->Moo->formatText($comment['comment']) ?></span>
                                <div class="feed-time date">
                                <?php echo  $this->Moo->getTime($comment['created'], Configure::read('core.date_format'), $utz) ?>
                                    &nbsp;<a href="javascript:void(0)" onclick="likeActivity('activity_comment', <?php echo  $comment['id'] ?>, 1)" id="activity_comment_l_<?php echo  $comment['id'] ?>" class="comment-thumb <?php if (!empty($uid) && !empty($data['activity_likes']['comment_likes'][$comment['id']])): ?>active<?php endif; ?>"><i class="icon-thumbs-up-alt"></i></a>
                                    <?php
      $this->MooPopup->tag(array(
             'href'=>$this->Html->url(array("controller" => "likes",
                                            "action" => "ajax_show",
                                            "plugin" => false,
                                            'activity_comment',
                                            $comment['id'],
                                        )),
             'title' => __('People Who Like This'),
             'innerHtml'=> '<span id="activity_comment_like_' .  $comment['id'] .'">'. $comment['like_count'].'</span>',
          'data-dismiss' => 'modal'
     ));
 ?>
                                    <?php if(empty($hide_dislike)): ?>
                                    <a href="javascript:void(0)" onclick="likeActivity('activity_comment', <?php echo  $comment['id'] ?>, 0)" id="activity_comment_d_<?php echo  $comment['id'] ?>" class="comment-thumb <?php if (!empty($uid) && isset($data['activity_likes']['comment_likes'][$comment['id']]) && $data['activity_likes']['comment_likes'][$comment['id']] == 0): ?>active<?php endif; ?>"><i class="icon-thumbs-down-alt"></i></a> <span id="activity_comment_dislike_<?php echo  $comment['id'] ?>"><?php echo  $comment['dislike_count'] ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </li>
            <?php
        endforeach;
    endif;
    ?>

                <?php
                // comment form
                if ((!isset($data['is_member']) || $data['is_member'] ) && $activity['Activity']['params'] != 'no-comments' && empty($activity['Content']['Topic']['locked'])):
                    ?>
                    <li id="newComment_<?php echo  $activity['Activity']['id'] ?>">
                    <?php echo  $this->Moo->getItemPhoto(array('User' => $cuser),array( 'prefix' => '50_square'), array('class' => 'img_wrapper2 user_avatar_small')) ?>
                        <div class="comment">
                            <textarea class="commentBox" onfocus="showCommentButton(<?php echo  $activity['Activity']['id'] ?>)" placeholder="<?php echo  __('Write a comment...') ?>" id="commentForm_<?php echo  $activity['Activity']['id'] ?>"></textarea>
                            <div class="commentButton" id="commentButton_<?php echo  $activity['Activity']['id'] ?>">
        <?php if (!empty($uid)): ?>
                                    <a href="javascript:void(0)"  <?php if ( $activity['Activity']['params'] == 'item' ): ?> class="btn btn-action  viewer-submit-item-comment" data-item-type="<?php echo $item_type?>" data-activity-item-id="<?php echo $activity['Activity']['item_id']?>" data-activity-id="<?php echo $activity['Activity']['id']?>" <?php else: ?> class="btn btn-action  viewer-submit-comment" data-activity-id="<?php echo $activity['Activity']['id']?>" <?php endif; ?>><?php echo __('Comment')?></a>
        <?php else: ?>
                                    <?php echo  __('Please login or register') ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </li>
        <?php
    endif;
    ?>
            </ul>
        </div>
    </li>
    <?php
endforeach;
?>
<?php if ($data['bIsACtivityloadMore'] > 0): ?>

    <?php $this->Html->viewMore($data['more_url']) ?>
<?php endif; ?>