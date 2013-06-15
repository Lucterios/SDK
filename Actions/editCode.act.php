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

function addContextMenu($editor,$ExtensionName,$TableName,$is_print_xfer)
{
	require_once("PathInitial.inc.php");
	require_once("Class/Table.inc.php");
	require_once("Class/Action.inc.php");
	require_once("Class/Method.inc.php");
	require_once("Class/Printing.inc.php");
	$tbl=new Table($TableName,$ExtensionName);
	if (is_file($tbl->FileName()))
	{
		$editor->addSubMenu("$ExtensionName.$TableName",'Constructeur',"=new DBObj_".$ExtensionName."_".$TableName.";");
		$editor->addSubMenu("$ExtensionName.$TableName",'-','');
		require_once("CORE/DBObject.inc.php");
		global $field_dico;
		foreach($tbl->getCompletFields() as $fld_name=>$fld_obj) {
			$tp=$fld_obj['type'];
			$text="[".$field_dico[$tp][1]."] ".$fld_name." : ".$fld_obj['description'];
			$editor->addSubMenu("$ExtensionName.$TableName",$text,'->'.$fld_name);
		}

		$editor->addSubMenu("$ExtensionName.$TableName",'-','');
		$Mng=new MethodManage();
		$meth_list=$Mng->GetList($ExtensionName,$TableName);
		foreach($meth_list as $meth_name)
		{
			$meth=new Method($meth_name,$ExtensionName);
			$text=$Mng->GetNameNoTable($meth_name).$meth->GetParams();
			$editor->addSubMenu("$ExtensionName.$TableName",$text.':'.$meth->Description,'->'.$text.';');
		}

		$editor->addSubMenu("$ExtensionName.$TableName",'-','');
		$Mng=new ActionManage();
		$act_list=$Mng->GetList($ExtensionName,$TableName);
		foreach($act_list as $act_name)
		{
			$act=new Action($act_name,$ExtensionName);
			$text=$Mng->GetNameNoTable($act_name).$act->GetParams();
			$editor->addSubMenu("$ExtensionName.$TableName",$text.':'.$act->Description,"->NewAction('titre','icon.png','".$Mng->GetNameNoTable($act_name)."',FORMTYPE_MODAL,CLOSE_NO,SELECT_NONE);");
		}

		if ($is_print_xfer) {
			$editor->addSubMenu("$ExtensionName.$TableName",'-','');
			$Mng=new PrintingManage();
			$prt_list=$Mng->GetList($ExtensionName,$TableName);
			foreach($prt_list as $prt_name)
			{
				$prt=new Printing($prt_name,$ExtensionName);
				$text=$Mng->GetNameNoTable($prt_name).$act->GetParams();
				$editor->addSubMenu("$ExtensionName.$TableName",$text.':'.$prt->Description,"->PrintReport(\$xfer_result,'".$Mng->GetNameNoTable($prt_name)."','titre');");
			}
		}
	}
}

