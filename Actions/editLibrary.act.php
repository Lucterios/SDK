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

function editLibrary($Params,$extensionname)
{
	global $CNX_OBJ;
	$cnx=$CNX_OBJ;
	$xfer_result=&new Xfer_Container_Custom($extensionname,"editLibrary",$Params);
	require_once("Class/Library.inc.php");
	require_once("Class/Extension.inc.php");
	$extension=new Extension($extensionname);
	$lib = new Library($Params['library'],$extension->Name);
	$xfer_result->Caption='Edtion de bibliothèque "'.$lib->Name.'"';

	$lbl=new Xfer_Comp_LabelForm('title');
	$lbl->setValue("{[bold]}{[center]}bibliothèque '".$lib->Name."' de l'extension '$extensionname'{[/center]}{[/bold]}");
	$lbl->setLocation(0,0);
	$xfer_result->addComponent($lbl);

        $script='';
        foreach($lib->CodeFile as $code) 
        	$script.=$code."\n";
	$edt=new Xfer_Comp_Memo('code');
	$edt->setValue(urlencode($script));
	$edt->Encode=true;
	$edt->FirstLine=$lib->CodeLineBegin;
	$edt->setLocation(0,1);
	$xfer_result->addComponent($edt);

	require_once("Actions/phpTools.inc.php");
	$extDir = $lib->Mng->GetExtDir($lib->ExtensionName);
	$file_name = $extDir.$lib->Name.$lib->Mng->Suffix;
	$res=CheckSyntax($file_name);
	if (is_string($res)) {
		$lbl=new Xfer_Comp_LabelForm('code_error');
		$lbl->setValue("{[bold]}{[font color='red']}$res{[/font]}{[/bold]}");
		$lbl->setLocation(0,10,4);
		$xfer_result->addComponent($lbl);
	}

	if (!$cnx->IsReadOnly($extensionname))
		$xfer_result->addAction(new Xfer_Action("_Sauver","ok.png",$extensionname,"modifLibrary",FORMTYPE_MODAL,CLOSE_NO));
	$xfer_result->addAction(new Xfer_Action("_Fermer","close.png"));
	return $xfer_result;
}

?>
 
