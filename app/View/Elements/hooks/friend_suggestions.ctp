<?php
if ( !empty($uid) && !empty($friend_suggestions) ):
?>
<div class="box2">
    <h3>
        <?php
      $this->MooPopup->tag(array(
             'href'=>$this->Html->url(array("controller" => "friends",
                                            "action" => "ajax_suggestions",
                                            "plugin" => false,
                                            
                                        )),
             'title' => __('People You May Know'),
             'innerHtml'=> __('People You May Know'),
     ));
 ?>
         </h3>
    <div class="box_content">
        <ul class="list6">
        <?php foreach ($friend_suggestions as $friend): ?>
            <li><?php echo $this->Moo->getItemPhoto(array('User' => $friend['User']),array( 'prefix' => '50_square'), array('class' => 'img_wrapper2 user_avatar_large'))?>
                <div style="margin-left:50px">
                    <?php echo $this->Moo->getName($friend['User'])?><br />
                    <span class="date"><?php echo __n( '%s mutual friend', '%s mutual friends', $friend[0]['count'], $friend[0]['count'] )?></span><br />
                    <?php
      $this->MooPopup->tag(array(
             'href'=>$this->Html->url(array("controller" => "friends",
                                            "action" => "ajax_add",
                                            "plugin" => false,
                                            $friend['User']['id']
                                            
                                        )),
             'title' => sprintf( __('Send %s a friend request'), h($friend['User']['name'])  ),
             'innerHtml'=> __('Add as friend'),
          'id' => 'addFriend_' . $friend['User']['id']
     ));
 ?>
                   
                </div>
            </li>
        <?php endforeach; ?>
        </ul>
        <div class="view-all-link"><a href="<?php echo $this->request->base?>/friends/ajax_suggestions" class="overlay button button-tiny" title="<?php echo __('People You May Know')?>"><?php echo __('View all')?></a></div>
    </div>
</div>
<?php
endif;
?>