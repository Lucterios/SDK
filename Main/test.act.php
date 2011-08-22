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

function test($Params)
{
	$xfer_result=&new Xfer_Container_Custom("CORE","test",$Params);
	$xfer_result->Caption='Panneaux des tests';
	$actionRefrech=new Xfer_Action("","","CORE","test",FORMTYPE_REFRESH,CLOSE_NO);

	$DBUnitTest=file("CNX/DBUnitTest.dt");
	$dbuser=trim($DBUnitTest[0]);
	$dbpass=trim($DBUnitTest[1]);
	$dbname=trim($DBUnitTest[2]);

	$posY=0;
	$lbl=new Xfer_Comp_LabelForm('sep1');
	$lbl->setValue("{[hr/]}");
	$lbl->setLocation(0,$posY++,2);
	$xfer_result->addComponent($lbl);

	$lbl=new Xfer_Comp_LabelForm('dbnamelbl');
	$lbl->setValue("{[bold]}Nom base de test{[/bold]}");
	$lbl->setLocation(0,$posY);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Edit('dbname');
	$edt->setValue(isset($Params['dbname'])?$Params['dbname']:$dbname);
	$edt->setLocation(1,$posY++);
	$edt->setAction($actionRefrech);
	$xfer_result->addComponent($edt);

	$lbl=new Xfer_Comp_LabelForm('dbuserlbl');
	$lbl->setValue("{[bold]}Login de test{[/bold]}");
	$lbl->setLocation(0,$posY);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Edit('dbuser');
	$edt->setValue(isset($Params['dbuser'])?$Params['dbuser']:$dbname);
	$edt->setLocation(1,$posY++);
	$edt->setAction($actionRefrech);
	$xfer_result->addComponent($edt);

	$lbl=new Xfer_Comp_LabelForm('dbpasslbl');
	$lbl->setValue("{[bold]}Mot de passe{[/bold]}");
	$lbl->setLocation(0,$posY);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Passwd('dbpass');
	$edt->setValue(isset($Params['dbpass'])?$Params['dbpass']:$dbname);
	$edt->setLocation(1,$posY++);
	$edt->setAction($actionRefrech);
	$xfer_result->addComponent($edt);

	$lbl=new Xfer_Comp_LabelForm('dumpdatelbl');
	$lbl->setValue("{[bold]}Date du dump{[/bold]}");
	$lbl->setLocation(0,$posY);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Label('dumpdate');
	$edt->setValue(is_file("../tmp/$dbname.sql")?date ("d/m/Y H:i:s",filemtime("../tmp/$dbname.sql")):'---');
	$edt->setLocation(1,$posY++);
	$xfer_result->addComponent($edt);

	$lbl=new Xfer_Comp_LabelForm('sep2');
	$lbl->setValue("{[hr/]}");
	$lbl->setLocation(0,$posY++,2);
	$xfer_result->addComponent($lbl);

	$extensionname="".$Params['extensionname'];
	$sel=array('CORE'=>'CORE');
	require_once("Class/Extension.inc.php");
	$ext_list=Extension::getList();
	foreach($ext_list as $ext => $ext_version) {
		$sel[$ext]=$ext;
		if ($extensionname=="")
			$extensionname=$ext;
	}
	$lbl=new Xfer_Comp_LabelForm('extensionnamelbl');
	$lbl->setValue("{[bold]}Extension{[/bold]}");
	$lbl->setLocation(0,$posY);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Select('extensionname');
	$edt->setValue($extensionname);
	$edt->setSelect($sel);
	$edt->setLocation(1,$posY++);
	$edt->setAction($actionRefrech);
	$xfer_result->addComponent($edt);

	$extDir=($extensionname=='CORE')?'../CORE/':"../extensions/$extensionname/";
	$dh = @opendir($extDir);
	while(($file = @readdir($dh)) != false)
		if(substr($file,-9)=='.test.php') {
			$file_name=substr($file,0,-9);
			if ($file_name!='setup')
				$fileList[]=$file_name;
		}
	@closedir($dh);
	sort($fileList);
	$sel=array(-1=>'<Tous>',0=>'<RAZ>');
	foreach($fileList as $id=>$name)
		$sel[$id+1]=sprintf('%02d ',$id+1).str_replace('_APAS_','::',$name);
	$lbl=new Xfer_Comp_LabelForm('testnumlbl');
	$lbl->setValue("{[bold]}Test{[/bold]}");
	$lbl->setLocation(0,$posY);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Select('testnum');
	$edt->setValue($Params['testnum']);
	$edt->setSelect($sel);
	$edt->setLocation(1,$posY++);
	$xfer_result->addComponent($edt);

	$lbl=new Xfer_Comp_LabelForm('deletelbl');
	$lbl->setValue("{[bold]}Re-initialiser?{[/bold]}");
	$lbl->setLocation(0,$posY);
	$xfer_result->addComponent($lbl);
	$chk=new Xfer_Comp_Check('delete');
	$chk->setValue(isset($Params['delete'])?$Params['delete']:'n');
	$chk->setValue('n');
	$chk->setLocation(1,$posY++);
	$xfer_result->addComponent($chk);

	$btn=new Xfer_Comp_Button('starttest');
	$btn->setAction(new Xfer_Action("_Lancer les tests","","CORE",'performTests',FORMTYPE_NOMODAL,CLOSE_NO));
	$btn->setLocation(0,$posY++,2);
	$xfer_result->addComponent($btn);

	$lbl=new Xfer_Comp_LabelForm('sep4');
	$lbl->setValue("{[hr/]}");
	$lbl->setLocation(0,$posY,2);
	$xfer_result->addComponent($lbl);

	return $xfer_result;
}

?>
 
