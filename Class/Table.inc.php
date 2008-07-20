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

class TableManage extends AbstractClassManage
{
	var $Suffix=".tbl.php";

	function GetDependList($depends,$extensionName="")
	{
		$tbl_dep_list=array();
		$tbl_dep_list[$extensionName]=$this->GetList($extensionName);
		foreach($depends as $depend)
		{
			$ext_dep=$depend->name;
			if ($ext_dep!="")
				$tbl_dep_list[$ext_dep]=$this->GetList($ext_dep);
		}
		return $tbl_dep_list;
	}
	function Delete($name,$extensionName="")
	{
		AbstractClassManage::Delete($name,$extensionName);
		require_once("Action.inc.php");
		$mng=new ActionManage();
		$act_list=$mng->GetList($extensionName,$name);
		foreach($act_list as $actname)
			$mng->Delete($mng->GetNameNoTable($actname),$extensionName,$name);
	}
}

class Table extends AbstractClass
{
	var $Title = "";
	var $Fields=array();
	var $ToText="";
	var $DefaultFields=array();
	var $NbFieldsCheck=1;
	var $Heritage = "";
	var $PosChild=-1;
	var $Mng;

  	//constructor
  	function Table($name,$extensionName="")
	{
		$this->Mng=new TableManage();
		$this->AbstractClass($name,$extensionName);
	}

	function FileName()
	{
		$extDir = $this->Mng->__ExtDir($this->ExtensionName);
		$extTblFile = $extDir.$this->Name.$this->Mng->Suffix;
		return $extTblFile;
	}

	function Read()
	{
		$this->Fields=array();
		$this->ToText="";
		$this->DefaultFields=array();
		$this->NbFieldsCheck=1;
		$extTblFile = $this->FileName();
		if (is_file($extTblFile))
		{
			$tbl_file = file($extTblFile);
			foreach($tbl_file as $line)
			{
				if (substr($line,6,17)=="__DBMetaDataField")
				{
					eval(substr($line,4));
					$this->Fields=$__DBMetaDataField;
				}
				if (substr($line,6,8)=="__toText")
				{
					eval(substr($line,4));
					$this->ToText=$__toText;
				}
				if (substr($line,6,13)=="DefaultFields")
				{
					eval(substr($line,4));
					$this->DefaultFields=$DefaultFields;
				}
				if (substr($line,6,13)=="NbFieldsCheck")
				{
					eval(substr($line,4));
					$this->NbFieldsCheck=$NbFieldsCheck;
				}
				if (substr($line,6,8)=="Heritage")
				{
					eval(substr($line,4));
					$this->Heritage=$Heritage;
				}
				if (substr($line,6,5)=="Title")
				{
					eval(substr($line,4));
					$this->Title=$Title ;
				}
				if (substr($line,6,8)=="PosChild")
				{
					eval(substr($line,4));
					$this->PosChild=$PosChild;
				}
			}
		}
		if ($this->Heritage!='') {
			if ($this->PosChild<0)
				$this->PosChild=count($this->Fields);
			else
				$this->PosChild=Min($this->PosChild,count($this->Fields));
		}
		else
			$this->PosChild=-1;
		return "";
	}

	function GetTinyField($parent_tbl_name)
	{
		$field_list=array();
		foreach($this->Fields as $fname=>$fld)
		{
			if (($fld['type']==10) && ($fld['params']['TableName']==$parent_tbl_name))
				array_push($field_list,$fname);
		}
		return $field_list;
	}

