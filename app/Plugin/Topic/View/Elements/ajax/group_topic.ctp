<div class="bar-content">
    <div class="content_center">
         <div class="mo_breadcrumb">
            <h1 class="visible-xs visible-sm"><?php echo h($groupname)?></h1>
            <?php if ( !empty( $is_member ) ): ?> 
            <a href="javascript:void(0)" onclick="loadPage('topics', '<?php echo $this->request->base?>/topics/group_create')" class="topButton button button-action button-mobi-top"><?php echo __( 'Create New Topic')?></a>    
         
            <div class="clear"></div>
            <?php endif; ?>
        </div>
        <ul class="topic-content-list" id="list-content">
                <?php echo $this->element( 'group/topics_list' ); ?>
        </ul>
    </div>
</div>