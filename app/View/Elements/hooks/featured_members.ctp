<?php if ( !empty( $featured_users ) ): ?>
    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo __('Featured Members')?></h3>
        </div>
        <div class="panel-body">
            <?php echo $this->element('blocks/users_block', array( 'users' => $featured_users ) );?>
        </div>
    </div>
<?php endif; ?>