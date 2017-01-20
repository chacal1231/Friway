<?php
/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */
?>

<!DOCTYPE html>
<html ng-app="mooApp">
<head>
    <meta charset="utf-8">
    <title>
        <?php if ( Configure::read('core.site_offline') ) echo __('[OFFLINE]'); ?>

        <?php if (isset($title_for_layout) && $title_for_layout){ echo $title_for_layout; } else if(isset($mooPageTitle) && $mooPageTitle) { echo $mooPageTitle; } ?> | <?php echo Configure::read('core.site_name'); ?>
    </title>
    <meta name="description" content="<?php if (isset($description_for_layout) && $description_for_layout){ echo $description_for_layout; }else if(isset($mooPageDescription) && $mooPageDescription) {echo $mooPageDescription;}else if(Configure::read('core.site_description')){ echo Configure::read('core.site_description');}?>"/>
    <meta name="keywords" content="<?php if(isset($mooPageKeyword) && $mooPageKeyword){echo $mooPageKeyword;}else if(Configure::read('core.site_keywords')){ echo Configure::read('core.site_keywords');}?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    
    <meta property="og:url" content="<?php echo $this->Html->url( null, true ); ?>" />
    <link rel="canonical" href="<?php echo $this->Html->url( null, true ); ?>" /> 
    <?php if(isset($og_image)): ?>
    <meta property="og:image" content="<?php echo $og_image?>" />
    <?php else: ?>
    <meta property="og:image" content="<?php echo FULL_BASE_URL . $this->request->webroot?>img/og-image.png" />
    <?php endif; ?>

    <script>
        var baseUrl = '<?php echo $this->request->base?>';
        var root = '<?php echo $this->request->webroot?>';
        var autoLoadMore = '<?php echo  Configure::read('core.auto_load_more') ?>';
    </script>

    <?php echo  $this->Html->css('https://fonts.googleapis.com/css?family=Roboto:400,300,500,700'); ?>

    <?php
    if(!empty($site_rtl)){
        echo $this->Html->css('rtl');
    }
    ?>

    <?php
        echo $this->Html->meta('icon');
        $this->loadLibarary('mooCore');
        echo $this->fetch('meta');
        echo $this->fetch('css');
    ?>
    <?php
    if(!empty($site_rtl)){
        echo $this->Html->css('rtl');
    }
    ?>

</head>
<?php
    $cookies_warning = Configure::read('core.enable_cookies');
    $deny_url = Configure::read('core.deny_url');   
?>
<body class="default-body <?php if($cookies_warning && $deny_url && empty($accepted_cookie)):
?> page_has_cookies<?php endif; ?>" id="<?php echo $this->getPageId(); ?>">

<?php echo $this->element('misc/fb_include'); ?>
<?php echo $this->fetch('header'); ?>
<div class="navbar navbar-fixed-top sl-navbar" role="navigation" id="header">
<?php
    if($cookies_warning && $deny_url && empty($accepted_cookie)):
?>
    <div id="cookies-warning" class="cookies-warning">
        <?php echo __('This site uses cookies to store your information on your computer.') ?> <a href="http://www.allaboutcookies.org/"><?php echo __('Read more'); ?></a>
        <?php echo $this->Html->link(__('Accept'),'javascript:void(0)' ,array('class' => 'btn btn-success accept-cookie', 'data-answer' => '1')); ?>
        <?php echo $this->Html->link(__('Deny'),'javascript:void(0)',array('class' => 'btn btn-default accept-cookie', 'data-answer' => '2')); ?>
        <a class="delete-warning-cookies" href="javascript:void(0)"><i class="fa fa-times"></i></a>
    </div>
<?php endif; ?>

    <div class="header-bg"></div>
    <div class="container full_header">
        <div class="hidden-xs hidden-sm">
	    <?php echo $this->element('misc/logo'); ?>
        </div>
            <?php echo $this->element('main_menu'); ?>
    </div>
</div>
<div class="navbar navbar-fixed-top sl-navbar" role="navigation" id="header_mobi">
    <div class="container-fluid">
        <a id="openMenu" href="#" data-toggle="modal" data-target="#mobi_menu">
            <span class='arr-menu'></span>
            <span class='line'></span>
            <span class='line'></span>
            <span class='line'></span>
        </a>
	    <?php echo $this->element('misc/logo'); ?>
            <?php if (!empty($uid)): ?>
          
            <?php echo $this->Moo->getItemPhoto(array('User' => $cuser), array('class' => 'ava_mobi',"width" =>"45px" ,"alt" => $cuser['name'], 'prefix' => '100_square'))?>
           
        <?php endif; ?>
            
    </div>
</div>

<div class="container " id="content-wrapper" <?php $this->getNgController() ?>>
    <?php echo html_entity_decode( Configure::read('core.header_code') )?>


    <div class="row">
        <?php
        
        $flash_mess = $this->Session->flash();
        echo $flash_mess;
        if(empty($flash_mess))
            echo $this->Session->flash('confirm_remind');
        ?>
        <?php echo $this->fetch('content'); ?>
        <?php echo $this->element('footer_mobi'); ?>
    </div>
    <!-- Modal -->
    <?php $this->MooPopup->html(); ?>
    <section class="modal fade" id="langModal" role="basic" tabindex='-1' aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content"></div>
        </div>
    </section>
    <section class="modal fade modal-fullscreen force-fullscreen" tabindex='-1' id="photoModal" role="basic" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content"></div>
        </div>
    </section>

    <!-- BEGIN SAMPLE PORTLET CONFIGURATION MODAL FORM-->
    <div class="modal fade" id="portlet-config" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Modal title</h4>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <!-- Config -->
                    <button type="button" class="btn blue ok"><?php echo __('OK')?></button>
                    <button type="button" class="btn default" data-dismiss="modal"><?php echo __('Close')?></button>

                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
	<div class="modal fade" id="plan-view" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title" id="myModalLabel">Modal title</h4>
		  </div>
		  <div class="modal-body">
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			<button type="button" class="btn btn-primary">Save changes</button>
		  </div>
		</div>
	  </div>
	</div>
    <?php echo $this->fetch('footer'); ?>





<div id="shareFeedModal" data-backdrop="static" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo __('Share') ?></h4>
            </div>
			<div class="modal-body">
			<script>
               
				function ResizeIframe(id){
				  var frame = document.getElementById(id);
				  frame.height = frame.contentWindow.document.body.scrollHeight  + "px";
				}
           
			</script>
			  <iframe id="iframeShare" onload="ResizeIframe('iframeShare')" src="" width="99.6%" height="" frameborder="0"></iframe>
			</div>
   
		</div>
	</div>
</div>
</div>


<!--<script src="https://maps.google.com/maps/api/js?sensor=false"></script>-->
<?php
echo $this->fetch('mooPhrase');
echo $this->fetch('mooScript');
echo $this->fetch('script');
?>
<script>
                 $(document).ready(function () {
                $('#shareFeedModal').on('hidden.bs.modal', function (e) {
                   var frame = document.getElementById('iframeShare');
                    frame.height = '0px';
                    $(e.target).removeData('bs.modal');
                });
               
            });
            </script>
<?php echo $this->element('sql_dump'); ?>
<?php echo html_entity_decode( Configure::read('core.analytics_code') )?>
</body>
</html>