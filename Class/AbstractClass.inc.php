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
	public $Suffix=".inc.php";

	public function GetExtDir($extensionName="")
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

	public function Delete($name,$extensionName="")
	{
		$extDir = $this->GetExtDir($extensionName);
		$extName = $name.$this->Suffix;
		if (is_file($extDir.$extName))
		{
			require_once("Extension.inc.php");
			$extObj=new Extension($extensionName);
			$repo=$extObj->GetGitRepoObj();
			$repo->run("rm -f '$extName'");
		}
	}

	public function GetList($extensionName="")
	{
		$file_list=array();
		$extDir = $this->GetExtDir($extensionName);
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
	public $Name="";
	public $ExtensionName="";

	//constructor
	public function __construct($name,$extensionName="")
	{
		$this->ExtensionName=$extensionName;
		$this->Name=$name;
		$this->Read();
	}

	public function GetName($sep="::") 
	{
		return $this->Name;
	}

	protected function AddGitFile($fileName)	
	{
		require_once("Extension.inc.php");
		$extObj=new Extension($this->ExtensionName);
		$repo=$extObj->GetGitRepoObj();
		$res=$repo->add("'$fileName'");
		echo "<!--- res:$res -->\n";
	}

	public function Write()
	{
 	}

	public function Read()
	{
	}
}
