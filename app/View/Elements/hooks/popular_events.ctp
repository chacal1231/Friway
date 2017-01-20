<div class="box2">
    <h3><?php echo __('Popular Events')?></h3>
    <div class="box_content">
        <?php echo $this->element( 'blocks/events_block', array( 'events' => $popular_events ) ); ?>
    </div>
</div>