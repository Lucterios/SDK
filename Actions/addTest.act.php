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

function addTest($Params,$extensionname)
{
	$Params['type']='Test';
	require_once('Actions/addCode.act.php');
	$xfer_result=addCode($Params,$extensionname);
	$xfer_result->Caption='Ajouter un test';
	$xfer_result->m_action='addTest';
	$lbl=$xfer_result->getComponents('new_namelbl');
	$lbl->setValue("{[bold]}{[center]}Nom d'un test{[/center]}{[/bold]}");
	return $xfer_result;
}

?>
 
