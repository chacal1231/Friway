    <?php if(!empty ($users)): ?>
        <li class="header-filter" style="background-color:#F1F1F1"><span class="col-xs-9"><?php echo  __('PEOPLE'); ?></span><a class="col-xs-3" style="display:block" href="<?php echo  $this->request->base; ?>/search/suggestion/user/<?php echo  $searchVal; ?>"><?php echo  __('View all'); ?> </a></li>
        <?php foreach($users as $user):
            if(!empty($user['User'])){
                $user = $user['User'];
            }
        ?>
            <li class="suggestion-user">
                    <?php echo  $this->Moo->getItemPhoto(array('User' => $user), array('class' => 'user_avatar_small attached-image', 'prefix' => '50_square'));?>
                <div class="suggest-right">
                    <a class="suggest_name" href="<?php echo $this->request->webroot?>users/view/<?php echo $user['id'];?>" >
                        <?php echo  h($this->Text->truncate($user['name'], 40)); ?>
                    </a>
                    <div class="suggest_more_info">
                         <?php echo __n( '%s friend', '%s friends', $user['friend_count'], $user['friend_count'] )?> .
                            <?php echo __n( '%s photo', '%s photos', $user['photo_count'], $user['photo_count'] )?>
                    </div>
                </div>
               
            </li>

        <?php endforeach; ?>

    <?php endif; ?>

    <?php if(!empty($other_suggestion)): ?>
        <?php
            //if(count($users) < 4 )
            //{
                //$length = 8 - count($users);
                //$other_suggestion = array_slice($other_suggestion,0,$length);
                $count = 0;
                if(!empty($users))
                    $count = count($users);
        ?>
        <?php   foreach($other_suggestion as $type=> &$others):?>
                    <?php if(!empty($others)): ?>
                <li class="header-filter" style="background-color:#F1F1F1"><span class="col-xs-9"><?php echo  strtoupper($type); ?></span><a class="col-xs-3" style="display:block" href="<?php echo  $this->request->base; ?>/search/suggestion/<?php echo  $type; ?>/<?php echo  $searchVal; ?>"><?php echo  __('View all'); ?> </a></li>
                <?php foreach($others as &$other): ?>
                            <li class="suggestion-user">
                            <?php if (!empty($other['img'])):?>
               
                                
                                    <a href="<?php echo $this->request->webroot?><?php echo  $other['view_link'].$other['id']?>" class="attached-image" style="width:100%; display:block">
                                        <img class="img_wrapper2" src="<?php echo $this->request->webroot?>uploads/<?php echo $other['img']?>" style="width:45px;">
                                        <div class="suggest-right">
                                            <i class="suggest_name"><?php echo  h($this->Text->truncate($other['title'], 40)); ?></i>
                                            <?php if (isset($other['more_info'])): ?>
                                            <div class="suggest_more_info"><?php echo $other['more_info'] ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </a>
                            <?php else: ?>
                                    <a href="<?php echo $this->request->webroot?><?php echo  $other['view_link'].$other['id']?>" class="attached-image" style="width:100%; display:block">
                                        <img class="img_wrapper2" src="<?php echo $this->request->webroot?>img/noimage/noimage-<?php echo  $type; ?>.png" style="width:45px;">
                                        <div class="suggest-right">
                                            <i class="suggest_name"><?php echo  h($this->Text->truncate($other['title'], 40)); ?></i>
                                            <?php if (isset($other['more_info'])): ?>
                                            <div class="suggest_more_info"><?php echo $other['more_info'] ?></div>
                                            <?php endif; ?>
                                        </div>
                                        
                                    </a>
                            <?php endif; ?>
                                
                            </li>
                            <?php $count++; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php endforeach;
            //}
        ?>
    <?php endif; ?>
    <li class="header-filter viewall" style="text-align: center;background-color:#F1F1F1"><a style="display:block;" href="<?php echo  $this->request->base; ?>/search/index/<?php echo  $searchVal; ?>"><?php echo  __('See All Results'); ?> </a></li>
