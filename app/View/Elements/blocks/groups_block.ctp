<?php 
if (!empty($groups)): 
?>
<ul class="list6 list6sm"> 
<?php foreach ($groups as $group): ?>
	<li><a href="<?php echo $this->request->base?>/groups/view/<?php echo $group['Group']['id']?>/<?php echo seoUrl($group['Group']['name'])?>"><img src="<?php echo $this->Moo->getItemPicture($group['Group'], 'groups', true)?>" class="img_wrapper2" style="width:40px"></a>
		<div style="margin-left: 50px;">
			<a href="<?php echo $this->request->base?>/groups/view/<?php echo $group['Group']['id']?>/<?php echo seoUrl($group['Group']['name'])?>"><?php echo h($this->Text->truncate($group['Group']['name'], 50, array('exact' => false)))?></a><br />
			<span class="date"><?php echo __n( '%s member', '%s members', $group['Group']['group_user_count'], $group['Group']['group_user_count'] )?></span>
		</div>
	</li>
<?php endforeach; ?>
</ul>
<?php 
else:
	echo __('Nothing found');
endif; 
?>