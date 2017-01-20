<?php 
if ( $uid )
{
    $friends = array_reverse($friends, true);
    $friends[$uid] = $cuser['name'] . ' (' . __('Me') . ')';
    $friends = array_reverse($friends, true);
}
                 
foreach ($friends as $id => $name): 
?>
    <a href="javascript:void(0)" onclick="submitTag(<?php echo $id?>, '<?php echo h($name)?>')"><?php echo h($name)?></a><br />
<?php 
endforeach; 
?>