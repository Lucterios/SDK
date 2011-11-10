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

function addExtValid($Params)
{
	$xfer_result=&new Xfer_Container_Acknowledge("CORE","addExtValid",$Params);
	require_once("Class/Extension.inc.php");
	$extName=$Params['newExt'];
	$extSetupFile = Extension::__ExtDir($extName)."setup.inc.php";
	if (!is_file($extSetupFile))
	{
		require_once("CORE/setup_param.inc.php");
		require_once("Class/Library.inc.php");
		require_once("Class/Event.inc.php");

		$ext=new Extension($Params['newExt']);
		$ext->Rights[0]=new Param_Rigth('Modification',80);
		$ext->Write();

		$lib=new Library('postInstall',$extName);	
		$lib->CodeFile=array("",'function install_'.$extName.'($ExensionVersions)',"{","// Fonction appelée en fin d'installation.","}","");
		$lib->Write();

		$lib=new Event('status@CORE',$extName);	
		$lib->Write();

		$lib=new Event('config@CORE',$extName);	
		$lib->Write();

		global $CNX_OBJ;

		Extension::ModifLock($Params['newExt'],$CNX_OBJ);

		$CNX_OBJ->Modified[]=$extName;
		$CNX_OBJ->Write();

		$cnx_adm=new Connect('admin');
		$cnx_adm->Modified[]=$extName;
		$cnx_adm->Write();
	}
	$xfer_result->redirectAction(new Xfer_Action('menu','','CORE','menu'));
	return $xfer_result;
}

?>
 
