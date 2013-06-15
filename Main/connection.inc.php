<?php
// 
//     This file is part of Lucterios.
// 
//     Lucterios is free software; you can redistribute it and/or modify
//     it under the terms of the GNU General Public License as published by
//     the Free Software Foundation; either version 2 of the License, or
//     (at your option) any later version.
// 
//     Lucterios is distributed in the hope that it will be useful,
//     but WITHOUT ANY WARRANTY; without even the implied warranty of
//     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//     GNU General Public License for more details.
// 
//     You should have received a copy of the GNU General Public License
//     along with Lucterios; if not, write to the Free Software
//     Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
// 
// 	Contributeurs: Fanny ALLEAUME, Pierre-Olivier VERSCHOORE, Laurent GAY
//  // library file write by SDK tool
// --- Last modification: Date 06 April 2007 10:16:08 By Laurent GAY ---

//@BEGIN@

function mustAutentificate($mess)
{
	global $REPONSE;
	$REPONSE.="<REPONSE observer='CORE.Auth' source_extension='CORE' source_action='authentification'><![CDATA[$mess]]></REPONSE>";
}

$IS_CONNECTED = false;

if (!array_key_exists("ses", $GLOBAL) && !array_key_exists("login", $GLOBAL))
{
	mustAutentificate('NEEDAUTH');
}
elseif(array_key_exists("login", $GLOBAL) && array_key_exists("pass", $GLOBAL))
{
	$login = $GLOBAL['login'];
	require "Class/Connect.inc.php";

	global $CNX_OBJ;
	$CNX_OBJ=new Connect($login);
	if ($CNX_OBJ->IsValid($GLOBAL["pass"]))
	{
		$realName=$CNX_OBJ->LongName;
		$GLOBAL["ses"] =$login."#".$CNX_OBJ->Pwcrypt;
	}
	else
		$GLOBAL["ses"] ="";

	if($GLOBAL["ses"]=="")
	{
		mustAutentificate('BADAUTH');
	}
	else
	{
		require_once "../CORE/setup_param.inc.php";
		require_once "../CORE/setup.inc.php";
		$Version="$version_max.$version_min.$version_release.$version_build";

		echo "<!-- A -->\n";
		if (!is_file("setup.inc.php") && is_file("template_setup.inc.php")) {
		    echo "<!-- B -->\n";
		    $temp_content = file("template_setup.inc.php");
		    for($temp_i=0;$temp_i<count($temp_content);$temp_i++) {
		      if (substr($temp_content[$temp_i],0,9) == '$version_')
			    $temp_content[$temp_i] = str_replace('X','0',$temp_content[$temp_i]);
		    }
		    file_put_contents("setup.inc.php",$temp_content);
		    echo "<!-- C -->\n";
		}
		require_once "setup.inc.php";
		$SDK_Version="$version_max.$version_min.$version_release.$version_build";
		
		require_once "$ApplisDir/setup.inc.php";
		$server_dir=$_SERVER["PHP_SELF"];
		$pos=strpos($server_dir,'coreIndex.php');
		$server_dir=substr($server_dir,0,$pos);
		$REPONSE.= "<REPONSE observer='CORE.Auth' source_extension='CORE' source_action='authentification'>
				<CONNECTION>
				<TITLE>LUCTERIOS SDK</TITLE>
				<SUBTITLE>$extention_appli  $extention_titre</SUBTITLE>
				<VERSION>$SDK_Version</VERSION>
				<SERVERVERSION>$Version</SERVERVERSION>
				<COPYRIGHT>Copyrigth 2008- Lucterios.org</COPYRIGHT>
				<LOGONAME>http://".$_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT'].$server_dir."images/LucteriosLogo.gif</LOGONAME>
				<LOGIN>".$login."</LOGIN>
				<REALNAME>$realName</REALNAME>
				</CONNECTION>
				<PARAM name='ses' type='str'>".$GLOBAL["ses"]."</PARAM>
				<![CDATA[OK]]>
			</REPONSE>";
		$IS_CONNECTED = true;
	}
}
elseif(array_key_exists("ses", $GLOBAL))
{
	$sess=$GLOBAL["ses"];
	$pos=strrpos($sess,"#");
	if ($pos!==false)
	{
		$login=substr($sess,0,$pos);
		$pass=substr($sess,$pos+1);
	}
	else
	{
		$login="";
		$pass="";
	}

	require "Class/Connect.inc.php";
	global $CNX_OBJ;
	$CNX_OBJ=new Connect($login);
	if ($CNX_OBJ->Pwcrypt==$pass)
		$IS_CONNECTED = true;
	else
		mustAutentificate('BADSESS');
}

//@END@
?>
