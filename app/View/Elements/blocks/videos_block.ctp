<?php 
if (!empty($videos)): 
?>
<ul class="list4 list6sm albums video_block">
<?php foreach ($videos as $video): ?>
	<li class="col-md-4">
            <div class="item-content">
                <a class="album_cover" href="<?php echo $this->request->base?>/videos/view/<?php echo $video['Video']['id']?>/<?php echo seoUrl($video['Video']['title'])?>">
                    
                    <img src="<?php echo $this->Moo->getItemImageUrl('video', 'thumb', $video['Video']['id'], $video['Video']['thumb'], 't_')?>" class="img_wrapper2">
                </a>
		<div class="video_info">
			<a href="<?php echo $this->request->base?>/videos/view/<?php echo $video['Video']['id']?>/<?php echo seoUrl($video['Video']['title'])?>">
                            <?php echo h($this->Text->truncate($video['Video']['title'], 40, array('exact' => false)))?>
                        </a>
			<div class="like_count">
                            <?php echo __n( '%s like', '%s likes', $video['Video']['like_count'], $video['Video']['like_count'] )?>
                        </div>
		</div>
            </div>
	</li>
<?php endforeach; ?>
</ul>
<?php 
else:
	echo __('Nothing found');
endif; 
?>