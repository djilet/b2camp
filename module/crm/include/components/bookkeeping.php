<?php 
require_once(dirname(__FILE__)."/../component.php");

class BookkeepingViewComponent extends BaseComponent
{
// 	var $imageParams;
	
// 	function BookkeepingViewComponent($name, $config)
// 	{
// 		parent::BaseComponent($name, $config);
// 		$this->imageParams = LoadImageConfig("ManagerImage", "user", $config["Image"]);
// 	}
	
	function PrepareBeforeShow(&$item, $user)
	{		
		$stmt = GetStatement();

		if(isset($_GET['ArticleType']))
		{
			$item['ArticleTypeUrl'] = '&&EntityID=&ArticleType='.$_GET['ArticleType'];
			$item['ArticleType'] = $_GET['ArticleType'];
		}
		
		if (isset($item["EntityID"]))
		{
			$query = "SELECT DocumentNumber Number FROM crm_bookkeeping where BookkeepingID = ".$item["EntityID"];
		}
		else	
			$query = "SELECT DocumentNumber Number FROM crm_bookkeeping order by DocumentNumber desc";
		
		$result = $stmt->FetchRow($query);

		$item["DocumentNumber"] = isset($item["EntityID"]) ? $result['Number'] : $result['Number'] + 1;
		
		
// 		$item['ArticleTypeHidden'] = '';
		
// 		if(isset($_GET['ArticleType']))
// 			$item['ArticleTypeHidden'] = $_GET['ArticleType'] == 3 ? 2 : 1;
	}
}

class BookkeepingAmountViewComponent extends BaseComponent
{
	function PrepareBeforeShow(&$item, $user)
	{
		if (isset($item["EntityID"]))
		{
			$stmt = GetStatement();
			$query = 'SELECT Amount, ArticleType FROM crm_bookkeeping WHERE BookkeepingID = '.intval($item["EntityID"]);
			$result = $stmt->FetchRow($query);
			
			if($result['ArticleType'] == 1)
				$_GET['AmountIncome'] = isset($_GET['AmountIncome']) ? $_GET['AmountIncome'] + $result['Amount'] : '';
			elseif($result['ArticleType'] == 2)
				$_GET['AmountOutcome'] = isset($_GET['AmountOutcome']) ? $_GET['AmountOutcome'] + $result['Amount'] : '';
		}
	}
}

?>