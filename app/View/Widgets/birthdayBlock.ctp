<?php

if ( !( empty($uid) && Configure::read('core.force_login') ) ):
    
    if(isset($title_enable)&&($title_enable)=== "") $title_enable = false; else $title_enable = true;
    $new_utz = str_replace('/','-',$utz);
    ?>
    <?php if ( !empty( $birthday ) ): ?>
    <div class="box2">
        <?php if($title_enable): ?>
            <h3>
                    <?php if(empty($title)) $title = "Today's Birthdays";?>
                    <?php echo $title;?>

            </h3>
        <?php endif; ?>
        <div class="box_content box_online_user">
            <?php if ( !empty( $birthday ) ): ?>
                <div class="info-birthday">
                    
                   <div class="birthday-item">




                        <?php
      $this->MooPopup->tag(array(
             'href'=>$this->Html->url(array("controller" => "users",
                                            "action" => "ajax_birthday_more",
                                            "plugin" => false,
                                            'utz:' . $new_utz,
                                          
                                        )),
             'title' => '',
             'innerHtml'=> $birthday[0]['User']['name'] . "'s",
          'data-dismiss' => 'modal',
          'target' => 'langModal',
     ));
 ?>

                        <?php if(count($birthday)>1):?>
                            <?php if(count($birthday)==2): ?>
                                &  <?php
      $this->MooPopup->tag(array(
             'href'=>$this->Html->url(array("controller" => "users",
                                            "action" => "ajax_birthday_more",
                                            "plugin" => false,
                                            'utz:' . $new_utz,
                                          
                                        )),
             'title' => '',
             'innerHtml'=> $birthday[1]['User']['name'] . "'s birthday is today!",
          'data-dismiss' => 'false',
          'target' => 'langModal',
     ));
 ?>
                            <?php else: ?>
                                &  <?php
      $this->MooPopup->tag(array(
             'href'=>$this->Html->url(array("controller" => "users",
                                            "action" => "ajax_birthday_more",
                                            "plugin" => false,
                                            'utz:' . $new_utz,
                                          
                                        )),
             'title' => '',
             'innerHtml'=> count($birthday) -1 . " others birthday is today!",
          'data-dismiss' => 'false',
          'target' => 'langModal',
     ));
 ?>
                            <?php endif; ?>
                        <?php else:?>
                            birthday is today!
                        <?php endif; ?>
                    </div>
                </div>
                <div class='clear'></div>
            <?php endif; ?>

        </div>
    </div>
<?php endif; ?>
<?php
endif;
?>
