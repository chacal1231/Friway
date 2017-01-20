<div class="box2">
    <h3><?php echo __('Popular Albums')?></h3>
    <div class="box_content">
        <?php echo $this->element('blocks/photos_block', array('albums' => $popular_albums)); ?>
    </div>
</div>