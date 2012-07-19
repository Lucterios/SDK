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
require_once("Extension.inc.php");

define('SEP',"_APAS_");

class CodeAbstractManage extends AbstractClassManage
{
	public $Suffix=".inc.php";
    	public $SEP="_APAS_";

	public function GetFileName($name,$extensionName="",$tableName="")
	{
		$extDir = $this->GetExtDir($extensionName);
		if ($tableName=="")
			return $extDir.$name.$this->Suffix;
		else
			return $extDir.$tableName.$this->SEP.$name.$this->Suffix;
	}

	public function GetNameWithSep($name,$tableName)
	{
		if ($tableName=="")
			return $name;
		else
			return $tableName.$this->SEP.$name;
	}

	public function Delete($name,$extensionName="",$tableName="")
	{
		$result=false;
		$extFile = $this->GetFileName($name,$extensionName,$tableName);
		if (is_file($extFile))
		{
			unlink($extFile);
			$result=false;
		}
		return $result;
	}

	public function GetTableName($FileName)
	{
		$pos=strpos($FileName,$this->SEP);
		if ($pos === false)
			return "";
		else
			return substr($FileName,0,$pos);
	}

	public function GetNameNoTable($FileName)
	{
		$pos=strpos($FileName,$this->SEP);
		if ($pos === false)
			return $FileName;
		else
			return substr($FileName,$pos+strlen($this->SEP));
	}

	public function GetName($FileName)
	{
		$tbl=$this->GetTableName($FileName);
		$nm=$this->GetNameNoTable($FileName);
		if ($tbl=="")
			return $nm;
		else
			return "$tbl::$nm";
	}

	public function IsInTable($extensionName="",$FileName,$tableName="")
	{
		if ($tableName=="*")
			return true;
		elseif ($tableName=="")
		{
			$table_name=$this->GetTableName($FileName);
			return (!is_file($this->GetExtDir($extensionName)."/$table_name.tbl.php"));
		}
		elseif (($tableName!="") && (substr($FileName,0,strlen($tableName)+strlen($this->SEP))==($tableName.$this->SEP)))
			return true;
		else
			return false;
	}

	public function GetList($extensionName="",$tableName="")
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
					if ($this->IsInTable($extensionName,$FileName,$tableName))
						array_push($file_list,$FileName);
				}
			}
		}
		sort($file_list);
		return $file_list;
	}
}

class CodeAbstract extends AbstractClass
{
	public $TableName="";

	public $Description="";
	public $IndexName="";
	public $TableFiles=array();
	public $CodeFunction=array();
	public $Parameters=array();
	public $CodeLineBegin=0;
	public $CodeLineEnd=0;
	public $Mng;

  	//constructor
  	public function __construct($name,$extensionName="",$tableName="")
	{
		if ($tableName!="")
			$this->TableName=$tableName;
		else
			$this->TableName=$this->Mng->GetTableName($name);
		parent::__construct($this->Mng->GetNameNoTable($name),$extensionName);
	}

	public function GetParams($WithSep=true)
	{
		$params="";
		if ($WithSep)
			$params.="(";
		foreach($this->Parameters as $Param_name=>$Param_val)
		{
			$params.="\$$Param_name";
			if (is_string($Param_val))
				$params.="=$Param_val";
			$params.=", ";
		}
		if (count($this->Parameters)>0)
			$params=substr($params,0,-2);
		if ($WithSep)
			$params.=")";
		return $params;
	}

	public function SetParams($code_params)
	{
		$this->Parameters=array();
		$params=explode(",",$code_params);
		foreach($params as $param)
		{
			$param_val=explode("=",trim($param));
			if (count($param_val)>0)
			{
				$val_name=$param_val[0];
				if (($val_name!="") && ($val_name[0]=='$'))
					$val_name=substr($val_name,1);
			}
			if (count($param_val)==1)
				$this->Parameters[$val_name]=0;
			elseif (count($param_val)==2)
				$this->Parameters[$val_name]=$param_val[1];
		}
	}

	public function Modify($code_id,$tablename)
	{
		global $script_code;
		global $code_name;
		global $code_desc;
		global $code_params;
		global $code_tableFiles;
		global $code_index;
		if (isset($code_params) && isset($code_name) && isset($code_desc))
		{
			if (isset($code_tableFiles))
				$this->TableFiles=$code_tableFiles;
			else
				$this->TableFiles=array();
			if (isset($code_index))
				$this->IndexName=$code_index;
			$this->Description=$code_desc;
			if (isset($script_code))
				$this->CodeFunction=explode("\n",rtrim($script_code));
			$this->SetParams($code_params);
			if (isset($code_index))
			{
				if ((strlen($code_index)>0) && (substr($code_index,0,1)=='$'))
					$code_index=substr($code_index,1);
				$this->IndexName=$code_index;
			}
	
			if ($code_name!=$code_id)
			{
				$this->Mng->Delete($this->Name,$this->ExtensionName,$this->TableName);
				$this->TableName=$tablename;
				$this->Name=$this->Mng->GetNameNoTable($code_name);
				$table_name=$this->Mng->GetTableName($code_name);
				if ($table_name!="")
					$this->TableName=$table_name;
			}
			return true;
		}
		else
		{
			return false;
		}
	}

	public function GetFileName()
	{
		return $this->Mng->GetFileName($this->Name,$this->ExtensionName,$this->TableName);
	}

