<?php
//echo $this->Session->flash();
echo $this->Html->script(array('jquery.fileuploader', 'jquery.Jcrop.min', 'jquery.mp.min'), array('inline' => false));
echo $this->Html->css(array( 'fineuploader', 'jquery.Jcrop', 'jquery.mp' ));
?>
<style>
    #themeModal .modal-body{
        padding:15px;
    }
</style>
<?php $this->Html->scriptStart(array('inline' => false)); ?>
<?php if ( $uid == $user['User']['id'] ): ?>
var jcrop_api;
var x = 0
    y = 0
    w = 0
    h = 0;

$(document).ready(function(){
    $('#themeModal').on('click',' .save-avatar',function() {
        $('#avatar_wrapper').spin('large');
        var modal = $('#themeModal');

        $.post('<?php echo $this->request->base?>/upload/thumb', {x: x, y: y, w: w, h: h}, function(data) {
            
            modal.modal('hide');

            if ( data != '' ){
                var json = $.parseJSON(data);
                $('#member-avatar').attr('src', json.thumb);
                $('#av-img').attr('src', json.avatar_mini);
            }
        });

    });
    $('#themeModal').on('click',' .save-cover',function() {
        var modal = $('#themeModal');
        $('#cover_wrapper').spin('large');

        jcrop_width = $('#cover_wrapper .jcrop-holder').width();
        jcrop_height = $('#cover_wrapper .jcrop-holder').height();

        $.post('<?php echo $this->request->base?>/upload/thumb_cover', {x: x, y: y, w: w, h: h, jcrop_width: jcrop_width, jcrop_height: jcrop_height}, function(data) {

            modal.modal('hide');

            if ( data != '' ){
                var json = $.parseJSON(data);
                $('#cover_img_display').attr("src",json.thumb);
            }
        });
    });
});

function storeCoords(c)
{
    x = c.x;
    y = c.y;
    w = c.w;
    h = c.h;
}
<?php else: ?>
function removeFriend(id)
{
    $.fn.SimpleModal({
        btn_ok: '<?php echo addslashes(__('OK'))?>',
        callback: function(){
            $.post('<?php echo $this->request->base?>/friends/ajax_remove', {id: id}, function() {
	            location.reload();
	        });
        },
        title: '<?php echo addslashes(__('Please Confirm'))?>',
        contents: "<?php echo addslashes(__('Are you sure you want to remove this friend?'))?>",
        model: 'confirm', hideFooter: false, closeButton: false        
    }).showModal();
}

<?php endif; ?>
    
function showAlbums()
{
    $('#user_photos').spin('tiny');
    $('#user_photos').children('.badge_counter').hide();
    $('#profile-content').load('<?php echo  $this->request->base ?>/photos/profile_user_album/<?php echo  $user['User']['id'] ?>', {noCache: 1}, function (response) {
        jQuery(this).html(response);
        $('#user_photos').spin(false);
        $('#user_photos').children('.badge_counter').fadeIn();
    });
}
<?php $this->Html->scriptEnd(); ?>

