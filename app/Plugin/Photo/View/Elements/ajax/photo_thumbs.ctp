<?php if (!empty($photos)): ?>
<?php
$photoHelper = MooCore::getInstance()->getHelper('Photo_Photo');
?>
    <?php foreach ($photos as $p): ?>
        <li id="photo_thumb_<?php echo $p['Photo']['id']?>">
            <a href="javascript:void(0)" onclick="showPhoto(<?php echo $p['Photo']['id']?>)">
                <img width="50" src="<?php echo $photoHelper->getImage($p, array('prefix' => '75_square'));?>" />
            </a>
        </li>
    <?php endforeach; ?>

    <?php if ($photosAlbumCount > $page * Configure::read('Photo.photo_item_per_pages')):?>
        <li class="viewmore-photo">
            <a id="photo_load_btn" href="javascript:void(0)" onclick="loadMoreThumbs()"><i class="fa fa-ellipsis-v fa-2x"></i></a>
        </li>
    <?php endif; ?>

<?php endif; ?>