	function Write()
	{
		require_once("FunctionTool.inc.php");
		$extTblFile = $this->FileName();
		if (!$fh=OpenInWriteFile($extTblFile,"table"))
		{
			return "Fichier table '$extTblFile' non créé!";
			exit;
		}
		fwrite($fh,"require_once('CORE/DBObject.inc.php');\n");
		fwrite($fh,"\n");
		fwrite($fh,"class DBObj_".$this->ExtensionName."_".$this->Name." extends DBObj_Basic\n");
		fwrite($fh,"{\n");
		fwrite($fh,"\tvar \$Title=\"".$this->Title."\";\n");
		fwrite($fh,"\tvar \$tblname=\"".$this->Name."\";\n");
		fwrite($fh,"\tvar \$extname=\"".$this->ExtensionName."\";\n");
		fwrite($fh,"\tvar \$__table=\"".$this->ExtensionName."_".$this->Name."\";\n");

		fwrite($fh,"\n");
		fwrite($fh,"\tvar \$DefaultFields=".ArrayToString($this->DefaultFields).";\n");
		fwrite($fh,"\tvar \$NbFieldsCheck=".$this->NbFieldsCheck.";\n");
		fwrite($fh,"\tvar \$Heritage=\"".$this->Heritage."\";\n");
		fwrite($fh,"\tvar \$PosChild=".$this->PosChild.";\n");

		fwrite($fh,"\n");
		foreach($this->Fields as $fieldname=>$field)
			fwrite($fh,"\tvar \$".$fieldname.";\n");

		fwrite($fh,"\tvar \$__DBMetaDataField=");
		fwrite($fh,ArrayToString($this->Fields).";\n");
		fwrite($fh,"\n");
		if ($this->ToText!="")
		{
			fwrite($fh,"\tvar \$__toText='".$this->ToText."';\n");
		}
		fwrite($fh,"}\n");
		fwrite($fh,"\n");
		fwrite($fh,"?>\n");
		fclose($fh);
		return "";
	}

	function UpField($field_name)
	{
		if (substr($field_name,0,3)=='___')
		{
			$this->PosChild=Max($this->PosChild-1,0);
			return $this->Write();
		}
		$field_keys=array_keys($this->Fields);
		$fk=array_flip($field_keys);
		if (array_key_exists($field_name,$fk))
			$field_index=$fk[$field_name];
		else
			$field_index=-1;
		if ($this->Heritage!='') {
			if ($field_index==$this->PosChild)
			{
				$this->PosChild=Min($this->PosChild+1,count($this->Fields));
				return $this->Write();
			}
		}
		if ($field_index>0)
		{
			$new_fields=array();
			for($id=0;$id<($field_index-1);$id++)
			{
				$fname=$field_keys[$id];
				$new_fields[$fname]=$this->Fields[$fname];
			}
			$fname=$field_keys[$field_index];
			$new_fields[$fname]=$this->Fields[$fname];
			$fname=$field_keys[$field_index-1];
			$new_fields[$fname]=$this->Fields[$fname];
			for($id=($field_index+1);$id<count($field_keys);$id++)
			{
				$fname=$field_keys[$id];
				$new_fields[$fname]=$this->Fields[$fname];
			}
			$this->Fields=$new_fields;
			return $this->Write();
		}
		return "";
	}

	function DownField($field_name)
	{
		if (substr($field_name,0,3)=='___')
		{
			$this->PosChild=Min($this->PosChild+1,count($this->Fields));
			return $this->Write();
		}
		$field_keys=array_keys($this->Fields);
		$fk=array_flip($field_keys);
		if (array_key_exists($field_name,$fk))
			$field_index=$fk[$field_name];
		else
			$field_index=-1;
		if (($field_index+1)==$this->PosChild) 
		{
			$this->PosChild=Max($this->PosChild-1,0);
			return $this->Write();
		}
		if (($field_index>=0) && ($field_index<(count($field_keys)-1)))
			$this->UpField($field_keys[$field_index+1]);
		return "";
	}

	function ModifyField($field_name,$description,$type,$notnull,$param)
	{
		if ($notnull)
			$notnull=true;
		else
			$notnull=false;
		$field=array("description"=>$description, "type"=>$type, "notnull"=>$notnull, "params"=>$param);

		$field_id=-1;
		$this->Fields[$field_name]=$field;
		return $this->Write();
	}

	function DelField($field_name)
	{
		if ((substr($field_name,0,3)!='___') && array_key_exists($field_name,$this->Fields))
		{
			unset($this->Fields[$field_name]);
			return $this->Write();
		}
		return "";
	}

	function DelRow($rownum)
	{
		if (array_key_exists($rownum,$this->DefaultFields))
		{
			unset($this->DefaultFields[$rownum]);
			return $this->Write();
		}
		return "";
	}
}

?>