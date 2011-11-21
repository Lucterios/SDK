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

global $xfer_dico;
$xfer_dico=array();
$xfer_dico["custom"]=array("xfer_custom","Xfer_Container_Custom");
$xfer_dico["dialogbox"]=array("xfer_dialogBox","Xfer_Container_DialogBox");
$xfer_dico["acknowledge"]=array("xfer","Xfer_Container_Acknowledge");
$xfer_dico["menu"]=array("xfer_menu","Xfer_Container_Menu");
$xfer_dico["print"]=array("xfer_printing","Xfer_Container_Print");
$xfer_dico["template"]=array("xfer_printing","Xfer_Container_Template");

require_once("Class/CodeAbstract.inc.php");

define('LOCK_MODE_NO',0);
define('LOCK_MODE_ACTION',1);
define('LOCK_MODE_EVENT',2);

class ActionManage extends CodeAbstractManage
{
	public $Suffix=".act.php";

	public function GetActionId($name,$extensionName="",$tableName)
	{
		require_once("Class/Extension.inc.php");
		$ext=new Extension($extensionName);
		$id=-1;
		$name_sep=$this->GetNameWithSep($name,$tableName);
		foreach($ext->Actions as $key=>$act)
			if ($act->action==$name_sep)
				$id=$key;
		return $id;
	}

	public function Delete($name,$extensionName="",$tableName="")
	{
		$result=parent::Delete($name,$extensionName,$tableName);
		if ($result)
		{
			$id=GetActionId($name,$extensionName,$tableName);
			if ($id!=-1)
				unset($ext->Actions[$id]);
		}
		return $result;
	}
}

class Action extends CodeAbstract
{
	public $TableName="";
	public $IndexName="";

	public $XferCnt="custom";

	public $WithTransaction=false;
	public $LockMode=LOCK_MODE_NO;

	public $RigthName="";

  	//constructor
  	public function __construct($name,$extensionName="",$tableName="")
	{
		$this->Mng=new ActionManage();
		parent::__construct($name,$extensionName,$tableName);
	}

	public function Check()
	{
		$this->XferCnt="custom";
		parent::Check();
	}

	public function Modify($code_id,$tablename)
	{
		if (parent::Modify($code_id,$tablename))
		{
			global $action_XferCnt;
			global $action_Lock;
			global $action_Transaction;
			if ($action_XferCnt!="")
			{
				$this->XferCnt=$action_XferCnt;
				$this->WithTransaction=isset($action_Transaction);
				if (($this->TableName!="") && ($this->IndexName!=""))
					$this->LockMode=$action_Lock;
				else
					$this->LockMode=LOCK_MODE_NO;
				return true;
			}
			else
			{
				return false;
			}
		}
		else
			return false;
	}

	public function Read()
	{
		parent::Read();
		require_once("Class/Extension.inc.php");
		$ext=new Extension($this->ExtensionName);
		$id=-1;
		$name_sep=$this->GetName($this->Mng->SEP);
		foreach($ext->Actions as $key=>$act)
			if ($act->action==$name_sep)
			{
				$this->Description=$act->description;
				$this->RigthName="";
				if (array_key_exists($act->rightNumber,$ext->Rights))
					$this->RigthName=$ext->Rights[$act->rightNumber]->description;
			}
	}

	protected function WriteSpecial($fh)
	{
		if ($this->XferCnt=='')
			$this->XferCnt="custom";
		global $xfer_dico;
		list($xfer_file,$xfer_class)=$xfer_dico[$this->XferCnt];
		fwrite($fh,"//@XFER:".$this->XferCnt."\n");
		fwrite($fh,"require_once('CORE/$xfer_file.inc.php');\n");
		fwrite($fh,"//@XFER:".$this->XferCnt."@\n");
		fwrite($fh,"\n");
	}

