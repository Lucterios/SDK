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

function wizardClasse($Params,$extensionname)
{
	$classe=$Params['classe'];
	require_once("Class/Extension.inc.php");
	require_once("Class/Table.inc.php");
	$table=new Table($classe,$extensionname);
	$extension=new Extension($extensionname);
	$xfer_result=&new Xfer_Container_Custom($extensionname,"wizardClasse",$Params);

	$lbl=new Xfer_Comp_LabelForm('titlelbl');
	$lbl->setValue("{[bold]}{[center]}Générateur d'actions de la classe '$classe'{[/center]}{[/bold]}");
	$lbl->setLocation(0,0,6);
	$xfer_result->addComponent($lbl);

	$lbl=new Xfer_Comp_LabelForm('descriptionlbl');
	$lbl->setValue("Description{[newline]}(sing./plur.)");
	$lbl->setLocation(0,1,2);
	$xfer_result->addComponent($lbl);

	$edt=new Xfer_Comp_Select('genre');
	$edt->setValue(0);
	$edt->setSelect(array(0=>'Masc.',1=>'Fem.'));
	$edt->setLocation(2,1);
	$xfer_result->addComponent($edt);
	
	$edt=new Xfer_Comp_Edit('descriptionS');
	$edt->setValue($classe);
	$edt->setLocation(3,1,2);
	$edt->setSize(20,100);
	$edt->needed=true;
	$xfer_result->addComponent($edt);
	$edt=new Xfer_Comp_Edit('descriptionP');
	$edt->setValue($classe."s");
	$edt->setLocation(5,1);
	$edt->setSize(20,100);
	$edt->needed=true;
	$xfer_result->addComponent($edt);

	$lbl=new Xfer_Comp_LabelForm('suffixlbl');
	$lbl->setValue("Suffix");
	$lbl->setLocation(0,2,3);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Edit('suffix');
	$edt->setValue($classe);
	$edt->setLocation(3,2,3);
	$edt->needed=true;
	$xfer_result->addComponent($edt);

	$lbl=new Xfer_Comp_LabelForm('addlbl');
	$lbl->setValue("Ajouter");
	$lbl->setLocation(1,3,2);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Check('add');
	$edt->setValue('n');
	$edt->setLocation(0,3);
	$edt->JavaScript="
var val=current.getValue();
var valm=parent.get('modif').getValue();
parent.get('droitAjoutModif').setEnabled((val=='o') || (valm=='o'));
";
	$xfer_result->addComponent($edt);

	$lbl=new Xfer_Comp_LabelForm('fichelbl');
	$lbl->setValue("Consulter");
	$lbl->setLocation(1,4,2);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Check('fiche');
	$edt->setValue('n');
	$edt->setLocation(0,4);
	$edt->JavaScript="
var val=current.getValue();
parent.get('printFile').setEnabled(val=='o');

var vall=parent.get('list').getValue();
var vals=parent.get('search').getValue();2
parent.get('droitVisu').setEnabled((val=='o') || (vall=='o') || (vals=='o'));
";
	$xfer_result->addComponent($edt);

	$lbl=new Xfer_Comp_LabelForm('modiflbl');
	$lbl->setValue("Modifier");
	$lbl->setLocation(1,5,2);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Check('modif');
	$edt->setValue('n');
	$edt->setLocation(0,5);
	$edt->JavaScript="
var val=current.getValue();
var vala=parent.get('add').getValue();
parent.get('droitAjoutModif').setEnabled((val=='o') || (vala=='o'));
";
	$xfer_result->addComponent($edt);

	$lbl=new Xfer_Comp_LabelForm('dellbl');
	$lbl->setValue("Supprimer");
	$lbl->setLocation(1,6,2);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Check('del');
	$edt->setValue('n');
	$edt->setLocation(0,6);
	$edt->JavaScript="
var val=current.getValue();
parent.get('droitDel').setEnabled((val=='o'));
";
	$xfer_result->addComponent($edt);

	$lbl=new Xfer_Comp_LabelForm('listlbl');
	$lbl->setValue("Listing");
	$lbl->setLocation(4,3);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Check('list');
	$edt->setValue('n');
	$edt->setLocation(3,3);
	$edt->JavaScript="
var val=current.getValue();
var vals=parent.get('search').getValue();
parent.get('printList').setEnabled((val=='o') || (vals=='o'));
parent.get('listNb').setEnabled(val=='o');

var vale=parent.get('fiche').getValue();
parent.get('droitVisu').setEnabled((val=='o') || (vale=='o') || (vals=='o'));
";
	$xfer_result->addComponent($edt);
	$edt=new Xfer_Comp_Float('listNb',1,count($table->Fields),0);
	$edt->setValue(count($table->Fields));
	$edt->setLocation(5,3);
	$xfer_result->addComponent($edt);

	$lbl=new Xfer_Comp_LabelForm('searchlbl');
	$lbl->setValue("Recherche");
	$lbl->setLocation(4,4);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Check('search');
	$edt->setValue('n');
	$edt->setLocation(3,4);
	$edt->JavaScript="
var val=current.getValue();
var vall=parent.get('list').getValue();
parent.get('printList').setEnabled((val=='o') || (vall=='o'));
parent.get('searchNb').setEnabled(val=='o');

var vale=parent.get('fiche').getValue();
parent.get('droitVisu').setEnabled((val=='o') || (vall=='o') || (vale=='o'));
";
	$xfer_result->addComponent($edt);
	$edt=new Xfer_Comp_Float('searchNb',1,count($table->Fields),0);
	$edt->setValue(count($table->Fields));
	$edt->setLocation(5,4);
	$xfer_result->addComponent($edt);

	$lbl=new Xfer_Comp_LabelForm('printListlbl');
	$lbl->setValue("Impression de listing");
	$lbl->setLocation(4,5,2);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Check('printList');
	$edt->setValue('n');
	$edt->setLocation(3,5);
	$xfer_result->addComponent($edt);

	$lbl=new Xfer_Comp_LabelForm('printFilelbl');
	$lbl->setValue("Impression de fiche");
	$lbl->setLocation(4,6,2);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Check('printFile');
	$edt->setValue('n');
	$edt->setLocation(3,6);
	$xfer_result->addComponent($edt);

	$lbl=new Xfer_Comp_LabelForm('useMethodlbl');
	$lbl->setValue("Utilisation des methodes dans les actions");
	$lbl->setLocation(1,7,5);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Check('useMethod');
	$edt->setValue('o');
	$edt->setLocation(0,7,2);
	$xfer_result->addComponent($edt);

	foreach($extension->Rights as $rigth_id=>$rigth_name)
		$select_right[$rigth_id]=$rigth_name->description;

	$lbl=new Xfer_Comp_LabelForm('droitVisulbl');
	$lbl->setValue("Droit de Visualisation");
	$lbl->setLocation(0,10,3);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Select('droitVisu');
	$edt->setValue(0);
	$edt->setSelect($select_right);
	$edt->setLocation(3,10,3);
	$xfer_result->addComponent($edt);

	$lbl=new Xfer_Comp_LabelForm('droitAjoutModiflbl');
	$lbl->setValue("Droit de Modification");
	$lbl->setLocation(0,11,3);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Select('droitAjoutModif');
	$edt->setValue(0);
	$edt->setSelect($select_right);
	$edt->setLocation(3,11,3);
	$xfer_result->addComponent($edt);

	$lbl=new Xfer_Comp_LabelForm('droitDellbl');
	$lbl->setValue("Droit de Suppression");
	$lbl->setLocation(0,12,3);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Select('droitDel');
	$edt->setValue(0);
	$edt->setSelect($select_right);
	$edt->setLocation(3,12,3);
	$xfer_result->addComponent($edt);

	require_once "Class/Image.inc.php";
	$img_mng=new ImageManage();
	$dir_img=$img_mng->GetExtDir($extensionname);
	$images=$img_mng->GetList($extensionname);
	$lbl=new Xfer_Comp_LabelForm('iconlbl');
	$lbl->setValue("Icon");
	$lbl->setLocation(0,13,3);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Select('icon');
	$edt->setValue('');
	$select_i['']='';
	foreach($images as $image)
		$select_i[$image]=$image;
	$edt->setSelect($select_i);
	$edt->setLocation(3,13,3);
	$xfer_result->addComponent($edt);

	$xfer_result->addAction(new Xfer_Action("_Modifier","ok.png",$extensionname,'wizardClasseValid',FORMTYPE_MODAL,CLOSE_YES));
	$xfer_result->addAction(new Xfer_Action("_Fermer","close.png"));
	return $xfer_result;
}

?>
 
