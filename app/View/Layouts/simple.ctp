<!DOCTYPE html>
<html ng-app="mooApp">
<head>
    <meta charset="utf-8">
    <?php echo  $this->Html->css('https://fonts.googleapis.com/css?family=Roboto:400,300,500,700'); ?>
    <?php
        echo $this->Html->meta('icon');
        $this->loadLibarary('mooCore');
        echo $this->fetch('meta');
        echo $this->fetch('css');
    ?>
</head>
<body class="simple" id="<?php echo $this->getPageId(); ?>">
<?php 
	echo $this->fetch('content'); 
?>
<!-- GET NOTIFICATION -->
<?php $this->Html->scriptStart(array('inline' => false)); ?>
	MooNotification.setActive(false);
<?php $this->Html->scriptEnd(); ?>
<?php
	echo $this->fetch('mooPhrase');
	echo $this->fetch('mooScript');
	echo $this->fetch('script');
?>
</body>
</html>