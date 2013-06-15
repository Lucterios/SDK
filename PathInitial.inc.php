<?php
//
//    This file is part of SDK Lucterios.
//
//    SDK Lucterios is free software; you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation; either version 2 of the License, or
//    (at your option) any later version.
//
//    SDK Lucterios is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with Lucterios; if not, write to the Free Software
//    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
//
//	Contributeurs: Fanny ALLEAUME, Pierre-Olivier VERSCHOORE, Laurent GAY
//

$sep=$_SERVER['DOCUMENT_ROOT'][0]=="/"?"/:":"\\;";

$path = ini_get('include_path');
if ($path)
    ini_set('include_path',"..$sep.$sep$path");
else
    ini_set('include_path',"..$sep.$sep");

// recup des variables en get et post
$VAR_SEND = array_merge($_POST, $_GET);
$GLOBAL = array_merge($VAR_SEND, $_COOKIE, $_ENV, $_SERVER);

foreach($VAR_SEND as $key => $val)
	$$key=$val;

if (!is_dir('./tmp'))
	@mkdir('./tmp');
	
?>
