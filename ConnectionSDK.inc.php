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


$SDK_NOVIEW=array();
$SDK_READONLY=array();
$SDKUSER="";
$SDKLOGIN="";

$CNX_OBJ=null;

require_once("Class/Connect.inc.php");

if (!isset($__DEBUG_TEST))
{
	$title="no password";
	if (isset($_COOKIE['APAS_SDKUSER']))
	{
		$CNX_OBJ= new Connect($_COOKIE['APAS_SDKUSER']);
		if ($CNX_OBJ->Pwcrypt=="")
		{
			$CNX_OBJ=null;
			$title="bad session '".$_COOKIE['APAS_SDKUSER']."'";
		}
	}
	if (($CNX_OBJ==null) && isset($APAS_AUTH_USER) && isset($APAS_AUTH_PW))
	{
		$CNX_OBJ= new Connect($APAS_AUTH_USER);
		if (!$CNX_OBJ->IsValid($APAS_AUTH_PW))
		{
			$CNX_OBJ=null;
			$title="bad password";
		}
	}

	if ($CNX_OBJ!=null)
	{
		$SDKUSER=$CNX_OBJ->LongName;
		$SDKLOGIN=$CNX_OBJ->Name;
		$SDK_NOVIEW=$CNX_OBJ->NoView;
		$SDK_READONLY=$CNX_OBJ->ReadOnly;
		setcookie('APAS_SDKUSER',$SDKLOGIN,0);
	}
	else
		Connect::CallHeader($title);
}

?>