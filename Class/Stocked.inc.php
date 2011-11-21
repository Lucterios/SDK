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
require_once("CodeAbstract.inc.php");

function OpenSQLWriteFile($filename,$title)
{
	require_once("ConnectionSDK.inc.php");
	global $SDKUSER;
	if ($fh=fopen($filename,"w+"))
	{
		if (is_file('CNX/LICENSE'))
		{
			$license_lines = file('CNX/LICENSE');
			foreach($license_lines as $license_line)
				fwrite($fh,"-- $license_line"); 
		}
		fwrite($fh,"-- $title file write by SDK tool\n"); 
		fwrite($fh,"-- Last modification: Date ".date("d F Y G:i:s")." By $SDKUSER ---\n"); 
		fwrite($fh,"\n");
	}
	return $fh;
}

class StockedManage extends CodeAbstractManage
{
	public $Suffix=".fsk";
}

class Stocked extends CodeAbstract
{
  	//constructor
  	public function __construct($name,$extensionName="",$tableName="")
	{
		$this->Mng=new StockedManage();
		parent::__construct($name,$extensionName,$tableName);
		if (($this->TableName!='') && (count($this->CodeFunction)==0))
			$this->Parameters['ObjId']='int(10)';
	}

	public function GetParams($WithSep=true)
	{
		$params="";
		if ($WithSep)
			$params.="(";
		foreach($this->Parameters as $Param_name=>$Param_val)
		{
			$params.="$Param_name $Param_val";
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
		$code_params=trim($code_params);
		if ($code_params!='') {
			$params=explode(",",$code_params);
			foreach($params as $param)
			{
				$param_val=explode(" ",trim($param));
				while (($key=array_search('', $param_val))!==false) {
					unset($param_val[$key]);
				}
				if (count($param_val)>0)
				{
					$val_name=trim($param_val[0]);
				}
				$param_val=array_values($param_val);
				if (count($param_val)==1)
					$this->Parameters[$val_name]='INTEGER';
				elseif (count($param_val)>=2)
					$this->Parameters[$val_name]=trim($param_val[1]);
			}
		}
	}

	protected function WriteParams($fh)
	{
		foreach($this->Parameters as $Param_name=>$Param_val)
		{
			fwrite($fh,"-- @PARAM@ $Param_name");
			fwrite($fh," $Param_val");
			fwrite($fh,"\n");
		}
		fwrite($fh,"\n");
		fwrite($fh,"CREATE FUNCTION ".$this->ExtensionName."_FCT_".$this->GetName($this->Mng->SEP)."(");
		$first=true;
		foreach($this->Parameters as $Param_name=>$Param_val)
			if (trim($Param_name)!="")
			{
				if (!$first)
					fwrite($fh,",");
				fwrite($fh,"$Param_name $Param_val");
				$first=false;
			}
		fwrite($fh,")\n");
		fwrite($fh,"RETURNS TEXT\n");
		fwrite($fh,"READS SQL DATA\n");
		fwrite($fh,"BEGIN\n");
		fwrite($fh,"DECLARE result TEXT DEFAULT '';\n");
	}

	protected function WriteEnding($fh)
	{
		fwrite($fh,"RETURN result;\n");
		fwrite($fh,"END\n");
	}

	public function Write()
	{
		$extCodeFile = $this->GetFileName();
		if (!$fh=OpenSQLWriteFile($extCodeFile,get_class($this)))
		{
			return "Fichier ".get_class($this)." '$extCodeFile' non créé!";
			exit;
		}

		fwrite($fh,"\n");
		fwrite($fh,"-- @DESC@".$this->Description);
		fwrite($fh,"\n");

		$this->WriteParams($fh);
		fwrite($fh,"-- @CODE_ACTION@\n");
		foreach($this->CodeFunction as $code)
		{
			$code=str_replace(array("\\\"","\\'","\\\\"),array('"',"'","\\"),$code);
			fwrite($fh,$code."\n");
		}
		fwrite($fh,"-- @CODE_ACTION@\n");
		$this->WriteEnding($fh);
		fwrite($fh,"\n");
		fclose($fh);
		chmod($extCodeFile, 0666);
		return "";
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
				if (substr($source,0,9)=='-- @DESC@')
					$this->Description=trim(substr($source,9));
				else if (substr($source,0,10)=='-- @PARAM@')
				{
					$val=trim(substr($source,10));
					$pos_eq=strpos($val," ");
					if ($pos_eq>0)
					{
						$val_name=trim(substr($val,0,$pos_eq));
						$val_default=trim(substr($val,$pos_eq+1));
					}
					else
					{
						$val_name=trim($val);
						$val_default='INTEGER';
					}
					if ($val_name!='')
						$this->Parameters[$val_name]=$val_default;
				}
				else if (substr($source,0,16)=='-- @CODE_ACTION@')
				{
					$code_sect=!$code_sect;
					if ($code_sect)
						$this->CodeLineBegin=$line_idx+1;
					else
						$this->CodeLineEnd=$line_idx-1;
				}
				else if (substr($source,0,4)=='-- @')
					$line_idx=$this->ReadSpecial($source,$hi,$line_idx);
				
				if (($code_sect) && (substr($source,0,16)!='-- @CODE_ACTION@'))
					array_push($this->CodeFunction,rtrim($line));
			}
		}
		return "";
	}

}

?>