<?php $this->setNotEmpty('west');?>
<?php $this->start('west'); ?>
	<?php if ( $canView ): ?>
	<div id="browse" class="menu block-body">
		<ul class="list2 menu_top_list">
			<li class="current">
				<a data-url="<?php echo $this->request->base?>/users/ajax_profile/<?php echo $user['User']['id']?>" rel="profile-content" href="<?php echo $this->Moo->getProfileUrl( $user['User'] )?>"><i class="icon-user"></i> <?php echo __('Profile')?></a>
			</li>
			<li>
				<a data-url="<?php echo $this->request->base?>/users/ajax_info/<?php echo $user['User']['id']?>" rel="profile-content" href="#"><i class="icon-file-text-alt"></i> <?php echo __('Info')?></a>
			</li>
			<li>
				<a data-url="<?php echo $this->request->base?>/users/profile_user_friends/<?php echo $user['User']['id']?>" rel="profile-content" href="#"><i class="icon-group"></i> <?php echo __('Friends')?>
				<span class="badge_counter"><?php echo $user['User']['friend_count']?></span></a>
			</li>	
			<?php if (Configure::read('Photo.photo_enabled')): ?>
			<li>
				<a data-url="<?php echo $this->request->base?>/photos/profile_user_photo/<?php echo $user['User']['id']?>" rel="profile-content" id="user_photos" href="#"><i class="icon-picture"></i> <?php echo __('Albums')?>
				<span class="badge_counter"><?php echo $albums_count?></span></a>
			</li>		
			<?php endif; ?>
			<?php if (Configure::read('Blog.blog_enabled')): ?>
			<li>
			    <a data-url="<?php echo $this->request->base?>/blogs/profile_user_blog/<?php echo $user['User']['id']?>" rel="profile-content" href="#"><i class="icon-edit-1"></i> <?php echo __('Blogs')?>
				<span class="badge_counter"><?php echo $user['User']['blog_count']?></span></a>
			</li>
			<?php endif; ?>
                        <?php if (Configure::read('Topic.topic_enabled')): ?>
			<li>
			    <a data-url="<?php echo $this->request->base?>/topics/profile_user_topic/<?php echo $user['User']['id']?>" rel="profile-content" href="#"><i class="icon-comments"></i> <?php echo __('Topics')?>
				<span class="badge_counter"><?php echo $user['User']['topic_count']?></span></a>
			</li>		
			<?php endif; ?>
                        <?php if (Configure::read('Video.video_enabled')): ?>
			<li><a data-url="<?php echo $this->request->base?>/videos/profile_user_video/<?php echo $user['User']['id']?>" rel="profile-content" href="#"><i class="icon-facetime-video"></i> <?php echo __('Videos')?>
				<span class="badge_counter"><?php echo $user['User']['video_count']?></span></a>
			</li>	
			<?php endif; ?>
                        
                        <?php if (Configure::read('Group.group_enabled')): ?>
			<li><a data-url="<?php echo $this->request->base?>/groups/profile_user_group/<?php echo $user['User']['id']?>" rel="profile-content" href="#"><i class="icon-group"></i> <?php echo __('Groups')?>
				<span class="badge_counter"><?php echo $user['User']['group_count']?></span></a>
			</li>	
			<?php endif; ?>
                        
                        <?php if (Configure::read('Event.event_enabled')): ?>
                            <?php
                            $this->Html->script(
                                array('https://maps.google.com/maps/api/js?sensor=false'), array('block' => 'mooScript')
                            );
                            ?>
			<li><a data-url="<?php echo $this->request->base?>/events/profile_user_event/<?php echo $user['User']['id']?>" rel="profile-content" href="#"><i class="icon-calendar"></i> <?php echo __('Events')?>
				<span class="badge_counter"><?php echo $user['User']['event_count']?></span></a>
			</li>	
			<?php endif; ?>
                        
			<?php
				$this->getEventManager()->dispatch(new CakeEvent('profile.afterRenderMenu', $this)); 
			?>
			<?php
            if ( $this->elementExists('menu/user') )
                echo $this->element('menu/user');
            ?>
		</ul>
	</div>

	<?php if ($user['User']['friend_count']): ?>
	<div class="box2 box-friend" >
		<h3><?php echo __('Friends')?> (<?php echo $user['User']['friend_count']?>)</h3>
		<div class="box_content">
		    <?php echo $this->element( 'blocks/users_block', array( 'users' => $friends ) ); ?>
		</div>
	</div>
	<?php endif; ?>
	
	<?php if ( !empty( $mutual_friends ) ): ?>
	<div class="box2 mutual-friend">
		<h3>
                    <?php
      $this->MooPopup->tag(array(
             'href'=>$this->Html->url(array("controller" => "friends",
                                            "action" => "ajax_show_mutual",
                                            "plugin" => false,
                                            $user['User']['id']
                                            
                                        )),
             'title' => __('Mutual Friends'),
             'innerHtml'=> __('Mutual Friends'),
     ));
 ?>
                    </h3>
		<div class="box_content">
		    <?php echo $this->element( 'blocks/users_block', array( 'users' => $mutual_friends ) ); ?>
		</div>
	</div>
	<?php endif; ?>
    <?php endif; ?>

	<?php if ( $canView ): ?>
	    
	    <?php echo $this->element('hooks', array('position' => 'profile_sidebar') ); ?>
	
		 <?php echo $this->element('Video.blocks/videos_block'); ?>
	
		<?php echo $this->element('Blog.blocks/blogs_block'); ?>
	
		<?php echo $this->element('Group.blocks/group_block'); ?>
		
	<?php endif; ?>
		
	<div class="box2">
            <div class="box_content">
		<ul class="list6 list6sm">
			<?php if ( !empty($cuser['role_id']) && $cuser['Role']['is_admin'] && !$user['User']['featured'] ): ?>
			<li><a href="<?php echo $this->request->base?>/admin/users/feature/<?php echo $user['User']['id']?>"><?php echo __('Feature User')?></a></li>
			<?php endif; ?>
			<?php if ( !empty($cuser['role_id']) && $cuser['Role']['is_admin'] && $user['User']['featured'] ): ?>
			<li><a href="<?php echo $this->request->base?>/admin/users/unfeature/<?php echo $user['User']['id']?>"><?php echo __('Unfeature User')?></a></li>
			<?php endif; ?>
			<?php if ( !empty($cuser['role_id']) && $cuser['Role']['is_admin'] && !$user['Role']['is_admin'] ): ?>
			<li><a href="<?php echo $this->request->base?>/admin/users/edit/<?php echo $user['User']['id']?>"><?php echo __('Edit User')?></a></li>
			<?php endif; ?>
			<li>
                            <?php
      $this->MooPopup->tag(array(
             'href'=>$this->Html->url(array("controller" => "reports",
                                            "action" => "ajax_create",
                                            "plugin" => false,
                                            'user',
                                            $user['User']['id']
                                        )),
             'title' => __('Report User'),
             'innerHtml'=> __('Report User'),
     ));
 ?>
                          </li>
			<?php if ( !empty($uid) && $areFriends ): ?>
            <li><?php
      $this->MooPopup->tag(array(
             'href'=>$this->Html->url(array("controller" => "friends",
                                            "action" => "ajax_remove",
                                            "plugin" => false,
                                            $user['User']['id']
                                            
                                        )),
             'title' => __('Unfriend'),
             'innerHtml'=> __('Unfriend'),
     ));
 ?></li>
            <?php endif; ?>			
		</ul>	
            </div>
	</div>	
