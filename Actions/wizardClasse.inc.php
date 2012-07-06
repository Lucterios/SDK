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

require_once("Class/Table.inc.php");
require_once("Class/Action.inc.php");
require_once("Class/Method.inc.php");
require_once("Class/Extension.inc.php");

function checkParam($Params,$name)
{
	if (array_key_exists($name,$Params)) {
		if ($Params[$name]=='o')
			return true;
		else
			return false;
	}
	else
 		return false;
}

class generator 
{
	public $extensionName;
	public $classe;
	
	public $descriptionS;
	public $descriptionP;
	public $genre;
	public $suffix;
	
	public $add;
	public $fiche;
	public $modif;
	public $del;
	public $list;
	public $search;
	public $printList;
	public $printFile;

	public $show;
	public $edit;
	public $grid;
	public $finder;

	public $useMethod;
	
	public $listNb;
	public $searchNb;


	public $droitVisu;
	public $droitAjoutModif;
	public $droitDel;
	public $icon;

	public $extension;
	public $table;
	public $PosSon=-1;

  	//constructor
  	public function __construct($Params,$extensionName)
	{
		$this->extensionName=$extensionName;
		$this->classe=$Params['classe'];
	
		$this->descriptionS=$Params['descriptionS'];
		$this->descriptionP=$Params['descriptionP'];
		$this->genre=$Params['genre'];
		$this->suffix=$Params['suffix'];
	
		$this->add=checkParam($Params,'add');
		$this->fiche=checkParam($Params,'fiche');
		$this->modif=checkParam($Params,'modif');
		$this->del=checkParam($Params,'del');
		$this->list=checkParam($Params,'list');
		$this->search=checkParam($Params,'search');
		$this->printList=checkParam($Params,'printList');
		$this->printFile=checkParam($Params,'printFile');

		$this->show=checkParam($Params,'show');
		$this->edit=checkParam($Params,'edit');
		$this->grid=checkParam($Params,'grid');
		$this->finder=checkParam($Params,'finder');
		$this->useMethod=checkParam($Params,'useMethod');
		if ($this->useMethod) {
			$this->show=$this->fiche;
			$this->edit=($this->add || $this->modif);
			$this->grid=($this->list || $this->search);
			$this->finder=$this->search;
		}
	
		$this->listNb=$Params['listNb'];
		$this->searchNb=$Params['searchNb'];

		$this->droitVisu=$Params['droitVisu'];
		$this->droitAjoutModif=$Params['droitAjoutModif'];
		$this->droitDel=$Params['droitDel'];
		$this->icon=$Params['icon'];

		$this->extension=new Extension($extensionName);
		$tbl=new Table($this->classe,$extensionName);
		if ($tbl->Heritage!='')
			$this->PosSon=$tbl->PosChild;
		else
			$this->PosSon=-1;
	}

