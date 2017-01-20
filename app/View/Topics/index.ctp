<?php $this->setNotEmpty('west');?>
<?php $this->start('west'); ?>
	<div class="box2 menu">
            <h3 class="visible-xs visible-sm"><?php echo __('Browse')?></h3>
            <div class="box_content">
                
		<ul class="list2 menu-list" id="browse">
			<li <?php if ( empty( $this->request->named['category_id'] ) ): ?>class="current"<?php endif; ?> id="browse_all"><a data-url="<?php echo $this->request->base?>/topics/ajax_browse/all" href="<?php echo $this->request->base?>/topics"><?php echo __('All Categories')?></a></li>
			<li id="my_topics"><a data-url="<?php echo $this->request->base?>/topics/ajax_browse/my" href="#"><?php echo __('My Topics')?></a></li>
			<li id="friend_topics"><a data-url="<?php echo $this->request->base?>/topics/ajax_browse/friends" href="#"><?php echo __("Friends' Topics")?></a></li>
                        <li class="separate"></li>
                        <li class="cat-header"><?php echo __('Categories')?></li>
                        <?php echo $this->element('lists/categories_list', array('controller' => 'topics'))?>
		</ul>
		<div id="filters" style="margin-top:5px">
                    <?php if(!Configure::read('core.guest_search') && empty($uid)): ?>
                    <?php else: ?>
			<?php echo $this->Form->text( 'keyword', array('placeholder' => __('Enter keyword to search'), 'rel' => 'topics' ) );?>
                    <?php endif; ?>
		</div>
            </div>
	</div>
<?php $this->end(); ?>


<div class="bar-content">  
    <div class="content_center">
    <?php echo $this->element('hooks', array('position' => 'topics_top') ); ?> 
        <div class="mo_breadcrumb">
            <h1><?php echo __('Topics')?></h1>
            <?php 
            if (!empty($uid)):
            ?>
            <a href="<?php echo $this->request->base?>/topics/create" class="button button-action topButton button-mobi-top"><?php echo __('Create New Topic')?></a>
            <?php
            endif;
            ?>
        </div>
	

	
	<ul class="list6 comment_wrapper list-mobile" id="list-content">
		<?php 
		if ( !empty( $this->request->named['category_id'] ) )
			echo $this->element( 'lists/topics_list', array( 'more_url' => '/topics/ajax_browse/category/' . $this->request->named['category_id'] . '/page:2' ) );
		else
			echo $this->element( 'lists/topics_list', array( 'more_url' => '/topics/ajax_browse/all/page:2' ) ); 
		?>
	</ul>	
    </div>
</div>
