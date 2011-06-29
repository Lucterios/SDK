<?php
header('Content-Type: text/html; charset=ISO-8859-1');
require_once "../CORE/setup_param.inc.php";
require_once "setup.inc.php";
$SDK_Version="$version_max.$version_min.$version_release.$version_build";?>
<html>
<head>
	<title>Kit de développement Luctérios (v<?php echo $SDK_Version; ?>) - $APPLI_NAME</title>
	<meta http-equiv='content-type' content='text/html;charset=iso-8859-1'>
	<meta name='description' content='Lucterios'>
	<meta NAME='Author' CONTENT='lucterios.org'>
	<meta NAME='Copyright' CONTENT='(c)2008 - lucterios.org'>
	<meta NAME='Reply-to' CONTENT='lucterios@lucterios.org'>
	<link rel='stylesheet' type='text/css' href='./GUI/style_menu.css'/>
</head>
<frameset border="1" frameborder="1" framespacing="1">
	<frame src="./Help/index.html" name="MainFrame">
</frameset>
<noframes>
<body/>
</noframes>
</html>

