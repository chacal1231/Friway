<?php
/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */
?>
<!DOCTYPE html>

<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->

<head>
    <meta charset="utf-8"/>
    <title>
        <?php if (Configure::read('core.site_offline')) echo __('[OFFLINE]'); ?>
        <?php echo $title_for_layout; ?> | <?php echo Configure::read('core.site_name'); ?>
    </title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="description"
          content="<?php echo  $title_for_layout ?> | <?php echo  Configure::read('core.site_name') ?> | <?php echo  Configure::read('core.site_description') ?>"/>
    <meta name="keywords" content="<?php echo Configure::read('core.site_keywords'); ?>"/>
    <meta property="og:image" content="<?php echo  FULL_BASE_URL . $this->request->webroot ?>img/og-image.png"/>
    <meta content="" name="author"/>
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <!-- Use new way for google web fonts
     http://www.smashingmagazine.com/2012/07/11/avoiding-faux-weights-styles-google-web-fonts -->
    <!--<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,700" rel="stylesheet" type="text/css"/>
    <!-- END GLOBAL MANDATORY STYLES -->
    <!-- BEGIN PAGE LEVEL STYLES -->
    <!-- END PAGE LEVEL STYLES -->
    <script>
        var baseUrl = '<?php echo $this->request->base?>';
        var root = '<?php echo $this->request->webroot?>';
    </script>
    <!-- BEGIN THEME STYLES -->
    <!-- END THEME STYLES -->
    <link rel="shortcut icon" href="favicon.ico"/>
    <?php
    echo $this->Html->meta('icon');
    echo $this->Html->css(array(
//        'all.css?' . Configure::read('core.version'),
        // BEGIN GLOBAL MANDATORY STYLES
        'font-awesome/css/font-awesome.min.css?' . Configure::read('core.version'),
        'simple-line-icons/simple-line-icons.min.css?' . Configure::read('core.version'),
        'bootstrap/css/bootstrap.min.css?' . Configure::read('core.version'),
        'uniform/css/uniform.default.css?' . Configure::read('core.version'),
        'bootstrap-switch/css/bootstrap-switch.min.css?' . Configure::read('core.version'),
        //FONT TELL0
        'fontello/css/animation.css?' . Configure::read('core.version'),
        'fontello/css/fontello.css?' . Configure::read('core.version'),
        'fontello/css/fontello-codes.css?' . Configure::read('core.version'),
        'fontello/css/fontello-embedded.css?' . Configure::read('core.version'),
        'fontello/css/fontello-ie7.css?' . Configure::read('core.version'),
        'fontello/css/fontello-ie7-codes.css?' . Configure::read('core.version'),
        
        'bootstrap-toastr/toastr.min.css?' . Configure::read('core.version'),
        // END GLOBAL MANDATORY STYLES
        // END PAGE LEVEL STYLES
        'select2/select2.css?' . Configure::read('core.version'),
        'datatables/plugins/bootstrap/dataTables.bootstrap.css?' . Configure::read('core.version'),
        // BEGIN THEME STYLES
        'components.css?' . Configure::read('core.version'),
        'plugins.css?' . Configure::read('core.version'),
        'layout/css/layout.css?' . Configure::read('core.version'),
        'layout/css/themes/default.css?' . Configure::read('core.version'),
        'layout/css/custom.css?' . Configure::read('core.version'),
        // END THEME STYLES
    ));




    echo $this->fetch('meta');
    echo $this->fetch('css');
    ?>
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<!-- DOC: Apply "page-header-fixed-mobile" and "page-footer-fixed-mobile" class to body element to force fixed header or footer in mobile devices -->
<!-- DOC: Apply "page-sidebar-closed" class to the body and "page-sidebar-menu-closed" class to the sidebar menu element to hide the sidebar by default -->
<!-- DOC: Apply "page-sidebar-hide" class to the body to make the sidebar completely hidden on toggle -->
<!-- DOC: Apply "page-sidebar-closed-hide-logo" class to the body element to make the logo hidden on sidebar toggle -->
<!-- DOC: Apply "page-sidebar-hide" class to body element to completely hide the sidebar on sidebar toggle -->
<!-- DOC: Apply "page-sidebar-fixed" class to have fixed sidebar -->
<!-- DOC: Apply "page-footer-fixed" class to the body element to have fixed footer -->
<!-- DOC: Apply "page-sidebar-reversed" class to put the sidebar on the right side -->
<!-- DOC: Apply "page-full-width" class to the body element to have full width page without the sidebar menu -->

