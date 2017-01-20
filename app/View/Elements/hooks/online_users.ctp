<?php
if ( !( empty($uid) && Configure::read('core.force_login') ) ):
?>
    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title"><a href="<?php echo $this->request->base?>/users/index/online:1"><?php echo __("Who's Online")?> (<?php echo $online['total']?>)</a></h3>
        </div>
        <div class="panel-body">
            <?php echo $this->element('blocks/users_sm_block', array('users' => $online['members'])); ?>
            <?php
            printf( __('There are currently %s and %s online'), __n( '%s member', '%s members', count($online['userids']), count($online['userids']) ), __n( '%s guest', '%s guests', $online['guests'], $online['guests'] ) );
            ?>
        </div>
    </div>
<?php
endif;
?>

