<?php
echo $this->Html->script(array('jquery.fileuploader'), array('inline' => false));
echo $this->Html->css(array( 'fineuploader' ));
$photoHelper = MooCore::getInstance()->getHelper('Photo_Photo');
?>
<?php $this->Html->scriptStart(array('inline' => false)); ?>
$(document).ready(function(){
    $(".sharethis").hideshare({media: '<?php echo $photoHelper->getAlbumCover($album['Album']['cover'], array('prefix' => '300_square'))?>', linkedin: false});
});
<?php $this->Html->scriptEnd(); ?>

<div class="bar-content full_content ">
        <div class="content_center">
            <div class=" post_body album_view_detail">
            
                <div class="mo_breadcrumb">
                <h1><?php echo h($album['Album']['title'])?></h1>
               
                    <?php if ( empty( $album['Album']['type'] ) ): ?>
                    <ul class="list7 header-list header-button-list">
                        <?php if ( $uid == $album['User']['id'] ): ?>
                        <li class="btn-album">
                            <?php
      $this->MooPopup->tag(array(
             'href'=>$this->Html->url(array("controller" => "photos",
                                            "action" => "ajax_upload",
                                            "plugin" => 'photo',
                                            'Photo_Album',
                                            $album['Album']['id'],
                                        )),
             'title' => h($album['Album']['title']),
             'innerHtml'=> __( 'Upload Photos'),
          'class' => 'button button-action topButton button-mobi-top',
          'data-backdrop' => 'static'
     ));
 ?>
                        
                        </li>
                        <?php endif; ?>
                         
                        <li class="list_option">
                            <div class="dropdown">
                                <button id="dLabel" type="button" data-toggle="dropdown" aria-haspopup="true" role="button" aria-expanded="false">
                                    <i class="icon-ellipsis-vert"></i>
                                </button>
                                <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                                    <?php if ( $uid == $album['User']['id'] || ( !empty($cuser) && $cuser['Role']['is_admin'] ) ): ?>
                                    <li>
                                        <?php
      $this->MooPopup->tag(array(
             'href'=>$this->Html->url(array("controller" => "albums",
                                            "action" => "create",
                                            "plugin" => 'photo',
                                            $album['Album']['id']
                                           
                                        )),
             'title' => __( 'Edit Album'),
             'innerHtml'=> __( 'Edit Album'),
          "data-backdrop" => "static"
     ));
 ?>
                                          </li>
                                    <li><a href="javascript:void(0);" onclick="mooConfirm( '<?php echo addslashes(__( 'Are you sure you want to delete this album?<br />All photos will also be deleted!'))?>', '<?php echo $this->request->base?>/albums/do_delete/<?php echo $album['Album']['id']?>' )"><?php echo __( 'Delete Album')?></a></li>
                                    <li><a href="<?php echo $this->request->base?>/albums/edit/<?php echo $album['Album']['id']?>"><?php echo __( 'Edit Photos')?></a></li>
                                     <?php endif; ?>
                                    <li>
                                        <?php
      $this->MooPopup->tag(array(
             'href'=>$this->Html->url(array("controller" => "reports",
                                            "action" => "ajax_create",
                                            "plugin" => false,
                                            'photo_album',
                                            $album['Album']['id'],
                                        )),
             'title' =>  __( 'Report Album'),
             'innerHtml'=>  __( 'Report Album'),
     ));
 ?>
                                      </li>
                                      
                                      <?php if ($album['Album']['privacy'] != PRIVACY_ME): ?>
                                        <!-- not allow sharing only me item -->
                                      <li>
                                          <a href="javascript:void(0);" share-url="<?php echo $this->Html->url(array(
                                                'plugin' => false,
                                                'controller' => 'share',
                                                'action' => 'ajax_share',
                                                'Photo_Album',
                                                'id' => $album['Album']['id'],
                                                'type' => 'album_item_detail'
                                            ), true); ?>" class="shareFeedBtn"><?php echo __('Share'); ?></a>
                                      </li>
                                      <?php endif; ?>
                                </ul>
                            </div>
                        </li>
                       
                    </ul>
                     
                <?php endif; ?>


                 </div>
            
                <div class="album-detail-info">
                    <?php echo __( 'Posted by %s', $this->Moo->getName($album['User']))?> <?php echo __( 'in')?> <a href="<?php echo $this->request->base?>/photos/index/<?php echo $album['Album']['category_id']?>/<?php echo seoUrl($album['Category']['name'])?>"><?php echo $album['Category']['name']?></a> <?php echo $this->Moo->getTime( $album['Album']['created'], Configure::read('core.date_format'), $utz )?>
                    &nbsp;&middot;&nbsp;<?php if ($album['Album']['privacy'] == PRIVACY_PUBLIC): ?>
                        <?php echo __('Public') ?>
                        <?php elseif ($album['Album']['privacy'] == PRIVACY_PRIVATE): ?>
                        <?php echo __('Private') ?>
                        <?php elseif ($album['Album']['privacy'] == PRIVACY_FRIENDS): ?>
                        <?php echo __('Friend') ?>
                        <?php endif; ?>
                </div>

                <?php $this->Html->rating($album['Album']['id'], 'albums','Photo');  ?>

            <?php echo $this->element( 'lists/photos_list', array( 'type' => 'Photo_Album' ) ); ?>
            <div class="p_m_10">
                <div class="comment_message"><?php echo $this->Moo->formatText( $album['Album']['description'], false, true, array('no_replace_ssl' => 1) )?></div>

                <?php if (!empty($tags)): ?>
                <div style="margin-top:5px"><b><?php echo __( 'Tags')?></b>:
                    <?php echo $this->element( 'blocks/tags_item_block' ); ?>
                </div>
                <?php endif; ?>
                
            </div>
            
        </div>
        </div>
</div>
<div class="bar-content full_content p_m_10">
    <div class="content_center">
        <?php echo $this->element( 'likes', array('item' => $album['Album'], 'type' => 'Photo_Album' ) ); ?>

    </div>
</div>
<div class="bar-content full_content p_m_10">
    <div class="content_center">
        <div class="album-detail-comment">
            <?php echo $this->renderComment();?>
        </div>
    </div>
</div>

