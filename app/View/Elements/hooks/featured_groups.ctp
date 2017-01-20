<?php if ( !empty( $featured_groups ) ): ?> 
<div class="box2">
    <h3><?php echo __('Featured Groups')?></h3>
    <div class="box_content">
        <?php echo $this->element('blocks/groups_block', array('groups' => $featured_groups)); ?>
    </div>
</div>
<?php endif; ?>