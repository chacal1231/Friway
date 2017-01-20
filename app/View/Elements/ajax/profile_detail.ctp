<?php
$photoHelper = MooCore::getInstance()->getHelper('Photo_Photo');
$friendModel = MooCore::getInstance()->getModel('Friend');
$photoModel = MooCore::getInstance()->getModel('Photo_Photo');
?>
<?php echo $this->element('hooks', array('position' => 'profile_top') ); ?>

<?php if (Configure::read('Photo.photo_enabled')): ?>
<?php if (!empty($albums)): ?>
<div class="bar-content full_content p_m_10 ">
    <div class="content_center">
        <h2><?php echo __('Album Photo')?></h2>
        <ul class="photo-list">
        <?php foreach ($albums as $album): ?>
        	<?php
        	 $covert = '';
		    	if ($album['Album']['type'] == 'newsfeed' &&  $role_id != ROLE_ADMIN && $uid != $album['Album']['user_id'] && (!$uid || $friendModel->areFriends($uid,$album['Album']['user_id'])))  
		    	{
			    	$photo = $photoModel->getPhotoCoverOfFeedAlbum($album['Album']['id']);
			    	if ($photo)
			    	{
			    		$covert = $photoHelper->getImage($photo, array('prefix' => '150_square'));
			    	}
			    	else
			    	{
			    		$covert = $photoHelper->getAlbumCover('', array('prefix' => '150_square'));
			    	}
		    	}
		    	else
		    	{
		    		$covert = $photoHelper->getAlbumCover($album['Album']['cover'], array('prefix' => '150_square'));
		    	} 
        	?>
                <li class="photoItem">
                    <div class="p_2">
                         <a class="layer_square" style="background-image:url(<?php echo $covert?>);" href="<?php echo $this->request->base?>/albums/view/<?php echo $album['Album']['id']?>"></a>
                    </div>
                </li>
        <?php endforeach; ?>
        </ul>
        <div class="clear"></div>
        </div>
</div>
<?php endif; ?>
<?php endif; ?>
    
<div class="p_7">
    <h2 class="header_title"><?php echo __('Recent Activities')?></h2>
    <?php $this->MooActivity->wall($profileActivities)?>
</div>