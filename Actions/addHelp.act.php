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

function addHelp($Params,$extensionname)
{
	require_once('Class/Extension.inc.php');
	global $CNX_OBJ;
	$cnx=$CNX_OBJ;

	$xfer_result=new Xfer_Container_Custom($extensionname,"addHelp",$Params);
	$xfer_result->Caption='Editer une Aide';
	require_once('Class/Help.inc.php');
	if (array_key_exists('help',$Params)) 
		$help_name=$Params['help'];
	 else 
		$help_name='';
	$help=new Help($help_name,$extensionname);

	$lbl=new Xfer_Comp_LabelForm('help_namelbl');
	$lbl->setValue("{[bold]}{[center]}Nom{[/center]}{[/bold]}");
	$lbl->setLocation(0,1);
	$xfer_result->addComponent($lbl);
	if ($help_name=='') {
		$edt=new Xfer_Comp_Edit('help');
		$edt->setValue('');
		$edt->ExprReg="[a-zA-Z][a-zA-Z0-9]*";
		$edt->StringSize=100;
		$edt->needed=true;
		$edt->setLocation(1,1);
		$xfer_result->addComponent($edt);
	} else {
		$edt=new Xfer_Comp_Label('help');
		$edt->setValue($help->Name);
		$edt->setLocation(1,1);
		$xfer_result->addComponent($edt);
	}

	$desc=$help->getDescription();
	$lbl=new Xfer_Comp_LabelForm('help_desclbl');
	$lbl->setValue("{[bold]}{[center]}Description{[/center]}{[/bold]}");
	$lbl->setLocation(0,2);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Edit('help_desc');
	$edt->setValue($desc[1][1]);
	$edt->setLocation(1,2);
	$xfer_result->addComponent($edt);

	$select_menu=array('-1'=>'[Hors menu]','0'=>'[Premier]');
	foreach($help->Mng->HelpDescriptions as $id=>$val)
		if ($val[2]==1)
			$select_menu[$id+1]=$val[0];
	$lbl=new Xfer_Comp_LabelForm('Help_menulbl');
	$lbl->setValue("{[bold]}{[center]}Menu (Après){[/center]}{[/bold]}");
	$lbl->setLocation(0,3);
	$xfer_result->addComponent($lbl);
	$edt=new Xfer_Comp_Select('Help_menu');
	$edt->setValue($desc[0]);
	$edt->setSelect($select_menu);
	$edt->setLocation(1,3);
	$xfer_result->addComponent($edt);

	$CodeLineBegin=$help->CodeLineBegin;
        $script='';
        foreach($help->CodeFile as $help_txt) 
        	$script.=$help_txt."\n";
	$edt=new Xfer_Comp_Memo('help_codefile');
	$edt->setValue(urlencode($script));
	$edt->Encode=true;
	$edt->FirstLine=$CodeLineBegin;
	$edt->setLocation(0,4,2);
	$xfer_result->addComponent($edt);

	if (!$cnx->IsReadOnly($extensionname))
		$xfer_result->addAction(new Xfer_Action("_Sauver","ok.png",$extensionname,"addHelpValid",FORMTYPE_MODAL,CLOSE_NO));
	$xfer_result->addAction(new Xfer_Action("_Fermer","close.png"));
	return $xfer_result;
}

?>
 
 
