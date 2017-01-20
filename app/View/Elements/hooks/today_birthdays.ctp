<?php if ( !empty( $birthday_users ) ): ?> 
<div class="box2">
    <h3><?php echo __('Today Birthdays')?></h3>
    <div class="box_content">
    <?php
    $i = 1;
    foreach ($birthday_users as $user)
    {
        echo $this->Moo->getName( $user['User'], false );

        if ($i < count($birthday_users)) 
            echo ', ';
        $i++;
    }
    ?>
    </div>
</div>
<?php endif; ?>