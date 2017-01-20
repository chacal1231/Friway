<?php if($this->request->is('ajax')):?>
<script type="text/javascript">
<?php else: ?>
<?php echo $this->Html->scriptStart(array('inline' => false)); ?>
<?php endif; ?>
$(document).ready(function(){
    $("#feed-type a").click(function(){
        $('#whats_new').spin('tiny');
        $("#feed-type a").removeClass('current');
        $(this).addClass('current');
        $("#list-content").load(jQuery(this).attr('href'), {noCache: 1}, function(){
            $('#whats_new').spin(false);
            MooResponsive.init();
        });
        return false;
    });
});
<?php if($this->request->is('ajax')):?>
</script>
<?php else: ?>
<?php echo $this->Html->scriptEnd(); ?>
<?php endif; ?>

<?php if(!$this->request->is('ajax')):?>
<div class="p_l_7 home_content_feed">
    <div id="home-content">
<?php endif;?>
        <?php if ( empty( $tab ) ): ?>
        <div class="p_l_7 check-home">
            <?php
            if(isset($title_enable)&&($title_enable)=== "") $title_enable = false; else $title_enable = true;
            ?>
            <div class="mo_breadcrumb">
                <?php if ( !empty( $uid ) || ( empty( $uid ) && !Configure::read('core.hide_activites') ) ): ?>
                <?php if($title_enable): ?>
                    <h1><?php echo __("What's New")?></h1>
                <?php endif; ?>
                <?php endif; ?>
                <?php if ( !empty( $uid ) && Configure::read('core.feed_selection') ): ?>
                <ul class="list7 header-list" id="feed-type">
                    <li><a href="<?php echo $this->request->base?>/activities/ajax_browse/everyone" <?php if ( isset($activity_feed) && $activity_feed == 'everyone' ) echo 'class="current"'; ?>><?php echo __('Everyone')?></a></li>
                    <li><a href="<?php echo $this->request->base?>/activities/ajax_browse/friends" <?php if ( isset($activity_feed) && $activity_feed == 'friends' ) echo 'class="current"'; ?>><?php echo __('Friends & Me')?></a></li>
                </ul>
                <?php endif; ?>
            </div>
            <?php $this->MooActivity->wall($homeActivityWidgetParams)?>
        </div>
        <?php else: ?>
         <?php echo __('Loading...')?>
        <?php endif; ?>
<?php if(!$this->request->is('ajax')):?>        
    </div>
</div>
<?php endif;?>