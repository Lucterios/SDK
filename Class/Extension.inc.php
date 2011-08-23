<?php
//
//    This file is part of SDK Lucterios.
//
//    SDK Lucterios is free software; you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation; either version 2 of the License, or
//    (at your option) any later version.
//
//    SDK Lucterios is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with Lucterios; if not, write to the Free Software
//    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
//
//	Contributeurs: Fanny ALLEAUME, Pierre-Olivier VERSCHOORE, Laurent GAY
//

require_once("ConnectionSDK.inc.php");

function listSort($a, $b)
{
	if ($a=='CORE')
		return -1;
	if ($b=='CORE')
		return 1;
	if ($a=='applis')
		return -1;
	if ($b=='applis')
		return 1;
	if ($a == $b)
		return 0;
	return ($a < $b) ? -1 : 1;
}

function getStringToWrite($Text,$WithCote=true)
{
	$new_text=str_replace(array('\\'),array('\\\\'),$Text);
	$new_text=str_replace(array('$','"'),array('\$','\"'),$new_text);
	if ($WithCote)
		return '"'.$new_text.'"';
	else
		return $new_text;
}

class Extension
{
	var $Name="";
	var $Appli="";
	var $Famille='';
	var $Titre='';
	var $Description="";
	var $Libre='o';
	var $Version=array(0,0,0,1);
	var $Depencies=array();
	var $Rights=array();
	var $Actions=array();
	var $Menus=array();
	var $Params=array();
	var $AsInstallFunc=false;
	var $SignBy="";
	var $SignMD5="";
	var $ExtendTables=array();

	function __ExtDir($name)
	{
		if (($name=="") || ($name=="CORE"))
			$extDir = "../CORE/";
		else
			$extDir = "../extensions/$name/";
		return $extDir;
	}

	function GetList($CNX_OBJ=null)
	{
		require_once("../CORE/setup_param.inc.php");
		$ext_name=array();
		$ext_name[]='CORE';
		$extDir = "../extensions/";
		if (is_dir($extDir))
		{
			$dh=opendir($extDir);
			while (($file = readdir($dh)) != false)
			{
				if(is_dir($extDir . $file) && is_file($extDir.$file."/setup.inc.php") && ($file[0]!='.'))
					$ext_name[]=$file;
			}
		}
		usort($ext_name,'listSort');
		$ext_list=array();
		foreach($ext_name as $extname) {
			if (((($CNX_OBJ==null) || $CNX_OBJ->IsViewModule($extname)) && ($extname!='applis') && ($extname!='CORE')) ||
			 (($CNX_OBJ!=null) && $CNX_OBJ->IsViewModule($extname) && (($extname=='CORE') || ($extname=='applis'))))
			{
				$ext=new Extension($extname);
				$ext_list[$extname]=$ext->GetVersion();
			}
		}
		return $ext_list;
	}

	function GetLock($module)
	{
		if ($module=="CORE")
			$module="";
		$lock_info="";
		$extDir = Extension::__ExtDir($module);
		$lockfile=$extDir."apaslock.sdk";
		if (is_file($lockfile))
		{
			$file_cnx = file($lockfile);
			if (count($file_cnx)==2)
			{
				$user=trim($file_cnx[0]);
				$date=trim($file_cnx[1]);
				$lock_info="$user - $date";
			}
		}
		return $lock_info;
	}

	function GetBackupFile($module,$lock=null)
	{
		$bachup_file="";
		$BcDir='Backup';
		if (!is_dir($BcDir)) mkdir($BcDir);
		if ($lock==null)
			$lock=Extension::GetLock($module);
		if ($lock!="")
		{
			if ($module=="") $module="CORE";
			$ext_obj=new Extension($module);
			$file_list=glob("$BcDir/".$module."_".str_replace(array(" - "," ",":"),array("~","_","-"),$lock)."^*.tar");
			if (count($file_list)==1)
				$bachup_file=$file_list[0];
		}
		return $bachup_file;
	}

	function GetArchiveFile($suffic=".tar")
	{
		$bachup_file="";
		$BcDir='Temp';
		if (!is_dir($BcDir)) mkdir($BcDir);
		$version=$this->GetVersion();
		$version=str_replace('.','-',$version);
		if ($this->Name!='applis')
			$bachup_file="$BcDir/".$this->Name."_".$version.$suffic;
		else
			$bachup_file="$BcDir/".$this->Appli."_".$version.$suffic;
		return $bachup_file;
	}

