<?php

class Matheus_AssignToGrouped_StartController extends Mage_Adminhtml_Controller_Action{
	public function indexAction(){
              	  /** Set default timezone */
                  date_default_timezone_set('America/Bahia');
                  /** Set file directory */
                  $this->tmpDir = __DIR__.'/../temp/grouped.csv';
                  $sheetName = basename($_FILES['file_to_upload']['name']);
                  $excelFileType = strtolower(pathinfo($sheetName,PATHINFO_EXTENSION));
                  try{
                          move_uploaded_file($_FILES["file_to_upload"]["tmp_name"], $this->sheetDir);
                  }catch(Exception $e){
                          echo $e;
                  }
                  if($excelFileType!='csv'){
                          echo "<b>".date('H:i:s')." </b>Error: file format '".$excelFileType."' isn't accepted. You need to select a csv file.<br><br>";
                  }
                  elseif(move_uploaded_file($_FILES["file_to_upload"]["tmp_name"], $this->tmpDir)){
                          echo "<b>".date('H:i:s')." </b>Starting process...<br><br>";
			  $this->assignToGrouped();
                  }
                  $this->deleteTmpFile();
	}		
	private function assignToGrouped(){
   		  require_once dirname(__FILE__).'/../Classes/PHPExcel.php';
                  $inputFileType = PHPExcel_IOFactory::identify($this->tmpDir);
                  $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                  $objPHPExcel = $objReader->load($this->tmpDir);
                  $worksheet  = $objPHPExcel->getActiveSheet();
                  $highestRow = $worksheet->getHighestRow();
		  $errorLines = array();
		  $errorIndex = 0;
		  $successIndex = 0;
		  for($i=2; $i<=(int)$highestRow; $i++){
			  $sku_grouped = $worksheet->getCellByColumnAndRow(0, $i)->getValue();
			  if($this->skuExists($sku_grouped)){
				  $grouped = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku_grouped);
				  $children = $worksheet->getCellByColumnAndRow(1, $i)->getValue();
				  if($children !== NULL && !is_int($children) && !is_float($children)){
					  $this->assignChildren($children, $grouped);
					  $successIndex += 1;
				  }
			  } else{
				  $errorLines[$errorIndex] = $i;
				  $errorIndex += 1; 
			  }
		  }
		  echo "<b>".date('H:i:s')." </b> Products assigned. <b>".$successIndex."</b> products were assigned to their categories.<br><br>";
		  if($errorIndex != 0){
			  $errorString = "{";
			  foreach($errorLines as $line){
				  $errorString .= $line.",";
			  }
			  $errorString = substr($errorString, 0, -1);
			  $errorString .= "}";
			  echo "<b>".date('H:i:s')," </b>The following rows had invalid skus or categories and were ignored: <b>".$errorString."</b>";
		  }
	}
	private function skuExists($sku){
		$ver = True;
		$product = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);
		if(!$product){
			$ver = False;
		}
		return $ver;
	}
	private function assignChildren($children, $grouped){
		$childrenIds = [];
		$groupedId = $grouped->getId();
		$productsLinks = Mage::getModel('catalog/product_link_api');
		$childrenSku = explode(';', $children);
		for($i = 0; $i < count($childrenSku); $i++){
			$childrenIds[$i] = Mage::getModel('catalog/product')->getIdBySku($childrenSku[$i]);
		}
		foreach($childrenIds as $id) {
			$productsLinks->assign('grouped', $groupedId, $id);
		}
	}

	private function deleteTmpFile(){
                  chmod($this->tmpDir,0755); //Change the file permissions if allowed
                  unlink($this->tmpDir); //remove the file
        }

}
