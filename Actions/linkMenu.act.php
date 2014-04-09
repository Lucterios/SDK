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

function linkMenu($Params,$extensionname)
{
	$xfer_result=new Xfer_Container_Acknowledge($extensionname,"linkMenu",$Params);
	$xfer_result->Caption='Lier à un Menu';

	$action_name=$Params['action'];
	
	require_once("../CORE/setup_param.inc.php");
	require_once("Class/Extension.inc.php");
	$extension=new Extension($extensionname);
	unset($xfer_result->m_context['menu']);
	foreach($extension->Menus as $MenuIndex=>$MenuItem) {
		if ($MenuItem->act==$action_name) {
			$xfer_result->m_context['menu']=$MenuIndex;
		}
	}
	if (!isset($xfer_result->m_context['menu'])) {
		require_once("Class/Action.inc.php");
		$cd=new Action($action_name,$extensionname);
		echo "<!-- name:$action_name - action:".print_r($cd,true)." -->\n";
		$xfer_result->m_context['actiontitle']=$cd->Description;
	}
	$xfer_result->redirectAction(new Xfer_Action('','',$extensionname,'addMenu',FORMTYPE_MODAL,CLOSE_NO));
	return $xfer_result;
}

?>
 
