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


require_once("AbstractClass.inc.php");

class ConfigManage extends AbstractClassManage
{
	public $Suffix=".php";
	
	public function GetExtDir()
	{
		return "../conf/";
	}
	public function GetList()
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
					$file_list[]=$FileName;
				}
			}
		}
		return $file_list;
	}

}

class Config extends AbstractClass
{
	public $dbcnf=array();
	public $debugMode=false;
	public $xmlSaving="";
	public $Mng;

  	//constructor
  	public function __construct($name,$extensionName="")
	{
		$this->Mng=new ConfigManage();
		parent::__construct($name,$extensionName);
	}

	public function CallHeader()
	{
		setcookie("APAS_SDKUSER","",0);
		unset($_COOKIE['APAS_SDKUSER']);
	}

	public function Read()
	{
		$this->dbcnf=array("dbtype"=>"mysql","dbhost"=>"localhost","dbuser"=>"root","dbpass"=>"","dbname"=>"lucterios");
		$this->debugMode=false;
		$this->xmlSaving="";
		$extDir = $this->Mng->GetExtDir();
		$cnxfile = $extDir.$this->Name.$this->Mng->Suffix;
		if (is_file($cnxfile))
		{
			require($cnxfile);
			if (is_array($dbcnf))
				$this->dbcnf=$dbcnf;
			if (isset($debugMode))
				$this->debugMode=($debugMode=='o');
			if (isset($XML_SAVING))
				$this->xmlSaving=$XML_SAVING;
		}
	}
	public function Write()
	{
		$extDir = $this->Mng->GetExtDir();
		$cnxfile = $extDir.$this->Name.$this->Mng->Suffix;
		if ($fh=fopen($cnxfile,"w+"))
		{
			fwrite($fh,"<?php\n");
			fwrite($fh,"/******************************************************************************/\n");
			fwrite($fh,"/* Fichier cnf.db.php\n");
			fwrite($fh,"/* fichier de configuration de la base de données de l'application\n");
			fwrite($fh,"/******************************************************************************/\n");
			fwrite($fh,"\n");
			fwrite($fh,"\$dbcnf = array(\n");
			fwrite($fh,"\t\"dbtype\"=>\"mysql\",\n");
			fwrite($fh,"\t\"dbhost\"=>\"".$this->dbcnf['dbhost']."\",\n");
			fwrite($fh,"\t\"dbuser\"=>\"".$this->dbcnf['dbuser']."\",\n");
			fwrite($fh,"\t\"dbpass\"=>\"".$this->dbcnf['dbpass']."\",\n");
			fwrite($fh,"\t\"dbname\"=>\"".$this->dbcnf['dbname']."\"\n");
			fwrite($fh,");\n");
			fwrite($fh,"\n");
			fwrite($fh,"// activation de debug\n");
			if ($this->debugMode)
				fwrite($fh,"\$debugMode = 'o';\n");
			else
				fwrite($fh,"\$debugMode = 'n';\n");
			fwrite($fh,"\n");
			if ($this->xmlSaving!='')
				fwrite($fh,"\$XML_SAVING = '".$this->xmlSaving."';\n");
			fwrite($fh,"\n");
			fwrite($fh,"/******************************************************************************/\n");
			fwrite($fh,"?>\n");
			fclose($fh);
		}
		return (is_file($cnxfile)==true);
	}
}

?>