	private function createAction_AddModif()
	{
		$article=($this->genre==0)?'un':'une';
		$act=new Action("AddModify",$this->extensionName,$this->classe);
		if (count($act->CodeFunction)==0) {
			if ($this->add && $this->modif)
				$act->Description="Ajouter/Modifier $article ".$this->descriptionS;
			else if ($this->add)
				$act->Description="Ajouter $article ".$this->descriptionS;
			else
				$act->Description="Modifier $article ".$this->descriptionS;
			if ($this->modif)
				$act->IndexName=$this->suffix;
			else
				$act->IndexName="";
			$act->XferCnt="custom";
			$act->WithTransaction=false;
			$act->LockMode=LOCK_MODE_EVENT;
			$act->CodeFunction=array();
			if ($this->add && $this->modif) {
				$act->CodeFunction[]='if ($self->id>0)';
				$act->CodeFunction[]='	$xfer_result->Caption='.getStringToWrite("Modifier $article ".$this->descriptionS).';';
				$act->CodeFunction[]='else';
				$act->CodeFunction[]='	$xfer_result->Caption='.getStringToWrite("Ajouter $article ".$this->descriptionS).';';
			}
			else if ($this->add)
				$act->CodeFunction[]='$xfer_result->Caption='.getStringToWrite("Ajouter $article ".$this->descriptionS).';';
			else
				$act->CodeFunction[]='$xfer_result->Caption='.getStringToWrite("Modifier $article ".$this->descriptionS).';';
			if ($this->icon!='') {
				$act->CodeFunction[]='$img=new Xfer_Comp_Image("img");';
				$act->CodeFunction[]='$img->setLocation(0,0,1,5);';
				$act->CodeFunction[]='$img->setValue("'.$this->icon.'");';
				$act->CodeFunction[]='$xfer_result->addComponent($img);';
			}
			$act->CodeFunction[]='$self->setFrom($Params);';
			if ($this->useMethod)
				$act->CodeFunction[]='$xfer_result=$self->edit(1,0,$xfer_result);';
			else
				$act->CodeFunction[]='$xfer_result->setDBObject($self,null,false,0,1);';
			$act->CodeFunction[]='$xfer_result->addAction($self->newAction("_Ok", "ok.png", "AddModifyAct",FORMTYPE_MODAL,CLOSE_YES));';
			$act->CodeFunction[]='$xfer_result->addAction(new Xfer_Action("_Annuler", "cancel.png"));';
			$act->Write();
			$this->addActionInExtension($act->Description, $act->GetName(SEP), $this->droitAjoutModif);
		}

		$act=new Action("AddModifyAct",$this->extensionName,$this->classe);
		if (count($act->CodeFunction)==0) {
			$act->Description="Valider $article ".$this->descriptionS;
			$act->IndexName="";
			if ($this->modif)
				$act->Parameters=array($this->suffix=>'');
			$act->XferCnt="acknowledge";
			$act->WithTransaction=true;
			$act->LockMode=LOCK_MODE_EVENT;
			$act->CodeFunction=array();
			$act->CodeFunction[]='if($'.$this->suffix.'>0)';
			$act->CodeFunction[]='	$find=$self->get($'.$this->suffix.');';
			$act->CodeFunction[]='$self->setFrom($Params);';
			$act->CodeFunction[]='if ($find)';
			$act->CodeFunction[]='	$self->update();';
			$act->CodeFunction[]='else';
			$act->CodeFunction[]='	$self->insert();';
			if ($self->fiche) {
				$act->CodeFunction[]='if ('.$this->suffix.'<=0)';
				$act->CodeFunction[]='{';
				$act->CodeFunction[]='  $xfer_result->m_context=array("'.$this->suffix.'"=>$self->id);';
				$act->CodeFunction[]='  $xfer_result->redirectAction($self->NewAction("editer","","Fiche"));';
				$act->CodeFunction[]='}';
			}
	
			$act->Write();
			$this->addActionInExtension($act->Description, $act->GetName(SEP), $this->droitAjoutModif);
		}
	}

	private function createMethod_Edit()
	{
		$article=($this->genre==0)?'un':'une';
		$meth=new Method("edit",$this->extensionName,$this->classe);
		if (count($meth->CodeFunction)>0) return;
		$meth->Description="Editer $article ".$this->descriptionS;
		$meth->Parameters=array('posX'=>0,'posY'=>0,'xfer_result'=>0);
		$table=new Table($this->classe,$this->extensionName);
		$field_keys=array_keys($table->Fields);
		$num=1;
		foreach($field_keys as $key) {
			if ($table->Fields[$key]['type']!=9)
				$meth->CodeFunction[]='$xfer_result->setDBObject($self,"'.$key.'",false,$posY++,$posX);';
			if ($this->PosSon==$num) {
				$meth->CodeFunction[]='$xfer_result = $self->Super->edit($posX,$posY,$xfer_result);';
				$meth->CodeFunction[]='$posY = $xfer_result->getComponentCount()+1;';
			}
			$num++;
		}			
		if ($this->PosSon>=$num) {
			$meth->CodeFunction[]='$xfer_result = $self->Super->edit($posX,$posY,$xfer_result);';
		}
		$meth->CodeFunction[]='return $xfer_result;';
		$meth->Write();
	}