	protected function WriteParams($fh)
	{
		global $xfer_dico;
		list($xfer_file,$xfer_class)=$xfer_dico[$this->XferCnt];
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
		if ($this->WithTransaction)
			fwrite($fh,"//@TRANSACTION:\n");
		fwrite($fh,"\n");
		fwrite($fh,"//@LOCK:".$this->LockMode."\n");

		fwrite($fh,"\n");
		fwrite($fh,"function ".$this->GetName($this->Mng->SEP)."(\$Params)\n");
		fwrite($fh,"{\n");

		$list="";
		foreach($this->Parameters as $Param_name=>$Param_val)
			if (!is_string($Param_val) && (trim($Param_name)!=""))
				$list.=",\"$Param_name\"";
		if ($list!="")
			fwrite($fh, "if ((\$ret=checkParams(\"".$this->ExtensionName."\", \"".$this->GetName($this->Mng->SEP)."\",\$Params $list))!=null)\n\treturn \$ret;\n");
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
		$name_sep=$this->GetName($this->Mng->SEP);
		if ($this->LockMode!=LOCK_MODE_NO)
		{
			fwrite($fh,"\n");
			fwrite($fh, "\$self->lockRecord(\"$name_sep\");\n");
		}
		if ($this->WithTransaction)
		{
			fwrite($fh,"\n");
			fwrite($fh,"global \$connect;\n");

			fwrite($fh,"\$connect->begin();\n");
		}
		fwrite($fh,"try {\n");
		fwrite($fh,"\$xfer_result=&new $xfer_class(\"".$this->ExtensionName."\",\"".$this->GetName($this->Mng->SEP)."\",\$Params);\n");
		fwrite($fh,"\$xfer_result->Caption=".getStringToWrite($this->Description).";\n");
		if ($this->LockMode==LOCK_MODE_EVENT)
		{
			fwrite($fh, "\$xfer_result->m_context['ORIGINE']=\"$name_sep\";\n");
			fwrite($fh, "\$xfer_result->m_context['TABLE_NAME']=\$self->__table;\n");
			fwrite($fh, "\$xfer_result->m_context['RECORD_ID']=\$self->id;\n");
		}
	}

	protected function WriteEnding($fh)
	{
		if ($this->LockMode==LOCK_MODE_EVENT)

			fwrite($fh,"	\$xfer_result->setCloseAction(new Xfer_Action('unlock','','CORE','UNLOCK',FORMTYPE_MODAL,CLOSE_YES,SELECT_NONE));\n");

		if ($this->WithTransaction)
			fwrite($fh,"	\$connect->commit();\n");
		if ($this->LockMode==LOCK_MODE_ACTION)
		{
			$name_sep=$this->GetName($this->Mng->SEP);
			fwrite($fh, "	\$self->unlockRecord(\"$name_sep\");\n");
		}
		fwrite($fh,"}catch(Exception \$e) {\n");
		if ($this->WithTransaction)
			fwrite($fh,"	\$connect->rollback();\n");
		if ($this->LockMode!=LOCK_MODE_NO)
		{
			$name_sep=$this->GetName($this->Mng->SEP);
			fwrite($fh, "	\$self->unlockRecord(\"$name_sep\");\n");
		}
		fwrite($fh,"	throw \$e;\n");
		fwrite($fh,"}\n");
		fwrite($fh,"return \$xfer_result;\n");
		fwrite($fh,"}\n");
	}

	protected function ReadSpecial($source,$hi,$line_idx)
	{
		if (substr($source,0,8)=='//@XFER:')
			$this->XferCnt=substr($source,8,-1);
		elseif (substr($source,0,9)=='//@INDEX:')
			$this->IndexName=substr($source,9);
		elseif (substr($source,0,15)=='//@TRANSACTION:')
			$this->WithTransaction=true;
		elseif (substr($source,0,8)=='//@LOCK:')
			$this->LockMode=(int)substr($source,8);
		return $line_idx;
	}
}

?>