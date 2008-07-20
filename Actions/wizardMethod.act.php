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

function wizardMethod($Params,$extensionname)
{
	$classe=$Params['classe'];
	require_once("Class/Extension.inc.php");
	require_once("Class/Table.inc.php");
	$table=new Table($classe,$extensionname);
	$extension=new Extension($extensionname);
	$xfer_result=&new Xfer_Container_Custom($extensionname,"wizardMethod",$Params);

	$lbl=new Xfer_Comp_LabelForm('titlelbl');
	$lbl->setValue("{[bold]}{[center]}Générateur de methodes de la classe '$classe'{[/center]}{[/bold]}");
	$lbl->setLocation(0,0,5);
	$xfer_result->addComponent($lbl);

	$lbl=new Xfer_Comp_LabelForm('descriptionlbl');
	$lbl->setValue("Description");
	$lbl->setLocation(0,1,2);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Edit('description');
	$edt->setValue($classe);
	$edt->setLocation(2,1,3);
	$edt->needed=true;
	$xfer_result->addComponent($edt);

	$lbl=new Xfer_Comp_LabelForm('suffixlbl');
	$lbl->setValue("Suffix");
	$lbl->setLocation(0,2,2);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Edit('suffix');
	$edt->setValue($classe);
	$edt->setLocation(2,2,3);
	$edt->needed=true;
	$xfer_result->addComponent($edt);

	$lbl=new Xfer_Comp_LabelForm('showlbl');
	$lbl->setValue("Show");
	$lbl->setLocation(1,3);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Check('show');
	$edt->setValue('n');
	$edt->setLocation(0,3);
	$xfer_result->addComponent($edt);

	$lbl=new Xfer_Comp_LabelForm('editlbl');
	$lbl->setValue("Edit");
	$lbl->setLocation(1,5);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Check('edit');
	$edt->setValue('n');
	$edt->setLocation(0,5);
	$xfer_result->addComponent($edt);

	$lbl=new Xfer_Comp_LabelForm('finderlbl');
	$lbl->setValue("Finder");
	$lbl->setLocation(3,3);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Check('finder');
	$edt->setValue('n');
	$edt->setLocation(2,3);
	$edt->JavaScript="
var val=current.getValue();
parent.get('searchNb').setEnabled(val=='o');
";
	$xfer_result->addComponent($edt);
	$edt=new Xfer_Comp_Float('searchNb',1,count($table->Fields),0);
	$edt->setValue(count($table->Fields));
	$edt->setLocation(4,3);
	$xfer_result->addComponent($edt);

	$lbl=new Xfer_Comp_LabelForm('gridlbl');
	$lbl->setValue("Grid");
	$lbl->setLocation(3,5);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Check('grid');
	$edt->setValue('n');
	$edt->setLocation(2,5);
	$edt->JavaScript="
var val=current.getValue();
parent.get('listNb').setEnabled(val=='o');
";
	$xfer_result->addComponent($edt);
	$edt=new Xfer_Comp_Float('listNb',1,count($table->Fields),0);
	$edt->setValue(count($table->Fields));
	$edt->setLocation(4,5);
	$xfer_result->addComponent($edt);

	if (is_file("../extensions/$extensionname/".$classe."_APAS_AddModify.act.php"))
		$xfer_result->m_context['add']='o';
	if (is_file("../extensions/$extensionname/".$classe."_APAS_Fiche.act.php"))
		$xfer_result->m_context['fiche']='o';
	if (is_file("../extensions/$extensionname/".$classe."_APAS_Del.act.php"))
		$xfer_result->m_context['del']='o';

	$xfer_result->addAction(new Xfer_Action("_Modifier","ok.png",$extensionname,'wizardClasseValid',FORMTYPE_MODAL,CLOSE_YES));
	$xfer_result->addAction(new Xfer_Action("_Fermer","close.png"));
	return $xfer_result;
}

?>
 
