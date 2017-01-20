<?php
$photoHelper = MooCore::getInstance()->getHelper('Photo_Photo');
?>
<?php if (!empty($photos)): ?>
<?php foreach ($photos as $p): ?>
<li style="background-image:url(<?php echo $photoHelper->getImage($p, array('prefix' => '75_square'));?>);">
   <a href="javascript:void(0)" onclick="showPhoto(<?php echo $p['Photo']['id']?>)"></a>    
</li>
<?php endforeach; ?>

<?php if (count($photos) >= RESULTS_LIMIT):?>
<a id="photo_load_btn" href="javascript:void(0)"  onclick="loadMorePhotos()"><i class="icon-ellipsis-horizontal icon-2x"></i></a>
<?php endif; ?>

<script>
<?php foreach ($photos as $key => $photo): ?>
var img<?php echo $key?> = new Image();
img<?php echo $key?>.src = "<?php echo $this->request->webroot?><?php echo $photo['Photo']['path']?>";
<?php endforeach; ?>
</script>
<?php endif; ?>