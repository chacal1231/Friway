<?php
if ( empty( $hide_container ) ):
?>
<div class="like-section">
<div class="like-action">
   <a href="javascript:void(0)" onclick="likeIt('<?php echo $type?>', <?php echo $item['id']?>, 1)" class="<?php if ( !empty($uid) && !empty( $like['Like']['thumb_up'] ) ): ?>active<?php endif; ?>">
    <i class="icon-thumbs-up-alt"></i>

    </a>
    <?php
      $this->MooPopup->tag(array(
             'href'=>$this->Html->url(array("controller" => "likes",
                                            "action" => "ajax_show",
                                            "plugin" => false,
                                            $type,
                                            $item['id']
                                        )),
             'title' => __('People Who Like This'),
             'innerHtml'=> '<span id="like_count"> ' . $item['like_count'] . '</span>',
          'data-dismiss'=> 'modal'
     ));
 ?>


    <?php if(empty($hide_dislike)): ?>
    <a href="javascript:void(0)" onclick="likeIt('<?php echo $type?>', <?php echo $item['id']?>, 0)" class="<?php if ( !empty($uid) && isset( $like['Like']['thumb_up'] ) && $like['Like']['thumb_up'] == 0 ): ?>active<?php endif; ?>">
        <i class="icon-thumbs-down-alt"></i>

    </a>
    <?php
      $this->MooPopup->tag(array(
             'href'=>$this->Html->url(array("controller" => "likes",
                                            "action" => "ajax_show",
                                            "plugin" => false,
                                            $type,
                                            $item['id'],1
                                        )),
             'title' => __('People Who DisLike This'),
             'innerHtml'=> '<span id="dislike_count">' . $item['dislike_count'] . '</span>',
          'data-dismiss' => 'modal'
     ));
 ?>
    <?php endif; ?>

	</div>
        <div class="likes" >
        <?php
            $total_users = count($likes);
            $new_likes = array();
            if($total_users > 6):
                for($i = 0; $i <6; $i++):
                    $new_likes[] = $likes[$i];
                endfor;
            else:
                $new_likes = $likes;
            endif;
        ?>
            <span id="like_count2"><?php echo $total_users ?></span> <?php echo __('people liked this') ?>
	    <?php echo $this->element( 'blocks/users_block', array( 'users' => $new_likes ) ); ?>
<?php
endif;
?>
	    
<?php
if ( empty( $hide_container ) ):
?>
	   </div>
  
</div>


<?php
endif;
?>