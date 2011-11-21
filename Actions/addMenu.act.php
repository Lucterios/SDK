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

function addMenu($Params,$extensionname)
{
	$xfer_result=&new Xfer_Container_Custom($extensionname,"addMenu",$Params);
	$xfer_result->Caption='Ajouter un Menu';

	global $CNX_OBJ;
	$cnx=$CNX_OBJ;
	require_once("../CORE/setup_param.inc.php");
	require_once("Class/Extension.inc.php");
	$extension=new Extension($extensionname);
	if (array_key_exists('menu',$Params)) {
		$menu=$extension->Menus[$Params['menu']];
	} else 
		$menu=new Param_Menu('','');
	
	$lbl=new Xfer_Comp_LabelForm('descriptionlbl');
	$lbl->setValue("{[bold]}Description{[/bold]}");
	$lbl->setLocation(0,0);
	$xfer_result->addComponent($lbl);
	$ext_list=$extension->getList($cnx);
	$edt=new Xfer_Comp_Edit('description');
	$edt->setLocation(1,0);
	$edt->setValue($menu->description);
	$edt->setNeeded(true);
	$xfer_result->addComponent($edt);

	$lbl=new Xfer_Comp_LabelForm('positionlbl');
	$lbl->setValue("{[bold]}Position{[/bold]}");
	$lbl->setLocation(0,1);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Float('position',1,10000,0);
	$edt->setValue($menu->position);
	$edt->setLocation(1,1);
	$xfer_result->addComponent($edt);

	$lbl=new Xfer_Comp_LabelForm('helplbl');
	$lbl->setValue("{[bold]}Aide{[/bold]}");
	$lbl->setLocation(0,2);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Memo('help');
	$edt->setValue($menu->help);
	$edt->setLocation(1,2,2);
	$xfer_result->addComponent($edt);

	$lbl=new Xfer_Comp_LabelForm('actionlbl');
	$lbl->setValue("{[bold]}Action{[/bold]}");
	$lbl->setLocation(0,3);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Select('action');
	$edt->setValue($menu->act);
	$select_a['']='';
	foreach($extension->Actions as $action_item)
		$select_a[$action_item->action]=$action_item->action;
	$edt->setSelect($select_a);
	$edt->setLocation(1,3);
	$xfer_result->addComponent($edt);

	$menu_list_without_act=$extension->GetMenuListWithoutAction();
	$lbl=new Xfer_Comp_LabelForm('perelbl');
	$lbl->setValue("{[bold]}Père{[/bold]}");
	$lbl->setLocation(0,4);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Select('pere');
	$edt->setValue($menu->pere);
	$select_m['']='';
	foreach($menu_list_without_act as $menu_desc)
		$select_m[$menu_desc]=$menu_desc;
	$edt->setSelect($select_m);
	$edt->setLocation(1,4);
	$xfer_result->addComponent($edt);

	require_once "Class/Image.inc.php";
	$img_mng=new ImageManage();
	$dir_img=$img_mng->GetExtDir($extension->Name);
	$images=$img_mng->GetList($extension->Name);
	$lbl=new Xfer_Comp_LabelForm('iconlbl');
	$lbl->setValue("{[bold]}Icon{[/bold]}");
	$lbl->setLocation(0,5);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Select('icon');
	$edt->setValue($menu->icon);
	$select_i['']='';
	foreach($images as $image)
		$select_i[$image]=$image;
	$edt->setSelect($select_i);
	$edt->setLocation(1,5);
	$xfer_result->addComponent($edt);

	$lbl=new Xfer_Comp_LabelForm('shortcutlbl');
	$lbl->setValue("{[bold]}Racourcis clavier{[/bold]}");
	$lbl->setLocation(0,6);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Edit('shortcut');
	$edt->setValue($menu->shortcut);
	$edt->setLocation(1,6);
	$xfer_result->addComponent($edt);

	$lbl=new Xfer_Comp_LabelForm('modallbl');
	$lbl->setValue("{[bold]}Modalité{[/bold]}");
	$lbl->setLocation(0,7);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Check('modal');
	$edt->setValue($menu->modal);
	$edt->setLocation(1,7);
	$xfer_result->addComponent($edt);

	$xfer_result->addAction(new Xfer_Action("_OK","ok.png",$extensionname,"addMenuValid",FORMTYPE_MODAL,CLOSE_YES));
	$xfer_result->addAction(new Xfer_Action("_Fermer","close.png"));
	return $xfer_result;
}

?>
 
