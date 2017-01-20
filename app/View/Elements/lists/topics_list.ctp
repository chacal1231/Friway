<?php if($this->request->is('ajax')) $this->setCurrentStyle(4) ?>
<?php
if (count($topics) > 0)
{	
    $i = 1;
	foreach ($topics as $topic):
?>
	<li class="full_content p_m_10" <?php if( $i == count($topics) ) echo 'style="border-bottom:0"'; ?>>
            <a href=<?php if ( !empty( $ajax_view ) ): ?>"javascript:void(0)" onclick="loadPage('topics', '<?php echo $this->request->base?>/topics/ajax_view/<?php echo $topic['Topic']['id']?>')"<?php else: ?>"<?php echo $this->request->base?>/topics/view/<?php echo $topic['Topic']['id']?>/<?php echo seoUrl($topic['Topic']['title'])?>"<?php endif; ?>>
               <?php if($topic['Topic']['thumbnail']): ?>
                <img width="45" src="<?php echo  $this->request->base . '/' . $topic['Topic']['thumbnail']?>" class="img_wrapper2 user_list">
                <?php else: ?>
                    <img width="45" src="<?php echo $this->request->base?>/img/noimage/noimage-topic.png" class="img_wrapper2 user_list"/>
                <?php endif; ?>
               
            </a>
		<div class="topics_count"><?php echo $topic['Topic']['comment_count']?></div>
		<div class="comment">
			<a href=<?php if ( !empty( $ajax_view ) ): ?>"javascript:void(0)" onclick="loadPage('topics', '<?php echo $this->request->base?>/topics/ajax_view/<?php echo $topic['Topic']['id']?>')"<?php else: ?>"<?php echo $this->request->base?>/topics/view/<?php echo $topic['Topic']['id']?>/<?php echo seoUrl($topic['Topic']['title'])?>"<?php endif; ?>><b><?php echo h($topic['Topic']['title'])?></b></a>
			&nbsp;
			<?php if ( $topic['Topic']['pinned'] ): ?>
			<i class="icon-pin icon-small tip" title="<?php echo __('Pinned')?>"></i>
			<?php endif; ?>
			<?php if ( $topic['Topic']['attachment'] ): ?>
            <i class="icon-paper-clip icon-small tip" title="<?php echo __('Attached files')?>"></i>
            <?php endif; ?>
			<?php if ( $topic['Topic']['locked'] ): ?>
            <i class="icon-lock icon-small tip" title="<?php echo __('Locked')?>"></i>
            <?php endif; ?>
			<div class="comment_message">
    			<?php echo $this->Text->truncate(strip_tags(str_replace(array('<br>','&nbsp;'), array(' ',''), $topic['Topic']['body'])), 85, array('exact' => false))?>
    		    <div class="date">
    				<?php echo __('Last posted by %s', $this->Moo->getName($topic['LastPoster'], false))?>
    				<?php echo $this->Moo->getTime( $topic['Topic']['last_post'], Configure::read('core.date_format'), $utz )?>
    			</div>
    		</div>
		</div>	
	</li>
<?php 
    $i++;
	endforeach;
}
else
	echo '<div class="full_content p_m_10" align="center">' . __('No more results found') . '</div>';
?>

<?php if (count($topics) >= RESULTS_LIMIT): ?>

	<?php $this->Html->viewMore($more_url); ?>
<?php endif; ?>