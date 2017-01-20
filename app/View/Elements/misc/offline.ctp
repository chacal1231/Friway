<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <?php echo $this->Html->charset(); ?>
    <title>
        <?php echo __('Offline Mode')?>
    </title>

    <?php
    echo $this->Html->meta('icon');
    echo $this->Html->css( array('main', 'all') );
    //echo $this->Html->script( array('scripts', 'global','moocore/ServerJS.js') );
    $this->loadLibarary('mooCore');
    echo $this->fetch('mooPhrase');
    echo $this->fetch('mooScript');
    echo $this->fetch('script');
    ?>
</head>
<body class="maintenance-page">
<script type="text/javascript">
$(function() {
    $('#loginButton').on('click', function(){
        $('#loginForm').toggle();
    });
});
</script>
<div class="sl-navbar" id="header" style="min-height: 50px;">
    <div class="container full_header">
 
        <?php echo $this->element('misc/logo'); ?>
        <?php echo $this->element('userbox'); ?>
        <!--Login form-->
      <a href="#" class="button button-flat-primary button-flat" id="loginButton"> <?php echo __('Login')?> <i class="icon-down-small"></i></a>
        <div id="loginForm" class="moo-dropdown">
        	<div class="dropdown-caret right">
              <span class="caret-outer"></span>
              <span class="caret-inner"></span>
            </div>
        <form action="<?php echo $this->request->base?>/users/login" method="post">
            <?php echo $this->Form->email( 'email', array( 'placeholder' => __('Email'), 'id' => 'login_email', 'name' => 'data[User][email]' ) )?>
            <?php echo $this->Form->password( 'password', array( 'placeholder' => __('Password'), 'id' => 'login_password', 'name' => 'data[User][password]') )?>
            <input type="submit" value="<?php echo __('Login')?>" class="button button-action">
            <div class="login-box">
                <?php echo $this->Form->checkbox( 'remember', array( 'checked' => true ) )?> <?php echo __('Remember me')?>
            </div>
            <p><a href="<?php echo $this->request->base?>/users/recover"><?php echo __('Forgot password?')?></a></p>
            <?php
            if ( !empty( $return_url ) )
                echo $this->Form->hidden( 'return_url', array( 'value' => $return_url ) );
            ?>
        </form>
        </div>
    </div>
</div>
<div class="container">
    <div id="content">
        <?php echo $this->Session->flash(); ?>
        <h1><?php echo __('Sorry, our site is temporarily down for maintenance. Please check back again soon.')?></h1>
        <?php echo nl2br($offline_message)?>
    </div>
</div><?php die(); ?>
</body>
</html> 