<?php
$groupHelper = MooCore::getInstance()->getHelper('Group_Group');
echo $this->Html->css(array('token-input', 'fineuploader', 'jquery.mp'), null, array('inline' => false));
echo $this->Html->script(array( 'jquery.tokeninput', 'tinymce/tinymce.min',	'jquery.fileuploader', 'jquery.mp.min'), array('inline' => false));
?>
<?php if($this->request->is('ajax')): ?>
<script>
<?php else: ?>
<?php $this->Html->scriptStart(array('inline' => false)); ?>
<?php endif; ?>
function removeMember(id)
{
	$.fn.SimpleModal({
        btn_ok: '<?php echo addslashes(__( 'OK'))?>',
        callback: function(){
            $.post('<?php echo $this->request->base?>/groups/ajax_remove_member', {id: id}, function() {
                $('#member_'+id).fadeOut();

                if ( $("#group_user_count").html() != '0' )
                    $("#group_user_count").html( parseInt($("#group_user_count").html()) - 1 );
            });
        },
        title: '<?php echo addslashes(__( 'Please Confirm'))?>',
        contents: "<?php echo addslashes(__( 'Are you sure you want to remove this member?'))?>",
        model: 'confirm', hideFooter: false, closeButton: false
    }).showModal();
	return false;
}

function changeAdmin(id, type)
{
	var msg = "<?php echo addslashes(__( 'Are you sure you want to make this member a group admin?'))?>";
	if ( type == 'remove' )
	   msg = "<?php echo addslashes(__( 'Are you sure you want to demote this group admin?'))?>";

	$.fn.SimpleModal({
        btn_ok: '<?php echo addslashes(__( 'OK'))?>',
        btn_cancel: '<?php echo addslashes(__( 'Cancel'))?>',
        callback: function(){
            $.post('<?php echo $this->request->base?>/groups/ajax_change_admin', {id: id, type: type}, function() {
                //$('#teams').trigger('click');
                window.location.reload();
            });
        },
        title: '<?php echo addslashes(__( 'Please Confirm'))?>',
        contents: msg,
        model: 'confirm', hideFooter: false, closeButton: false
    }).showModal();
	return false;
}

function inviteMore()
{
    $('#themeModal .modal-content').load('<?php echo $this->request->base?>/groups/ajax_invite/<?php echo $group['Group']['id']?>');
}

function loadPage( link_id, url, jsonView )
{
	$('#' + link_id).children('.badge_counter').hide();
	$('#' + link_id).spin('tiny');

    MooAjax.post({
        url: url,
        data: {group_id: <?php echo $group['Group']['id']?>}
    },function(response){
        if(jsonView)
            $('#profile-content').html(response.data);
        else
            $('#profile-content').html(response);
        $('#' + link_id).children('.badge_counter').fadeIn();
        $('#' + link_id).spin(false);
        // reattach events
        $('textarea').autogrow();
        $(".tip").tipsy({ html: true, gravity: 's' });
        registerImageOverlay();
        $('.tipsy').remove();

        <?php if(empty($is_member) && $group['Group']['type'] == PRIVACY_PRIVATE): ?>
            $('.left-right-menu').remove();
        <?php endif; ?>
        
        if(typeof mooMention !== 'undefined'){
            mooMention.init('postComment');
        }
        if (typeof mooEmoji != 'undefined'){
            mooEmoji.init($(response).find('textarea').attr('id'));
        }

    })
}

function ajaxCreateItem( type ,jsonView)
{
    var ext = '';
    var ajax_save = 'save';
	if ( type == 'topics' && !isMobile.any())
		$('#editor').val(tinyMCE.activeEditor.getContent());
    if(jsonView)
    {
        ajax_save = 'save';
    }
    disableButton('ajaxCreateButton');
    var is_edit = $("#createForm #id").val();
    $.post("<?php echo $this->request->base?>/" + type + "/"+ajax_save, $("#createForm").serialize(), function(data){
        enableButton('ajaxCreateButton');
        var json = $.parseJSON(data);
        if ( json.result == 1 )
        {
            loadPage(type, '<?php echo $this->request->base?>/' + type + '/ajax_view/' + json.id+ext);
           
            if (is_edit === undefined){
                $("#group_" + type + "_count").html( parseInt($("#group_" + type + "_count").html()) + 1 );
            }
        }
        else
        {
            $(".error-message").show();
            $(".error-message").html(json.message);
        }
    });
}

