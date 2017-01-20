<?php $this->setCurrentStyle(4) ?>
<?php $this->Html->scriptStart(array('inline' => false)); ?>
$(document).ready(function(){
   $('#select_photos').change(function(){
      if ( $(this).val() == 'move' )
         $('#album_id').show();
      else
         $('#album_id').hide();
   });
});
<?php  $this->Html->scriptEnd(); ?>
<?php
$photoHelper = MooCore::getInstance()->getHelper('Photo_Photo');
?>
<div class="bar-content">
    <div class="content_center">
        <form action="<?php echo $this->request->base?>/albums/edit/<?php echo $album['Album']['id']?>" method="post">
        <?php echo $this->Form->hidden('id', array('value' => $album['Album']['id'])); ?>
        <div class="box3">
            <div class="mo_breadcrumb">
                <h1><?php echo h($album['Album']['title'])?></h1>
                <a href="<?php echo $this->request->base?>/albums/view/<?php echo $album['Album']['id']?>" class="button button-action topButton button-mobi-top"><?php echo __( 'View Album')?></a>
            </div>
            
                	

                <?php
                if (count($photos) == 0):
                        echo __( 'No photos found');
                else:
                ?>
                <ul class="photos_edit">
                        <?php foreach ($photos as $photo): ?>
                        <li class="col-md-3 full_content">
                            <div class="albums_edit_item">
                                <div class="albums_photo_edit" style="background-image: url(<?php echo $photoHelper->getImage($photo, array('prefix' => '250'));?>)"></div>
                                <div class="album_info_edit">
                                    <?php echo $this->Form->textarea('caption_' . $photo['Photo']['id'], array('value' => $photo['Photo']['caption'], 'placeholder' => __( 'Caption'), 'class' => 'no-grow')) ?><br />
                                    <input type="radio" name="cover" value="<?php echo $photo['Photo']['thumbnail']?>" <?php if ($photo['Photo']['thumbnail'] == $album['Album']['cover']) echo 'checked'; ?>> <?php echo __( 'Album cover')?>
                                    <input type="checkbox" name="select_<?php echo $photo['Photo']['id']?>" value="1" class="photo_edit_checkbox" >
                                </div>
                             </div>
                        </li>
                        <?php endforeach; ?>
                </ul>
                <?php
                endif;
                ?>
                <div class="clear"></div>
                <div class='full_content p_m_10'>
                <div style="float:right">
                <?php echo $this->Form->select('select_photos', array('move' => __( 'Move to'), 'delete' => __( 'Delete') ), array( 'empty' => __( 'With selected...') ) ); ?>
                <?php echo $this->Form->select('album_id', $albums, array( 'style' => 'display:none' ) ); ?>
            </div>
                <div class='clear'></div>    
                <div align="center" style="margin-top: 30px">
                        <input type="submit" value="<?php echo __( 'Save Changes')?>" class="btn btn-action">
                        <?php if ( empty( $album['Album']['type'] ) ): ?>
                        <input type="button" value="<?php echo __( 'Delete Album')?>" class="button" onclick="mooConfirm( '<?php echo addslashes(__( 'Are you sure you want to delete this album?<br />All photos will also be deleted!'))?>', '<?php echo $this->request->base?>/albums/do_delete/<?php echo $album['Album']['id']?>' )">
                        <?php endif; ?>
                </div>
                    <div class='clear'></div>
                        
                </div>
        </div>
        </form>
    </div>
</div>