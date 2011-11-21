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
require_once('FunctionTool.inc.php');

function editClasse($Params,$extensionname)
{
	$classe=$Params['classe'];
	$xfer_result=&new Xfer_Container_Custom($extensionname,"editClasse",$Params);
	$xfer_result->Caption="Gestion de la classe '$classe'";

	global $CNX_OBJ;
	$cnx=$CNX_OBJ;
	require_once("Class/Extension.inc.php");
	$extension=new Extension($extensionname);
	$ReadOnly=$cnx->IsReadOnly($extensionname);

	require_once("Class/Table.inc.php");
	$table=new Table($classe,$extensionname);

	$lbl=new Xfer_Comp_LabelForm('titlelbl');
	$lbl->setValue("{[bold]}{[center]}Gestion de la classe '$classe' de l'extension '$extensionname'{[/center]}{[/bold]}");
	$lbl->setLocation(0,0);
	$xfer_result->addComponent($lbl);

//====================================================================================================================

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
	foreach($mng->GetList($extensionname,$classe) as $Code) {
		$cd=new Action($Code,$extensionname);
		$grid->setValue($Code,'A',$mng->GetName($Code).$cd->GetParams());
		$grid->setValue($Code,'B',$cd->Description);
		$grid->setValue($Code,'C',$cd->RigthName);
	}
	$grid->addAction(new Xfer_Action("_Editer","",$extensionname,"editAction",FORMTYPE_NOMODAL,CLOSE_NO,SELECT_SINGLE));
if (!$ReadOnly) {
	$grid->addAction(new Xfer_Action("_Supprimer","",$extensionname,"deleteAction",FORMTYPE_MODAL,CLOSE_NO,SELECT_SINGLE));
	$grid->addAction(new Xfer_Action("_Ajouter","",$extensionname,"addAction",FORMTYPE_MODAL,CLOSE_NO,SELECT_NONE));
	if (count($table->Fields)>0)
		$grid->addAction(new Xfer_Action("_Générateur","",$extensionname,"wizardClasse",FORMTYPE_MODAL,CLOSE_NO,SELECT_NONE));
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
	foreach($mng->GetList($extensionname,$classe) as $Code) {
		$cd=new Method($Code,$extensionname);
		$grid->setValue($Code,'A',$mng->GetName($Code).$cd->GetParams());
		$grid->setValue($Code,'B',$cd->Description);
	}
	$grid->addAction(new Xfer_Action("_Editer","",$extensionname,"editMethod",FORMTYPE_NOMODAL,CLOSE_NO,SELECT_SINGLE));
if (!$ReadOnly) {
	$grid->addAction(new Xfer_Action("_Supprimer","",$extensionname,"deleteMethod",FORMTYPE_MODAL,CLOSE_NO,SELECT_SINGLE));
	$grid->addAction(new Xfer_Action("_Ajouter","",$extensionname,"addMethod",FORMTYPE_MODAL,CLOSE_NO,SELECT_NONE));
	if (count($table->Fields)>0)
		$grid->addAction(new Xfer_Action("_Générateur","",$extensionname,"wizardMethod",FORMTYPE_MODAL,CLOSE_NO,SELECT_NONE));
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
	foreach($mng->GetList($extensionname,$classe) as $Code) {
		$cd=new Stocked($Code,$extensionname);
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
	$xfer_result->newTab("Les Champs");
	$lbl=new Xfer_Comp_LabelForm('fieldlbl');
	$lbl->setValue("{[bold]}{[center]}Les Champs{[/center]}{[/bold]}");
	$lbl->setLocation(0,0,2);
	$xfer_result->addComponent($lbl);

	require_once("CORE/DBObject.inc.php");
	global $field_dico;

	$grid=new Xfer_Comp_Grid('field');
	$grid->newHeader('A',"Nom",4);
	$grid->newHeader('B',"Description",4);
	$grid->newHeader('C',"Type",4);
	$grid->newHeader('D',"Obligatoire",4);
	$grid->newHeader('E',"Paramètre",4);
	$complet_fields=$table->getCompletFields();
	foreach($complet_fields as $key=>$field)
	{
		$id=$key;
		$left='{[bold]}';
		$right='{[/bold]}';
		if (isset($field['super']))
		{
			$id='___'.$key;
			$left='{[italic]}';
			$right='{[/italic]}';
		}
		$type_id=(int)$field['type'];
		if (array_key_exists('notnull',$field) && ($field['notnull']))
			$notnull="Oui";
		else
			$notnull="Non";
		$grid->setValue($id,'A',$left.$key.$right);
		$grid->setValue($id,'B',$left.$field['description'].$right);
		$grid->setValue($id,'C',$left.$field_dico[$type_id][1].$right);
		$grid->setValue($id,'D',$left.$notnull.$right);
		$param_text=ArrayToString($field['params'], true);
		$param_text=str_replace(array('_APAS_'),'::',$param_text);
		$grid->setValue($id,'E',$left.$param_text.$right);
	}

	$grid->addAction(new Xfer_Action("_Editer","",$extensionname,"addField",FORMTYPE_MODAL,CLOSE_NO,SELECT_SINGLE));
if (!$ReadOnly) {
	$grid->addAction(new Xfer_Action("_Monter","",$extensionname,"upField",FORMTYPE_MODAL,CLOSE_NO,SELECT_SINGLE));
	$grid->addAction(new Xfer_Action("_Descendre","",$extensionname,"downField",FORMTYPE_MODAL,CLOSE_NO,SELECT_SINGLE));
	$grid->addAction(new Xfer_Action("_Supprimer","",$extensionname,"deleteField",FORMTYPE_MODAL,CLOSE_NO,SELECT_SINGLE));
	$grid->addAction(new Xfer_Action("_Ajouter","",$extensionname,"addField",FORMTYPE_MODAL,CLOSE_NO,SELECT_NONE));
}
	$grid->setLocation(0,1,2);
	$xfer_result->addComponent($grid);

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
	foreach($mng->GetList($extensionname,$classe) as $Code) {
		$cd=new Printing($Code,$extensionname);
		$grid->setValue($Code,'A',$mng->GetName($Code).$cd->GetParams());
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
	$xfer_result->newTab("Les valeurs initials");
	$lbl=new Xfer_Comp_LabelForm('valuelbl');
	$lbl->setValue("{[bold]}{[center]}Les valeurs initials{[/center]}{[/bold]}");
	$lbl->setLocation(0,0,2);
	$xfer_result->addComponent($lbl);
	$grid=new Xfer_Comp_Grid('initial');
	$grid->newHeader('A',"Rafraichir",3);
	$grid->newHeader('B',"ID",4);
	foreach($table->Fields as $key=>$field)
	{
		$type_id=(int)$field['type'];
		if ($type_id!=9)
		switch($type_id)
		{
			case 0:
				$grid->newHeader($key,$field['description'],0);
				break;
			case 1:
				$grid->newHeader($key,$field['description'],1);
				break;
			case 3:
				$grid->newHeader($key,$field['description'],3);
				break;
			default:
				$grid->newHeader($key,$field['description'],4);
				break;
		}
	}
	foreach($table->DefaultFields as $rowid=>$DefField)
	{
		if (array_key_exists('@refresh@',$DefField) && $DefField['@refresh@'])
			$grid->setValue($rowid,'A','Oui');
		else
			$grid->setValue($rowid,'A','Non');
		if (array_key_exists('id',$DefField))
			$grid->setValue($rowid,'B',$DefField['id']);
		else
			$grid->setValue($rowid,'B','');
		foreach($table->Fields as $key=>$field)
		{
			if (array_key_exists($key,$DefField))
			{
				$type_id=(int)$field['type'];
				$params=$field['params'];
				if ($type_id==3){
					if ($DefField[$key]=='o')
						$grid->setValue($rowid,$key,'Oui');
					else
						$grid->setValue($rowid,$key,'Non');
				}
				else if ($type_id==4)
					$grid->setValue($rowid,$key,convertDate($DefField[$key]));
				else if ($type_id==5)
					$grid->setValue($rowid,$key,convertTime($DefField[$key]));
				else if ($type_id==8)
					$grid->setValue($rowid,$key,$params['Enum'][$DefField[$key]]);
				else
					$grid->setValue($rowid,$key,$DefField[$key]);
			}
		}
	}
if (!$ReadOnly) {
	$grid->addAction(new Xfer_Action("_Editer","",$extensionname,"addInitial",FORMTYPE_MODAL,CLOSE_NO,SELECT_SINGLE));
	$grid->addAction(new Xfer_Action("_Supprimer","",$extensionname,"deleteInitial",FORMTYPE_MODAL,CLOSE_NO,SELECT_SINGLE));
	$grid->addAction(new Xfer_Action("_Ajouter","",$extensionname,"addInitial",FORMTYPE_MODAL,CLOSE_NO,SELECT_NONE));
}
	$grid->setLocation(0,1,2);
	$xfer_result->addComponent($grid);

//#############################################
	$xfer_result->newTab("Les paramètres");
	$lbl=new Xfer_Comp_LabelForm('paramlbl');
	$lbl->setValue("{[bold]}{[center]}Les paramètres{[/center]}{[/bold]}");
	$lbl->setLocation(0,0,4);
	$xfer_result->addComponent($lbl);

	$lbl=new Xfer_Comp_Label('border1lbl');
	$lbl->setValue(" ");
	$lbl->setLocation(0,1,1,6);
	$lbl->setsize(200,500);
	$xfer_result->addComponent($lbl);

	$lbl=new Xfer_Comp_LabelForm('lbltable_title');
	$lbl->setValue("{[bold]}titre de la classe{[/bold]}");
	$lbl->setLocation(1,1);
	$xfer_result->addComponent($lbl);

	$edt=new Xfer_Comp_Edit('table_title');
	$edt->setValue($table->Title);
	$edt->setLocation(2,1);
	$xfer_result->addComponent($edt);

	$lbl=new Xfer_Comp_Label('border2lbl');
	$lbl->setValue(" ");
	$lbl->setLocation(3,1,1,6);
	$lbl->setsize(200,500);
	$xfer_result->addComponent($lbl);

	$lbl=new Xfer_Comp_LabelForm('lblHeritage');
	$lbl->setValue("{[bold]}Héritage{[/bold]}");
	$lbl->setLocation(1,2);
	$xfer_result->addComponent($lbl);

	require_once("Class/Table.inc.php");
	$mng=new TableManage();
	$tbl_names=$mng->GetDependList($extension->Depencies,$extension->Name);
	$DependedTable=array(""=>"");
	foreach($tbl_names as $tbl_ext_name => $table_Names)
		foreach($table_Names as $tableName)
			if (($tbl_ext_name!=$extension->Name) || ($tableName!=$classe)) 
				$DependedTable["$tbl_ext_name/$tableName"]="$tbl_ext_name.$tableName";
	$edt=new Xfer_Comp_Select('heritage');
	$edt->setSelect($DependedTable);
	$edt->setValue($table->Heritage);
	$edt->setLocation(2,2);
	$xfer_result->addComponent($edt);

	$lbl=new Xfer_Comp_LabelForm('table_toTextlbl');
	$lbl->setValue("{[bold]}Text par défaut{[/bold]}");
	$lbl->setLocation(1,3);
	$xfer_result->addComponent($lbl);

	$edt=new Xfer_Comp_Edit('table_toText');
	$edt->setValue($table->ToText);
	$edt->setLocation(2,3);
	$xfer_result->addComponent($edt);

	$lbl=new Xfer_Comp_LabelForm('table_NbFieldsChecklbl');
	$lbl->setValue("{[bold]}Nb de champs de recherche{[/bold]}");
	$lbl->setLocation(1,4);
	$xfer_result->addComponent($lbl);

	$edt=new Xfer_Comp_Float('table_NbFieldsCheck',1,count($table->tbl->Fields),0);
	$edt->setValue($table->NbFieldsCheck);
	$edt->setLocation(2,4);
	$xfer_result->addComponent($edt);

if (!$ReadOnly) {
	$btn=new Xfer_Comp_Button('modifParam');
	$btn->setAction(new Xfer_Action("_Modifier","",$extensionname,'modifTableParam',FORMTYPE_MODAL,CLOSE_NO));
	$btn->setLocation(1,5,2);
	$xfer_result->addComponent($btn);
}

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
	foreach($mng->GetList($extensionname,$classe) as $Code) {
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

//====================================================================================================================

	$xfer_result->addAction(new Xfer_Action("_Fermer","close.png"));
	return $xfer_result;
}

?>
 
 