	private function createAction_Fiche()
	{
		$article=($this->genre==0)?'un':'une';
		$act=new Action("Fiche",$this->extensionName,$this->classe);
		if (count($act->CodeFunction)>0) return;
		$act->Description="Fiche d'$article ".$this->descriptionS;
		$act->IndexName=$this->suffix;
		$act->XferCnt="custom";
		$act->WithTransaction=false;
		$act->LockMode=LOCK_MODE_EVENT;
		$act->CodeFunction=array();
		if ($this->icon!='') {
			$act->CodeFunction[]='$img=new Xfer_Comp_Image("img");';
			$act->CodeFunction[]='$img->setLocation(0,0,1,5);';
			$act->CodeFunction[]='$img->setValue("'.$this->icon.'");';
			$act->CodeFunction[]='$xfer_result->addComponent($img);';
		}
		if ($this->useMethod)
			$act->CodeFunction[]='$xfer_result=$self->show(1,0,$xfer_result);';
		else
			$act->CodeFunction[]='$xfer_result->setDBObject($self,null,true,0,1);';
		if ($this->modif)
			$act->CodeFunction[]='$xfer_result->addAction($self->newAction("_Modifier", "edit.png", "AddModify", FORMTYPE_MODAL,CLOSE_YES));';
		if ($this->printFile)
			$act->CodeFunction[]='$xfer_result->addAction($self->newAction("_Imprimer", "print.png", "PrintFiche", FORMTYPE_MODAL,CLOSE_NO));';
		$act->CodeFunction[]='$xfer_result->addAction(new Xfer_Action("_Fermer", "close.png"));';
		$act->Write();
		$this->addActionInExtension($act->Description, $act->GetName(SEP), $this->droitVisu);
	}

	private function createMethod_Show()
	{
		$meth=new Method("show",$this->extensionName,$this->classe);
		if (count($meth->CodeFunction)>0) return;
		$article=($this->genre==0)?'un':'une';
		$meth->Description="Voir $article ".$this->descriptionS;
		$meth->Parameters=array('posX'=>0,'posY'=>0,'xfer_result'=>0);
		$table=new Table($this->classe,$this->extensionName);
		$field_keys=array_keys($table->Fields);
		$num=1;
		foreach($field_keys as $key) {
			if ($table->Fields[$key]['type']!=9)
				$meth->CodeFunction[]='$xfer_result->setDBObject($self,"'.$key.'",true,$posY++,$posX);';
			else {
				$table_name=$table->Fields[$key]['params']['TableName'];
				$pos_sep=strrpos($table_name, "_");
				$ext=substr($table_name,0,$pos_sep);
				$tbl=substr($table_name,$pos_sep+1);
				$getgrid_file="../extensions/$ext/".$tbl."_APAS_getGrid.mth.php";
				if (is_file($getgrid_file)) {
					$meth->CodeFunction[]='$lbl = new Xfer_Comp_LabelForm("'.$key.'lbl");';
					$meth->CodeFunction[]='$lbl->setValue("{[bold]}'.getStringToWrite($table->Fields[$key]['description'],false).'{[/bold]}");';
					$meth->CodeFunction[]='$lbl->setLocation($posX,$posY++);';
					$meth->CodeFunction[]='$xfer_result->addComponent($lbl);';

					$meth->CodeFunction[]='$DB'.$key.'=$self->getField("'.$key.'");';
					$meth->CodeFunction[]='$grid = $DB'.$key.'->getGrid($Params);';
					$meth->CodeFunction[]='$grid->setLocation($posX+1,$posY++);';
					$meth->CodeFunction[]='$xfer_result->addComponent($grid);';
				}
				else
					$meth->CodeFunction[]='$xfer_result->setDBObject($self,"'.$key.'",true,$posY++,$posX);';
			}

			if ($this->PosSon==$num) {
				$meth->CodeFunction[]='$xfer_result = $self->Super->show($posX,$posY,$xfer_result);';
				$meth->CodeFunction[]='$posY = $xfer_result->getComponentCount()+1;';
				$num++;
			}
			$num++;
		}			
		if ($this->PosSon>=$num) {
			$meth->CodeFunction[]='$xfer_result = $self->Super->show($posX,$posY,$xfer_result);';
		}
		$meth->CodeFunction[]='return $xfer_result;';
		$meth->Write();
	}

