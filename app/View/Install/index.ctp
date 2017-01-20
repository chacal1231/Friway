<?php echo $this->Html->script(array('moophrase','scripts')); ?>


<script>
function doStep( step )
{
	$("#step_but i").attr("class", "icon-refresh icon-spin");
    $("#step_but").addClass('disabled');
    $("#step_but").spin('small');
	$.post("<?php echo $this->request->base?>/install/ajax_step" + step, $("#installForm").serialize(), function(data){
		$("#step_but i").attr("class", 'icon-ok');
		$("#step_but").spin(false);
        $("#step_but").removeClass('disabled');
		if (data.indexOf('mooError') > 0) {
			$(".error-message").show();
			$(".error-message").html(data);
		} else {
			$(".error-message").hide();
			$("#install").html(data);
		}
	});
}

</script>
<div id="header" role="navigation" class="navbar navbar-fixed-top sl-navbar">
    <div class="header-bg"></div>
    <div class="container full_header">
        <div class="hidden-xs hidden-sm">
            <div class="logo-default">
                <a href="<?php echo $this->request->webroot?>"><img alt="mooSocial" src="<?php echo $this->request->webroot?>theme/default/img/logo.png"></a>
            </div>
        </div>
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="wrapper">
                <div id="content" class="install-content">
                    <h1><?php echo __('Welcome to mooSocial Installation');?></h1>
                    <div class="error-message" style="display:none"></div>


                    <div id="install" >
                        <div class="bs-callout bs-callout-danger">
                            <h4 style="border-top:none;"><?php echo __('Please make sure that your server meets all the requirements before proceeding');?></h4>
                            <ul style="list-style:outside;padding-left:20px;">
                                <li><?php echo __('PHP 5.3 with short tags enabled or PHP 5.4+');?></li>
                                <li><?php echo __('MySql 5+');?></li>
                                <li><?php echo __('PHP extensions: MySql PDO, GD2, Curl, libxml, exif, zlib (if you need to export theme)');?></li>
                                <li><?php echo __('Magic quotes must be disabled');?></li>
                                <li><?php echo __('Memory Limit: 128M+');?></li>
                                <li><?php echo __('The following directories are writable by the web server user (e.g. change permission to 755 ): app/Config, app/tmp and all its subdirectories, app/webroot/uploads and all its subdirectories');?></li>
                            </ul>
                        </div>


                        <h2><?php echo __('Step 1: Database Configuration');?></h2>
                        <form id="installForm">
                            <ul class="list6">
                                <li><label><?php echo __('Database Host');?></label>
                                    <?php echo $this->Form->text('db_host', array('value' => 'localhost')); ?> <?php echo __('(this is usually "localhost")');?>
                                </li>
                                <li><label><?php echo __('Database Username');?></label>
                                    <?php echo $this->Form->text('db_username'); ?>
                                </li>
                                <li><label><?php echo __('Database Password');?></label>
                                    <?php echo $this->Form->password('db_password'); ?>
                                </li>
                                <li><label><?php echo __('Database Name');?></label>
                                    <?php echo $this->Form->text('db_name'); ?>
                                </li>
                                <li><label><?php echo __('Unix Socket');?></label>
                                    <?php echo $this->Form->text('db_socket'); ?> <?php echo __('(leave empty if you are not sure)');?>
                                </li>
                                <li><label><?php echo __('Table Prefix');?></label>
                                    <?php echo $this->Form->text('db_prefix'); ?> <?php echo __('(choose an optional table prefix which must end in an underscore)');?>
                                </li>
                                <li><label>&nbsp;</label>
                                    <a href="javascript:void(0)" onclick="doStep(1)" id="step_but" class="btn btn-danger"><i class="icon-ok"></i> <?php echo __('Next');?></a>
                                </li>
                            </ul>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

