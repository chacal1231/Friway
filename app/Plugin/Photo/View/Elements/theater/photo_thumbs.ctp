<?php if (!empty($photos)): ?>
<?php
$photoHelper = MooCore::getInstance()->getHelper('Photo_Photo');
?>
    <?php foreach ($photos as $key => $p): ?>
        <li photo-position="<?php echo $key + 1; ?>" id="photo_thumb_<?php echo $p['Photo']['id']?>">
            <a href="javascript:void(0)" onclick="MooPhotoTheater.showPhoto(<?php echo $p['Photo']['id']?>)">
                <img width="50" src="<?php echo $photoHelper->getImage($p, array('prefix' => '75_square'));?>" />
            </a>
        </li>
    <?php endforeach; ?>

<?php endif; ?>