	private function createAction_Del()
	{
		$act=new Action("Del",$this->extensionName,$this->classe);
		if (count($act->CodeFunction)>0) return;
		$article=($this->genre==0)?'un':'une';
		$act->Description="Supprimer $article ".$this->descriptionS;
		$act->IndexName=$this->suffix;
		$act->XferCnt="acknowledge";
		$act->WithTransaction=true;
		$act->LockMode=LOCK_MODE_EVENT;
		$act->CodeFunction=array();
		$act->CodeFunction[]='if (($res=$self->canBeDelete())!=0) {';
		$act->CodeFunction[]='	require_once("CORE/Lucterios_Error.inc.php");';
		$act->CodeFunction[]='	throw new LucteriosException(IMPORTANT,"Suppression de ".$self->toText()." impossible");';
		$act->CodeFunction[]='}';
		$act->CodeFunction[]='if ($xfer_result->confirme("Voulez vous supprimer ".$self->toText()."?"))';
		$act->CodeFunction[]='	$self->deleteCascade();';
		$act->Write();
		$this->addActionInExtension($act->Description, $act->GetName(SEP), $this->droitDel);
	}

	private function createAction_List()
	{
		$act=new Action("List",$this->extensionName,$this->classe);
		if (count($act->CodeFunction)>0) return;
		$act->Description="Lister des ".$this->descriptionP;
		$act->IndexName="";
		if ($this->search) 
			$act->Parameters=array('IsSearch'=>'0');
		$act->XferCnt="custom";
		$act->WithTransaction=false;
		$act->LockMode=LOCK_MODE_NO;
		$act->CodeFunction=array();
		if ($this->icon!='') {
			$act->CodeFunction[]='$img=new  Xfer_Comp_Image("img");';
			$act->CodeFunction[]='$img->setLocation(0,0);';
			$act->CodeFunction[]='$img->setValue("'.$this->icon.'");';
			$act->CodeFunction[]='$xfer_result->addComponent($img);';
		}
 		$act->CodeFunction[]='$lbl=new  Xfer_Comp_LabelForm("titre");';
		if ($this->icon!='') 
 			$act->CodeFunction[]='$lbl->setLocation(1,0);';
		else
 			$act->CodeFunction[]='$lbl->setLocation(0,0,2);';
 		$act->CodeFunction[]='$xfer_result->addComponent($lbl);';
		$tab='';
		if ($this->search) { 
			$act->CodeFunction[]='if ($IsSearch!=0)';
			$act->CodeFunction[]='{';
			$act->CodeFunction[]='	$self->setForSearch($Params);';
			$act->CodeFunction[]='	$lbl->setValue("{[center]}{[bold]}Résultat de la recherche{[/bold]}{[/center]}");';
			$act->CodeFunction[]='}';
			$act->CodeFunction[]='else {';
			$tab="	";
		}
		$act->CodeFunction[]=$tab.'$lbl->setValue("{[center]}{[bold]}'.getStringToWrite($act->Description,false).'{[/bold]}{[/center]}");';
		$act->CodeFunction[]=$tab.'$self->find();';
		if ($this->search)
			$act->CodeFunction[]='}';
		if ($this->useMethod)
			$act->CodeFunction[]='$grid = $self->getGrid($Params);';
		else {
			$act->CodeFunction[]='$grid = new Xfer_Comp_Grid("'.$this->suffix.'");';
			$act->CodeFunction[]='$grid->setDBObject($self, '.$this->listNb.');';
			if ($this->fiche)
				$act->CodeFunction[]='$grid->addAction($self->newAction("_Editer", "edit.png", "Fiche", FORMTYPE_MODAL,CLOSE_NO, SELECT_SINGLE));';
			if ($this->del)
				$act->CodeFunction[]='$grid->addAction($self->newAction("_Supprimer", "suppr.png", "Del", FORMTYPE_MODAL,CLOSE_NO, SELECT_SINGLE));';
			if ($this->add)
				$act->CodeFunction[]='$grid->addAction($self->newAction("_Ajouter", "add.png", "AddModify",FORMTYPE_MODAL,CLOSE_NO, SELECT_NONE));';
		}
		$act->CodeFunction[]='$grid->setLocation(0,1,2);';
		$act->CodeFunction[]='$xfer_result->addComponent($grid);';
		$act->CodeFunction[]='$lbl=new Xfer_Comp_LabelForm("nb");';
		$act->CodeFunction[]='$lbl->setLocation(0, 2,2);';
		$act->CodeFunction[]='$lbl->setValue("Nombre total : ".$grid->mNbLines);';
		$act->CodeFunction[]='$xfer_result->addComponent($lbl);';
		if ($this->printList)
			$act->CodeFunction[]='$xfer_result->addAction($self->newAction("_Imprimer", "print.png","PrintList",FORMTYPE_MODAL,CLOSE_NO));';
		if ($this->search) {
			$act->CodeFunction[]='if ($IsSearch!=0)';
			$act->CodeFunction[]='	$xfer_result->addAction($self->NewAction("Nouvelle _Recherche","search.png","Search",FORMTYPE_MODAL,CLOSE_YES));';
		}
		$act->CodeFunction[]='$xfer_result->addAction(new Xfer_Action("_Fermer", "close.png"));';
		$act->Write();

		$this->addActionInExtension($act->Description, $act->GetName(SEP), $this->droitVisu);
	}

