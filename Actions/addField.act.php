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
require_once('FunctionTool.inc.php');

function addField($Params,$extensionname)
{
	global $CNX_OBJ;
	$cnx=$CNX_OBJ;

	$classe=$Params['classe'];
	require_once("Class/Extension.inc.php");
	require_once("Class/Table.inc.php");
	$extension=new Extension($extensionname);
	$table=new Table($classe,$extensionname);
	if (array_key_exists('field',$Params)) {
		$field_name=$Params['field'];
		$field=$table->Fields[$field_name];
	} else {
		$field_name='';
		$field=array('description'=>'','type'=>0,'notnull'=>false,'params'=>array('Min'=>0,'Max'=>1000,'Prec'=>2,'Size'=>50,'Multi'=>'n','Enum'=>array(),'RefField'=>'','TableName'=>''));
	}

	$select_xml="";
	$script_ref="childFields=new Array();\n";
	$tbl_names=$table->Mng->GetDependList($extension->Depencies,$table->ExtensionName);
	$tbl_idx=0;
	$fld_idx=0;
	$table_Name_long=$table->ExtensionName."_".$table->Name;
	foreach($tbl_names as $tbl_ext_name => $tbl_name_list)
		foreach($tbl_name_list as $tbl_name)
		{
			$other_tbl=new Table($tbl_name,$tbl_ext_name);
			$tbl_name_long=$tbl_ext_name."_".$tbl_name;
			$tbl_name_desc=$tbl_ext_name."::".$tbl_name;
			$select_xml.="<CASE id=\"".$tbl_name_long."\">".$tbl_name_desc."</CASE>";
			$tbl_idx++;
			$field_list=$other_tbl->GetTinyField($table_Name_long);
			foreach($field_list as $fieldname)
			{
				$script_ref.="childFields[$fld_idx]=new Array(\"$tbl_name_long\",\"$tbl_name_desc\",\"$fieldname\");\n";
				$fld_idx++;
			}
		}

	$xfer_result=&new Xfer_Container_Custom($extensionname,"addField",$Params);
	$xfer_result->Caption="Edition d'un champ";

	$lbl=new Xfer_Comp_LabelForm('namelbl');
	$lbl->setValue("{[bold]}Nom{[/bold]}");
	$lbl->setLocation(0,0);
	$xfer_result->addComponent($lbl);
if ($field_name=='') {
	$edt=new Xfer_Comp_Edit('name');
	$edt->setLocation(1,0,2);
	$edt->setNeeded(true);
	$edt->ExprReg="[a-zA-Z][a-zA-Z0-9]*";
	$edt->StringSize=100;
	$xfer_result->addComponent($edt);
}else{
	$xfer_result->m_context['name']=$field_name;
	$lbl=new Xfer_Comp_Label('name');
	$lbl->setValue($field_name);
	$lbl->setLocation(1,0,2);
	$xfer_result->addComponent($lbl);
}
	$lbl=new Xfer_Comp_LabelForm('descriptionlbl');
	$lbl->setValue("{[bold]}Description{[/bold]}");
	$lbl->setLocation(0,2);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Edit('field_description');
	$edt->setValue($field['description']);
	$edt->setNeeded(true);
	$edt->setLocation(1,2,2);
	$xfer_result->addComponent($edt);

	require_once("CORE/DBObject.inc.php");
	global $field_dico;
	$lbl=new Xfer_Comp_LabelForm('typelbl');
	$lbl->setValue("{[bold]}Type{[/bold]}");
	$lbl->setLocation(0,3);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Select('field_type');
	$edt->setValue($field['type']);
	foreach($field_dico as $key => $val)
		$select[$key]=$val[1];
	$edt->setSelect($select);
	$edt->setLocation(1,3,2);
	$edt->JavaScript="
var type=current.getValue();
parent.get('field_ValMin').setEnabled((type=='0') || (type=='1') || (type=='13'));
parent.get('field_ValMax').setEnabled((type=='0') || (type=='1') || (type=='13'));
parent.get('field_ValPrec').setEnabled((type=='1') || (type=='13'));
parent.get('field_ValSize').setEnabled(type=='2');
parent.get('field_ValMulti').setEnabled(type=='2');
parent.get('field_ValEnum').setEnabled(type=='8');
parent.get('field_TableName').setEnabled((type=='9') || (type=='10'));
parent.get('field_RefField').setEnabled(type=='9');
parent.get('field_Function').setEnabled(type=='11');
parent.get('field_MethodGet').setEnabled((type=='12') || (type=='13'));
parent.get('field_MethodSet').setEnabled((type=='12') || (type=='13'));
if (type=='9') {
".$script_ref."
	var new_text='<SELECT>".$field['params']['TableName']."';
	for (i=0; i<childFields.length; i++)
	{
		var table_name_long=childFields[i][0];
		var table_name_desc=childFields[i][1];
		new_text+='<CASE id=\"'+table_name_long+'\">'+table_name_desc+'</CASE>';
	}
	new_text+='</SELECT>';
	parent.get('field_TableName').setValue(new_text);
	parent.get('field_TableName').actionPerformed(null);
}
if (type=='10') {
	var new_text='<SELECT>".$field['params']['TableName'].$select_xml."</SELECT>';
	parent.get('field_TableName').setValue(new_text);
}
";
	$xfer_result->addComponent($edt);

	$lbl=new Xfer_Comp_LabelForm('field_notnulllbl');
	$lbl->setValue("{[bold]}Obligatoire{[bold]}");
	$lbl->setLocation(0,4);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Check('field_notnull');
	$edt->setValue($field['notnull']);
	$edt->setLocation(1,4,2);
	$xfer_result->addComponent($edt);

	if ($field['type']==0)
		$Prec=0;
	else if (isset($field['params']['Prec']))
		$Prec=$field['params']['Prec'];
	else
		$Prec=2;

	$lbl=new Xfer_Comp_LabelForm('Parametreslbl');
	$lbl->setValue("{[bold]}{[newline]}{[newline]}Paramètres{[/bold]}");
	$lbl->setLocation(0,5,1,8);
	$xfer_result->addComponent($lbl);

	$lbl=new Xfer_Comp_LabelForm('field_ValMinlbl');
	$lbl->setValue("Min");
	$lbl->setLocation(1,5);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Float('field_ValMin',-10000000,10000000,10);
	$edt->setValue($field['params']['Min']);
	$edt->setLocation(2,5);
	$xfer_result->addComponent($edt);

	$lbl=new Xfer_Comp_LabelForm('field_ValMaxlbl');
	$lbl->setValue("Max");
	$lbl->setLocation(1,6);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Float('field_ValMax',-10000000,10000000,10);
	$edt->setValue($field['params']['Max']);
	$edt->setLocation(2,6);
	$xfer_result->addComponent($edt);

	$lbl=new Xfer_Comp_LabelForm('field_ValPreclbl');
	$lbl->setValue("Précision");
	$lbl->setLocation(1,7);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Float('field_ValPrec',1,10,0);
	$edt->setValue($Prec);
	$edt->setLocation(2,7);
	$xfer_result->addComponent($edt);

	$lbl=new Xfer_Comp_LabelForm('field_ValSizelbl');
	$lbl->setValue("Taille");
	$lbl->setLocation(1,8);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Float('field_ValSize',1,1000,0);
	$edt->setValue($field['params']['Size']);
	$edt->setLocation(2,8);
	$xfer_result->addComponent($edt);

	$lbl=new Xfer_Comp_LabelForm('field_ValMultilbl');
	$lbl->setValue("Multiligne");
	$lbl->setLocation(1,9);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Check('field_ValMulti');
	$edt->setValue($field['params']['Multi']);
	$edt->setLocation(2,9);
	$xfer_result->addComponent($edt);

	$lbl=new Xfer_Comp_LabelForm('field_ValEnumlbl');
	$lbl->setValue("Enumeration");
	$lbl->setLocation(1,10);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Edit('field_ValEnum');
	$enum_val="";
	foreach($field['params']['Enum'] as $enum_item)
		$enum_val.=$enum_item.";";
	if ($enum_val!='') $enum_val=substr($enum_val,0,-1);
	$edt->setValue($enum_val);
	$edt->setLocation(2,10);
	$xfer_result->addComponent($edt);

	$lbl=new Xfer_Comp_LabelForm('field_TableNamelbl');
	$lbl->setValue("Table liée");
	$lbl->setLocation(1,11);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Select('field_TableName');
	$edt->setValue($field['params']['TableName']);
	$edt->setSelect($select_table);
	$edt->setLocation(2,11);
	$edt->JavaScript=$script_ref."
var type=parent.get('field_type').getValue();
if (type=='9') {
	var choix=current.getValue();
	var new_text='<SELECT>".$field['params']['RefField']."';
	for (i=0; i<childFields.length; i++)
	{
		var table_name_long=childFields[i][0];
		var field_name=childFields[i][2];
		if (table_name_long==choix)
			new_text+='<CASE id=\"'+field_name+'\">'+field_name+'</CASE>';
	}
	new_text+='</SELECT>';
	parent.get('field_RefField').setValue(new_text);
	java.lang.System.out.print(new_text);
}
";
	$xfer_result->addComponent($edt);

	$lbl=new Xfer_Comp_LabelForm('field_RefFieldlbl');
	$lbl->setValue("Champ enfant");
	$lbl->setLocation(1,12);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Select('field_RefField');
	$edt->setValue($field['params']['RefField']);
	$edt->setSelect(array());
	$edt->setLocation(2,12);
	$xfer_result->addComponent($edt);

	$functionList=array();
	require_once("Class/Stocked.inc.php");	
	$mng=new StockedManage();
	foreach($mng->GetList($extensionname,$classe) as $storage)
		$functionList[$extensionname."_FCT_".$storage]=$mng->GetName($storage);
	$lbl=new Xfer_Comp_LabelForm('field_Functionlbl');
	$lbl->setValue("Fonction");
	$lbl->setLocation(1,13);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Select('field_Function');
	$edt->setValue($field['params']['Function']);
	$edt->setSelect($functionList);
	$edt->setLocation(2,13);
	$xfer_result->addComponent($edt);


	$methodList=array(""=>"");
	require_once("Class/Method.inc.php");	
	$mng=new MethodManage();
	foreach($mng->GetList($extensionname,$classe) as $method)
		$methodList[$mng->GetNameNoTable($method)]=$mng->GetNameNoTable($method);

	$lbl=new Xfer_Comp_LabelForm('field_MethodGetlbl');
	$lbl->setValue("Methode (get)");
	$lbl->setLocation(1,14);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Select('field_MethodGet');
	$edt->setValue($field['params']['MethodGet']);
	$edt->setSelect($methodList);
	$edt->setLocation(2,14);
	$xfer_result->addComponent($edt);
	$lbl=new Xfer_Comp_LabelForm('field_MethodSetlbl');
	$lbl->setValue("Methode (set)");
	$lbl->setLocation(1,15);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Select('field_MethodSet');
	$edt->setValue($field['params']['MethodSet']);
	$edt->setSelect($methodList);
	$edt->setLocation(2,15);
	$xfer_result->addComponent($edt);


	if (!$cnx->IsReadOnly($extensionname))
		$xfer_result->addAction(new Xfer_Action("_OK","ok.png",$extensionname,"addFieldValid",FORMTYPE_MODAL,CLOSE_YES));
	$xfer_result->addAction(new Xfer_Action("_Fermer","close.png"));

	return $xfer_result;
}

?>
 
