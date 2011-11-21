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

function addText($list,$new)
{
	$list.=";$new";
	return $list;
}

function reFill($list)
{
	$new=array();
	foreach($list as $item)
		if (trim($item)!="")
			$new[]=trim($item);
	return $new;
}

require_once("AbstractClass.inc.php");

class ConnectManage extends AbstractClassManage
{
	public $Suffix=".info";
	public function GetExtDir()
	{
		return "./CNX/";
	}
	public function GetList($WithAdmin=true)
	{
		$file_list=array();
		$extDir = $this->GetExtDir();
		if (is_dir($extDir))
		{
			$dh=opendir($extDir);
			while (($file = readdir($dh)) != false)
			{
				$size_suffix=strlen($this->Suffix);
				if(is_file($extDir . $file) && (substr($file,-1*$size_suffix,$size_suffix)==$this->Suffix))
				{
					$FileName=substr($file,0,-1*$size_suffix);
					if ($WithAdmin || (!$WithAdmin && ($FileName!="admin")))
						$file_list[]=$FileName;
				}
			}
		}
		return $file_list;
	}

}

class Connect extends AbstractClass
{
	public $Pwcrypt="";
	public $LongName="";
	public $NoView=array();
	public $Modified=array();
	public $Mng;

  	//constructor
  	public function __construct($name,$extensionName="")
	{
		$this->Mng=new ConnectManage();
		parent::__construct($name,$extensionName);
	}

	public function CallHeader()
	{
		setcookie("APAS_SDKUSER","",0);
		unset($_COOKIE['APAS_SDKUSER']);
	}

	public function Read()
	{
		$this->Pwcrypt="";
		$this->LongName="";
		$this->NoView=array();
		$this->ReadOnly=array();
		$extDir = $this->Mng->GetExtDir($this->ExtensionName);
		$cnxfile = $extDir.$this->Name.$this->Mng->Suffix;
		if (is_file($cnxfile))
		{
			$file_cnx = file($cnxfile);
			if (count($file_cnx)>0)
				$this->Pwcrypt=trim($file_cnx[0]);
			if (count($file_cnx)>1)
				$this->LongName=trim($file_cnx[1]);
			if (count($file_cnx)>2)
				$this->NoView=reFill(explode(";",trim($file_cnx[2])));
			if (count($file_cnx)>3)
				$this->Modified=reFill(explode(";",$file_cnx[3]));
		}
	}
	public function Write()
	{
		$extDir = $this->Mng->GetExtDir($this->ExtensionName);
		$cnxfile = $extDir.$this->Name.$this->Mng->Suffix;
		if ($fh=fopen($cnxfile,"w+"))
		{
			fwrite($fh,$this->Pwcrypt."\n");
			fwrite($fh,$this->LongName."\n");
			fwrite($fh,array_reduce($this->NoView,'addText')."\n");
			fwrite($fh,array_reduce($this->Modified,'addText')."\n");
			fclose($fh);
		}
		return (is_file($cnxfile)==true);
	}
	public function CheckLockText($LockText)
	{
		$size=strlen($this->Name);
		$text=substr($LockText,0,$size);
		return ($text==$this->Name);
	}

	public function ChangePWD($PassWord)
	{
		$this->Pwcrypt=md5($this->Name.$PassWord);
	}
	public function IsValid($PassWord)
	{
		if (($this->Name=="admin") && ($this->Pwcrypt==""))
		{
			$this->ChangePWD($PassWord);
			$this->LongName="Administrateur";
			$this->NoView=array();
			$this->ReadOnly=array();
			return $this->Write();
		}
		else
		{
			$pwd=md5($this->Name.$PassWord);
			return ($this->Pwcrypt==$pwd);
		}
	}

	public function CanWriteModule($ModuleName)
	{
		return (!in_array($ModuleName,$this->NoView) && in_array($ModuleName,$this->Modified));
	}

	public function IsViewModule($ModuleName)
	{
		return (!in_array($ModuleName,$this->NoView));
	}

	public function IsReadOnly($ModuleName)
	{
		if ($this->CanWriteModule($ModuleName))
		{
			$lock=Extension::GetLock($ModuleName);
			if ($lock=="")
				return true;
			else
				return (!$this->CheckLockText($lock));
		}
		else
			return true;
	}
}

?>