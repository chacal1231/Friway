<div class="box2">
    <h3><?php echo __('Popular Topics')?></h3>
    <div class="box_content">
        <?php echo $this->element('blocks/topics_block', array("topics" => $popular_topics)); ?>
    </div>
</div>