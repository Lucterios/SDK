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

class TestManage extends CodeAbstractManage
{
	var $Suffix=".test.php";
}

class Test extends CodeAbstract
{
  	//constructor
  	function Test($name,$extensionName="",$tableName="")
	{
		$this->Mng=new TestManage();
		parent::CodeAbstract($name,$extensionName,$tableName);
	}

	function WriteParams($fh)
	{
		foreach($this->Parameters as $Param_name=>$Param_val)
		{
			fwrite($fh,"//@PARAM@ $Param_name");
			if (is_string($Param_val))
				fwrite($fh,"=$Param_val");
			fwrite($fh,"\n");
		}
		fwrite($fh,"\n");
		fwrite($fh,"function ".$this->ExtensionName."_".$this->GetName($this->Mng->SEP)."(&\$test)\n");
		fwrite($fh,"{\n");
	}

	function Write()
	{
		require_once("FunctionTool.inc.php");
		$extCodeFile = $this->GetFileName();
		if (!$fh=OpenInWriteFile($extCodeFile,get_class($this)))
		{
			return "Fichier ".get_class($this)." '$extCodeFile' non crיי!";
			exit;
		}
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

}

?>