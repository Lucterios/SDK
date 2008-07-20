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

function addInitial($Params,$extensionname)
{
	$classe=$Params['classe'];
	require_once("Class/Extension.inc.php");
	require_once("Class/Table.inc.php");
	$extension=new Extension($extensionname);
	$table=new Table($classe,$extensionname);
	if (array_key_exists('initial',$Params)) {
		$initial_id=$Params['initial'];
		if ($initial['@refresh@']) $initial['@refresh@']="Oui"; else $initial['@refresh@']="Non";
		$initial=$table->DefaultFields[$initial_id];
	} else {
		$initial_id=-1;
		$initial=array('@refresh@'=>"Oui",'id'=>'');
		foreach($table->Fields as $key=>$field)
			$initial[$key]='';
	}

	$xfer_result=&new Xfer_Container_Custom($extensionname,"addInitial",$Params);
	$xfer_result->Caption="Edition d'une valeur initial";

	$lbl=new Xfer_Comp_LabelForm('refreshïdatalbl');
	$lbl->setValue("Rafraichir");
	$lbl->setLocation(0,0);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Check('refreshïdata');
	$edt->setValue($initial['@refresh@']);
	$edt->setLocation(1,0);
	$xfer_result->addComponent($edt);

	$lbl=new Xfer_Comp_LabelForm('idlbl');
	$lbl->setValue("ID");
	$lbl->setLocation(0,1);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Float('id',0,10000,0);
	$edt->setValue($initial['id']);
	$edt->setLocation(1,1);
	$xfer_result->addComponent($edt);

	$pos_y=2;
	foreach($table->Fields as $key=>$field)
	{
		$type_id=(int)$field['type'];
		if ($type_id!=9) {
			$params=$field['params'];
			$description=$field['description'];
			$notnull=$field['notnull'];

			$lbl=new Xfer_Comp_LabelForm($key.'lbl');
			$lbl->setValue($description);
			$lbl->setLocation(0,$pos_y);
			$xfer_result->addComponent($lbl);
	
			switch ($type_id)
			{
				case 0: 
					$edt=new Xfer_Comp_Float($key,$params['Min'],$params['Max'],0);
					$edt->setValue($initial[$key]);
					$edt->setLocation(1,$pos_y);
					$xfer_result->addComponent($edt);
					break;
				case 1: 
					$edt=new Xfer_Comp_Float($key,$params['Min'],$params['Max'],$params['Prec']);
					$edt->setValue($initial[$key]);
					$edt->setLocation(1,$pos_y);
					$xfer_result->addComponent($edt);
					break;
				case 2:
					if ($params['Multi']=='o')
						$edt=new Xfer_Comp_Memo($key);
					else  
						$edt=new Xfer_Comp_Edit($key);
					$edt->StringSize=(int)$params['field_ValSize'];
					$edt->setValue($initial[$key]);
					$edt->setLocation(1,$pos_y);
					$xfer_result->addComponent($edt);
					break;
				case 3:
					$edt=new Xfer_Comp_Check($key);
					if ($initial[$key]=='o')
						$edt->setValue(1);
					else
						$edt->setValue(0);
					$edt->setLocation(1,$pos_y);
					$xfer_result->addComponent($edt);
					break;
				case 4:
					$edt=new Xfer_Comp_Date($key);
					$edt->setValue($initial[$key]);
					$edt->setLocation(1,$pos_y);
					$xfer_result->addComponent($edt);
					break;
				case 5:
					$edt=new Xfer_Comp_Time($key);
					$val=$initial[$key];
					$edt->setValue($val.':00');
					$edt->setLocation(1,$pos_y);
					$xfer_result->addComponent($edt);
					break;
				case 7:
					$edt=new Xfer_Comp_Memo($key);
					$edt->setValue($initial[$key]);
					$edt->setLocation(1,$pos_y);
					$xfer_result->addComponent($edt);
					break;
				case 8:
					$edt=new Xfer_Comp_Select($key);
					$edt->setSelect($params['Enum']);
					$edt->setValue($initial[$key]);
					$edt->setLocation(1,$pos_y);
					$xfer_result->addComponent($edt);
					break;
				case 10: 
					$edt=new Xfer_Comp_Float($key,0,1000,0);
					$edt->setValue($initial[$key]);
					$edt->setLocation(1,$pos_y);
					$xfer_result->addComponent($edt);
					break;
				default:
					$edt=new Xfer_Comp_Edit($key);
					$edt->setValue($initial[$key]);
					$edt->setLocation(1,$pos_y);
					$xfer_result->addComponent($edt);
					break;
			}
			$pos_y++;
		}
	}
	$xfer_result->addAction(new Xfer_Action("_OK","ok.png",$extensionname,"addInitialValid",FORMTYPE_MODAL,CLOSE_YES));
	$xfer_result->addAction(new Xfer_Action("_Fermer","close.png"));

	return $xfer_result;
}

?>
