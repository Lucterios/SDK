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
//  // setup file write by SDK tool
// --- Last modification: Date 01 March 2008 12:28:58 By  ---

$extention_name="SDK";
$extention_description="Software Developpement Kit pour Lucterios.{[newline]}Permet de créer et modifier de nouveaux modules.";
$extention_appli="";
$extention_famille="client";
$extention_titre="Lucterios SDK";
$extension_libre=true;

$version_max=0;
$version_min=91;
$version_release=1;
$version_build=3129;

$depencies=array();
$depencies[0] = new Param_Depencies("CORE", 0, 91, 0, 91, false);

$rights=array();

$menus=array();

$actions=array();

$params=array();
?>