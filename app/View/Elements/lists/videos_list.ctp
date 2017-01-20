<?php if($this->request->is('ajax')) $this->setCurrentStyle(4) ?>

<?php
if (count($videos) > 0)
{
	foreach ($videos as $video):
?>
    <li class="full_content <?php if ( !empty( $type ) && $type == APP_GROUP ) echo 'col-md-4'; else echo 'col-md-3'; ?>">
        <div class="item-content">
            <a href=<?php if ( !empty( $ajax_view ) ): ?>"javascript:void(0)" onclick="loadPage('videos', '<?php echo $this->request->base?>/videos/ajax_view/<?php echo $video['Video']['id']?>', true)"<?php else: ?>"<?php echo $this->request->base?>/videos/view/<?php echo $video['Video']['id']?>/<?php echo seoUrl($video['Video']['title'])?>"<?php endif; ?> class="album_cover">
               <img src='<?php echo $this->request->webroot?>uploads/videos/<?php echo $video['Video']['thumb']?>' />
               <div class="infoLayer hidden-xs hidden-sm"><p><?php echo h($video['Video']['title'])?></p></div>
               <div class="date video-date"><i class="icon-thumbs-up-alt"></i>&nbsp;<?php echo $video['Video']['like_count']?> </div>
            </a>
            <div class="album_info">
                <a href=<?php if ( !empty( $ajax_view ) ): ?>"javascript:void(0)" onclick="loadPage('videos', '<?php echo $this->request->base?>/videos/ajax_view/<?php echo $video['Video']['id']?>', true)"<?php else: ?>"<?php echo $this->request->base?>/videos/view/<?php echo $video['Video']['id']?>/<?php echo seoUrl($video['Video']['title'])?>"<?php endif; ?>><?php echo h($this->Text->truncate( $video['Video']['title'], 25 ))?></a>
                <div class="date visible-xs visible-sm"><?php echo $video['Video']['like_count']?> likes</div>
            </div>
        </div>
    </li>
<?php 
	endforeach;
        echo '<div class="clear"></div>';
} 

else
	echo '<div align="center" style="width:100%;overflow:hidden">' . __( 'No more results found') . '</div>';
?>

<?php if (count($videos) >= RESULTS_LIMIT): ?>
    <?php $this->Html->viewMore($more_url); ?>
<?php endif; ?>