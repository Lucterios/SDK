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

function addNewFileInGit($filename) {
	if (!is_file($filename)) {
		$extensionName='';
		$file_expl=explode('/',$filename);
		if (($file_expl[0]=='..') && ($file_expl[1]=='extensions')) {
		      $extensionName=$file_expl[2];
		      $newFile=substr($filename,5+strlen($file_expl[1])+strlen($extensionName));
		}
		else if (($file_expl[0]=='..') && ($file_expl[1]=='CORE')) {
		      $extensionName='CORE';
		      $newFile=substr($filename,4+strlen($file_expl[1]));
		}
		if ($extensionName!='') {
			if ($fh=fopen($filename,"w+")) {
				fwrite($fh,"\n");
				fclose($fh);
			}
			require_once("Class/Extension.inc.php");
			$extObj=new Extension($extensionName);
			$extObj->Name=$extensionName;
			$repo=$extObj->GetGitRepoObj();
			$repo->add("'$newFile'");
		}
	}
}

function OpenInWriteFile($filename,$title)
{
	addNewFileInGit($filename);
	if ($fh=fopen($filename,"w+"))
	{
		fwrite($fh,"<?php\n");
		if (is_file('CNX/LICENSE'))
		{
			$license_lines = trim(file('CNX/LICENSE'));
			foreach($license_lines as $license_line)
				fwrite($fh,"// $license_line"); 
		}
		fwrite($fh,"// $title file write by SDK tool\n"); 
		fwrite($fh,"\n");
	}
	return $fh;
}

function StringToArray($string)
{
	echo "$string<br>";
	if (is_array($string))
		$result=$string;
	elseif ((strtoupper($string)=="FALSE") || (strtoupper($string)=="TRUE"))
	{
		$result=(strtoupper($string)=="TRUE");
		echo "bool:$result<br>";
	}
	elseif (is_int($string))
	{
		$result=(int)$string;
		echo "int:$result<br>";
	}
	elseif (is_float($string))
	{
		$result=(float)$string;
		echo "float:$result<br>";
	}
	elseif (strtoupper(substr($string,0,6))=="ARRAY(")
	{
		$result=array();
		$string=substr($string,6,-2);
		$list= explode(",",$string);
		foreach($list as $item)
		{
			$pos=strrpos($item,"=>");
			echo "-array-:$item [$pos,".strlen($item)."]<br>";
			if ($pos==false)
				array_push($result,StringToArray(trim($item)));
			else
			{
				$key=trim(substr($item,0,$pos));
				$val_item=trim(substr($item,$pos+2));
				echo "+array+:$key~$val_item<br>";
				$result[StringToArray($key)]=StringToArray($val_item);
			}
		}
		echo "array:$result<br>";
	}
	elseif (strlen($string)>2)
	{
		$result=substr($string,1,-1);
		echo "str:$result<br>";
	}
	else
	{
		$result=$string;
		echo "-->:$result<br>";
	}
	return $result;
}

function ArrayToString($array,$simple=false)
{
	$result="";
	if (!is_array($array))
		$result.=$array;
	else
	foreach($array as $key=>$val)
	{
		if ($result!="")
			$result.=", ";
		if (is_string($key))
		{
			if ($simple)
				$result.="$key=";
			else
				$result.="'$key'=>";
		}
		if (is_string($val))
		{
			$val= str_replace("'","\\'",$val);
			$result.="'$val'";
		}
		elseif (is_array($val))
		{
			if ($simple)
				$result.="(".ArrayToString($val,$simple).")";
			else
				$result.=ArrayToString($val,$simple);
		}
		elseif (is_bool($val))
		{
			if ($val)
				$result.="true";
			else
				$result.="false";
		}
		else
			$result.=$val;
	}
	if (!$simple)
		$result="array(".$result.")";
	return $result;
}

function deleteDir($dirPath)
{
  	if(is_dir($dirPath)) 
	{
		$dh = opendir($dirPath);
		while (($file = readdir($dh)) != false) 
		{
			if (($file=='.') || ($file=='..'))
				continue;
			if (is_file($dirPath.'/'.$file))
				unlink($dirPath.'/'.$file);
			elseif(is_dir($dirPath.'/'.$file))
				deleteDir($dirPath.'/'.$file);
		}
		closedir($dh);
		rmdir($dirPath);
	}
}

function getTraceException($e) {
	$trace="";
	foreach($e->getTrace() as $num => $trace_line) {
		if($num == 0) {
			$trace_line['file'] = $e->getFile();
			$trace_line['line'] = $e->getLine();
		}
		$trace .= $num."|";
		$trace .= $trace_line['file']."|";
		$trace .= $trace_line['line']."|";
		$trace .= str_replace('_APAS_','::',$trace_line['function'])."\n";
	}
	return $trace;
}

?>