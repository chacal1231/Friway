<?php $this->Html->scriptStart(array('inline' => false)); ?>
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
        
        if($(window).width() < 992){
            $('#leftnav').modal('hide');
            $('body').scrollTop(0);
        }
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
        $('#list-content .view-more').remove();
        $('#list-content').find('.loading:first').remove();
        $("#list-content").append(data);     
        registerOverlay();
    }); 
}
<?php $this->Html->scriptEnd(); ?>

<?php $this->setNotEmpty('west');?>
<?php $this->start('west'); ?>
	<div class="box2 filter_block">
            <h3 class="visible-xs visible-sm"><?php echo __('Browse')?></h3>
            <div class="box_content">
		<ul class="list2 menu-list" id="browse">
			<li class="current" id="everyone"><a data-url="<?php echo $this->request->base?>/users/ajax_browse/all" href="<?php echo $this->request->base?>/users"><?php echo __('Everyone')?></a></li>
			<?php if (!empty($cuser)): ?>
                        <li><a data-url="<?php echo $this->request->base?>/users/ajax_browse/friends" href="<?php echo $this->request->base?>/users/ajax_browse/friends"><?php echo __('My Friends')?></a></li>
                        <?php endif; ?>
		</ul>
            </div>
	</div>
	
	<?php echo $this->element('hooks', array('position' => 'users_sidebar') ); ?>

        <!-- search form -->
        <?php echo $this->element('user/search_form'); ?>
        <!-- end search form -->

<?php $this->end(); ?>

    <div class="bar-content">
        <div class="content_center full_content p_m_10">
        <?php echo $this->element('hooks', array('position' => 'users_top') ); ?>
            <div class="mo_breadcrumb">
                <h1><?php echo __('People')?></h1>
            </div>
            
            <ul class="users_list" id="list-content">
                    <?php 
                    if ( !empty( $values ) || !empty($online_filter) )
                            echo __('Loading...');
                    else
                            echo $this->element( 'lists/users_list', array( 'more_url' => '/users/ajax_browse/all/page:2' ) );
                    ?>
            </ul>
            <div class="clear"></div>
        </div>
    </div>

