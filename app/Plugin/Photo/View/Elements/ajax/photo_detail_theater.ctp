<script type="text/javascript">
    $(document).ready(function(){
        window.initShareBtn();
    });
</script>
<?php if($this->request->is('ajax')) $this->setCurrentStyle(4) ?>
<?php
$photoHelper = MooCore::getInstance()->getHelper('Photo_Photo');
$nextPhoto = '';
if(!empty($neighbors['next']['Photo']['id'])){
    $nextPhoto = $neighbors['next']['Photo']['id'];
}
else if(empty($neighbors['next']['Photo']['id']) && !empty($neighbors['prev']['Photo']['id'])){
    $nextPhoto = $neighbors['prev']['Photo']['id'];
}
?>
<div data-nextphoto="<?php echo $nextPhoto; ?>" data-thumbfull="<?php echo FULL_BASE_URL . $this->request->webroot .'uploads/photos/thumbnail/'. $photo['Photo']['id']. '/' .$photo['Photo']['thumbnail'];?>" data-taguid="<?php if ( !empty( $this->request->named['uid'])) echo $this->request->named['uid']; else echo 0;?>" data-photoid="<?php echo $photo['Photo']['id']?>" data-photocount="<?php echo $photosAlbumCount;?>" data-page="<?php echo $page;?>" id="photo_wrapper" >
    <div class="info">        
        <a class="toogleThumb" href="javascript:void(0)"><i class="icon-th"></i></a>
        <?php echo __('Photo') ?> <span><?php echo "<span id='photo_position'>" . $photo_position . "</span>" . __(' of ') . $total_photos; ?></span>
        <ul class="theater-photo-option">            
            <li>
                <div class="dropdown">
                    <button id="dLabel" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      <i class="icon-edit"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <?php if ( $can_tag ): ?>
                            <li id="tagPhoto"><a href="javascript:void(0)"><?php echo __( 'Tag Photo')?></a></li>
                        <?php endif; ?>
                        <?php if ($uid == $photo['Photo']['user_id']): ?>
                        <li><a href="javascript:void(0);" id="set_photo_cover" class=""><?php echo __( 'Set as cover')?></a><span id="set_cover"></span></li>
                        <li><a href="javascript:void(0);" id="set_profile_picture"><?php echo __( 'Set as profile picture')?></a><span id="set_avatar"></span></li>
                        <?php endif; ?>

                        <?php if ( ( $uid && $cuser['Role']['is_admin'] ) || ( !empty( $admins ) && in_array( $uid, $admins ) ) ): ?>

                            <li><a data-dismiss="modal" id="delete_photo" href="javascript:void(0)"><?php echo __( 'Delete Photo')?></a></li>
                        <?php endif; ?>
                        <?php if ( !empty( $photo['Photo']['original'] ) ): ?>
                        <li><a href="<?php echo $this->request->webroot?><?php echo $photo['Photo']['original']?>" target="_blank"><i class="icon-download-alt"></i> <?php echo __( 'Download Hi-res')?></a></li>
                        <?php endif; ?>
                        <li>
                            <?php
                                    $this->MooPopup->tag(array(
                                           'href'=>$this->Html->url(array("controller" => "reports",
                                                                          "action" => "ajax_create",
                                                                          "plugin" => false,
                                                                          'photo_photo',
                                                                          $photo['Photo']['id'],
                                                                      )),
                                           'title' => __( 'Report Photo'),
                                           'innerHtml'=> __( 'Report Photo'),
                                        'data-dismiss' => 'modal'
                                   ));
                               ?>
                           </li>
                           
                        <?php if (!empty($photo['Album']['moo_privacy']) && $photo['Album']['moo_privacy'] != PRIVACY_ME): ?>
                        <li>
                            <a href="javascript:void(0);" share-url="<?php echo $this->Html->url(array(
                                  'plugin' => false,
                                  'controller' => 'share',
                                  'action' => 'ajax_share',
                                  'Photo_Photo',
                                  'id' => $photo['Photo']['id'],
                                  'type' => 'photo_item_detail'
                              ), true); ?>" class="shareFeedBtn"><?php echo __('Share'); ?></a>
                        </li>
                        <?php endif; ?>
                        
                    </ul>
                </div>   
            </li>
            
        </ul>
    </div>   
    <div id="tag-wrapper">
        <div class="photo_img">
    	<?php if (!empty($neighbors['next']['Photo']['id'])): ?>
    	<a href="javascript:void(0)" onclick="MooPhotoTheater.showPhoto(<?php echo $neighbors['next']['Photo']['id']?>,true)" id="photo_left_arrow_lg" class="lb_icon">
            <i class="icon-left-open-big icon-4x"></i>
        </a>
        <?php endif;?>
        <?php if (!empty($neighbors['prev']['Photo']['id'])): ?>
        <a href="javascript:void(0)" onclick="MooPhotoTheater.showPhoto(<?php echo $neighbors['prev']['Photo']['id']?>,true)" id="photo_right_arro_lgw" class="lb_icon">
            <i class="icon-right-open-big icon-4x"></i>
        </a>
        <?php endif;?>
        <img style="visibility:hidden" src="<?php echo $photoHelper->getImage($photo, array('prefix' => '1500'));?>" id="photo_src">
        <div id="tag-target"></div>
        <div id="tag-input">
            <?php echo __( "Enter person's name")?>
            <input type="text" id="tag-name">
            <?php echo __( 'Or select a friend')?>
            <div id="friends_list" class="tag_friends_list"></div>
            <a href="#" id="tag-submit" class="button button-action"><?php echo __( 'Submit')?></a>
            <a href="#" id="tag-cancel" class="button"><?php echo __( 'Cancel')?></a>
        </div>
        <?php 
        foreach ( $photo_tags as $tag ): 
        ?>
        <div style="<?php echo $tag['PhotoTag']['style']?>" class="hotspot" id="hotspot-0-<?php echo $tag['PhotoTag']['id']?>"><span>
            <?php
            if ( $tag['PhotoTag']['user_id'] )
                echo $this->Moo->getName( $tag['User'], false );
            else
                echo h($tag['PhotoTag']['value']);
            ?>
        </span></div>
        <?php
        endforeach;
        ?>   
    </div>
    </div>
    
    <div id="lb_description">
        <?php if ( $photo['Photo']['type'] == 'Group_Group' ): ?>
        <a href="<?php echo $this->request->base?>/groups/view/<?php echo $photo['Photo']['target_id']?>/<?php echo seoUrl($photo['Group']['name'])?>"><?php echo __( 'Photos of %s', $photo['Group']['name'])?></a>
        <?php else: ?>
        <a href="<?php echo $this->request->base?>/albums/view/<?php echo $photo['Photo']['target_id']?>/<?php echo seoUrl($photo['Album']['title'])?>"><?php echo h($photo['Album']['title'])?></a>
        <?php endif; ?> 
        
    </div>
