<?php

$groupHelper = MooCore::getInstance()->getHelper('Group_Group');
?>
<div class="title-modal">
    <?php echo __( 'Joined Groups')?>
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
</div>
<div class="modal-body">
<ul class="activity_content">
<?php foreach ( $groups as $group ): ?>
    <li>
        <div class="activity_item">
            <div class="activity_left">
                <a href="<?php echo $group['Group']['moo_href']?>">
                    <img src="<?php echo $groupHelper->getImage($group, array('prefix' => '150_square'))?>" class="img_wrapper2" />
                </a>
            </div>
            <div class="activity_right ">
                <a class="feed_title" href="<?php echo $group['Group']['moo_href']?>"><?php echo h($group['Group']['moo_title'])?></a>
                <div class="date comment_message feed_detail_text">
                    <?php echo h($this->Text->truncate($group['Group']['description'], 125, array('exact' => false)))?>
                </div>
            </div>
        </div>
    </li>
<?php endforeach; ?>
</ul>
</div>
