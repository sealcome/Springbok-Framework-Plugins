<? HHtml::doctype() ?>
<html>
	<head>
		<? HHtml::metaCharset() ?>
		<title><?= Config::$projectName ?> - {$layout_title}</title>
		<!--[if lt IE 9]><?php HHtml::jsLink('/ie-lt9') ?><![endif]-->
		<?php HHtml::cssLink('/Dev/dev'); HHtml::jsLink('/Dev/dev'); HHtml::jsI18n() ?>
		<? HHtml::favicon() ?>
	</head>
	<body>{=$layout_content}</body>
</html>