<?php if($this->request->is('ajax')) $this->setCurrentStyle(2); ?>
<?php if(!$this->request->is('ajax')):?>
    <?php $this->setNotEmpty('west');?>
    <?php $this->start('west'); ?>
    <?php
        $searchParams = array('tabs'=>((!empty($tabs))? $tabs : ''),'link'=>$this->request->base.'/search/hashtags/'.$keyword);
        $this->requireJs('moocore/search.js','var searchParams ='.json_encode($searchParams,true).';mooSearch.hashInit(searchParams);');
    ?>
    <div class='bar-content'>
        <div class="box2">
            <h3><?php echo __('Search Filters')?></h3>
            <div class='box_content'>
                <ul class="list2" id="global-search-filters">
                    <li <?php echo ($type == 'all')? 'class="current"':'' ?>><a href="<?php echo $this->request->base?>/search/hashtags/<?php echo $keyword?>" class="no-ajax"><i class="icon-th-list"></i> <?php echo __('All Results')?></a></li>
                    <?php if(!empty($activities)): ?>
                    <li <?php echo ($type == 'activities')? 'class="current"':'' ?>><a href="<?php echo $this->request->base?>/search/hashtags/<?php echo $keyword?>/tabs:activities" data-url="<?php echo $this->request->base?>/search/hashtags/<?php echo $keyword?>/activities" id="filter-activities" href="#"><i class="icon-user"></i> <?php echo __('Activities')?></a></li>
                        <?php
                            echo $this->Html->script(array('jquery.fileuploader'),array('inline' => false));
                            echo $this->Html->css(array( 'fineuploader' ));
                        ?>
                    <?php endif; ?>
                    <?php if(!empty($comments) || !empty($activity_comments)): ?>
                    <li <?php echo ($type == 'comments')? 'class="current"':'' ?>><a href="<?php echo $this->request->base?>/search/hashtags/<?php echo $keyword?>/tabs:comments" data-url="<?php echo $this->request->base?>/search/hashtags/<?php echo $keyword?>/comments" id="filter-comments" href="#"><i class="icon-user"></i> <?php echo __('Comments')?></a></li>
                    <?php endif; ?>
                    <?php if ( !empty( $other_suggestion ) ): ?>
                        <?php foreach($other_suggestion as $k => $value):?>
                            <?php
                            if($k == 'events'){
                                $this->Html->script(
                                    array('https://maps.google.com/maps/api/js?sensor=false'), array('block' => 'mooScript')
                                );
                            }
                            ?>
                            <li <?php echo  ($type == $k)? 'class="current"':'' ?>>
                                <a href="<?php echo $this->request->base?>/search/hashtags/<?php echo $keyword?>/tabs:<?php echo  lcfirst($k);?>" data-url="<?php echo $this->request->base?>/search/hashtags/<?php echo $keyword;?>/<?php echo  lcfirst($k);?>" id="filter-<?php echo strtolower($k);?>" href="#">
                                    <i class="<?php echo $value['icon_class']?>"></i> <?php echo $value['header']?>
                                </a>
                            </li>
                        <?php endforeach;?>
                    <?php endif;?>
                </ul>
            </div>
        </div>
    </div>
    <script>//var tabs = '<?php echo (!empty($tabs))? $tabs : false ?>';</script>
    <?php if($this->request->is('ajax')): ?>
        <script>
    <?php else: ?>
        <?php $this->Html->scriptStart(array('inline' => false)) ?>
    <?php endif; ?>

    <?php if($this->request->is('ajax')): ?>
        </script>
    <?php else: ?>
        <?php $this->Html->scriptEnd() ?>
    <?php endif; ?>

    <?php $this->end(); ?>
<?php endif; ?>