	public function GetName($sep="::")
	{
		if ($this->TableName=="")
			return $this->Name;
		else
			return $this->TableName.$sep.$this->Name;
	}

	public function Check()
	{
		$extCodeFile = $this->GetFileName();
		if (!is_file($extCodeFile))
			$this->Write();
	}
	
	protected function WriteTables($fh)
	{
		fwrite($fh,"//@TABLES@\n");
		if ($this->TableName!="")
		{
			$owneTable=$this->ExtensionName."/".$this->TableName;
			if (!in_array($owneTable,$this->TableFiles))
				$this->TableFiles[]=$owneTable;
		}
		foreach($this->TableFiles as $tbl_file)
			if (substr($tbl_file,0,4)=="CORE")
				fwrite($fh,"require_once('$tbl_file.tbl.php');\n");
			else if ($tbl_file!='')
				fwrite($fh,"require_once('extensions/$tbl_file.tbl.php');\n");
		fwrite($fh,"//@TABLES@\n");
	}

	protected function WriteSpecial($fh)
	{
	}

	protected function WriteParams($fh)
	{
		foreach($this->Parameters as $Param_name=>$Param_val)
		{
			fwrite($fh,"//@PARAM@ $Param_name");
			if (is_string($Param_val))
				fwrite($fh,"=$Param_val");
			fwrite($fh,"\n");
		}
		fwrite($fh,"\n");
		fwrite($fh,"function ".$this->GetName($this->Mng->SEP)."(&\$self");
		foreach($this->Parameters as $Param_name=>$Param_val)
			if (trim($Param_name)!="")
			{
				fwrite($fh,",\$$Param_name");
				if (is_string($Param_val))
					fwrite($fh,"=$Param_val");
			}
		fwrite($fh,")\n");
		fwrite($fh,"{\n");
	}

	protected function WriteEnding($fh)
	{
		fwrite($fh,"}\n");
	}

	public function Write()
	{
		require_once("FunctionTool.inc.php");
		$extCodeFile = $this->GetFileName();
		if (!$fh=OpenInWriteFile($extCodeFile,get_class($this)))
		{
			return "Fichier ".get_class($this)." '$extCodeFile' non créé!";
			exit;
		}
		fwrite($fh,"require_once('CORE/xfer_exception.inc.php');\n");
		fwrite($fh,"require_once('CORE/rights.inc.php');\n");
		fwrite($fh,"\n");
	
		$this->WriteTables($fh);

		$this->WriteSpecial($fh);

		fwrite($fh,"\n");
		fwrite($fh,"//@DESC@".$this->Description);
		fwrite($fh,"\n");

		$this->WriteParams($fh);
		fwrite($fh,"//@CODE_ACTION@\n");
		foreach($this->CodeFunction as $code)
		{
			$code=str_replace(array("\\\"","\\'","\\\\"),array('"',"'","\\"),$code);
			fwrite($fh,$code."\n");
		}
		fwrite($fh,"//@CODE_ACTION@\n");
		$this->WriteEnding($fh);
		fwrite($fh,"\n");
		fwrite($fh,"?>\n");
		fclose($fh);
		return "";
	}

	protected function ReadSpecial($source,$hi,$line_idx)
	{
		return $line_idx;
	}

	public function Read()
	{
		$this->XferCnt='';
		$this->TableFiles=array();
		$this->CodeFunction=array();
		$this->Parameters=array();
		$extCodeFile = $this->GetFileName();
		if (is_file($extCodeFile))
		{
			$hi = file($extCodeFile);
			$line_idx=1;
			$table_sect=0;
			$code_sect=0;
			for($line_idx=1;$line_idx<=count($hi);$line_idx++)
			{
				$line=$hi[$line_idx-1];
				$source=trim($line);
				if (substr($source,0,10)=='//@TABLES@')
					$table_sect=!$table_sect;
				else if (substr($source,0,8)=='//@DESC@')
					$this->Description=trim(substr($source,8));
				else if (substr($source,0,9)=='//@PARAM@')
				{
					$val=trim(substr($source,9));
					$pos_eq=strpos($val,"=");
					if ($pos_eq>0)
					{
						$val_name=trim(substr($val,0,$pos_eq));
						$val_default=substr($val,$pos_eq+1);
					}
					else
					{
						$val_name=trim($val);
						$val_default=null;
					}
					if ($val_name!='')
						$this->Parameters[$val_name]=$val_default;
				}
				else if (substr($source,0,15)=='//@CODE_ACTION@')
				{
					$code_sect=!$code_sect;
					if ($code_sect)
						$this->CodeLineBegin=$line_idx+1;
					else
						$this->CodeLineEnd=$line_idx-1;
				}
				else if (substr($source,0,3)=='//@')
					$line_idx=$this->ReadSpecial($source,$hi,$line_idx);
				
				if (($table_sect) && (substr($source,0,10)!='//@TABLES@'))
				{
					if (substr($source,0,14)=="require_once('")
					{
						$file_tmp=substr($source,14,-11);
						if (substr($file_tmp,0,11)=="extensions/")
							array_push($this->TableFiles,substr($file_tmp,11));
						else
							array_push($this->TableFiles,$file_tmp);
					}
				}
				if (($code_sect) && (substr($source,0,15)!='//@CODE_ACTION@'))
					array_push($this->CodeFunction,rtrim($line));
			}
		}
		return "";
	}
}

?>