function editCode($Params,$extensionname,$phpEditor=true)
{
	$xfer_result=&new Xfer_Container_Custom($extensionname,"editCode",$Params);

	global $CNX_OBJ;
	$cnx=$CNX_OBJ;
	$type=$Params['type'];
	if (array_key_exists('classe',$Params))
		$tablename=$Params['classe'];
	else	
		$tablename="";
	$id=$Params[strtolower($type)];

	require_once("Class/Extension.inc.php");
	require_once("Class/$type.inc.php");
	require_once("Class/Table.inc.php");
	$extension=new Extension($extensionname);
	$code = new $type($id,$extension->Name,$tablename);
	$mng=new TableManage();
	$tbl_names=$mng->GetDependList($extension->Depencies,$extension->Name);
	$SelectedTableFiles=$code->TableFiles;
	$DependedTable=array();
	foreach($tbl_names as $tbl_ext_name => $table_Names)
		foreach($table_Names as $tableName)
			if (($tbl_ext_name!=$extension->Name) || ($tableName!=$tablename)) 
				$DependedTable["$tbl_ext_name/$tableName"]="$tbl_ext_name.$tableName";

	//ValueCode
	$lbl=new Xfer_Comp_LabelForm('code_namelbl');
	$lbl->setValue("{[bold]}{[center]}Nom{[/center]}{[/bold]}");
	$lbl->setLocation(0,5);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Edit('code_name');
	$edt->setValue($code->Mng->GetNameNoTable($code->Name));
	$edt->setLocation(1,5);
	$xfer_result->addComponent($edt);

	$lbl=new Xfer_Comp_LabelForm('code_desclbl');
	$lbl->setValue("{[bold]}{[center]}Description{[/center]}{[/bold]}");
	$lbl->setLocation(0,6);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Edit('code_desc');
	$edt->setValue($code->Description);
	$edt->setLocation(1,6);
	$xfer_result->addComponent($edt);

	$xfer_result->newTab("Editeur",1);
	//HeaderCode
	$lbl=new Xfer_Comp_Label('code_paramslbl');
	$lbl->setValue("function ".$code->GetName().'(');
	$lbl->setLocation(0,10);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Edit('code_params');
	$edt->setValue($code->GetParams(false));
	$edt->setLocation(1,10);
	$xfer_result->addComponent($edt);
	$lbl=new Xfer_Comp_Label('code_paramslbl2');
	$lbl->setValue(")");
	$lbl->setLocation(2,10);
	$xfer_result->addComponent($lbl);
if ($phpEditor && ($code->TableName!="")) {
	$lbl=new Xfer_Comp_Label('code_paramslbl3');
	$lbl->setValue("\$self=new DBObj_".$code->ExtensionName."_".$code->TableName);
	$lbl->setLocation(0,11,3);
	$xfer_result->addComponent($lbl);
}

	//ModifCode
	$is_print_xfer=(($type=='Action') && ($code->XferCnt=='print'));
	$CodeLineBegin=$code->CodeLineBegin;
        $script='';
        foreach($code->CodeFunction as $code_txt) 
        	$script.=$code_txt."\n";
	$edt=new Xfer_Comp_Memo('script_code');
	$edt->setValue(urlencode($script));
	$edt->Encode=true;
	$edt->FirstLine=$CodeLineBegin;
	$edt->setLocation(0,15,4);
	if ($phpEditor) {
		foreach($SelectedTableFiles as $SelectedTableFile) {
			list($ext_name,$tbl_name)=explode('/',$SelectedTableFile);
			addContextMenu($edt,$ext_name,$tbl_name,$is_print_xfer);
		} 
		$extList=Extension::GetList();
		$extList['CORE']='';
		foreach($extList as $extname=>$ver){
			$nb=0;
			$extension=new Extension(($extname!="CORE")?$extname:'');
			foreach($extension->Signals as $signal) {
			    $param_txt=str_replace('&','',$signal[1]);
			    $edt->addSubMenu("Signaux",$signal[0]."($param_txt):".$signal[2],'$signalRet=$xfer_result->signal("'.$signal[0].'",'.$param_txt.');');
			    $nb++;
			}
			if ($nb>0)
				$edt->addSubMenu("Signaux",'-','');
		}
	}
	$xfer_result->addComponent($edt);

	if ($phpEditor) {
		require_once("Actions/phpTools.inc.php");
		$res=CheckSyntax($code->Mng->GetFileName($code->Name,$code->ExtensionName,$code->TableName));
		if (is_string($res)) {
			$lbl=new Xfer_Comp_LabelForm('code_error');
			$lbl->setValue("{[bold]}{[font color='red']}$res{[/font]}{[/bold]}");
			$lbl->setLocation(0,20,4);
			$xfer_result->addComponent($lbl);
		}

		$xfer_result->newTab("Modèle",2);
	
		$xfer_result->newTab("Paramètres",3);
		$lbl=new Xfer_Comp_LabelForm('tablesDependslbl');
		$lbl->setValue("{[bold]}{[center]}Tables dépendantes{[/center]}{[/bold]}");
		$lbl->setLocation(0,20);
		$xfer_result->addComponent($lbl);
		$edt=new Xfer_Comp_CheckList('code_tableFiles');
		$edt->setSelect($DependedTable);
		$edt->setValue($SelectedTableFiles);
		$edt->setLocation(1,20,2);
		$edt->setsize(200,200);
		$xfer_result->addComponent($edt);

		$btn=new Xfer_Comp_Button('code_adddep');
		$btn->setAction(new Xfer_Action("_Ajouter dépendance","",$extensionname,"addDepend",FORMTYPE_MODAL,CLOSE_NO,SELECT_NONE));
		$btn->setLocation(2,21);
		$xfer_result->addComponent($btn);

	}
	if (!$cnx->IsReadOnly($extensionname))
		$xfer_result->addAction(new Xfer_Action("_Sauver","ok.png",$extensionname,"modifCode",FORMTYPE_MODAL,CLOSE_NO));
	$xfer_result->addAction(new Xfer_Action("_Fermer","close.png"));
	return $xfer_result;
}

?>
 