	private function createMethod_getGrid()
	{
		$meth=new Method("getGrid",$this->extensionName,$this->classe);
		if (count($meth->CodeFunction)>0) return;
		$meth->Description="créateur de list de ".$this->descriptionP;
		$meth->Parameters=array('Params'=>0);

		$field_list=array();
		$table=new Table($this->classe,$this->extensionName);
		$field_keys=array_keys($table->Fields);
		for($f_index=0; $f_index<$this->listNb ;$f_index++) {
			 $field_list[]="'".$field_keys[$f_index]."'";
		}
		echo "<!-- this->listNb:$this->listNb - field_list:".print_r($field_list,true)." -->\n";
		$meth->CodeFunction[]='$grid = new Xfer_Comp_Grid("'.$this->suffix.'");';
		$meth->CodeFunction[]='$grid->setDBObject($self, array('.implode(',',$field_list).'),"",$Params);';
		if ($this->fiche)
			$meth->CodeFunction[]='$grid->addAction($self->newAction("_Editer", "edit.png", "Fiche", FORMTYPE_MODAL,CLOSE_NO, SELECT_SINGLE));';
		else if ($this->add)
			$meth->CodeFunction[]='$grid->addAction($self->newAction("_Modifier", "edit.png", "AddModify",FORMTYPE_MODAL,CLOSE_NO, SELECT_SINGLE));';
		if ($this->del)
			$meth->CodeFunction[]='$grid->addAction($self->newAction("_Supprimer", "suppr.png", "Del", FORMTYPE_MODAL,CLOSE_NO, SELECT_SINGLE));';
		if ($this->add)
			$meth->CodeFunction[]='$grid->addAction($self->newAction("_Ajouter", "add.png", "AddModify",FORMTYPE_MODAL,CLOSE_NO, SELECT_NONE));';
		$meth->CodeFunction[]='$grid->setSize(200,750);';
		$meth->CodeFunction[]='return $grid;';
		$meth->Write();
	}

