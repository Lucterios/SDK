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
require_once("ConnectionSDK.inc.php");

require_once("AbstractClass.inc.php");

class ImageManage extends AbstractClassManage
{
	public $Suffix="";
	public function GetExtDir($extensionName="")
	{
		if (($extensionName=="") || ($extensionName=="CORE"))
			$extDir = "../images/";
		elseif ($extensionName=="applis")
		{
			$extDir = "../applis/images/";
			if (!is_dir($extDir))
				$extDir = "../extensions/applis/images/";
		}
		else
			$extDir = "../extensions/$extensionName/images/";
		return $extDir;
	}
}

class Image extends AbstractClass
{
	public $Mng;

  	//constructor
  	public function __construct($name,$extensionName="")
	{
		$this->Mng=new ImageManage();
		parent::__construct($name,$extensionName);
	}

	public function Add($file)
	{
		$extDir = $this->Mng->GetExtDir($this->ExtensionName);
		if (!is_dir($extDir))
			mkdir($extDir,0777);
		$extImgFile = $extDir.$file['name'];
		copy($file['tmp_name'],$extImgFile);
	}

	public function AddBase64Img($image)
	{
		List($name,$filebased64)=explode(';',$image);
		$extDir = $this->Mng->GetExtDir($this->ExtensionName);
		if (!is_dir($extDir))
			mkdir($extDir,0777);
		$extImgFile = $extDir.$name;
		if ($handle = fopen($extImgFile, 'w')) {
			$content=base64_decode($filebased64,true);
			if (fwrite($handle,$content) === FALSE)
				return "Erreur d'écriture";
			fclose($handle);
			chmod($extImgFile, 0666);
			return '';
		}
		else
			return "Erreur d'ouverture";
	}

	public function AddBase64($filename,$filebased64)
	{                           
		$extDir = $this->Mng->GetExtDir($this->ExtensionName);
		if (!is_dir($extDir))
			mkdir($extDir,0777);
		$extImgFile = $extDir.$filename;
		if ($handle = fopen($extImgFile, 'w')) {
			$filebased64=str_replace(' ','+',$filebased64);
			$content=base64_decode($filebased64,true);
			if ($content == FALSE)
				return "Erreur d'encodage";
			if (fwrite($handle,$content) === FALSE)
				return "Erreur d'écriture";
			fclose($handle);
			chmod($extImgFile, 0666);
			return '';
		}
		else
			return "Erreur d'ouverture";
	}

}

?>