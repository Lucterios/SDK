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
require_once("AbstractClass.inc.php");

class HelpManage extends AbstractClassManage
{
	var $Suffix=".xhlp";

	var $HelpDescriptions=array();
	var $HelpTitle='';
	var $HelpPosition=-1;

	function HelpManage($ExtensionName)
	{
		$HelpTitle='';
		$HelpPosition=-1;			
		$extDir = $this->__ExtDir($ExtensionName);
		if (!is_dir($extDir))
			mkdir($extDir,0777);
		$hlp_mn_file=$extDir.'menu.hlp.php';
		if (is_file($hlp_mn_file))
		{
			require($hlp_mn_file);
			$this->HelpDescriptions=$HelpDescriptions;
			$this->HelpTitle=$HelpTitle;	
			$this->HelpPosition=$HelpPosition;
		}
	}

	function addHelp($ExtensionName,$HelpName,$Description,$num)
	{
		if ($this->HelpDescriptions[$num][0]!=$HelpName)
		{
			$new_desc=array();
			foreach($this->HelpDescriptions as $id=>$val)
			{
				if (count($new_desc)==$num)
					array_push($new_desc,array($HelpName,$Description,1));
				if ($val[0]!=$HelpName)
					array_push($new_desc,$val);
			}
			if (count($new_desc)==$num)
				array_push($new_desc,array($HelpName,$Description,1));
			if ($num<0)
				array_push($new_desc,array($HelpName,$Description,0));
			$this->HelpDescriptions=$new_desc;
		}
		else
		{
			$this->HelpDescriptions[$num][1]=$Description;
			if ($num<0)
				$this->HelpDescriptions[$num][2]=0;
			else
				$this->HelpDescriptions[$num][2]=1;
		}
		$this->writeHelp($ExtensionName);
	}

	function Delete($name,$extensionName="")
	{
		AbstractClassManage::Delete($name,$extensionName);
		$pos=0;
		$new_desc=array();
		foreach($this->HelpDescriptions as $val)
			if ($val[0]!=$name)
				array_push($new_desc,$val);
		$this->HelpDescriptions=$new_desc;
		$this->writeHelp($extensionName);
	}

	function writeHelp($ExtensionName)
	{
		$extDir = $this->__ExtDir($ExtensionName);
		$hlp_mn_file=$extDir.'menu.hlp.php';
		require_once("FunctionTool.inc.php");
		if (!$fh=OpenInWriteFile($hlp_mn_file,"help"))
			return "Fichier help '$hlp_mn_file' non créé!";
		fwrite($fh,$code."\$HelpTitle='".$this->HelpTitle."';\n");
		fwrite($fh,$code."\$HelpPosition=".$this->HelpPosition.";\n");
		fwrite($fh,$code."\n");
		fwrite($fh,$code."\$HelpDescriptions=array();\n");
		$id=0;
		foreach($this->HelpDescriptions as $line)
		{
			fwrite($fh,$code."\$HelpDescriptions[$id]=array('".$line[0]."','".$line[1]."',".$line[2].");\n");
			$id++;
		}
		fwrite($fh,$code."\n");
		fwrite($fh,"?>\n");
		fclose($fh);
		return null;
	}

	function getHelp($HelpName)
	{
		foreach($this->HelpDescriptions as $id=>$val)
			if ($val[0]==$HelpName)
			{
				if ($val[2]==1)
					return array($id,$val);
				else
					return array(-1,$val);
			}
		return array(-1,array($HelpName,'',0));
	}

	function __ExtDir($extensionName="")
	{
		if (($extensionName=="") || ($extensionName=="CORE"))
			$extDir = "../CORE/help/";
		elseif ($extensionName=="applis")
		{
			$extDir = "../applis/help/";
			if (!is_dir($extDir))
				$extDir = "../extensions/applis/help/";
		}
		else
			$extDir = "../extensions/$extensionName/help/";
		return $extDir;
	}

 	function GetImageList($extensionName)
	{
		$imgs=array();
		$extDir = $this->__ExtDir($extensionName);
		if (is_dir($extDir))
		{
			$dh=opendir($extDir);
			while (($file = readdir($dh)) != false)
			{
				$size_suffix=strlen($this->Suffix);
				if(is_file($extDir . $file) && (substr($file,-1*$size_suffix,$size_suffix)!=$this->Suffix) && ($file!="menu.hlp.php"))
					array_push($imgs,$file);
			}
		}
		sort($imgs);
		return $imgs;
	}

	function DeleteImage($name,$extensionName="")
	{
		$extDir = $this->__ExtDir($extensionName);
		$extFile = $extDir.$name;
		if (is_file($extFile))
		{
			unlink($extFile);
		}
	}

}

class Help extends AbstractClass
{
	var $CodeFile=array();
	var $Mng;

  	//constructor
  	function Help($name,$extensionName="")
	{
		$this->Mng=new HelpManage($extensionName);
		$this->AbstractClass($name,$extensionName);
                $this->CodeLineBegin=1;
	}

	function getDescription()
	{
		return $this->Mng->getHelp($this->Name);
	}

	function AddBase64Img($image)
	{
		List($name,$filebased64)=split(';',$image);
		$extDir = $this->Mng->__ExtDir($this->ExtensionName);
		if (!is_dir($extDir))
			mkdir($extDir,0777);
		$extImgFile = $extDir.$name;
		if ($handle = fopen($extImgFile, 'w')) {
			$content=base64_decode($filebased64,true);
			if (fwrite($handle,$content) === FALSE)
				return "Erreur d'écriture";
			fclose($handle);
			return '';
		}
		else
			return "Erreur d'ouverture";
	}

	function AddImage($file)
	{
		$extDir = $this->Mng->__ExtDir($this->ExtensionName);
		$extImgFile = $extDir.$file['name'];
		copy($file['tmp_name'],$extImgFile);
	}

	function Write()
	{
		require_once("FunctionTool.inc.php");
		$extDir = $this->Mng->__ExtDir($this->ExtensionName);
		if (!is_dir($extDir))
			mkdir($extDir,0777);
		$extLibFile = $extDir.$this->Name.$this->Mng->Suffix;
		if (!$fh=fopen($extLibFile,"w+"))
		{
			return "Fichier help '$extLibFile' non créé!";
			exit;
		}
		foreach($this->CodeFile as $code)
		{
			$code=str_replace(array("\\\"","\\'","\\\\"),array('"',"'","\\"),$code);
			fwrite($fh,$code."\n");
		}
		fclose($fh);
		return "";
	}

	function Read()
	{
		$this->CodeFile=array();
		$extDir = $this->Mng->__ExtDir($this->ExtensionName);
		$extHelpFile = $extDir.$this->Name.$this->Mng->Suffix;
		if (is_file($extHelpFile))
		{
			$help_lines=file($extHelpFile);
			foreach($help_lines as $help_line)
				array_push($this->CodeFile,trim($help_line));
		}
                $this->CodeLineBegin=0;
		return "";
	}
}

?>