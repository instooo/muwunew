<?php
class ExcelToArrary {
		public function __construct() {
			//Vendor("Excel.PHPExcel");//引入phpexcel类(注意你自己的路径)
			//Vendor("Excel.PHPExcel.IOFactory"); 	
			include dirname(dirname(__FILE__))."/Excel/PHPExcel.php";  
			include dirname(dirname(__FILE__))."/Excel/PHPExcel/IOFactory.php";  
		}
  		/*导入Excel*/
  		public function read($filename,$encode,$file_type)
		{
			if(strtolower ( $file_type )=='xls')//判断excel表类型为2003还是2007
			{
				print_r(Vendor("Excel.PHPExcel.Reader.Excel5")); 
				$objReader = PHPExcel_IOFactory::createReader('Excel5');
			}elseif(strtolower ( $file_type )=='xlsx')
			{
				Vendor("Excel.PHPExcel.Reader.Excel2007"); 
				$objReader = PHPExcel_IOFactory::createReader('Excel2007');
			}
			$objReader->setReadDataOnly(true);
			$objPHPExcel = $objReader->load($filename);
			$objWorksheet = $objPHPExcel->getActiveSheet();
			$highestRow = $objWorksheet->getHighestRow();
			$highestColumn = $objWorksheet->getHighestColumn();
			$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
			$excelData = array();
			for ($row = 1; $row <= $highestRow; $row++) {
				for ($col = 0; $col < $highestColumnIndex; $col++) {
					$excelData[$row][] =(string)$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
					}
			}
			return $excelData;
		}
		
		
}
?>