<?php if(empty($tabs)): ?>
    <?php if($type != 'all'): ?>
        <?php if(empty($more_link)):  ?>
            <div class="bar-content">

                <div class='content_center'>
                    <div class='mo_breadcrumb'>
                        <h1><?php echo __('Search Results')?> "#<?php echo h($keyword)?>"</h1>
                    </div>
                    <div id="search-content" class='bar-content'>
                        <?php if ( !empty( $result ) ): ?>
                            <ul <?php echo ($type != 'photos')? 'id="list-content"':''; ?> class="search-list-filter list6 comment_wrapper">
                                <?php echo $this->element($element_list_path);?>
                            </ul>
                        <?php else: ?>
                            <p align="center"><?php echo __('Nothing found')?></p>
                        <?php endif; ?>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
        <?php else: ?>
            <?php if ( !empty( $result ) ): ?>
                <?php if(isset($page) && $page == 1): ?>
                    <ul id="list-content" class="list6 comment_wrapper">
                <?php endif; ?>
                <?php echo $this->element($element_list_path);?>
                <?php if(isset($page) && $page == 1): ?>
                    </ul>
                <?php endif; ?>
            <?php else:?>
                <p align="center"><?php echo __('Nothing found')?></p>
            <?php endif; ?>
        <?php endif; ?>
    <?php else: ?>
        <div class="bar-content">
            <div class='content_center'>
                <div class='mo_breadcrumb'>
                    <h1><?php echo __('Search Results')?> "#<?php echo h($keyword)?>"</h1>
                </div>

                <div id="search-content">
                    <?php if ( !empty( $activities ) ): ?>
                        <h2><?php echo __('Activities')?></h2>
                        <div class="search-more">
                            <?php if(empty($filter)): ?>
                                <a href="javascript:void(0)" onclick="mooSearch.globalSearchMore('activities')" class="button"><?php echo __('View More Results')?></a>
                            <?php else: ?>
                                <?php echo $this->Html->link(__('View More Results'),array('controller' => 'search','action' => 'hashtags',$keyword,'activities'),array('class' => 'button')) ?>
                            <?php endif; ?>
                        </div>
                        <div class="clear"></div>
                        <ul id="list-content" class="list6 comment_wrapper">
                            <?php echo $this->element( 'activities' ); ?>
                        </ul>
                    <?php endif; ?>

                    <?php if ( !empty( $comments ) || !empty($activity_comments) ): ?>
                        <h2><?php echo __('Comments')?></h2>
                        <div class="search-more">
                            <?php if(empty($filter)): ?>
                                <a href="javascript:void(0)" onclick="mooSearch.globalSearchMore('comments')" class="button"><?php echo __('View More Results')?></a>
                            <?php else: ?>
                                <?php echo $this->Html->link(__('View More Results'),array('controller' => 'search','action' => 'hashtags',$keyword,'comments'),array('class' => 'button')) ?>
                            <?php endif; ?>
                        </div>
                        <div class="clear"></div>
                        <ul id="list-content" class="list6 comment_wrapper">
                            <?php echo $this->element( 'lists/comments_list' ); ?>
                        </ul>
                    <?php endif; ?>

                    <?php if ( !empty( $other_suggestion ) ): ?>
                        <?php foreach($other_suggestion as $k => $search):?>
                            <h2><?php echo $search['header']?></h2>
                            <div class="search-more">
                                <?php if(empty($filter)): ?>
                                    <a href="javascript:void(0)" onclick="mooSearch.globalSearchMore('<?php echo strtolower($k);?>')" class="button"><?php echo __('View More Results')?></a>
                                <?php else: ?>
                                    <?php echo $this->Html->link(__('View More Results'),array('controller' => 'search','action' => 'hashtags',$keyword,strtolower($k)),array('class' => 'button')) ?>
                                <?php endif; ?>
                            </div>
                            <div class="clear"></div>
                            <ul class="list6">
                                <?php echo $this->element($search['view']);?>
                            </ul>
                        <?php endforeach;?>
                    <?php endif; ?>

                    <?php if(empty($activities) && empty($activity_comments) && empty($comments) && empty($other_suggestion)): ?>
                        <div align="center"><?php echo __('Nothing found')?></div>
                    <?php endif; ?>

                </div>
                <div class="clear"></div>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>