	function ArchiveExtension($module,$BackupFile,$NoDirectory=false,$compress=null)
	{
		if ($BackupFile!="")
		{
			$extDir = Extension::__ExtDir($module);
			if (!is_dir("Backup")) mkdir("Backup");
			unlink($BackupFile);
			require_once("../CORE/ArchiveTar.inc.php");
			$tar = new ArchiveTar($BackupFile,$compress);
			if ($NoDirectory)
 				$tar->addModify($extDir,"",$extDir);
			else {
				if (substr($extDir,0,3)=='../')
				  $tar->addModify($extDir,"","../");
				else
				  $tar->add($extDir);
			}
			if (($module=="") || ($module=="CORE"))
			{
				$tar->addModify("../images/","","../");
				$tar->addModify("../conf/cnf.inc.php","","../");
				$tar->addModify("../index.php","","../");
				$tar->addModify("../coreIndex.php","","../");
				$tar->addModify("../install.php","","../");
				$tar->addModify("../Tests.php","","../");
				$tar->addModify("../Help.php","","../");
			}
			return true;
		}
		return false;
	}

	function BackupFiles($module)
	{
		$ext_list=array();
		$extDir = "Backup/";
		if (is_dir($extDir))
		{
			$dh=opendir($extDir);
			while (($file = readdir($dh)) != false)
				if (substr($file,0,strlen($module))==$module)
				{
					$archive=substr($file,strlen($module)+1,-4);
					$pos=strpos($archive,"~");
					$user="";
					$date="";
					if ($pos>=0)
					{
						$user=substr($archive,0,$pos);
						$date=str_replace(array("_","-"),array(" ",":"),substr($archive,$pos+1));
						$vers="";
					}
					else
					{
						$user="";
						$date=str_replace(array("_","-"),array(" ",":"),$archive);
					}
					$pos_v=strpos($date,"^");
					$vers="";
					if ($pos_v>=0)
					{
						$vers=substr($date,$pos_v+1);
						$date=substr($date,0,$pos_v);
					}
					$ext_list[$file]=array($vers,$user,$date);
				}
		}
		return $ext_list;
	}


	function codeSignature($pass)
	{
		$text=$this->Name;
		$text.=$this->Appli;
		$text.=$this->Description;
		$text.=$pass;
		return md5($text);
	}

	function Unsign($pass)
	{
		$error="";
		if (($this->SignBy!='') && ($this->SignMD5!=''))
		{
			if ($this->SignMD5==$this->codeSignature($pass))
			{
				$this->SignBy='';
				$this->SignMD5='';
			}
			else
				$error="Mauvais mot de passe!";
		}
		else
			$error="Extension non signï¿½e!";
		return $error;
	}

	function Sign($CNX_OBJ,$pass)
	{
		$error="";
		if (($this->SignBy=='') && ($this->SignMD5==''))
		{
			$this->SignBy=$CNX_OBJ->LongName;
			$this->SignMD5=$this->codeSignature($pass);
		}
		else
			$error="Extension déjà signée!";
		return $error;
	}

	function ModifLock($module,$CnxObj)
	{
		if ($module=="CORE")
			$module="";
		$error="";
		$extDir = Extension::__ExtDir($module);
		$lockfile=$extDir."apaslock.sdk";
		$lock_info=Extension::GetLock($module);
		if ($CnxObj->CheckLockText($lock_info))
		{
			unlink($lockfile);
		}
		elseif ($lock_info=="")
		{
			if ($fh=fopen($lockfile,"w+"))
			{
				fwrite($fh,$CnxObj->Name."\n");
				fwrite($fh,date("d F Y G:i:s")."\n");
				fclose($fh);
			}
			$bachup_file=Extension::GetBackupFile($module);
			Extension::ArchiveExtension($module,$bachup_file);
		}
		else
			$error="Module vï¿½rouillï¿½";
		return $error;
	}

	function reloadArchive($module,$archiveFile)
	{
		if (is_file($archiveFile))
		{
			$extDir = Extension::__ExtDir($module);
			require_once("FunctionTool.inc.php");
			deleteDir($extDir);
			if ($module=="")
				deleteDir("../images/");
			require_once("../CORE/ArchiveTar.inc.php");
			$tar = new ArchiveTar($archiveFile);
			$tar->extract("../");
			$lockfile=$extDir."apaslock.sdk";
			unlink($lockfile);
		}
	}

	function CancelLock($module,$CNX_OBJ)
	{
		if ($module=="CORE")
			$module="";
		$error="";
		$extDir = Extension::__ExtDir($module);
		$lock_info=Extension::GetLock($module);
		if ($CNX_OBJ->CheckLockText($lock_info))
		{
			$bachup_file=Extension::GetBackupFile($module,$lock_info);
			Extension::reloadArchive($module,$bachup_file);
			unlink($bachup_file);
		}
		else
			$error="Annulation impossible";
		return $error;
	}

  	function Delete($name)
	{
		$extDir = "../extensions/$name/";
		require_once("FunctionTool.inc.php");
		deleteDir($extDir);
	}

