<?
header('Content-Type: text/html; charset=ISO-8859-1');

$appli_dir="../applis";
if (!is_dir($appli_dir)) $appli_dir="./applis";
if (!is_dir($appli_dir)) $appli_dir="../extensions/applis";
if (!is_dir($appli_dir)) $appli_dir="./extensions/applis";

$copy_right='CopyRight Lucterios.org';

if (is_file($appli_dir."/setup.inc.php")) {
	$core_dir="../CORE";
	if (!is_dir($core_dir)) $core_dir="./CORE";
	include $core_dir."/setup_param.inc.php";
	include $appli_dir."/setup.inc.php";
} else {
	$extention_description='Lucterios';
	$extention_appli='Lucterios';
}

?>
<html>
<head>
  <title><? echo $extention_description;?></title>
	<style type="text/css">
	<!--
		BODY {
		background-color: white;
		}
		
		h1 {
		font-family : Helvetica, serif;
		font-size : 10mm;
		font-weight : bold;
		text-align : center;
		vertical-align : middle;
		}
		
		h2 {
		font-size : 8mm;
		font-style : italic;
		font-weight : lighter;
		text-align : center;
		}
		
		h3 {
		font-size : 6mm;
		font-style : italic;
		text-decoration : underline;
		text-align : center;
		}
		
		img {
		border-style: none;
		}
		
		TABLE.main {
		width: 100%;
		height: 95%;
		background-color: rgb(18, 0, 130);
		}
		
		/* banniere */
		
		TR.banniere {
		}
		
		TD.banniere {
		width: 980px;
		height: 70px;
		border-style: solid;
		border-color: orange;
		border-width: 1px;
		}
		
		h1.banniere {
		color: orange;
		}

		TD.menu {
		width: 120px;
		vertical-align: top;
		color: white;
		}	
			
		/* corps */
		TR.corps {
		width: 980px;
		}
		
		TD.corps {
		width: 860px;
		vertical-align: top;
		background-color: white;
		}
		
		/* pied */
		TR.pied {
		width: 980px;
		height: 15px;
		background-color: rgb(100, 100, 200);
		}
		
		TD.pied {
		width: 980px;
		height: 15px;
		color: rgb(230, 230, 230);
		font-size: 9px;
		text-align: right;
		font-weight: bold;
		}	
		
		.go {
		font-family : verdana,helvetica;
		font-size : 10pt;
		font-style : oblique;
		text-decoration : none;
		text-indent : 2cm;
		}
		
		TD {
		font-size: 10pt;
		font-family: verdana,helvetica;
		}
		
		a.invisible {
		color: rgb(18, 0, 130);
		font-size: 0px;
		}
		
		li {
		}
		
		ul {
		font-style : italic;
		}
	-->
	</style>
</head>
<body>
<table class="main">
    <tr class="banniere">
        <td colspan="2" class="banniere">
            <table border="0" cellspacing="0" cellpadding="0" width="100%">
                <tr>
                    <td>
                        <img src='<? echo $appli_dir."/images/logo.gif";?>' alt='logo' />
                    </td>
                    <td align="center">
                        <h1 class="banniere">SDK <? echo $extention_description;?></h1>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr class="corps">	
        <td class="menu">
        </td>
        <td class="corps">
		<br>
		<h3>Page de configuration du SDK</h3>
		<br>
		<br>
		Pour installer le SDK:<br>
		<ul>
			<li>Installer un client Lucterios</li>
			<li>Ajouter une configuration:
			<ul>
<?
		global $extention_description;
		global $extention_appli;
		$server_name=$_SERVER["SERVER_NAME"];
		$server_port=$_SERVER["SERVER_PORT"];
		$server_dir=$_SERVER["PHP_SELF"];
		if ($server_dir[0]=='/')
			$server_dir=substr($server_dir,1);
		$sep=strrpos($server_dir,'/');
		$server_dir=substr($server_dir,0,$sep);
		

			echo "<li>Nom: SDK $extention_appli</li>";
			echo "<li>Serveur: $server_name</li>";
			echo "<li>port: $server_port</li>";
			echo "<li>Répertoire: $server_dir</li>";
?>
			</ul>
			</li>
			<li>Lancez le client avec le nouveau serveur configuré.</li>
		</ul>
		<br>
        </td>
    </tr>
    <tr class="pied">
        <td colspan="2" class="pied">
            <table border="0" cellspacing="0" cellpadding="0" width="100%">
                <tr>
                    <td class="pied">
                        <? echo $copy_right;?> - Mise à jour <? echo date ("d/m/Y", filemtime("index.php")); ?>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
 