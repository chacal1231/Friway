<script>
    $(document).ready(function()
    {
        $(".multi").multiSelect({
            selectAll: false,
            noneSelected: '',
            oneOrMoreSelected: '<?php echo addslashes(__('% selected'))?>'
        });

        $("#searchPeople").click(function(){
            $('#everyone a').spin('tiny');
            $('#browse .current').removeClass('current');
            $('#everyone').addClass('current');

            $.post('<?php echo $this->request->base?>/users/ajax_browse/search', $("#filters").serialize(), function(data){
                $('#everyone a').spin(false);
                $('#list-content').html(data);
                registerOverlay();
            });
        });

        <?php if ( !empty( $values ) || !empty($online_filter) ): ?>
        $('#searchPeople').trigger('click');
        <?php endif; ?>

    });

    function moreUserSearchResults( url )
    {
        $('#list-content .view-more a').spin('small');
        $('#list-content .view-more a').css('color', 'transparent');
        $.post('<?php echo $this->request->base?>' + url, $("#filters").serialize(), function(data){
            $('#list-content .view-more a').spin(false);
            $('#list-content').find('.loading:first').remove();
            $('#list-content .view-more').remove();
            $("#list-content").append(data);
            registerOverlay();
        });
    }
</script>

<?php $this->start('west'); ?>


<?php echo $this->element('hooks', array('position' => 'users_sidebar') ); ?>

<div class="box2">
    <h3><?php echo __('Search')?></h3>
    <div class="box_content">
        <form id="filters">
            <ul class="list6">
                <li><label><?php echo __('Name')?></label><?php echo $this->Form->text('name');?></li>
                <li><label><?php echo __('Email')?></label><?php echo $this->Form->text('email');?></li>
                <li><label><?php echo __('Gender')?></label>
                    <?php echo $this->Form->select('gender', array('Male' => __('Male'), 'Female' => __('Female')), array( 'multiple' => 'multiple', 'class' => 'multi'));?>
                </li>
                <?php echo $this->element( 'custom_fields' ); ?>
                <li><label for="picture"><?php echo __('Profile Picture')?></label> <?php echo $this->Form->checkbox('picture');?> </li>
                <li><label for="online"><?php echo __('Online Users')?></label>
                    <?php
                    if ( !empty( $online_filter ) )
                        echo $this->Form->checkbox('online', array( 'checked' => true ));
                    else
                        echo $this->Form->checkbox('online');
                    ?>
                </li>
                <li style="margin-top:20px"><input type="button" value="<?php echo __('Search')?>" id="searchPeople" class="button button-action"></li>
            </ul>
        </form>
    </div>
</div>
<?php $this->end(); ?>


<?php echo $this->element('hooks', array('position' => 'users_top') ); ?>

<h1><?php echo __('People')?></h1>
<?php
//$this->setCurrentUri($uri['Page']['uri']);
echo $this->currentUri();
?>
