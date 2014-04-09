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

function validUser($Params)
{
	$xfer_result=new Xfer_Container_Acknowledge("CORE","validUser",$Params);
	$xfer_result->Caption='Validation';

	$pass1=$Params['pass1'];
	$pass2=$Params['pass2'];
	$username=$Params['alias'];
	$LongName=$Params['LongName'];
	$NoView=$Params['NoView'];
	$Modified=$Params['Modified'];
	if ($pass1==$pass2)
	{
		$cnx=new Connect($username);
		$cnx->LongName=$LongName;
		$cnx->NoView=explode(';',$NoView);
		$cnx->Modified=explode(';',$Modified);
		if ($cnx->Pwcrypt!="")
		{
			if ($pass1!='')
				$cnx->ChangePWD($pass1);
			$cnx->Write();
		}
		else {
			$cnx->ChangePWD($pass1);
			if ($pass1!='')
				$cnx->Write();
			else
				$xfer_result->message("Mots de passe vide!",3);
		}
	}
	else
		$xfer_result->message("Mots de passe différents!",3);
	return $xfer_result;
}

?>
 