</div>
<div class="photo_comments">
    <a data-dismiss="modal" id="photo_close_icon" class="lb_icon"><i class="icon-delete icon-2x topButton"></i></a>
    <div class="photo_right">
            <div class="owner-photo">
                <?php echo $this->Moo->getImage(array('User' => $photo['User']), array('prefix' => '50_square')); ?>
                <div class="owner-info">
                    <?php echo $this->Moo->getName($photo['User']); ?>
                    <div><?php echo $this->Moo->getTime( $photo['Photo']['created'], Configure::read('core.date_format'), $utz )?></div>
                </div>
            </div>
                

                <div class="comment_message" style="margin:4px 0">
                    <?php echo $this->Moo->formatText( $photo['Photo']['caption'], false, true, array('no_replace_ssl' => 1) )?>
                </div>
                <div id="tags" style="margin:5px 0;">
                    <?php if(count($photo_tags)) : ?>
                    <span class="photo_view_info"><?php echo __( 'In this photo')?>: </span>
                    <?php 
                    $count = 0;
                    foreach ( $photo_tags as $tag ): 
                    ?>
                    <span onmouseout="MooPhotoTheater.hideTag('0-<?php echo $tag['PhotoTag']['id']?>')" onmouseover="MooPhotoTheater.showTag('0-<?php echo $tag['PhotoTag']['id']?>')" id="hotspot-item-0-<?php echo $tag['PhotoTag']['id']?>">
                        <?php
                        if ( $tag['PhotoTag']['user_id'] )
                            echo $this->Moo->getName( $tag['User'], false );
                        else
                            echo h($tag['PhotoTag']['value']);

                        if (( $uid && $cuser['Role']['is_admin'] ) || $uid == $tag['PhotoTag']['tagger_id'] || $uid == $tag['PhotoTag']['user_id'] ):
                        ?><a onclick="MooPhotoTheater.removeTag('0-<?php echo $tag['PhotoTag']['id']?>', <?php echo $tag['PhotoTag']['id']?>)" href="javascript:void(0)"><i class="icon-delete cross-icon-sm"></i></a>
                        <?php
                        endif;
                        ?>
                    </span>
                    <?php
                        $count++; 
                    endforeach; 
                    ?>
                    <?php endif; ?>
                </div>
                

                
        
    </div>
    <div class="photo_left">
        
           
                <ul class="photo-theater-comment">
                    <li class="pull-left"><i class="icon-comments"></i> Comment</li>
                     <?php if ( !empty($uid) ): ?>
                    <?php if(empty($hide_dislike)): ?>
                    <li class="pull-right">
                        <a href="javascript:void(0)" id="photo_dislike_count" onclick="likePhoto(<?php echo $photo['Photo']['id']?>, 0)" class="<?php if ( !empty( $uid ) && isset( $like['Like']['thumb_up'] ) && $like['Like']['thumb_up'] == 0 ): ?>active<?php endif; ?>"><i class="icon-thumbs-down-alt"></i></a>
                        <?php
                        $this->MooPopup->tag(array(
                                 'href'=>$this->Html->url(array("controller" => "likes",
                                                                "action" => "ajax_show",
                                                                "plugin" => false,
                                                                'Photo_Photo',
                                                                $photo['Photo']['id'],
                                                                1
                                                            )),
                                 'title' => __( 'People Who Dislike This'),
                                 'innerHtml'=> '<span id="photo_dislike_count2">' . $photo['Photo']['dislike_count'] . '</span>',
                        ));
                        ?>
                    </li>
                    <?php endif; ?>
                    <li class="pull-right">
                        <a href="javascript:void(0)" id="photo_like_count" onclick="likePhoto(<?php echo $photo['Photo']['id']?>, 1)" class="<?php if ( !empty( $uid ) && !empty( $like['Like']['thumb_up'] ) ): ?>active<?php endif; ?>"><i class="icon-thumbs-up-alt"></i></a>
                        <?php
      $this->MooPopup->tag(array(
             'href'=>$this->Html->url(array("controller" => "likes",
                                            "action" => "ajax_show",
                                            "plugin" => false,
                                            'Photo_Photo',
                                            $photo['Photo']['id']
                                        )),
             'title' => __( 'People Who Like This'),
             'innerHtml'=> '<span id="photo_like_count2">' . $photo['Photo']['like_count'] . '</span>',
          'data-dismiss' => 'modal'
     ));
 ?>
                     
                    </li>
                    
                    <?php endif; ?>
                </ul>
        
                <?php 
                if ( !isset( $is_member ) || $is_member  )
                    echo $this->element( 'comment_form', array('commentFormId' => 'theaterPhotoCommentForm', 'commentFormTextId'=> 'theaterPhotoComment', 'target_id' => $photo['Photo']['id'], 'type' => 'Photo_Photo', 'class' => 'commentForm' ) ); 
                else
                    echo __( 'This a group photo. Only group members can leave comment');    
                ?>
                <div class="clear"></div>
                <ul class="list6 comment_wrapper" id="theaterComments">
                <?php echo $this->element('comments', array('blockCommentId' => 'theaterComments'));?>
                </ul>
    </div>
    
    
</div>