<?php if(isset($css_links) && count($css_links) > 0): ?>
	<?php foreach($css_links as $css): ?>
		<link href="<?php echo $css; ?>" rel="stylesheet" type="text/css" />
	<?php endforeach; ?>
<?php endif; ?>
<?php if(isset($js_links) && count($js_links) > 0): ?>
	<?php foreach($js_links as $js): ?>
		<script type="text/javascript" src="<?php echo $js; ?>"></script>
	<?php endforeach; ?>
<?php endif; ?>

