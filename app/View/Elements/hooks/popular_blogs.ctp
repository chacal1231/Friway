<div class="box2">
    <h3><?php echo __('Popular Entries')?></h3>
    <div class="box_content">
        <?php echo $this->element('blocks/blogs_block', array('blogs' => $popular_blogs)); ?>
    </div>
</div>