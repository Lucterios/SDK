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

function paramDelRepo($Params)
{
	$xfer_result=new Xfer_Container_Acknowledge("CORE","paramDelRepo",$Params);
	$xfer_result->Caption="Supprimer un repository";
	$depRepo=$Params['depRepository'];
	$conf_file=file("CNX/Conf_Manage.dt");
	$depRepo_name=$conf_file[$depRepo];

	if ($xfer_result->confirme("Voulez-vous supprimer le repository '$depRepo_name' ?")) {
		$new_conf_file=array();
		for($i=0;$i<count($conf_file);$i++) 
			if ($i!=$depRepo)
				$new_conf_file[]=$conf_file[$i];
		if (count($conf_file)==0)
			unlink("CNX/Conf_Manage.dt");
		else {
			if ($fh=fopen("CNX/Conf_Manage.dt","w+"))
			{
				for($i=0;$i<count($new_conf_file);$i++) {
					$conf_line=trim($new_conf_file[$i]);
					if (($i<2) || ($conf_line!=''))
						fwrite($fh,"$conf_line\n"); 
				}
				fclose($fh);
				chmod("CNX/Conf_Manage.dt", 0666);
			}
		}
	}
	return $xfer_result;
}

?>
 
