<?php 
require_once(dirname(__FILE__)."/../action.php");

class ExportAction extends BaseAction
{
	function DoAction($request, $user)
	{
		switch($this->name)
		{
			case "Export": 
				$this->ActionExport($request, $user);
			break;
		}
	}
	
	private function ActionExport($request, $user)
	{
		es_include("phpexcel/PHPExcel.php");
		es_include("phpexcel/PHPExcel/Writer/Excel5.php");

		$config = $this->actionConfig;
		$config['ListConfig']['Filters'] = $this->config['ListConfig']['Filters'];
		
		$itemList = new ItemList("crm", $this->actionConfig["Entity"], $config);
		$itemList->Load($request, $user);
		$request->SetProperty("FullList", 1);
		$page = new PopupPage("crm", true);
		$content = $page->Load($this->actionConfig["Template"]);
		$content->LoadFromObjectList("ItemList", $itemList);
		
		$html = $page->Grab($content);
		$html = mb_convert_encoding($html, "cp1251", "utf-8");
		
		$tmpfile = PROJECT_DIR."var/log/".time().'.html';
		file_put_contents($tmpfile, $html);
		
		// Read the contents of the file into PHPExcel Reader class
		$reader = new PHPExcel_Reader_HTML; 
		$content = $reader->load($tmpfile); 
		
		// Pass to writer and output as needed
		header("Expires: Mon, 1 Apr 1974 05:00:00 GMT" );
		header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT" );
		header("Cache-Control: no-cache, must-revalidate" );
		header("Pragma: no-cache" );
		header("Content-type: application/vnd.ms-excel" );
		header("Content-Disposition: attachment; filename='".$this->actionConfig["Entity"].".xlsx'" );
		
		$objWriter = PHPExcel_IOFactory::createWriter($content, 'Excel2007');
		$objWriter->save('php://output');
		
		// Delete temporary file
		unlink($tmpfile);
		exit();
	}
}

?>