<?php if($this->request->action != 'admin_login'){ ?>

<body class="page-header-fixed page-quick-sidebar-over-content ">
<?php echo $this->element('misc/fb_include'); ?>
<!-- BEGIN HEADER -->
<div class="page-header navbar navbar-fixed-top">
<!-- BEGIN HEADER INNER -->
<div class="page-header-inner">
<?php echo $this->element('misc/logo'); ?>

<!-- BEGIN RESPONSIVE MENU TOGGLER -->
<a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
</a>
<!-- END RESPONSIVE MENU TOGGLER -->
<!-- BEGIN TOP NAVIGATION MENU -->
<div class="top-menu">
<ul class="nav navbar-nav pull-right">
<!-- BEGIN NOTIFICATION DROPDOWN -->
<li class="dropdown dropdown-extended dropdown-notification hide" id="header_notification_bar">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
        <i class="icon-bell"></i>
					<span class="badge badge-default">
					7 </span>
    </a>
    <ul class="dropdown-menu">
        <li>
            <p>
                You have 14 new notifications
            </p>
        </li>
        <li>
            <ul class="dropdown-menu-list scroller" style="height: 250px;">
                <li>
                    <a href="#">
									<span class="label label-sm label-icon label-success">
									<i class="fa fa-plus"></i>
									</span>
                        New user registered. <span class="time">
									Just now </span>
                    </a>
                </li>
                <li>
                    <a href="#">
									<span class="label label-sm label-icon label-danger">
									<i class="fa fa-bolt"></i>
									</span>
                        Server #12 overloaded. <span class="time">
									15 mins </span>
                    </a>
                </li>
                <li>
                    <a href="#">
									<span class="label label-sm label-icon label-warning">
									<i class="fa fa-bell-o"></i>
									</span>
                        Server #2 not responding. <span class="time">
									22 mins </span>
                    </a>
                </li>
                <li>
                    <a href="#">
									<span class="label label-sm label-icon label-info">
									<i class="fa fa-bullhorn"></i>
									</span>
                        Application error. <span class="time">
									40 mins </span>
                    </a>
                </li>
                <li>
                    <a href="#">
									<span class="label label-sm label-icon label-danger">
									<i class="fa fa-bolt"></i>
									</span>
                        Database overloaded 68%. <span class="time">
									2 hrs </span>
                    </a>
                </li>
                <li>
                    <a href="#">
									<span class="label label-sm label-icon label-danger">
									<i class="fa fa-bolt"></i>
									</span>
                        2 user IP blocked. <span class="time">
									5 hrs </span>
                    </a>
                </li>
                <li>
                    <a href="#">
									<span class="label label-sm label-icon label-warning">
									<i class="fa fa-bell-o"></i>
									</span>
                        Storage Server #4 not responding. <span class="time">
									45 mins </span>
                    </a>
                </li>
                <li>
                    <a href="#">
									<span class="label label-sm label-icon label-info">
									<i class="fa fa-bullhorn"></i>
									</span>
                        System Error. <span class="time">
									55 mins </span>
                    </a>
                </li>
                <li>
                    <a href="#">
									<span class="label label-sm label-icon label-danger">
									<i class="fa fa-bolt"></i>
									</span>
                        Database overloaded 68%. <span class="time">
									2 hrs </span>
                    </a>
                </li>
            </ul>
        </li>
        <li class="external">
            <a href="#">
                See all notifications <i class="m-icon-swapright"></i>
            </a>
        </li>
    </ul>
</li>
<!-- END NOTIFICATION DROPDOWN -->
<!-- BEGIN INBOX DROPDOWN -->
<li class="dropdown dropdown-extended dropdown-inbox hide" id="header_inbox_bar">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
        <i class="icon-envelope-open"></i>
					<span class="badge badge-default">
					4 </span>
    </a>
    <ul class="dropdown-menu">
        <li>
            <p>
                You have 12 new messages
            </p>
        </li>
        <li>
            <ul class="dropdown-menu-list scroller" style="height: 250px;">
                <li>
                    <a href="inbox14c8.html?a=view">
									
									<span class="subject">
									<span class="from">
									Lisa Wong </span>
									<span class="time">
									Just Now </span>
									</span>
									<span class="message">
									Vivamus sed auctor nibh congue nibh. auctor nibh auctor nibh... </span>
                    </a>
                </li>
                <li>
                    <a href="inbox14c8.html?a=view">
									
									<span class="subject">
									<span class="from">
									Richard Doe </span>
									<span class="time">
									16 mins </span>
									</span>
									<span class="message">
									Vivamus sed congue nibh auctor nibh congue nibh. auctor nibh auctor nibh... </span>
                    </a>
                </li>
                <li>
                    <a href="inbox14c8.html?a=view">
									
									<span class="subject">
									<span class="from">
									Bob Nilson </span>
									<span class="time">
									2 hrs </span>
									</span>
									<span class="message">
									Vivamus sed nibh auctor nibh congue nibh. auctor nibh auctor nibh... </span>
                    </a>
                </li>
                <li>
                    <a href="inbox14c8.html?a=view">
									
									<span class="subject">
									<span class="from">
									Lisa Wong </span>
									<span class="time">
									40 mins </span>
									</span>
									<span class="message">
									Vivamus sed auctor 40% nibh congue nibh... </span>
                    </a>
                </li>
                <li>
                    <a href="inbox14c8.html?a=view">
									
									<span class="subject">
									<span class="from">
									Richard Doe </span>
									<span class="time">
									46 mins </span>
									</span>
									<span class="message">
									Vivamus sed congue nibh auctor nibh congue nibh. auctor nibh auctor nibh... </span>
                    </a>
                </li>
            </ul>
        </li>
        <li class="external">
            <a href="inbox.html">
                See all messages <i class="m-icon-swapright"></i>
            </a>
        </li>
    </ul>
</li>
<!-- END INBOX DROPDOWN -->
<!-- BEGIN TODO DROPDOWN -->
<li class="dropdown dropdown-extended dropdown-tasks hide" id="header_task_bar">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
        <i class="icon-calendar"></i>
					<span class="badge badge-default">
					3 </span>
    </a>
    <ul class="dropdown-menu extended tasks">
        <li>
            <p>
                You have 12 pending tasks
            </p>
        </li>
        <li>
            <ul class="dropdown-menu-list scroller" style="height: 250px;">
                <li>
                    <a href="#">
									<span class="task">
									<span class="desc">
									New release v1.2 </span>
									<span class="percent">
									30% </span>
									</span>
									<span class="progress">
									<span style="width: 40%;" class="progress-bar progress-bar-success" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100">
									<span class="sr-only">
									40% Complete </span>
									</span>
									</span>
                    </a>
                </li>
                <li>
                    <a href="#">
									<span class="task">
									<span class="desc">
									Application deployment </span>
									<span class="percent">
									65% </span>
									</span>
									<span class="progress progress-striped">
									<span style="width: 65%;" class="progress-bar progress-bar-danger" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100">
									<span class="sr-only">
									65% Complete </span>
									</span>
									</span>
                    </a>
                </li>
                <li>
                    <a href="#">
									<span class="task">
									<span class="desc">
									Mobile app release </span>
									<span class="percent">
									98% </span>
									</span>
									<span class="progress">
									<span style="width: 98%;" class="progress-bar progress-bar-success" aria-valuenow="98" aria-valuemin="0" aria-valuemax="100">
									<span class="sr-only">
									98% Complete </span>
									</span>
									</span>
                    </a>
                </li>
                <li>
                    <a href="#">
									<span class="task">
									<span class="desc">
									Database migration </span>
									<span class="percent">
									10% </span>
									</span>
									<span class="progress progress-striped">
									<span style="width: 10%;" class="progress-bar progress-bar-warning" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100">
									<span class="sr-only">
									10% Complete </span>
									</span>
									</span>
                    </a>
                </li>
                <li>
                    <a href="#">
									<span class="task">
									<span class="desc">
									Web server upgrade </span>
									<span class="percent">
									58% </span>
									</span>
									<span class="progress progress-striped">
									<span style="width: 58%;" class="progress-bar progress-bar-info" aria-valuenow="58" aria-valuemin="0" aria-valuemax="100">
									<span class="sr-only">
									58% Complete </span>
									</span>
									</span>
                    </a>
                </li>
                <li>
                    <a href="#">
									<span class="task">
									<span class="desc">
									Mobile development </span>
									<span class="percent">
									85% </span>
									</span>
									<span class="progress progress-striped">
									<span style="width: 85%;" class="progress-bar progress-bar-success" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100">
									<span class="sr-only">
									85% Complete </span>
									</span>
									</span>
                    </a>
                </li>
                <li>
                    <a href="#">
									<span class="task">
									<span class="desc">
									New UI release </span>
									<span class="percent">
									18% </span>
									</span>
									<span class="progress progress-striped">
									<span style="width: 18%;" class="progress-bar progress-bar-important" aria-valuenow="18" aria-valuemin="0" aria-valuemax="100">
									<span class="sr-only">
									18% Complete </span>
									</span>
									</span>
                    </a>
                </li>
            </ul>
        </li>
        <li class="external">
            <a href="#">
                See all tasks <i class="m-icon-swapright"></i>
            </a>
        </li>
    </ul>
</li>
<!-- END TODO DROPDOWN -->
<?php echo $this->element('userbox'); ?>

<!-- BEGIN QUICK SIDEBAR TOGGLER -->
<li class="dropdown dropdown-quick-sidebar-toggler hide">
    <a href="javascript:;" class="dropdown-toggle">
        <i class="icon-logout"></i>
    </a>
</li>
<!-- END QUICK SIDEBAR TOGGLER -->
</ul>
</div>
<!-- END TOP NAVIGATION MENU -->
</div>
<!-- END HEADER INNER -->
</div>
<!-- END HEADER -->
<div class="clearfix">
</div>
<!-- BEGIN CONTAINER -->
<div class="page-container" >
<!-- BEGIN SIDEBAR -->
<div class="page-sidebar-wrapper">
<!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
<!-- DOC: Change data-auto-speed="200" to adjust the sub menu slide up/down speed -->
<div class="page-sidebar navbar-collapse collapse">
<?php echo $this->fetch('sidebar-menu');?>

</div>
</div>
<!-- END SIDEBAR -->
<!-- BEGIN CONTENT -->
<?php echo  html_entity_decode(Configure::read('core.header_code')) ?>
<?php echo $this->element('hooks', array('position' => 'global_top')); ?>
<div class="page-content-wrapper">
<div class="page-content">

<!-- BEGIN SAMPLE PORTLET CONFIGURATION MODAL FORM-->
<div class="modal fade" id="portlet-config" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Modal title</h4>
            </div>
            <div class="modal-body">
                Widget settings form goes here
            </div>
            <div class="modal-footer">
                <!-- Config -->
                <button type="button" class="btn blue ok"><?php echo __('OK');?></button>
                <button type="button" class="btn default" data-dismiss="modal"><?php echo __('Close');?></button>

            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
    <div class="modal fade" id="ajax" role="basic" aria-hidden="true">
        <div class="page-loading page-loading-boxed">
            
									<span>
									&nbsp;&nbsp;<?php echo __('Loading...');?> </span>
        </div>
        <div class="modal-dialog">
            <div class="modal-content">
            </div>
        </div>
    </div>
    <div class="modal fade" id="ajax-translate" role="basic" aria-hidden="true">
        <div class="page-loading page-loading-boxed">
            
                                                                        <span>
                                                                        &nbsp;&nbsp;<?php echo __('Loading...');?></span>
        </div>
        <div class="modal-dialog">
            <div class="modal-content">
            </div>
        </div>
    </div>
    <div class="modal fade" id="ajax-large" role="basic" aria-hidden="true">
        <div class="page-loading page-loading-boxed">
            
									<span>
									&nbsp;&nbsp;<?php echo __('Loading...');?> </span>
        </div>
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
            </div>
        </div>
    </div>
    <div class="modal fade" id="ajax-small" role="basic" aria-hidden="true">
        <div class="page-loading page-loading-boxed">
            
									<span>
									&nbsp;&nbsp;<?php echo __('Loading...');?> </span>
        </div>
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
            </div>
        </div>
    </div>
<!-- /.modal -->
<!-- END SAMPLE PORTLET CONFIGURATION MODAL FORM-->
<!-- BEGIN STYLE CUSTOMIZER -->

<!-- END STYLE CUSTOMIZER -->
<!-- BEGIN PAGE HEADER-->

<h3 class="page-title">
    <?php echo  $title_for_layout ?>
</h3>
<div class="page-bar">
    <?php

        echo $this->Html->getCrumbList(
            array(
                'class'=>'page-breadcrumb',

                'firstClass' => 'first',
                'lastClass' => 'last',
                'separator' => '<i class="fa fa-angle-right"></i>',
                'escape' => true
            ),
            array('url' => '/admin/home'));
    ?>
    <?php echo $this->fetch('page-toolbar'); ?>


</div>
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
<div class="row">
    <div class="col-md-12">
        <?php echo $this->element('right_column'); ?>
        <?php echo $this->element('left_column'); ?>
    </div>
</div>


<!-- END PAGE CONTENT-->
</div>
</div>
<!-- END CONTENT -->
<!-- BEGIN QUICK SIDEBAR -->
<a href="javascript:;" class="page-quick-sidebar-toggler"><i class="icon-close"></i></a>

<!-- END QUICK SIDEBAR -->
</div>
<!-- END CONTAINER -->
<!-- BEGIN FOOTER -->
<?php echo $this->element('hooks', array('position' => 'global_bottom')); ?>

<div class="page-footer">
     <div class="page-footer-tools">
		<span class="go-top">
		<i class="fa fa-angle-up"></i>
		</span>
    </div>
    <div class="page-footer-inner">
        <?php echo $this->element('footer'); ?>
    </div>
   
</div>
<!-- END FOOTER -->
<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<!-- BEGIN CORE PLUGINS -->
<!--[if lt IE 9]>
<script src="<?php echo  FULL_BASE_URL . $this->request->webroot ?>assets/global/plugins/respond.min.js"></script>
<script src="<?php echo  FULL_BASE_URL . $this->request->webroot ?>assets/global/plugins/excanvas.min.js"></script>
<![endif]-->

<?php
echo $this->Html->script(array(
    'global/jquery-1.11.0.min.js?' . Configure::read('core.version'),
    'global/jquery-migrate-1.2.1.min.js?' . Configure::read('core.version'),
    // IMPORTANT! Load jquery-ui-1.10.3.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip
    'global/jquery-ui/jquery-ui-1.10.3.custom.min.js?'.Configure::read('core.version'),
    'global.js?' . Configure::read('core.version'),

    'global/bootstrap/js/bootstrap.min.js?'. Configure::read('core.version'),
    'global/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js?'. Configure::read('core.version'),
    'global/jquery-slimscroll/jquery.slimscroll.min.js?'. Configure::read('core.version'),
    'global/uniform/jquery.uniform.min.js?'. Configure::read('core.version'),
    'global/jquery.blockui.min.js?'. Configure::read('core.version'),
    'global/jquery.cokie.min.js?' . Configure::read('core.version'),
    'global/bootstrap-switch/js/bootstrap-switch.min.js?' . Configure::read('core.version'),
    '/js/mooajax.js?' . Configure::read('core.version'),
    '/js/moophrase.js?' . Configure::read('core.version'),

    // END CORE PLUGINS
    // BEGIN PAGE LEVEL PLUGINS
    'global/select2/select2.min.js?' . Configure::read('core.version'),
    // END PAGE LEVEL PLUGINS
    // BEGIN PAGE LEVEL SCRIPTS

    'admin/pages/scripts/ui-toastr.js?' . Configure::read('core.version'),
    'global/scripts/metronic.js?' . Configure::read('core.version'),
    'admin/layout/scripts/layout.js?' . Configure::read('core.version'),
    'admin/layout/scripts/quick-sidebar.js?' . Configure::read('core.version'),
    'admin/layout/scripts/demo.js?' . Configure::read('core.version'),
    'admin/layout/scripts/moo.js?' . Configure::read('core.version'),


));
$this->loadLibarary('mooAdmin');
$this->loadLibrary(array('adm'));
echo $this->fetch('mooPhrase');
echo $this->fetch('mooInit');
echo $this->fetch('script');
?>
<?php $this->MooPopup->html(); ?>
<script>
    jQuery(document).ready(function() {
        Metronic.init(); // init metronic core components
        Layout.init(); // init current layout
        QuickSidebar.init(); // init quick ajax_createsidebar
        Demo.init(); // init demo features
        //TableManaged.init();
        moo.init();
    });
</script>
<?php echo $this->element('sql_dump'); ?>

</body>

<?php } else{  ?>
    <body class="login">
        <!-- BEGIN LOGO -->
        <div class="logo">
            <a href="<?php echo  $this->request->base ?>/" alt="logo" class="logo-default hide">
                <img src="<?php echo $this->Moo->logo(); ?>"
                     alt="<?php echo Configure::read('core.site_name'); ?> Admin">
                <?php echo Configure::read('core.site_name'); ?> <span class="slogan"> Admin</span>
            </a>
        </div>
        <!-- END LOGO -->
        <!-- BEGIN LOGIN -->
        <div class="content">
        <!-- BEGIN LOGIN FORM -->
            <div class="row">
                <div class="col-md-12">
                    <?php echo $this->element('right_column'); ?>
                    <?php echo $this->element('left_column'); ?>
                </div>
            </div>
        <!-- END LOGIN FORM -->

        </div>
        <!-- END LOGIN -->
        <!-- BEGIN COPYRIGHT -->


</body>
    <!-- END BODY -->

<?php } ?>
<!-- END BODY -->
</html>



