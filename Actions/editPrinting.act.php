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

require_once('../CORE/xfer_custom.inc.php');

function editPrinting($Params,$extensionname)
{
	$Params['type']='Printing';
	require_once("Actions/editCode.act.php");
	$xfer_result=editCode($Params,$extensionname);
	$xfer_result->m_action='editPrinting';

	$xfer_result->m_tab=0;
	require_once("Class/Printing.inc.php");
	$print = new Printing($Params['printing'],$extensionname);
	$xfer_result->Caption="Edition du mod�le \"".$print->GetName()."\"";
	$lbl=new Xfer_Comp_LabelForm('title');
	$lbl->setValue("{[bold]}{[center]}Impression '".$print->GetName()." de l'extension '$extensionname'{[/center]}{[/bold]}");
	$lbl->setLocation(0,0,3);
	$xfer_result->addComponent($lbl);

	$xfer_result->m_tab=2;
        $script='';
	foreach($print->ModelDefault as $code_line)
	{
		$code_line=str_replace('#&160;',' ',$code_line);
		$code_line=str_replace('#&34;','"',$code_line);
		$code_line=str_replace('#&39;',"'",$code_line);
		$script.=$code_line."\n";
	}
	$edt=new Xfer_Comp_Memo('Print_ModelDefault');
	$edt->setValue(urlencode($script));
	$edt->Encode=true;
	$edt->FirstLine=1;
	$edt->setLocation(0,20,4);
	$xfer_result->addComponent($edt);

	return $xfer_result;
}

?>
 
