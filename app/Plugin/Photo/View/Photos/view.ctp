<?php echo $this->element('misc/photo_view_script'); ?>

<div id="photo_thumbs">
    <ul>
        <?php echo $this->element('ajax/photo_thumbs'); ?>
    </ul>    
</div>
       
<div id="photo-content">
	<?php echo $this->element('ajax/photo_detail'); ?>
</div>

<div id="friends" style="display:none">
<?php 
if ( $uid )
{
    $friends = array_reverse($friends, true);
    $friends[$uid] = $cuser['name'] . ' (' . __( 'Me') . ')';
    $friends = array_reverse($friends, true);
}
                 
foreach ($friends as $id => $name): 
?>
    <a href="javascript:void(0)" onclick="submitTag(<?php echo $id?>, '<?php echo h($name)?>')"><?php echo h($name)?></a><br />
<?php 
endforeach; 
?>
</div>

<div id="preload" style="display:none"></div>