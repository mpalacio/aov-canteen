<!DOCTYPE html>
<html>
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		AOV Canteen:
		<?php echo $title_for_layout; ?>
	</title>
	<?php
		echo $this->Html->meta('icon');

		echo $this->Html->css('bootstrap.min');
		echo $this->Html->css('bootstrap-theme.min');
		echo $this->Html->script('jquery-2.1.1.min');
		echo $this->Html->script('bootstrap.min');

		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
	?>
</head>
<body>
	<div class="container">
		<div id="header" class="row">
			<div class="col-md-12"></div>
		</div>
		<div id="content" class="row">
			<div class="col-md-12"><?php echo $this->Session->flash(); ?></div>
			<div class="col-md-12"><?php echo $this->fetch('content'); ?></div>
		</div>
		<div id="footer" class="row">
			<div class="col-md-12"></div>
		</div>
	</div>
</body>
</html>
