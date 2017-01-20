<div class="box2">
    <h3><?php echo __('Popular Groups')?></h3>
    <div class="box_content">
        <?php echo $this->element('blocks/groups_block', array('groups' => $popular_groups)); ?>
    </div>
</div>