	private function createAction_Search()
	{
		$article=($this->genre==0)?'un':'une';
		$act=new Action("Search",$this->extensionName,$this->classe);
		if (count($act->CodeFunction)>0) return;
		$act->Description="Rechercher $article ".$this->descriptionS;
		$act->IndexName="";
		$act->XferCnt="custom";
		$act->WithTransaction=false;
		$act->LockMode=LOCK_MODE_NO;
		$act->CodeFunction=array();
		if ($this->icon!='') { 
			$act->CodeFunction[]='$img=new  Xfer_Comp_Image("img");';
			$act->CodeFunction[]='$img->setValue("'.$this->icon.'");';
			$act->CodeFunction[]='$img->setLocation(0,0);';
			$act->CodeFunction[]='$xfer_result->addComponent($img);';
		}
		$act->CodeFunction[]='$img=new  Xfer_Comp_LabelForm("title");';
		$act->CodeFunction[]='$img->setValue("{[center]}{[underline]}{[bold]}Séléctionnez vos critères de recherche{[/bold]}{[/underline]}{[/center]}");';
		if ($this->icon!='')  
			$act->CodeFunction[]='$img->setLocation(1,0,2);';
		else
			$act->CodeFunction[]='$img->setLocation(0,0,3);';
		$act->CodeFunction[]='$xfer_result->addComponent($img);';
		$act->CodeFunction[]='$xfer_result->m_context["IsSearch"]=1;';
		if ($this->useMethod) {
			$act->CodeFunction[]='$Fields=$self->findFields();';
			$act->CodeFunction[]='$xfer_result->setSearchGUI($self,$Fields,1);';
		}
		else {
			$act->CodeFunction[]='$field_desc = $DBObjs->getDBMetaDataField();';
			$act->CodeFunction[]='$Fields=$DBObjs->getFieldEditable($RefTableName,'.$this->searchNb.');';
			$act->CodeFunction[]='$xfer_result->setSearchGUI($self,$Fields,1);';
		}
		$act->CodeFunction[]='$xfer_result->addAction($self->NewAction("_Rechercher","search.png","List",FORMTYPE_NOMODAL,CLOSE_YES));';
		$act->CodeFunction[]='$xfer_result->addAction($self->NewAction("_Annuler","cancel.png"));';
		$act->Write();
		$this->addActionInExtension($act->Description, $act->GetName(SEP), $this->droitVisu);
	}

	private function createMethod_Finder()
	{
		$article=($this->genre==0)?'un':'une';
		$meth=new Method("findFields",$this->extensionName,$this->classe);
		if (count($meth->CodeFunction)>0) return;
		$meth->Description="Recherche $article ".$this->descriptionS;
		$meth->Parameters=array();

		$table=new Table($this->classe,$this->extensionName);
		$field_keys=array_keys($table->Fields);
		$num=1;
		$meth->CodeFunction[]='$fields=array();';
		foreach($field_keys as $key) {
			if ($table->Fields[$key]['type']!=9)
				$meth->CodeFunction[]='$fields[]="'.$key.'";';
			if ($this->PosSon==$num) {
				$meth->CodeFunction[]='$superfields = $self->Super->findFields();';
				$meth->CodeFunction[]='foreach($superfields as $superfield) $fields[]=$superfield;';
			}
			if ($num==$this->searchNb) {
				$num++;
				break;
			}
			$num++;
		}			
		if ($this->PosSon>=$num) {
			$meth->CodeFunction[]='$superfields = $self->Super->findFields();';
			$meth->CodeFunction[]='foreach($superfields as $superfield) $fields[]=$superfield;';
		}
		$meth->CodeFunction[]='return $fields;';
		$meth->Write();
	}

