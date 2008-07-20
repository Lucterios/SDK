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

class AbstractClassManage
{
	var $Suffix=".inc.php";

	function __ExtDir($extensionName="")
	{
		if (($extensionName=="") || ($extensionName=="CORE"))
			$extDir = "../CORE/";
		elseif ($extensionName=="applis")
		{
			$extDir = "../applis/";
			if (!is_dir($extDir))
				$extDir = "../extensions/applis/";
		}
		else
			$extDir = "../extensions/$extensionName/";
		return $extDir;
	}

	function Delete($name,$extensionName="")
	{
		$extDir = $this->__ExtDir($extensionName);
		$extFile = $extDir.$name.$this->Suffix;
		if (is_file($extFile))
		{
			unlink($extFile);
		}
	}

	function GetList($extensionName="")
	{
		$file_list=array();
		$extDir = $this->__ExtDir($extensionName);
		if (is_dir($extDir))
		{
			$dh=opendir($extDir);
			while (($file = readdir($dh)) != false)
			{
				$size_suffix=strlen($this->Suffix);
				if(is_file($extDir . $file) && (($size_suffix==0) || (substr($file,-1*$size_suffix,$size_suffix)==$this->Suffix)) && ($file!="setup.inc.php"))
				{
					if ($size_suffix==0)
						$FileName=$file;
					else
						$FileName=substr($file,0,-1*$size_suffix);
					array_push($file_list,$FileName);
				}
			}
		}
		sort($file_list);
		return $file_list;
	}
}

class AbstractClass
{
	var $Name="";
	var $ExtensionName="";

  	//constructor
  	function AbstractClass($name,$extensionName="")
	{
		$this->ExtensionName=$extensionName;
		$this->Name=$name;
		$this->Read();
  	}

	function Write()
	{
	}

	function Read()
	{
	}
}