  	//constructor
	function Extension($name)
	{
		$this->Name=$name;
		$this->Read();
	}

	function GetVersion()
	{
		return $this->Version[0].".".$this->Version[1].".".$this->Version[2].".".$this->Version[3];
	}

	function Read()
	{
		require_once("../CORE/setup_param.inc.php");
		$extDir = Extension::__ExtDir($this->Name);
		$extSetupFile = $extDir."setup.inc.php";
		if (is_file($extSetupFile))
		{
			$extention_famille='';
			$extention_description='';
			$extention_titre='';
			$extension_libre=true;
			$extention_appli='';
			$extend_tables=array();
			require($extSetupFile);
			if ($extention_titre=='') $extention_titre=$extention_description;
			if (($this->Name=='CORE') || ($this->Name=='applis')) $extention_famille=$this->Name;
			$this->Name=$extention_name;
			$this->Description=$extention_description;
			$this->Famille=$extention_famille;
			$this->Titre=$extention_titre;
			$this->Libre=$extension_libre?'o':'n';
			$this->Appli=$extention_appli;
			$this->Version=array($version_max, $version_min, $version_release, $version_build);
			$this->Depencies=$depencies;
			$this->Rights=array();
			foreach($rights as $key=>$value)
			{
				if (is_object($value))
					$r=$value;
				else
					$r=&new Param_Rigth($value,50);
				$this->Rights[$key]=$r;
			}
			$this->Actions=array();
			foreach($actions as $key=>$act)
			{
				if (is_file($extDir.$act->action.".act.php"))
					$this->Actions[]=$act;
			}
			//$this->Actions=$actions;
			$this->Menus=$menus;
			$this->Params=array();
			foreach($params as $key=>$prm)
			{
				if (is_object($prm))
					$this->Params[$key]=$prm;
				else
					$this->Params[$key]=new Param_Parameters($key,$prm,$key);
			}
			$this->ExtendTables=$extend_tables;
		}
		else
		{
			if ($this->Name!="")
				$this->Description=$this->Name;
			else
				$this->Description="Le noyau Lucterios";
			$this->Version=array(0, 0, 0, 1);
			$this->Depencies=array();
			$this->Rights=array();
			$this->Actions=array();
			$this->Menus=array();
			$this->Params=array();
			$this->AsInstallFunc=false;
			$this->ExtendTables=array();
		}
		return "";
	}

	function GetMenuListWithoutAction()
	{
		$result=array();
		foreach($this->Menus as $menu_item)
			if (trim($menu_item->act)=="")
				array_push($result,$menu_item->description);
		foreach($this->Depencies as $depend_item)
		{
			$new_extension=new Extension($depend_item->name);
			foreach($new_extension->Menus as $menu_item)
				if (trim($menu_item->act)=="")
					array_push($result,$menu_item->description);
		}
		return $result;
	}