	private function createAction_PrintList()
	{
		$act=new Action("PrintList",$this->extensionName,$this->classe);
		if (count($act->CodeFunction)>0) return;
		$act->Description="Imprimer une liste de ".$this->descriptionP;
		$act->IndexName="";
		if ($this->search) 
			$act->Parameters=array('IsSearch'=>'0');
		$act->XferCnt="print";
		$act->WithTransaction=false;
		$act->LockMode=LOCK_MODE_NO;
		$act->CodeFunction=array();
		$act->CodeFunction[]='require_once "CORE/PrintListing.inc.php";';
		$act->CodeFunction[]='$listing=new PrintListing("Liste des '.$this->descriptionP.'");';
		$act->CodeFunction[]='$listing->Header="Liste des '.$this->descriptionP.'";';
		$table=new Table($this->classe,$this->extensionName);
		$field_keys=array_keys($table->Fields);
		$size=(int)(190/$this->listNb);
		for($i=0;$i<$this->listNb;$i++)
			$act->CodeFunction[]='$listing->GridHeader[]=array("'.$field_keys[$i].'",'.$size.');';
		if ($this->search) { 
			$act->CodeFunction[]='if ($IsSearch!=0)';
			$act->CodeFunction[]='	$self->setForSearch($Params);';
			$act->CodeFunction[]='else';
		}
		$act->CodeFunction[]='	$self->find();';
		$act->CodeFunction[]='while ($self->fetch()) {';
		$act->CodeFunction[]='	$one_row=array();';
		for($i=0;$i<$this->listNb;$i++)
			$act->CodeFunction[]='	$one_row[]=$self->'.$field_keys[$i].';';
		$act->CodeFunction[]='	$listing->GridContent[]=$one_row;';
		$act->CodeFunction[]='}';
		$act->CodeFunction[]='$xfer_result->printListing($listing);';
		$act->Write();
		$this->addActionInExtension($act->Description, $act->GetName(SEP), $this->droitVisu);
	}

	private function createAction_PrintFile()
	{
		$article=($this->genre==0)?'un':'une';
		$act=new Action("PrintFiche",$this->extensionName,$this->classe);
		if (count($act->CodeFunction)>0) return;
		$act->Description="Imprimer $article ".$this->descriptionS;
		$act->IndexName=$this->suffix;
		$act->XferCnt="print";
		$act->WithTransaction=false;
		$act->LockMode=LOCK_MODE_NO;
		$act->CodeFunction=array();
		$act->CodeFunction[]='require_once "CORE/PrintAction.inc.php";';
		$act->CodeFunction[]='$print_action=new PrintAction("'.$this->extensionName.'","'.$this->classe.SEP.'Fiche",$Params);';
		$act->CodeFunction[]='$print_action->TabChangePage=false;';
		$act->CodeFunction[]='$print_action->Extended=false;';
		$act->CodeFunction[]='$print_action->Title="Fiche descriptive";';
		$act->CodeFunction[]='$xfer_result->printListing($print_action);';		
		$act->Write();
		$this->addActionInExtension($act->Description, $act->GetName(SEP), $this->droitVisu);
	}

	private function addActionInExtension($CodeDesc, $CodeName, $ActionDroit)
	{
		require_once("../CORE/setup_param.inc.php");
		$action_id=count($this->extension->Actions);
		foreach($this->extension->Actions as $id_act=>$current_act)
			if ($current_act->action==$CodeName)
				$action_id=$id_act;
		$this->extension->Actions[$action_id]=new Param_Action($CodeDesc, $CodeName, $ActionDroit);
		$this->extension->IncrementBuild();
	}

	public function execute()
	{
		if ($this->show)
			$this->createMethod_Show();
		if ($this->edit)
			$this->createMethod_Edit();
		if ($this->grid)
			$this->createMethod_getGrid();
		if ($this->finder)
			$this->createMethod_Finder();

		if ($this->add || $this->modif)
			$this->createAction_AddModif();
		if ($this->fiche)
			$this->createAction_Fiche();
		if ($this->del)
			$this->createAction_Del();
		if ($this->list)
			$this->createAction_List();
		if ($this->search)
			$this->createAction_Search();
		if ($this->printList)
			$this->createAction_PrintList();
		if ($this->printFile)
			$this->createAction_PrintFile();

		$this->extension->IncrementBuild();
		$this->extension->write();
	}
}

?>
 
