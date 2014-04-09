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

function addRight($Params,$extensionname)
{
	$xfer_result=new Xfer_Container_Custom($extensionname,"addRight",$Params);
	$xfer_result->Caption='Ajouter un droit';

	global $CNX_OBJ;
	$cnx=$CNX_OBJ;
	require_once("../CORE/setup_param.inc.php");
	require_once("Class/Extension.inc.php");
	$extension=new Extension($extensionname);
	if (array_key_exists('right',$Params)) {
		$right_id=$Params['right'];
		$right=$extension->Rights[$right_id];
	} else {
		$right_id='---';
		$right=new Param_Rigth('');
	}
	
	$lbl=new Xfer_Comp_LabelForm('rightlbl');
	$lbl->setValue("{[bold]}N°{[/bold]}");
	$lbl->setLocation(0,0);
	$xfer_result->addComponent($lbl);
	$ext_list=$extension->getList($cnx);
	$edt=new Xfer_Comp_Label('right');
	$edt->setLocation(1,0);
	$edt->setValue($right_id+1);
	$xfer_result->addComponent($edt);

	$lbl=new Xfer_Comp_LabelForm('descriptionlbl');
	$lbl->setValue("{[bold]}Description{[/bold]}");
	$lbl->setLocation(0,1);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Edit('description');
	$edt->setValue($right->description);
	$edt->setNeeded(true);
	$edt->setLocation(1,1);
	$xfer_result->addComponent($edt);

	$lbl=new Xfer_Comp_LabelForm('weigthlbl');
	$lbl->setValue("{[bold]}Position{[/bold]}");
	$lbl->setLocation(0,2);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Float('weigth',0,101,0);
	$edt->setValue($right->weigth);
	$edt->setLocation(1,2);
	$xfer_result->addComponent($edt);

	$xfer_result->addAction(new Xfer_Action("_OK","ok.png",$extensionname,"addRightValid",FORMTYPE_MODAL,CLOSE_YES));
	$xfer_result->addAction(new Xfer_Action("_Fermer","close.png"));
	return $xfer_result;
}

?>
 
