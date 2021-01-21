<?php

include_once('../../config/config.inc.php');
include_once('../../init.php');
include_once('videostab.php');
include_once('videoUpload.php');

if (Tools::getValue('action') == 'deleteVideo' && Tools::getValue('id'))
     	{
			
			if (Tools::getValue('videoType')==1)
			{
				$row = Db::getInstance()->getRow('
                SELECT video FROM `'._DB_PREFIX_.'videos_tab_mod`
                WHERE `id` = '.intval(Tools::getValue('id')).'');
				
				
				 if (file_exists('./uploads/' . $row['video'])) 
       			 unlink('./uploads/' . $row['video']);
				}

	 		$query = Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'videos_tab_mod WHERE `id`='.(int)(Tools::getValue('id')));
			if($query)
			echo Tools::getValue('id');
     	}
		
if (Tools::getValue('action') == 'submitEmbedVideo' && Tools::getValue('id_product') && Tools::getValue('embedCode'))
     	{
			
	 	$query = Db::getInstance()->Execute('
            INSERT INTO `'._DB_PREFIX_.'videos_tab_mod`
		(`id_product`, `type`, `video`) VALUES(
		'.intval(Tools::getValue('id_product')).', 0, 
		\''.urldecode(Tools::getValue('embedCode')).'\')');
		
		
		$last_id = Db::getInstance()->Insert_ID(); 
		$data = array();
		$data['id'] = $last_id;
		$data['video'] = Tools::getValue('embedCode');
		if($query)
		print json_encode($data);
     	}


if (Tools::getValue('action') == 'submitUploadVideo' && Tools::getValue('id_product'))
     	{
$allowedExtensions = array("mp4");
$sizeLimit = 10485760;
$uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
$result = $uploader->handleUpload('uploads/');
$filename = $uploader->getName();
	if (isset($result['success']))
		{
			
			
			
			$query = Db::getInstance()->Execute('
            INSERT INTO `'._DB_PREFIX_.'videos_tab_mod`
		(`id_product`, `type`, `video`) VALUES(
		'.intval(Tools::getValue('id_product')).', 1, 
		\''.$result['filename'].'\')');
		
		
		$last_id = Db::getInstance()->Insert_ID(); 
		$result['id_video'] = $last_id;
		
	print json_encode($result);		
		}
		else
print json_encode($result);


     	}


