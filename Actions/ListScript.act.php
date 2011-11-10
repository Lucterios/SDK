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
require_once('Main/connection.inc.php');

function ListScript($Params,$extensionname)
{
	$xfer_result=&new Xfer_Container_Custom($extensionname,"ListScript",$Params);
	$xfer_result->Caption="Gestion des scripts de l'extension '$extensionname'";

	global $CNX_OBJ;
	$cnx=$CNX_OBJ;
	require_once("Class/Extension.inc.php");
	$extension=new Extension($extensionname);
	$ReadOnly=$cnx->IsReadOnly($extensionname);

	$lbl=new Xfer_Comp_Label('marge1');
	$lbl->setValue("");
	$lbl->setSize(10,200,10,200);
	$lbl->setLocation(0,0,1,4);
	$xfer_result->addComponent($lbl);
	$lbl=new Xfer_Comp_Label('marge2');
	$lbl->setValue("");
	$lbl->setSize(10,200,10,200);
	$lbl->setLocation(10,0,1,4);
	$xfer_result->addComponent($lbl);

	$lbl=new Xfer_Comp_LabelForm('namelbl');
	$lbl->setValue("{[bold]}Nom{[/bold]}");
	$lbl->setLocation(1,0);
	$xfer_result->addComponent($lbl);
	$lbl=new Xfer_Comp_LabelForm('name');
	$lbl->setValue($extension->Name);
	$lbl->setLocation(2,0,4);
	$xfer_result->addComponent($lbl);

	$lbl=new Xfer_Comp_LabelForm('extensiontitlelbl');
	$lbl->setValue("{[bold]}Titre{[/bold]}");
	$lbl->setLocation(1,1);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Label('extensiontitle');
	$edt->setValue($extension->Titre);
	$edt->setLocation(2,1,4);
	$xfer_result->addComponent($edt);

if ($extension->Name=='applis') {
	$lbl=new Xfer_Comp_LabelForm('extensionapplilbl');
	$lbl->setValue("{[bold]}Application{[/bold]}");
	$lbl->setLocation(1,2);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Label('extensionappli');
	$edt->setValue($extension->Appli);
	$edt->setLocation(2,2,4);
	$xfer_result->addComponent($edt);
}
if (($extension->Name!='applis') && ($extension->Name!='CORE')) {
	$lbl=new Xfer_Comp_LabelForm('extensionfamillelbl');
	$lbl->setValue("{[bold]}Famille{[/bold]}");
	$lbl->setLocation(1,2);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Label('extensionfamille');
	$edt->setValue($extension->Famille);
	$edt->setLocation(2,2,4);
	$xfer_result->addComponent($edt);
}

	$lbl=new Xfer_Comp_LabelForm('Versionlbl');
	$lbl->setValue("{[bold]}Version{[/bold]}");
	$lbl->setLocation(1,3);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Label('Version');
	$edt->setValue($extension->Version[0].".".$extension->Version[1].".".$extension->Version[2].".".$extension->Version[3]);
	$edt->setLocation(2,3,4);
	$xfer_result->addComponent($edt);

	$lbl=new Xfer_Comp_LabelForm('extensiondesclbl');
	$lbl->setValue("{[bold]}Description{[/bold]}");
	$lbl->setLocation(1,4);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Label('extensiondesc');
	$edt->setValue($extension->Description);
	$edt->setLocation(2,4,4);
	$edt->setSize(10,10);
	$xfer_result->addComponent($edt);

	$btn=new Xfer_Comp_Button('refreshBtn');
	$btn->setAction(new Xfer_Action("_Rafraichir","",$extensionname,'refreshExtension',FORMTYPE_MODAL,CLOSE_NO));
	$btn->setLocation(1,5,5);
	$xfer_result->addComponent($btn);

	$lbl=new Xfer_Comp_Label('marge3');
	$lbl->setValue("");
	$lbl->setSize(20,10);
	$lbl->setLocation(1,10,5);
	$xfer_result->addComponent($lbl);

//====================================================================================================================

if ($extensionname!="applis") {
//#############################################
	require_once("Class/Table.inc.php");
	$xfer_result->newTab("Les Classes");
	$lbl=new Xfer_Comp_LabelForm('classelbl');
	$lbl->setValue("{[bold]}{[center]}Les Classes{[/center]}{[/bold]}");
	$lbl->setLocation(0,0,2);
	$xfer_result->addComponent($lbl);
	$grid=new Xfer_Comp_Grid('classe');
	$grid->newHeader('A',"Nom",4);
	$Mng=new TableManage();
	foreach($Mng->GetList($extensionname) as $Table)
		$grid->setValue($Table,'A',$Table);
	$grid->addAction(new Xfer_Action("_Editer","",$extensionname,"editClasse",FORMTYPE_NOMODAL,CLOSE_NO,SELECT_SINGLE));
if (!$ReadOnly) {
	$grid->addAction(new Xfer_Action("_Supprimer","",$extensionname,"deleteClasse",FORMTYPE_MODAL,CLOSE_NO,SELECT_SINGLE));
	$grid->addAction(new Xfer_Action("_Ajouter","",$extensionname,"addClasse",FORMTYPE_MODAL,CLOSE_NO,SELECT_NONE));
}
	$grid->setLocation(0,1,2);
	$xfer_result->addComponent($grid);

//#############################################
	require_once("Class/Action.inc.php");
	$xfer_result->newTab("Les Actions");
	$lbl=new Xfer_Comp_LabelForm('actionlbl');
	$lbl->setValue("{[bold]}{[center]}Les Actions{[/center]}{[/bold]}");
	$lbl->setLocation(0,0,2);
	$xfer_result->addComponent($lbl);
	$grid=new Xfer_Comp_Grid('action');
	$grid->newHeader('A',"Nom",4);
	$grid->newHeader('B',"Description",4);
	$grid->newHeader('C',"Droit",4);
	$mng=new ActionManage;
	foreach($mng->GetList($extensionname) as $Code) {
		$cd=new Action($Code,$extensionname);
		$grid->setValue($Code,'A',$mng->GetName($Code).$cd->GetParams());
		$grid->setValue($Code,'B',$cd->Description);
		$grid->setValue($Code,'C',$cd->RigthName);
	}
	$grid->addAction(new Xfer_Action("_Editer","",$extensionname,"editAction",FORMTYPE_NOMODAL,CLOSE_NO,SELECT_SINGLE));
if (!$ReadOnly) {
	$grid->addAction(new Xfer_Action("_Supprimer","",$extensionname,"deleteAction",FORMTYPE_MODAL,CLOSE_NO,SELECT_SINGLE));
	$grid->addAction(new Xfer_Action("_Ajouter","",$extensionname,"addAction",FORMTYPE_MODAL,CLOSE_NO,SELECT_NONE));
}
	$grid->setLocation(0,1,2);
	$xfer_result->addComponent($grid);
}

//#############################################
	require_once("Class/Library.inc.php");
	$xfer_result->newTab("Les Bibliothèques");
	$lbl=new Xfer_Comp_LabelForm('librarylbl');
	$lbl->setValue("{[bold]}{[center]}Les Bibliothèques{[/center]}{[/bold]}");
	$lbl->setLocation(0,0,2);
	$xfer_result->addComponent($lbl);
	$grid=new Xfer_Comp_Grid('library');
	$grid->newHeader('A',"Nom",4);
	$mng=new LibraryManage;
	foreach($mng->GetList($extensionname) as $Code) {
		$grid->setValue($Code,'A',$Code);
	}
	$grid->addAction(new Xfer_Action("_Editer","",$extensionname,"editLibrary",FORMTYPE_NOMODAL,CLOSE_NO,SELECT_SINGLE));
if (!$ReadOnly) {
	$grid->addAction(new Xfer_Action("_Supprimer","",$extensionname,"deleteLibrary",FORMTYPE_MODAL,CLOSE_NO,SELECT_SINGLE));
	$grid->addAction(new Xfer_Action("_Ajouter","",$extensionname,"addLibrary",FORMTYPE_MODAL,CLOSE_NO,SELECT_NONE));
}
	$grid->setLocation(0,1,2);
	$xfer_result->addComponent($grid);

//#############################################
	require_once("Class/Event.inc.php");
	$xfer_result->newTab("Les Evenements");
	$lbl=new Xfer_Comp_LabelForm('eventlbl');
	$lbl->setValue("{[bold]}{[center]}Les Evenements{[/center]}{[/bold]}");
	$lbl->setLocation(0,0,2);
	$xfer_result->addComponent($lbl);
	$grid=new Xfer_Comp_Grid('event');
	$grid->newHeader('A',"Nom",4);
	$grid->newHeader('B',"Description",4);
	$mng=new EventManage;
	foreach($mng->GetList($extensionname) as $event) {
		$evt=new Event($event,$extensionname);
		$grid->setValue($event,'A',$mng->GetName($event).$evt->GetParams());
		$grid->setValue($event,'B',$evt->Description);
	}
	$grid->addAction(new Xfer_Action("_Editer","",$extensionname,"editEvent",FORMTYPE_NOMODAL,CLOSE_NO,SELECT_SINGLE));
if (!$ReadOnly) {
	$grid->addAction(new Xfer_Action("_Supprimer","",$extensionname,"deleteEvent",FORMTYPE_MODAL,CLOSE_NO,SELECT_SINGLE));
	$grid->addAction(new Xfer_Action("_Ajouter","",$extensionname,"addEvent",FORMTYPE_MODAL,CLOSE_NO,SELECT_NONE));
}
	$grid->setLocation(0,1,2);
	$xfer_result->addComponent($grid);

if ($extensionname!="applis") {
//#############################################
	require_once("Class/Printing.inc.php");
	$xfer_result->newTab("Les Impressions");
	$lbl=new Xfer_Comp_LabelForm('printlbl');
	$lbl->setValue("{[bold]}{[center]}Les Impressions{[/center]}{[/bold]}");
	$lbl->setLocation(0,0,2);
	$xfer_result->addComponent($lbl);
	$grid=new Xfer_Comp_Grid('printing');
	$grid->newHeader('A',"Nom",4);
	$grid->newHeader('B',"Description",4);
	$mng=new PrintingManage;
	foreach($mng->GetList($extensionname) as $Code) {
		$cd=new Printing($Code,$extensionname);
		$grid->setValue($Code,'A',$mng->GetName($Code).$cd->GetParams());
		$grid->setValue($Code,'B',$cd->Editeur);
		$grid->setValue($Code,'B',$cd->Description);
	}
	$grid->addAction(new Xfer_Action("_Editer","",$extensionname,"editPrinting",FORMTYPE_NOMODAL,CLOSE_NO,SELECT_SINGLE));
	$grid->addAction(new Xfer_Action("_Modeliser","",$extensionname,"modelPrinting",FORMTYPE_MODAL,CLOSE_NO,SELECT_SINGLE));
if (!$ReadOnly) {
	$grid->addAction(new Xfer_Action("_Supprimer","",$extensionname,"deletePrinting",FORMTYPE_MODAL,CLOSE_NO,SELECT_SINGLE));
	$grid->addAction(new Xfer_Action("_Ajouter","",$extensionname,"addPrinting",FORMTYPE_MODAL,CLOSE_NO,SELECT_NONE));
}
	$grid->setLocation(0,1,2);
	$xfer_result->addComponent($grid);

//#############################################
	require_once("Class/Method.inc.php");
	$xfer_result->newTab("Les Méthodes");
	$lbl=new Xfer_Comp_LabelForm('methodlbl');
	$lbl->setValue("{[bold]}{[center]}Les Méthodes{[/center]}{[/bold]}");
	$lbl->setLocation(0,0,2);
	$xfer_result->addComponent($lbl);
	$grid=new Xfer_Comp_Grid('method');
	$grid->newHeader('A',"Nom",4);
	$grid->newHeader('B',"Description",4);
	$mng=new MethodManage;
	foreach($mng->GetList($extensionname) as $Code) {
		$cd=new Method($Code,$extensionname);
		$grid->setValue($Code,'A',$mng->GetName($Code).$cd->GetParams());
		$grid->setValue($Code,'B',$cd->Description);
	}
	$grid->addAction(new Xfer_Action("_Editer","",$extensionname,"editMethod",FORMTYPE_NOMODAL,CLOSE_NO,SELECT_SINGLE));
if (!$ReadOnly) {
	$grid->addAction(new Xfer_Action("_Supprimer","",$extensionname,"deleteMethod",FORMTYPE_MODAL,CLOSE_NO,SELECT_SINGLE));
	$grid->addAction(new Xfer_Action("_Ajouter","",$extensionname,"addMethod",FORMTYPE_MODAL,CLOSE_NO,SELECT_NONE));
}
	$grid->setLocation(0,1,2);
	$xfer_result->addComponent($grid);

//#############################################
	require_once("Class/Stocked.inc.php");
	$xfer_result->newTab("Les fonctions stockées");
	$lbl=new Xfer_Comp_LabelForm('stockedlbl');
	$lbl->setValue("{[bold]}{[center]}Les fonctions stockées{[/center]}{[/bold]}");
	$lbl->setLocation(0,0,2);
	$xfer_result->addComponent($lbl);
	$grid=new Xfer_Comp_Grid('stocked');
	$grid->newHeader('A',"Nom",4);
	$grid->newHeader('B',"Description",4);
	$mng=new StockedManage;
	foreach($mng->GetList($extensionname) as $Code) {
		$cd=new Method($Code,$extensionname);
		$grid->setValue($Code,'A',$mng->GetName($Code).$cd->GetParams());
		$grid->setValue($Code,'B',$cd->Description);
	}
	$grid->addAction(new Xfer_Action("_Editer","",$extensionname,"editStocked",FORMTYPE_NOMODAL,CLOSE_NO,SELECT_SINGLE));
if (!$ReadOnly) {
	$grid->addAction(new Xfer_Action("_Supprimer","",$extensionname,"deleteStocked",FORMTYPE_MODAL,CLOSE_NO,SELECT_SINGLE));
	$grid->addAction(new Xfer_Action("_Ajouter","",$extensionname,"addStocked",FORMTYPE_MODAL,CLOSE_NO,SELECT_NONE));
}
	$grid->setLocation(0,1,2);
	$xfer_result->addComponent($grid);

//#############################################
	require_once("Class/Test.inc.php");
	$xfer_result->newTab("Les Tests");
	$lbl=new Xfer_Comp_LabelForm('testlbl');
	$lbl->setValue("{[bold]}{[center]}Les Tests{[/center]}{[/bold]}");
	$lbl->setLocation(0,0,2);
	$xfer_result->addComponent($lbl);
	$grid=new Xfer_Comp_Grid('test');
	$grid->newHeader('A',"Nom",4);
	$grid->newHeader('B',"Description",4);
	$mng=new TestManage;
	foreach($mng->GetList($extensionname) as $Code) {
		$cd=new Test($Code,$extensionname);
		$grid->setValue($Code,'A',$mng->GetName($Code).$cd->GetParams());
		$grid->setValue($Code,'B',$cd->Description);
	}
	$grid->addAction(new Xfer_Action("_Editer","",$extensionname,"editTest",FORMTYPE_NOMODAL,CLOSE_NO,SELECT_SINGLE));
if (!$ReadOnly) {
	$grid->addAction(new Xfer_Action("_Supprimer","",$extensionname,"deleteTest",FORMTYPE_MODAL,CLOSE_NO,SELECT_SINGLE));
	$grid->addAction(new Xfer_Action("_Ajouter","",$extensionname,"addTest",FORMTYPE_MODAL,CLOSE_NO,SELECT_NONE));
}
	$grid->setLocation(0,1,2);
	$xfer_result->addComponent($grid);

}

//====================================================================================================================

	$xfer_result->addAction(new Xfer_Action("_Fermer","close.png"));
	return $xfer_result;
}

?>
 
 