	function Write()
	{
		require_once("../CORE/setup_param.inc.php");
		require_once("FunctionTool.inc.php");
		$extDir = Extension::__ExtDir($this->Name);
		if (!is_dir($extDir))
		{
			mkdir($extDir);
			chmod($extDir,0777);
		}
		if (!is_dir($extDir))
		{
			return "Extension non crï¿½ï¿½!<br>";
			exit;
		}

		if (!$fh=OpenInWriteFile($extDir."/setup.inc.php","setup"))
		{
			return "Fichier setup non crï¿½ï¿½!";
			exit;
		}

		fwrite($fh,"\$extention_name=".getStringToWrite($this->Name).";\n");
		fwrite($fh,"\$extention_description=".getStringToWrite($this->Description).";\n");
		fwrite($fh,"\$extention_appli=".getStringToWrite($this->Appli).";\n");
		fwrite($fh,"\$extention_famille=".getStringToWrite($this->Famille).";\n");
		fwrite($fh,"\$extention_titre=".getStringToWrite($this->Titre).";\n");
		$libre=$this->Libre=='o'?'true':'false';
		fwrite($fh,"\$extension_libre=$libre;\n");
		fwrite($fh,"\n");

		fwrite($fh,"\$version_max=".$this->Version[0].";\n");
		fwrite($fh,"\$version_min=".$this->Version[1].";\n");
		fwrite($fh,"\$version_release=".$this->Version[2].";\n");
		fwrite($fh,"\$version_build=".$this->Version[3].";\n");
		fwrite($fh,"\n");

		fwrite($fh,"\$depencies=array();\n");
		foreach($this->Depencies as $key=>$value)
			fwrite($fh,"\$depencies[$key] = new Param_Depencies(".getStringToWrite($value->name).", $value->version_majeur_max, $value->version_mineur_max, $value->version_majeur_min, $value->version_mineur_min, ".($value->optionnal?'true':'false').");\n");
		fwrite($fh,"\n");

		fwrite($fh,"\$rights=array();\n");
		foreach($this->Rights as $key=>$value)
		{
			if (is_object($value))
				$r=$value;
			else
				$r=new Param_Rigth("$value");
			fwrite($fh,"\$rights[$key] = new Param_Rigth(".getStringToWrite($r->description).",".$r->weigth.");\n");
		}
		fwrite($fh,"\n");

		fwrite($fh,"\$menus=array();\n");
		foreach($this->Menus as $key=>$value)
		{
			if ($value->modal==1) $txt_modal='1'; else $txt_modal='0';
			fwrite($fh,"\$menus[$key] = new Param_Menu(".getStringToWrite($value->description).", ".getStringToWrite($value->pere).", ".getStringToWrite($value->act).", ".getStringToWrite($value->icon).", ".getStringToWrite($value->shortcut).", $value->position , $txt_modal, ".getStringToWrite($value->help).");\n");
		}
		fwrite($fh,"\n");

		fwrite($fh,"\$actions=array();\n");
		$act_texts=array();
		foreach($this->Actions as $value)
			array_push($act_texts,"$value->action|new Param_Action(".getStringToWrite($value->description).", ".getStringToWrite($value->action).", $value->rightNumber);\n");
		$act_texts=array_unique($act_texts);
		sort($act_texts);
		foreach($act_texts as $key=>$act_text)
		{
			$act_text=substr(strrchr($act_text,'|'),1);
			fwrite($fh,"\$actions[$key] = ".$act_text);
		}
		fwrite($fh,"\n");

		require_once "FunctionTool.inc.php";
		fwrite($fh,"\$params=array();\n");
		foreach($this->Params as $key=>$value)
			fwrite($fh,"\$params[\"$key\"] = new Param_Parameters(".getStringToWrite($value->name).", ".getStringToWrite($value->defaultvalue).", ".getStringToWrite($value->description).", ".$value->type.", ".ArrayToString($value->extend).");\n");
		fwrite($fh,"\n");

		fwrite($fh,"\$extend_tables=array();\n");
		foreach($this->ExtendTables as $key=>$tbl_values){
			$value_string='';
			$value_string.=getStringToWrite($tbl_values[0]).",";
			$value_string.=getStringToWrite($tbl_values[1]);
			if (isset($tbl_values[2])) {
				$value_string.=',array(';
				foreach($tbl_values[2] as $sub_key=>$sub_values)
					$value_string.=getStringToWrite($sub_key).'=>'.getStringToWrite($sub_values).',';
				$value_string.=')';
			}
			fwrite($fh,"\$extend_tables[\"$key\"] = array($value_string);\n");
		}
		fwrite($fh,"\n");

		fwrite($fh,"?>");
		fclose($fh);

		return "";
	}

	function GetTableList()
	{
		require_once("Table.inc.php");
		$mng=new TableManage();
		return $mng->GetList($this->Name);
	}

	function GetLibrayList()
	{
		require_once("Library.inc.php");
		$mng=new LibraryManage();
		return $mng->GetList($this->Name);
	}

	function GetPrintList()
	{
		require_once("Print.inc.php");
		$mng=new PrintingManage();
		return $mng->GetList($this->Name);
	}

	function GetImageList()
	{
		require_once("Image.inc.php");
		$mng=new ImageManage();
		return $mng->GetList($this->Name);
	}

	function IncrementBuild()
	{
		$this->refreshExtendTable();
		$this->Version[3]=1+(int)$this->Version[3];
		return $this->Write();
	}

	function IncrementRelease()
	{
		$this->refreshExtendTable();
		$this->Version[2]=1+(int)$this->Version[2];
		return $this->Write();
	}

	function IncrementSubVersion()
	{
		$this->refreshExtendTable();
		$this->Version[1]=1+(int)$this->Version[1];
		$this->Version[2]=1;
		return $this->Write();
	}

	function IncrementVersion()
	{
		$this->refreshExtendTable();
		$this->Version[0]=1+(int)$this->Version[0];
		$this->Version[1]=1;
		$this->Version[2]=1;
		return $this->Write();
	}

	function refreshExtendTable()
	{
		$this->ExtendTables=array();
		require_once("Class/Table.inc.php");
		$mng_tbl=new TableManage();
		$tbl_list=$mng_tbl->GetList($this->Name);
		foreach($tbl_list as $item) {
			$tbl=new Table($item,$this->Name);
			if ($tbl->Title=='')
				$title=$this->Name.'.'.$tbl->Name;
			else
				$title=$tbl->Title;
			$ref=$tbl->getReferenceList();
			$this->ExtendTables[$tbl->Name]=array($title,$tbl->Heritage,$ref);
		}
	}

}

?>
