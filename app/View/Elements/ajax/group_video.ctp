<?php if($this->request->is('ajax')) $this->setCurrentStyle(4) ?>
<div class="bar-content">
    <div class="content_center">
         <div class="mo_breadcrumb">
            <h1 class="visible-xs visible-sm"><?php echo $groupname?></h1>
            <?php if ( !empty( $is_member ) ): ?>
            <a href="javascript:void(0)" onclick="loadPage('videos', '<?php echo $this->request->base?>/videos/group_create')" class="topButton button button-action button-mobi-top"><?php echo __('Share New Video')?></a>
            <?php endif; ?>
         </div>
        <div class="clear"></div>
            <ul class="list4 albums <?php if ( !empty( $is_member ) ): ?>p_top_15<?php endif; ?>" id="list-content">
                    <?php echo $this->element( 'lists/videos_list', array( 'type' => APP_GROUP ) ); ?>
            </ul>
    </div>
</div>