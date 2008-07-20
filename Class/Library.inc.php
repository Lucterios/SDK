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

class LibraryManage extends AbstractClassManage
{
	var $Suffix=".inc.php";
}

class Library extends AbstractClass
{
	var $CodeFile=array();
	var $Mng;

  	//constructor
  	function Library($name,$extensionName="")
	{
		$this->Mng=new LibraryManage();
                $this->CodeLineBegin=1;
		$this->AbstractClass($name,$extensionName);
	}

	function Write()
	{
		require_once("FunctionTool.inc.php");
		$extDir = $this->Mng->__ExtDir($this->ExtensionName);
		$extLibFile = $extDir.$this->Name.$this->Mng->Suffix;
		if (!$fh=OpenInWriteFile($extLibFile,"library"))
		{
			return "Fichier library '$extLibFile' non créé!";
			exit;
		}
		fwrite($fh,"//@BEGIN@\n");
		foreach($this->CodeFile as $code)
		{
			$code=str_replace(array("\\\"","\\'","\\\\"),array('"',"'","\\"),$code);
			fwrite($fh,$code."\n");
		}
		fwrite($fh,"//@END@\n");
		fwrite($fh,"?>\n");
		fclose($fh);
		return "";
	}

	function Read()
	{
		$this->CodeFile=array();
		$extDir = $this->Mng->__ExtDir($this->ExtensionName);
		$extLibFile = $extDir.$this->Name.$this->Mng->Suffix;
		if (is_file($extLibFile))
		{
			$hi = file($extLibFile);
			$line=1;
			$line_begin=0;
			$line_end=10000;
			foreach($hi as $source)
			{
				$source=trim($source);
				if ((substr($source,0,2)=='<?') || (substr($source,0,9)=='//@BEGIN@'))
				{
					$line_begin=max($line_begin,$line);
				}
				if ((substr($source,0,2)=='?>') || (substr($source,0,6)=='//@END@'))
					$line_end=min($line_end,$line-1);
				$line++;
			}
			$line=1;
			foreach($hi as $source)
			{
				if (($line_begin<$line) && ($line_end>$line))
					array_push($this->CodeFile,rtrim($source));
				$line++;
			}
		}
                $this->CodeLineBegin=$line_begin+1;
		return "";
	}
}

?>