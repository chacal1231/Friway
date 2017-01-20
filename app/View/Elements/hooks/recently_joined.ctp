<?php if ( !empty( $new_users ) ): ?> 
    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo __('Recently Joined')?></h3>
        </div>
        <div class="panel-body">
            <?php echo $this->element('blocks/users_block', array( 'users' => $new_users ));?>
        </div>
    </div>
<?php endif; ?>
