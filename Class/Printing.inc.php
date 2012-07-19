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
require_once("CodeAbstract.inc.php");

class PrintingManage extends CodeAbstractManage
{
	public $Suffix=".prt.php";
}

class Printing extends CodeAbstract
{
	public $ModelDefault=array();
	public $IndexName="";

  	//constructor
  	public function __construct($name,$extensionName="",$tableName="")
	{
		$this->Mng=new PrintingManage();
		parent::__construct($name,$extensionName,$tableName);
		if (!is_file($this->GetFileName()))
		{
			$this->CodeFunction=array("\$xml_data.=\"<DATA>\";","\$xml_data.=\"</DATA>\";");
			$this->ModelDefault=array("<model>","</model>");
		}
	}

	protected function WriteSpecial($fh)
	{
		fwrite($fh,"//@MODEL_DEFAULT@\n");
		fwrite($fh,"\$MODEL_DEFAULT=\"\n");
		foreach($this->ModelDefault as $code)
		{
			$code=rtrim($code);
			$code=str_replace(array("\\\"","\\'","\\\\",'\#&39;','\#&34;'),array('"',"'","\\",'#&39;','#&34;'),$code);
			fwrite($fh,$code."\n");
		}
		fwrite($fh,"\";\n");
		fwrite($fh,"//@MODEL_DEFAULT_END@\n");
		fwrite($fh,"\n");
		fwrite($fh,"\$Title=\"".$this->Description."\";\n");
		fwrite($fh,"\n");
	}

	protected function WriteParams($fh)
	{
		global $xfer_dico;
		@list($xfer_file,$xfer_class)=$xfer_dico[$this->XferCnt];
		foreach($this->Parameters as $Param_name=>$Param_val)
		{
			fwrite($fh,"//@PARAM@ $Param_name");
			if (is_string($Param_val))
				fwrite($fh,"=$Param_val");
			fwrite($fh,"\n");
		}
		if ($this->IndexName!='')
			fwrite($fh,"//@INDEX:".$this->IndexName."\n");

		fwrite($fh,"\n");
		fwrite($fh,"function ".$this->ExtensionName.$this->Mng->SEP.$this->GetName($this->Mng->SEP)."_getXmlData(\$Params=array())\n");
		fwrite($fh,"{\n");
		$list="";
		foreach($this->Parameters as $Param_name=>$Param_val)
			if (!is_string($Param_val) && (trim($Param_name)!=""))
				$list.=",\"$Param_name\"";
		if ($list!="")
			fwrite($fh, "if ((\$ret=checkParams(\"".$this->ExtensionName."\", \"".$this->Name."\",\$Params $list))!=null)\n\treturn \$ret;\n");
		foreach($this->Parameters as $Param_name=>$Param_val)
			if (trim($Param_name)!="")
			{
				if (trim($Param_val)=="")
					fwrite($fh, "\$$Param_name=getParams(\$Params,\"$Param_name\");\n");
				else
					fwrite($fh, "\$$Param_name=getParams(\$Params,\"$Param_name\",$Param_val);\n");
			}
		if ($this->TableName!="")
		{
			fwrite($fh, "\$self=new DBObj_".$this->ExtensionName."_".$this->TableName."();\n");
			if ($this->IndexName!="")
			{
				fwrite($fh, "\$".$this->IndexName."=getParams(\$Params,\"".$this->IndexName."\",-1);\n");
				fwrite($fh, "if (\$".$this->IndexName.">=0) \$self->get(\$".$this->IndexName.");\n");
			}
		}
		fwrite($fh,"\$xml_data='';\n");
	}

	protected function WriteEnding($fh)
	{
		fwrite($fh,"return \$xml_data;\n");
		fwrite($fh,"}\n");
	}

	protected function ReadSpecial($source,$hi,$line_idx)
	{
		if (substr($source,0,17)=="//@MODEL_DEFAULT@")
		{
			$this->ModelDefault=array();
			$line_idx++;
			while ((($line_idx+1)<count($hi)) && (substr($hi[$line_idx+1],0,21)!="//@MODEL_DEFAULT_END@"))
			{
				$this->ModelDefault[]=rtrim($hi[$line_idx]);
				$line_idx++;
			}
		}
		elseif (substr($source,0,9)=='//@INDEX:')
			$this->IndexName=substr($source,9);
		return $line_idx;
	}

	public function Modify($code_id,$tablename)
	{
		if (parent::Modify($code_id,$tablename))
		{
			global $Print_ModelDefault;
			if ($Print_ModelDefault!="")
			{
				$model_list=explode("\n",rtrim($Print_ModelDefault));
				$this->ModelDefault=array();
				foreach($model_list as $model_line)
				{
					$model_line=str_replace(')] ',')]#&160;',$model_line);
					$model_line=str_replace('}] ','}]#&160;',$model_line);
					$model_line=str_replace('"','#&34;',$model_line);
					$model_line=str_replace("'",'#&39;',$model_line);
					$this->ModelDefault[]=$model_line;
				}
				return true;
			}
			else
				return false;
		}
		else
			return false;
	}

}

?>