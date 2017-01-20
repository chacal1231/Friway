<?php 
if (!empty($blogs)): 
?>
<ul class="list6 list6sm popular_blog_block">
<?php foreach ($blogs as $blog): ?>
	<li class="list-item-inline">
            <a href="<?php echo $this->request->base?>/blogs/view/<?php echo $blog['Blog']['id']?>/<?php echo seoUrl($blog['Blog']['title'])?>">
                <?php if($blog['Blog']['thumbnail']): ?>
                    <img width="70" src="<?php echo $this->request->base . '/' . $blog['Blog']['thumbnail']?>" class="img_wrapper2 user_list">
                <?php else: ?>
                    <img width="70" src="<?php echo $this->request->base?>/img/noimage/noimage-blog.png" class="img_wrapper2 user_list"/>
                <?php endif; ?>
            </a>
            <div class="blog_detail">
                <div class="title-list">
                    <a href="<?php echo $this->request->base?>/blogs/view/<?php echo $blog['Blog']['id']?>/<?php echo seoUrl($blog['Blog']['title'])?>">
                        <?php echo h($this->Text->truncate($blog['Blog']['title'], 35 ))?>
                    </a>
                </div>
                <div class="like_count">
			<?php echo __n( '%s comment', '%s comments', $blog['Blog']['comment_count'], $blog['Blog']['comment_count'] )?> .
			<?php echo __n( '%s like', '%s likes', $blog['Blog']['like_count'], $blog['Blog']['like_count'] )?>
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