$(document).ready(function(){

    $('#profile-content').on('click','#share-new',function(){
        MooAjax.post({
            url: $(this).attr('rel'),
            data: {group_id: <?php echo $group['Group']['id']?>}
        },function(response){
            $('#videoModal .modal-content').html(response);
        });
    });
    $('#profile-content').on('click','#fetchButton', function(){
        $('#fetchButton').spin('small');
        $("#videoForm .error-message").hide();
        disableButton('fetchButton');
        mooAjax("<?php echo $this->request->base?>/videos/aj_validate", 'post', $("#createForm").serialize(), function(data) {
            enableButton('fetchButton');
            if (data){
                $("#fetchForm .error-message").html($.parseJSON(data).error);
                $("#fetchForm .error-message").show();
                $('#fetchButton').spin(false);
            }else{
                mooAjax("<?php echo $this->request->base?>/videos/fetch", 'post', $("#createForm").serialize(), function(data) {
                    enableButton('fetchButton');
                    $("#fetchForm").slideUp();
                    $("#videoForm").html(data);
                });
            }
        });
        return false;
    });
	<?php if ( !empty( $this->request->named['topic_id'] ) ): ?>
	loadPage('topics', '<?php echo $this->request->base?>/topics/ajax_view/<?php echo $this->request->named['topic_id']?>');
	<?php endif; ?>

	<?php if ( !empty( $this->request->named['video_id'] ) ): ?>
	loadPage('videos', '<?php echo $this->request->base?>/videos/ajax_view/<?php echo $this->request->named['video_id']?>');
	<?php endif; ?>

    $(".sharethis").hideshare({media: '<?php echo $groupHelper->getImage($group,array('prefix' => '300_square'))?>', linkedin: false});

    <?php if ( !empty( $tab ) ): ?>
        if ($("#<?php echo $tab?>").length > 0)
        {
            $('#<?php echo $tab?>').spin('tiny');
            $('#<?php echo $tab?>').children('.badge_counter').hide();
            $('#browse .current').removeClass('current');
            $('#<?php echo $tab?>').parent().addClass('current');

            $('#profile-content').load( $('#<?php echo $tab?>').attr('data-url'), {noCache: 1}, function(response){
                //$('#profile-content').html($.parseJSON(response).data);
                    
                $('#<?php echo $tab?>').spin(false);
                $('#<?php echo $tab?>').children('.badge_counter').fadeIn();

                // reattach events
                $('textarea').autogrow();
                $(".tip").tipsy({ html: true, gravity: 's' });
                registerOverlay();
            });
        }
    <?php endif; ?>
});
<?php if($this->request->is('ajax')): ?>
</script>
<?php else: ?>
<?php $this->Html->scriptEnd(); ?>
<?php endif; ?>


<?php $this->setNotEmpty('west');?>
<?php $this->start('west'); ?>
    <?php
        $display = true;
        if ($group['Group']['type'] == PRIVACY_PRIVATE) {
            if (empty($is_member)) {
                $display = false;
                if(!empty($cuser) && $cuser['Role']['is_admin'])
                    $display = true;
            }
        }
    ?>
    
    <?php if($display): ?>
    <div class="left-right-menu">
        <img src="<?php echo $groupHelper->getImage($group, array('prefix' => '300_square'))?>" class="page-avatar" id="av-img">
            <h1 class="info-home-name"><?php echo h($group['Group']['name'])?></h1>
            <div class="menu block-body menu_top_list">
            <ul class="list2" id="browse" style="margin-bottom: 10px">
                <li class="current">
                            <a data-url="<?php echo $this->request->base?>/groups/details/<?php echo $group['Group']['id']?>" rel="profile-content" href="<?php echo $this->request->base?>/groups/view/<?php echo $group['Group']['id']?>"><i class="icon-home"></i> <?php echo __( 'Details')?></a>
                    </li>		
                    <li><a data-url="<?php echo $this->request->base?>/groups/members/<?php echo $group['Group']['id']?>" rel="profile-content" id="teams" href="<?php echo $this->request->base?>/groups/view/<?php echo $group['Group']['id']?>/tab:teams"><i class="icon-group"></i>
                            <?php echo __( 'Members')?> <span id="group_user_count" class="badge_counter"><?php echo $group['Group']['group_user_count']?></span></a>
                    </li>
                    <li><a data-url="<?php echo $this->request->base?>/photos/ajax_browse/group_group/<?php echo $group['Group']['id']?>" rel="profile-content" id="photos" href="<?php echo $this->request->base?>/groups/view/<?php echo $group['Group']['id']?>/tab:photos"><i class="icon-picture"></i>
                        <?php echo __('Photos')?> <span id="group_photo_count" class="badge_counter"><?php echo $photo_count;?></span></a>
                    </li>
                <?php foreach ($group_menu as $item): ?>
                <li><a data-url="<?php echo $item['dataUrl']?>" rel="profile-content" id="<?php echo $item['id']?>" href="<?php echo $item['href']?>"><i class="<?php echo $item['icon-class']?>"></i>
                    <?php echo $item['name']?> <span id="<?php echo $item['id_count']?>" class="badge_counter"><?php echo $item['item_count']?></span></a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <?php endif; ?>
    
<?php $this->end(); ?>

	<div id="profile-content" class="group-detail">
        <?php if ( empty( $tab ) ): ?>
		<?php 
		if ( !empty( $this->request->named['topic_id'] ) || !empty( $this->request->named['video_id'] ) )
			echo __( 'Loading...');
		else
			echo $this->element('ajax/group_detail');
		?>
	    <?php else: ?>
            <?php echo __( 'Loading...')?>
        <?php endif; ?>
    </div>
