<?php echo $this->Html->script(array('jquery.fileuploader'),array('inline' => false)); ?>
<?php echo $this->Html->css(array( 'fineuploader' )); ?>
<?php $this->requireJs('moocore/search.js','mooSearch.init();'); ?>
<?php $this->setNotEmpty('west');?>
<?php $this->start('west'); ?>

<div class='bar-content'>
    <div class="box2">
        <h3><?php echo __('Search Filters')?></h3>
        <div class='box_content'>
            <ul class="list2" id="global-search-filters">
                <li class="current"><a href="<?php echo $this->request->base?>/search/index/<?php echo $keyword?>" class="no-ajax"><i class="icon-th-list"></i> <?php echo __('All Results')?></a></li>
                <li><a data-url="<?php echo $this->request->base?>/search/suggestion/user/<?php echo $keyword?>" id="filter-users" href="#"><i class="icon-user"></i> <?php echo __('People')?></a></li>
                <?php if ( !empty( $searches ) ): ?>
                    <?php foreach($searches as $k => $search):?>
                        <?php
                        if($k == 'events' || $k == 'Event' ){
                            $this->requireJs('https://maps.google.com/maps/api/js?sensor=false');
                        }
                        ?>
                        <li>
                            <a data-url="<?php echo $this->request->base?>/search/suggestion/<?php echo  lcfirst($k);?>/<?php echo $keyword;?>" id="filter-<?php echo strtolower($k);?>s" href="#">
                                <i class="<?php echo $search['icon_class']?>"></i> <?php echo $search['header']?>
                            </a>
                        </li>
                    <?php endforeach;?>
                    
                <?php endif;?>
            </ul>
        </div>
    </div>
</div>

<?php $this->end(); ?>
<div class="bar-content">

    <div class='content_center'>
        <div class='mo_breadcrumb'>
             <h1><?php echo __('Search Results')?> "<?php echo h($keyword)?>"</h1>
        </div>
        
        <div id="search-content">
            <?php if ( !empty( $users ) ): ?>
            <h2><?php echo __('People')?></h2>
            <div class="search-more">
                <a href="javascript:void(0)" onclick="mooSearch.globalSearchMore('users')" class="button"><?php echo __('View More Results')?></a>
            </div>  
            <div class="clear"></div>
            <ul class="users_list">
                <?php echo $this->element( 'lists/users_list' ); ?>
            </ul>
            <?php endif; ?>
            <?php $emptyResult = true; ?>
            <?php if ( !empty( $searches ) ): ?>
                <?php foreach($searches as $k => $search):?>
                    <?php if(!empty($search['notEmpty'])): ?>
                        <h2><?php echo $search['header']?></h2>
                        <div class="search-more">
                            <a href="javascript:void(0)" onclick="mooSearch.globalSearchMore('<?php echo strtolower($k);?>s')" class="button"><?php echo __('View More Results')?></a>
                        </div>
                         <div class="clear"></div>
                         <ul class="list6">
                        <?php echo $this->element($search['view'], array(), array('plugin' => $k));?>
                         </ul>
                         <?php $emptyResult = false; ?>
                    <?php endif; ?>
                <?php endforeach;?>
            <?php endif; ?>
                         
            <?php if($emptyResult): ?>
            <div align="center"><?php echo __('No result found')?></div>
            <?php endif; ?>
            
        </div>
        <div class="clear"></div>
    </div>
</div>
