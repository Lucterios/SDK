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

function paramUserGit($Params)
{
	$xfer_result=&new Xfer_Container_Acknowledge("CORE","paramUserGit",$Params);
	$xfer_result->Caption="Modifier les config de GIT";

	$gitUser=$Params['gitUser'];
	$gitEmail=$Params['gitEmail'];

	$conf_file=file("CNX/Conf_Manage.dt");
	$conf_file[0]=$gitUser;
	$conf_file[1]=$gitEmail;

	if ($fh=fopen("CNX/Conf_Manage.dt","w+"))
	{
		for($i=0;$i<count($conf_file);$i++) {
			$conf_line=trim($conf_file[$i]);
			if (($i<2) || ($conf_line!=''))
				fwrite($fh,"$conf_line\n"); 
		}
		fclose($fh);
		chmod("CNX/Conf_Manage.dt", 0666);
	}

	require_once("Class/Extension.inc.php");
	$mods=Extension::GetList($cnx,false);
	foreach($mods as $mod_name=>$mod_ext)
	{
		$repo=$mod_ext->GetGitRepoObj();
		if ($repo!=null) {
		      $repo->run('config --local user.name "'.$gitUser.'"');
		      $repo->run('config --local user.email "'.$gitEmail.'"');
		}
	}

	return $xfer_result;
}

?>
 
