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

function editAction($Params,$extensionname)
{
	require_once("Class/Extension.inc.php");
	$extension=new Extension($extensionname);
	$Params['type']='Action';
	if (array_key_exists('classe',$Params))
		$tablename=$Params['classe'];
	else	
		$tablename="";
	require_once("Actions/editCode.act.php");
	$xfer_result=editCode($Params,$extensionname);

	require_once("Class/Action.inc.php");
	$act = new Action($Params['action'],$extensionname,$tablename);
	$xfer_result->Caption="Edition de l'Action \"".$act->GetName()."\"";
	$xfer_result->m_action='editAction';
	$xfer_result->m_tab=0;

	require_once("../CORE/setup_param.inc.php");
	$action_obj=new Param_Action('','',0);
	foreach($extension->Actions as $id_act=>$current_act)
		if ($current_act->action==$Params['action'])
			$action_obj=$current_act;
	$lbl=new Xfer_Comp_LabelForm('title');
	$lbl->setValue("{[bold]}{[center]}Action '".$act->GetName()."' de l'extension '$extensionname'{[/center]}{[/bold]}");
	$lbl->setLocation(0,0,3);
	$xfer_result->addComponent($lbl);

	$xfer_result->m_tab=1;

	if ($act->TableName!="") {
		$lbl=$xfer_result->getComponents('code_paramslbl3');
		$lbl->setValue("\$self=new DBObj_".$act->ExtensionName."_".$act->TableName."(");
		$lbl->setLocation(0,11);

		$index_name=$act->IndexName;
		if ($index_name!="")
			$index_name='$'.$index_name;
		$edt=new Xfer_Comp_Edit('code_index');
		$edt->setValue($index_name);
		$edt->setLocation(1,11);
		$xfer_result->addComponent($edt);

		$lbl=new Xfer_Comp_LabelForm('code_paramslbl4');
		$lbl->setValue(")");
		$lbl->setLocation(2,11);
		$xfer_result->addComponent($lbl);
	}

	$xfer_result->m_tab=3;
	$lbl=new Xfer_Comp_LabelForm('action_droitlbl');
	$lbl->setValue("{[center]}{[bold]}Droit N°{[/bold]}{[/center]}");
	$lbl->setLocation(0,0);
	$xfer_result->addComponent($lbl);
	foreach($extension->Rights as $rigth_id=>$rigth_name)
		$select_right[$rigth_id]=$rigth_name->description;
	$edt=new Xfer_Comp_Select('action_droit');
	$edt->setValue($action_obj->rightNumber);
	$edt->setSelect($select_right);
	$edt->setLocation(1,0);
	$xfer_result->addComponent($edt);

if ($act->TableName!=""){
	$lbl=new Xfer_Comp_LabelForm('action_Locklbl');
	$lbl->setValue("{[bold]}Vérouillage{[/bold]}");
	$lbl->setLocation(0,1);
	$xfer_result->addComponent($lbl);
	$LockList=array(LOCK_MODE_NO=>"Aucun",LOCK_MODE_ACTION=>"Sur l'action",LOCK_MODE_EVENT=>"Sur l'observeur");
	$edt=new Xfer_Comp_Select('action_Lock');
	$edt->setValue($act->LockMode);
	$edt->setSelect($LockList);
	$edt->setLocation(1,1);
	$xfer_result->addComponent($edt);
}

	$lbl=new Xfer_Comp_LabelForm('action_XferCntlbl');
	$lbl->setValue("{[bold]}Type d'observeur{[/bold]}");
	$lbl->setLocation(0,2);
	$xfer_result->addComponent($lbl);
	global $xfer_dico;
	foreach($xfer_dico as $key => $val)
		$select_xfer[$key] = $val[1];
	$edt=new Xfer_Comp_Select('action_XferCnt');
	$edt->setValue($act->XferCnt);
	$edt->setSelect($select_xfer);
	$edt->setLocation(1,2);
	$xfer_result->addComponent($edt);

	$lbl=new Xfer_Comp_LabelForm('action_Transactionlbl');
	$lbl->setValue("{[bold]}Transaction{[/bold]}");
	$lbl->setLocation(0,3);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Check('action_Transaction');
	$edt->setValue($act->WithTransaction);
	$edt->setLocation(1,3);
	$xfer_result->addComponent($edt);

	global $CNX_OBJ;
	$cnx=$CNX_OBJ;
	if (!$cnx->IsReadOnly($extensionname))
		$xfer_result->m_actions[0]->m_action='modifAction';

	return $xfer_result;
}

?>
 
