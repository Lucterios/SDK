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

global $GIT_STATUS_TAB;
$GIT_STATUS_TAB=array(  
'M '=>'modifié',
' M'=>'modifié localement',
'A '=>'ajouté',
' A'=>'ajouté localement',
'AM'=>'ajouté et modifié',
'D '=>'supprimé',
' D'=>'supprimé localement',
'R '=>'renommé',
'C '=>'copié',
'U '=>'mise à jour, mais non fusionné',
'DD'=>'non-fusionné, supprimé local/distant',
'AU'=>'non-fusionné, ajouté local',
'UD'=>'non-fusionné, supprimé distant',
'UA'=>'non-fusionné, ajoutée distant',
'DU'=>'non-fusionné, supprimé local',
'AA'=>'non-fusionné, ajoutée local/distant',
'UU'=>'non-fusionné, modifiée local/distant',
'??'=>'non-géré',
'!!'=>'ignoré');

class Extension
{
	public $Name="";
	public $Appli="";
	public $Famille='';
	public $Titre='';
	public $Description="";
	public $Libre='o';
	public $Version=array(0,0,0,1);
	public $Depencies=array();
	public $Rights=array();
	public $Actions=array();
	public $Menus=array();
	public $Params=array();
	public $AsInstallFunc=false;
	public $SignBy="";
	public $SignMD5="";
	public $ExtendTables=array();
	public $Signals=array();

	public static function GetExtDir($name)
	{
		if (($name=="") || ($name=="CORE"))
			$extDir = "../CORE/";
		else
			$extDir = "../extensions/$name/";
		return $extDir;
	}

	public static function GetList($CNX_OBJ=null,$onlyVersion=true)
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
				if ($onlyVersion)
				  $ext_list[$extname]=$ext->GetVersion();
				else
				  $ext_list[$extname]=$ext;
			}
		}
		return $ext_list;
	}

	public function GetArchiveFile($suffic=".tar")
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

	public static function ArchiveExtension($module,$BackupFile,$NoDirectory=false,$compress=null)
	{
		if ($BackupFile!="")
		{
			$extDir = Extension::GetExtDir($module);
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
				$tar->addModify("../BackgroundTask.php","","../");
			}
			return true;
		}
		return false;
	}

	//constructor
	public function __construct($name)
	{
		$this->Name=$name;
		$this->Read();
	}

  	public function Delete($name)
	{
		$extDir = "../extensions/$name/";
		require_once("FunctionTool.inc.php");
		deleteDir($extDir);
	}

	public function GetVersion()
	{
		return $this->Version[0].".".$this->Version[1].".".$this->Version[2].".".$this->Version[3];
	}

	public function GetGitRepoObj($initGit=false) {
		if (($this->Name=="") || ($this->Name=="CORE"))
			$extDir = "../";
		else
			$extDir = Extension::GetExtDir($this->Name);
		try {
		  require_once("Git.php");
		  if ($initGit) {
			$repo=Git::create($extDir);
			$conf_file=file("CNX/Conf_Manage.dt");
			$gitUser=$conf_file[0];
			$gitEmail=$conf_file[1];
			$repo->run('config --local user.name "'.$gitUser.'"');
			$repo->run('config --local user.email "'.$gitEmail.'"');
		  }
		  else
			$repo=Git::open($extDir);
		}
		catch(Exception $e){
		  $repo=NULL;
		}
		return $repo;
	}

	public static function CreateGitRepoByClone($currentDir,$repoUrl,$gitUser,$gitEmail) {
		require_once("Git.php");
		@mkdir($currentDir);
		$repo=new GitRepo($currentDir, true, false);
		$repo->run("clone $repoUrl ".realpath($currentDir));
		$repo->run('config --local user.name "'.$gitUser.'"');
		$repo->run('config --local user.email "'.$gitEmail.'"');
	}

	public function GetInfoGit() {
		$git_info="";
		$repo=$this->GetGitRepoObj();
		if ($repo!=NULL) {
		  $branch=$repo->active_branch();
		  if ($branch!='')
		      $git_info.="Branche:".$branch;
		  $status=$repo->getStatusNumber();
		  if ($status['?']>0)
		      $git_info.=" Non-archivés:".$status['?'];
		  if ($status['A']>0)
		      $git_info.=" Ajoutés:".$status['A'];
		  if ($status['D']>0)
		      $git_info.=" Supprimés:".$status['D'];
		  if ($status['M']>0)
		      $git_info.=" Modifiés:".$status['M'];
		  if ($status['U']>0)
		      $git_info.=" Non-mergés:".$status['U'];
		}
		else {
		  $git_info="** Non gérer par GIT **";
		}
		return $git_info;
	}

	public function Read()
	{
		require_once("../CORE/setup_param.inc.php");
		$extDir = Extension::GetExtDir($this->Name);
		$extSetupFile = $extDir."setup.inc.php";
		if (is_file($extSetupFile))
		{
			$extention_famille='';
			$extention_description='';
			$extention_titre='';
			$extension_libre=true;
			$extention_appli='';
			$extend_tables=array();
			$signals=array();
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
			$this->Signals=$signals;
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

	public function GetMenuListWithoutAction()
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

	public function Write()
	{
		require_once("../CORE/setup_param.inc.php");
		require_once("FunctionTool.inc.php");
		$extDir = Extension::GetExtDir($this->Name);
		if (!is_dir($extDir))
		{
			mkdir($extDir,0777);
		}
		if (!is_dir($extDir))
		{
			return "Extension non créé!<br>";
			exit;
		}

		if (!$fh=OpenInWriteFile($extDir."setup.inc.php","setup"))
		{
			return "Fichier setup non créé!";
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
		fwrite($fh,"\$signals=array();\n");
		$SignID=0;
		foreach($this->Signals as $key=>$sign_values){
			$value_string='';
			$value_string.=getStringToWrite($sign_values[0]).",";
			$value_string.=getStringToWrite($sign_values[1]).",";
			$value_string.=getStringToWrite($sign_values[2]);
			fwrite($fh,"\$signals[$SignID] = array($value_string);\n");
			$SignID++;
		}
		fwrite($fh,"\n");

		fwrite($fh,"?>");
		fclose($fh);
		chmod($extDir."/setup.inc.php", 0666);

		return "";
	}

	public function GetTableList()
	{
		require_once("Table.inc.php");
		$mng=new TableManage();
		return $mng->GetList($this->Name);
	}

	public function GetLibrayList()
	{
		require_once("Library.inc.php");
		$mng=new LibraryManage();
		return $mng->GetList($this->Name);
	}

	public function GetPrintList()
	{
		require_once("Print.inc.php");
		$mng=new PrintingManage();
		return $mng->GetList($this->Name);
	}

	public function GetImageList()
	{
		require_once("Image.inc.php");
		$mng=new ImageManage();
		return $mng->GetList($this->Name);
	}

	public function IncrementBuild()
	{
		$this->refreshExtendTable();
		$this->Version[3]=1+(int)$this->Version[3];
		return $this->Write();
	}

	public function IncrementRelease()
	{
		$this->refreshExtendTable();
		$this->Version[2]=1+(int)$this->Version[2];
		return $this->Write();
	}

	public function IncrementSubVersion()
	{
		$this->refreshExtendTable();
		$this->Version[1]=1+(int)$this->Version[1];
		$this->Version[2]=1;
		return $this->Write();
	}

	public function IncrementVersion()
	{
		$this->refreshExtendTable();
		$this->Version[0]=1+(int)$this->Version[0];
		$this->Version[1]=1;
		$this->Version[2]=1;
		return $this->Write();
	}

	public function refreshExtendTable()
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
