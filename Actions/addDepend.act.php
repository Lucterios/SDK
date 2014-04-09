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

function addDepend($Params,$extensionname)
{
	$xfer_result=new Xfer_Container_Custom($extensionname,"addDepend",$Params);
	$xfer_result->Caption='Ajouter une dépendance';

	global $CNX_OBJ;
	$cnx=$CNX_OBJ;
	require_once("../CORE/setup_param.inc.php");
	require_once("Class/Extension.inc.php");
	$extension=new Extension($extensionname);
	if (array_key_exists('depend',$Params)) {
		$depency=$extension->Depencies[$Params['depend']];
	} else 
		$depency=new Param_Depencies('',0,1);
	
	$lbl=new Xfer_Comp_LabelForm('namelbl');
	$lbl->setValue("{[bold]}Nom{[/bold]}");
	$lbl->setLocation(0,0);
	$xfer_result->addComponent($lbl);
	$ext_list=$extension->getList($cnx);
if ($depency->name=='') {
	$edt=new Xfer_Comp_Select('name');
	$edt->JavaScript="dependancy_version=new Array();\n";
	foreach($ext_list as $name => $ext_version)
		if (($name!=$extension->Name) && ($name!="applis")) {
			$selected=true;
			foreach($extension->Depencies as $depend)
				if ($depend->name==$name) $selected=false;
			if ($selected) {
				$select[$name]="$name ($ext_version)";
				$edt->JavaScript.="dependancy_version['$name']='$ext_version';\n";
			}
		}
	$edt->setSelect($select);
	$edt->setLocation(1,0);
	$edt->JavaScript.="var val=current.getValue();
var ext_vers=dependancy_version[val].split('.');
parent.get('version_majeur_max').setValue('<val prec=\"0\">'+ext_vers[0]+'</val>');
parent.get('version_mineur_max').setValue('<val prec=\"0\">'+ext_vers[1]+'</val>');
parent.get('version_majeur_min').setValue('<val prec=\"0\">'+ext_vers[0]+'</val>');
parent.get('version_mineur_min').setValue('<val prec=\"0\">'+ext_vers[1]+'</val>');
";
	$xfer_result->addComponent($edt);
}else{
	$xfer_result->m_context['name']=$depency->name;
	$lbl=new Xfer_Comp_Label('name');
	$lbl->setValue($depency->name." (".$ext_list[$depency->name].")");
	$lbl->setLocation(1,0);
	$xfer_result->addComponent($lbl);
}

	$lbl=new Xfer_Comp_LabelForm('version_majeur_maxlbl');
	$lbl->setValue("{[bold]}Version Majeur max{[/bold]}");
	$lbl->setLocation(0,1);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Float('version_majeur_max',0,1000,0);
	$edt->setValue($depency->version_majeur_max);
	$edt->setLocation(1,1);
	$xfer_result->addComponent($edt);

	$lbl=new Xfer_Comp_LabelForm('version_mineur_maxlbl');
	$lbl->setValue("{[bold]}Version Mineur max{[/bold]}");
	$lbl->setLocation(0,2);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Float('version_mineur_max',0,1000,0);
	$edt->setValue($depency->version_mineur_max);
	$edt->setLocation(1,2);
	$xfer_result->addComponent($edt);

	$lbl=new Xfer_Comp_LabelForm('version_majeur_minlbl');
	$lbl->setValue("{[bold]}Version Majeur min{[/bold]}");
	$lbl->setLocation(0,3);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Float('version_majeur_min',0,1000,0);
	$edt->setValue($depency->version_majeur_min);
	$edt->setLocation(1,3);
	$xfer_result->addComponent($edt);

	$lbl=new Xfer_Comp_LabelForm('version_mineur_minlbl');
	$lbl->setValue("{[bold]}Version Mineur min{[/bold]}");
	$lbl->setLocation(0,4);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Float('version_mineur_min',0,1000,0);
	$edt->setValue($depency->version_mineur_min);
	$edt->setLocation(1,4);
	$xfer_result->addComponent($edt);

	$lbl=new Xfer_Comp_LabelForm('optionnallbl');
	$lbl->setValue("{[bold]}Optionnel{[/bold]}");
	$lbl->setLocation(0,5);
	$xfer_result->addComponent($lbl);
	$lbl=new Xfer_Comp_Check('optionnal');
	$lbl->setValue($depency->optionnal);
	$lbl->setLocation(1,5);
	$xfer_result->addComponent($lbl);
	
	$xfer_result->addAction(new Xfer_Action("_OK","ok.png",$extensionname,"addDependValid",FORMTYPE_MODAL,CLOSE_YES));
	$xfer_result->addAction(new Xfer_Action("_Fermer","close.png"));
	return $xfer_result;
}

?>
 