<?php $this->end(); ?>

<div class="profilePage ">
	<div id="profile-content">
		<?php 
		if ( !empty( $activity ) )
		{   
			echo '<ul class="list6 comment_wrapper" id="list-content">';
                        ?>
                        <?php if (isset($groupTypeItem['type'])): ?>
                            <script>
                                function requestJoinGroup(group_id){
                                    $.post('<?php echo $this->request->base?>/groups/request_to_join', {group_id: group_id}, function() {
                                        $.fn.SimpleModal({
                                            btn_ok: '<?php echo addslashes(__('Done'))?>',
                                            callback: function(){
                                                window.location = "<?php echo $this->Html->url(array('plugin' => 'group', 'controller' => 'groups', 'action' => 'view', $groupTypeItem['id'])); ?>";
                                            },
                                            title: '<?php echo addslashes(__('Join Group Request'))?>',
                                            contents: "<?php echo addslashes(__('Your request to join group sent successfully'))?>",
                                            model: 'confirm', hideFooter: false, closeButton: false
                                        }).showModal();
                                    });
                                }
                            </script>

                            <?php if($groupTypeItem['type'] == PRIVACY_RESTRICTED && !$groupTypeItem['is_member']): ?>
                            <div class="privacy_mess">
                                <div class="m_b_5"><?php echo __('This content is private'); ?></div>
                                <a href="javascript:void(0);" onclick="return requestJoinGroup(<?php echo $groupTypeItem['id']; ?>);" class="btn btn-action"><?php echo __('Join Group to access'); ?></a>
                            </div>
                            <?php elseif($groupTypeItem['type'] == PRIVACY_PRIVATE && !$groupTypeItem['is_member']): ?>
                                <div class="privacy_mess"><?php echo __('This is a private group. You must be invited by a group admin in order to join'); ?></div>

                            <?php else: ?>
                                <?php echo $this->element( 'activities', array( 'activities' => array( $activity ) ) ); ?>
                            <?php endif; ?>
                        <?php elseif(isset($eventTypeItem) && empty($eventTypeItem)): ?>
                            <div class="privacy_mess"><?php echo __('This is a private event.'); ?></div>
                        <?php else: ?>
                            <?php echo $this->element( 'activities', array( 'activities' => array( $activity ) ) ); ?>
                        <?php endif; ?>
			<?php echo '</ul>';
		}
		else
		{		
			if ( $canView )
				echo $this->element('ajax/profile_detail');
			else
				printf( __('<div class="privacy_profile full_content p_m_10">%s only shares some information with everyone</div>'), $user['User']['name'] );
		}		
		?>
	</div>
</div> 