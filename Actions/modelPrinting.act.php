<?php
// 
//     This file is part of Lucterios.
// 
//     Lucterios is free software; you can redistribute it and/or modify
//     it under the terms of the GNU General Public License as published by
//     the Free Software Foundation; either version 2 of the License, or
//     (at your option) any later version.
// 
//     Lucterios is distributed in the hope that it will be useful,
//     but WITHOUT ANY WARRANTY; without even the implied warranty of
//     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//     GNU General Public License for more details.
// 
//     You should have received a copy of the GNU General Public License
//     along with Lucterios; if not, write to the Free Software
//     Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
// 
// 	Contributeurs: Fanny ALLEAUME, Pierre-Olivier VERSCHOORE, Laurent GAY


function modelPrinting($Params,$extensionname)
{
	global $CNX_OBJ;
	$cnx=$CNX_OBJ;

	$tablename=$Params['tablename'];
	$id=$Params['printing'];
	require_once("Class/Printing.inc.php");
	$code = new Printing($id,$extensionname);
	if (array_key_exists('title',$Params) && array_key_exists('model',$Params)) {
		require_once('../CORE/xfer.inc.php');
		$xfer_result=new Xfer_Container_Acknowledge($extensionname,"modelPrinting",$Params);
		require_once("Class/Extension.inc.php");
		$extension=new Extension($extensionname);
		$code->ModelDefault=explode("\n",$Params['model']);
		$code->Description=$Params['title'];
		$code->Write();
		$extension->IncrementBuild();
	} else {
		require_once('../CORE/xfer_printing.inc.php');
		$xfer_result=new Xfer_Container_Template($extensionname,"modelPrinting",$Params);	
		$xfer_result->Caption='Editer un modèle';
		$xfer_result->title=$code->Description;
        	$model_xml='';
		foreach($code->ModelDefault as $code_line)
		{
			$code_line=str_replace('#&160;',' ',$code_line);
			$code_line=str_replace('#&34;','"',$code_line);
			$code_line=str_replace('#&39;',"'",$code_line);
                	$model_xml.=$code_line."\r\n";
		}
		$xfer_result->m_model=$model_xml;
		$xfer_result->m_idmodel=1;

		$printfile="../extensions/$extensionname/$id.prt.php";
                $XmlDataFctName=$extensionname."_APAS_".$id."_getXmlData";
		$print_file_content=implode("",file($printfile));
		$print_file_content=str_replace("require_once('CORE/rights.inc.php');","require_once('Main/rights.inc.php');",$print_file_content);
		$print_file_content=str_replace("<?php","",$print_file_content);
		$print_file_content=str_replace("?>","",$print_file_content);
		if ($code->TableName!="") 
			$print_file_content=str_replace("\$self=new DBObj_".$extensionname."_".$code->TableName."();","",$print_file_content);
		$print_file_content.="\n\$m_xml_data=$XmlDataFctName();";
		eval($print_file_content);
		$xfer_result->m_xml_data=$m_xml_data;
	}

	return $xfer_result;
}

?>
 
