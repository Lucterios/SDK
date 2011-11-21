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

class EventManage extends CodeAbstractManage
{
	public $Suffix=".evt.php";
}

class Event extends CodeAbstract
{
  	//constructor
  	public function __construct($name,$extensionName="",$tableName="")
	{
		$this->Mng=new EventManage();
		list($eventname,$extname)=explode('@',$name);		
		parent::__construct($eventname,$extensionName,$tableName);
		if ($extname!='') {
			require_once("Class/Extension.inc.php");
			$extension=new Extension($extname);
			$signalData=array($eventname,'&$xfer_result,$DBObj','');
			foreach($extension->Signals as $signal) {
				if ($signal[0]==$eventname)
					$signalData=$signal;
			}
			$params=explode(',',$signalData[1]);
			foreach($params as $param){
				$param=str_replace(array('$','&'),'',$param);
				$this->Parameters[$param]=NULL;
			}
			$this->Description="Evenement relatif au signal '".$signalData[2]."' de '$extname'";
		}
		if (count($this->Parameters)==0)
		      $this->Parameters['xfer_result']=NULL;
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
		$param_txt="";
		foreach($this->Parameters as $Param_name=>$Param_val)
			if (trim($Param_name)!="")
			{
				if ($param_txt!="")
				  $param_txt.=",";
				$param_txt.="\$$Param_name";
				if (is_string($Param_val))
					$param_txt.="=$Param_val";
			}

		fwrite($fh,"function ".$this->ExtensionName.$this->Mng->SEP.$this->GetName($this->Mng->SEP)."(&$param_txt)\n");
		fwrite($fh,"{\n");
	}

}

?>