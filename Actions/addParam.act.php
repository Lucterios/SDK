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

function addParam($Params,$extensionname)
{
	$xfer_result=&new Xfer_Container_Custom($extensionname,"addParam",$Params);
	$xfer_result->Caption='Ajouter un paramètre';

	require_once("PathInitial.inc.php");
	require_once("FunctionTool.inc.php");
	$param_dico=array();
	$param_dico[0]="Text";
	$param_dico[1]="Entier";
	$param_dico[2]="Réel";
	$param_dico[3]="Booléan";
	$param_dico[4]="Enumération";

	global $CNX_OBJ;
	$cnx=$CNX_OBJ;
	require_once("../CORE/setup_param.inc.php");
	require_once("Class/Extension.inc.php");
	$extension=new Extension($extensionname);
	if (array_key_exists('param',$Params)) {
		$param=$extension->Params[$Params['param']];
	} else 
		$param=new Param_Parameters('','');
	
	$lbl=new Xfer_Comp_LabelForm('namelbl');
	$lbl->setValue("{[bold]}Nom{[/bold]}");
	$lbl->setLocation(0,0);
	$xfer_result->addComponent($lbl);
	$ext_list=$extension->getList($cnx);
if ($param->name=='') {
	$edt=new Xfer_Comp_Edit('name');
	$edt->setLocation(1,0,2);
	$edt->setNeeded(true);
	$xfer_result->addComponent($edt);
}else{
	$xfer_result->m_context['name']=$param->name;
	$lbl=new Xfer_Comp_Label('name');
	$lbl->setValue($param->name);
	$lbl->setLocation(1,0,2);
	$xfer_result->addComponent($lbl);
}

	$lbl=new Xfer_Comp_LabelForm('Valeurlbl');
	$lbl->setValue("{[bold]}Valeur{[/bold]}");
	$lbl->setLocation(0,1);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Edit('valeur');
	$edt->setValue($param->defaultvalue);
	$edt->setLocation(1,1,2);
	$xfer_result->addComponent($edt);

	$lbl=new Xfer_Comp_LabelForm('descriptionlbl');
	$lbl->setValue("{[bold]}Description{[/bold]}");
	$lbl->setLocation(0,2);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Edit('description');
	$edt->setValue($param->description);
	$edt->setNeeded(true);
	$edt->setLocation(1,2,2);
	$xfer_result->addComponent($edt);

	$lbl=new Xfer_Comp_LabelForm('typelbl');
	$lbl->setValue("{[bold]}Type{[/bold]}");
	$lbl->setLocation(0,3);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Select('type');
	$edt->setValue($param->type);
	foreach($param_dico as $key=>$value)
		$select["".$key.""]="$value";
	$edt->setSelect($select);
	$edt->setLocation(1,3,2);
	$edt->JavaScript="
var value=current.getRequete('').get('type').toString();
parent.get('ValMin').setEnabled((value=='1') || (value=='2'));
parent.get('ValMax').setEnabled((value=='1') || (value=='2'));
parent.get('ValPrec').setEnabled(value=='2');
parent.get('ValMulti').setEnabled(value=='0');
parent.get('ValEnum').setEnabled(value=='4');
";
	$xfer_result->addComponent($edt);

	$lbl=new Xfer_Comp_LabelForm('Parametreslbl');
	$lbl->setValue("{[bold]}{[newline]}{[newline]}Paramètres{[/bold]}");
	$lbl->setLocation(0,4,1,5);
	$xfer_result->addComponent($lbl);

	$lbl=new Xfer_Comp_LabelForm('ValMinlbl');
	$lbl->setValue("Min");
	$lbl->setLocation(1,4);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Float('ValMin',1,1000,2);
	$edt->setValue($param->extend['Min']);
	$edt->setLocation(2,4);
	$xfer_result->addComponent($edt);

	$lbl=new Xfer_Comp_LabelForm('ValMaxlbl');
	$lbl->setValue("Max");
	$lbl->setLocation(1,5);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Float('ValMax',1,1000,2);
	$edt->setValue($param->extend['Max']);
	$edt->setLocation(2,5);
	$xfer_result->addComponent($edt);

	$lbl=new Xfer_Comp_LabelForm('ValPreclbl');
	$lbl->setValue("Précision");
	$lbl->setLocation(1,6);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Float('ValPrec',1,10,0);
	$edt->setValue($param->extend['Prec']);
	$edt->setLocation(2,6);
	$xfer_result->addComponent($edt);

	$lbl=new Xfer_Comp_LabelForm('ValMultilbl');
	$lbl->setValue("Multiligne");
	$lbl->setLocation(1,7);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Check('ValMulti');
	$edt->setValue($param->extend['Multi']);
	$edt->setLocation(2,7);
	$xfer_result->addComponent($edt);

	$lbl=new Xfer_Comp_LabelForm('ValEnumlbl');
	$lbl->setValue("Enumeration");
	$lbl->setLocation(1,8);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Edit('ValEnum');
	$enum_val="";
	foreach($param->extend['Enum'] as $enum_item)
		$enum_val.=$enum_item.";";
	if ($enum_val!='') $enum_val=substr($enum_val,0,-1);
	$edt->setValue($enum_val);
	$edt->setLocation(2,8);
	$xfer_result->addComponent($edt);
	
	$xfer_result->addAction(new Xfer_Action("_OK","ok.png",$extensionname,"addParamValid",FORMTYPE_MODAL,CLOSE_YES));
	$xfer_result->addAction(new Xfer_Action("_Fermer","close.png"));
	return $xfer_result;
}

?>
 
