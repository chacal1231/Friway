<?php if (Configure::read('Video.video_enabled') == 1): ?>
    <?php
    $videoHelper = MooCore::getInstance()->getHelper('Video_Video');
    ?>
    <?php
    if (!empty($videos) && count($videos) > 0) {
        foreach ($videos as $video):
            ?>
            <li class="video-list-index full_content ">

                <div class="item-content">
                    <a href=<?php if (!empty($ajax_view)): ?>"javascript:void(0)" onclick="loadPage('videos', '<?php echo $this->request->base ?>/videos/ajax_view/<?php echo $video['Video']['id'] ?>')"<?php else: ?>"<?php echo $this->request->base ?>/videos/view/<?php echo $video['Video']['id'] ?>/<?php echo seoUrl($video['Video']['title']) ?>"<?php endif; ?> class="video_cover">
                       <div><img src='<?php echo $videoHelper->getImage($video, array('prefix' => '450')) ?>' /></div>
                    </a>
                    <?php if (($video['User']['id'] == $uid) || (!empty($cuser) && $cuser['Role']['is_admin'] ) || in_array($uid, $video['Video']['admins']) || (!empty($admins) && in_array($uid, $admins) )): ?>
                        <div class="list_option" style="top:2px;right:2px;">
                            <div class="dropdown">
                                <button id="dropdown-edit" data-target="#" data-toggle="dropdown" ><i class="icon-edit"></i></button>
                                <ul role="menu" class="dropdown-menu" aria-labelledby="dropdown-edit" style="float: right;">
                                    <li>
                                        <?php
                                        $this->MooPopup->tag(array(
                                            'href' => $this->Html->url(array("controller" => "videos",
                                                "action" => "group_create",
                                                "plugin" => 'video',
                                                $video['Video']['id']
                                            )),
                                            'title' => __('Edit Video Details'),
                                            'innerHtml' => __('Edit Video'),
                                        ));
                                        ?>
                                    </li>
                                    <li><a href="javascript:void(0)" onclick="mooConfirm('<?php echo addslashes(__('Are you sure you want to remove this video?')) ?>', '<?php echo $this->request->base ?>/videos/delete/<?php echo $video['Video']['id'] ?>')"> <?php echo __('Delete Video') ?></a></li>
                                    <li class="seperate"></li>
                                </ul>
                            </div>
                        </div>
            <?php endif; ?>
                    <div class="video_info">
                        <a href=<?php if (!empty($ajax_view)): ?>"javascript:void(0)" onclick="loadPage('videos', '<?php echo $this->request->base ?>/videos/ajax_view/<?php echo $video['Video']['id'] ?>')"<?php else: ?>"<?php echo $this->request->base ?>/videos/view/<?php echo $video['Video']['id'] ?>/<?php echo seoUrl($video['Video']['title']) ?>"<?php endif; ?>><?php echo h($this->Text->truncate($video['Video']['title'], 60)) ?></a>
                        <div class="extra_info"><?php echo __('Posted by') ?> <?php echo $this->Moo->getName($video['User'], false) ?><?php echo $this->Moo->getTime($video['Video']['created'], Configure::read('core.date_format'), $utz) ?></div>
            <?php $this->Html->rating($video['Video']['id'], 'videos', 'Video'); ?>
                    </div>
                </div>
            </li>
            <?php
        endforeach;
    } else
        echo '<li class="clear text-center" style="width:100%;overflow:hidden">' . __('No more results found') . '</li>';
    ?>
    <?php if (!empty($more_result)): ?>
        <?php $this->Html->viewMore($more_url) ?>
    <?php endif; ?>
<?php endif; ?>