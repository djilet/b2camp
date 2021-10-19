<?php 

	$stmt = GetStatement();
	$where = ' WHERE ArticleType = 1';
		
	if(isset($_POST['FilterDateFrom']) && !empty($_POST['FilterDateFrom']))
		$where .= ' AND Date >= "'.date("Y-m-d",strtotime($_POST['FilterDateFrom'])).'"';
	if(isset($_POST['FilterDateTo']) && !empty($_POST['FilterDateTo']))
		$where .= ' AND Date <= "'.date("Y-m-d",strtotime($_POST['FilterDateTo'])).'"';
	if(isset($_POST['FilterDirectoryCheckID']) && !empty($_POST['FilterDirectoryCheckID']))
		$where .= ' AND `Check` = "'.(int)$_POST['FilterDirectoryCheckID'].'"';
	if(isset($_POST['FilteArticleType']) && !empty($_POST['FilteArticleType']))
		$where .= ' AND ArticleType = "'.(int)$_POST['FilteArticleType'].'"';
	if(isset($_POST['FilterDirectoryIncomeID']) && !empty($_POST['FilterDirectoryIncomeID']))
		$where .= ' AND ArticleID = "'.(int)$_POST['FilterDirectoryIncomeID'].'"';
	if(isset($_POST['FilterDirectoryOutcomeID']) && !empty($_POST['FilterDirectoryOutcomeID']))
		$where .= ' AND ArticleID = "'.(int)$_POST['FilterDirectoryOutcomeID'].'"';
	if(isset($_POST['FilterDocumentNumber']) && !empty($_POST['FilterDocumentNumber']))
		$where .= ' AND DocumentNumber = "'.$_POST['FilterDocumentNumber'].'"';
	if(isset($_POST['FilterLastname']) && !empty($_POST['FilterLastname']))
		$where .= ' AND Lastname rlike "'.$_POST['FilterLastname'].'"';
	if(isset($_POST['FilterContractor']) && !empty($_POST['FilterContractor']))
		$where .= ' AND Contractor rlike "'.$_POST['FilterContractor'].'"';
	if(isset($_POST['FilterManagerID']) && !empty($_POST['FilterManagerID']))
		$where .= ' AND ManagerID = "'.(int)$_POST['FilterManagerID'].'"';
	if(isset($_POST['FilterAmount']) && !empty($_POST['FilterAmount']))
		$where .= ' AND Amount = "'.(int)$_POST['FilterAmount'].'"';
	
	
	$query = 'SELECT SUM(Amount) AmountIncome FROM crm_bookkeeping '.$where;
	$result = $stmt->FetchRow($query);
	$AmountIncome = $result['AmountIncome'] ? $result['AmountIncome'] : 0;

	$stmt = GetStatement();
	$where = ' WHERE ArticleType = 2';
	
	if(isset($_POST['FilterDateFrom']) && !empty($_POST['FilterDateFrom']))
		$where .= ' AND Date >= "'.date("Y-m-d",strtotime($_POST['FilterDateFrom'])).'"';
	if(isset($_POST['FilterDateTo']) && !empty($_POST['FilterDateTo']))
		$where .= ' AND Date <= "'.date("Y-m-d",strtotime($_POST['FilterDateTo'])).'"';
	if(isset($_POST['FilterDirectoryCheckID']) && !empty($_POST['FilterDirectoryCheckID']))
		$where .= ' AND `Check` = "'.(int)$_POST['FilterDirectoryCheckID'].'"';
	if(isset($_POST['FilteArticleType']) && !empty($_POST['FilteArticleType']))
		$where .= ' AND ArticleType = "'.(int)$_POST['FilteArticleType'].'"';
	if(isset($_POST['FilterDirectoryIncomeID']) && !empty($_POST['FilterDirectoryIncomeID']))
		$where .= ' AND ArticleID = "'.(int)$_POST['FilterDirectoryIncomeID'].'"';
	if(isset($_POST['FilterDirectoryOutcomeID']) && !empty($_POST['FilterDirectoryOutcomeID']))
		$where .= ' AND ArticleID = "'.(int)$_POST['FilterDirectoryOutcomeID'].'"';
	if(isset($_POST['FilterDocumentNumber']) && !empty($_POST['FilterDocumentNumber']))
		$where .= ' AND DocumentNumber = "'.(int)$_POST['FilterDocumentNumber'].'"';
	if(isset($_POST['FilterLastname']) && !empty($_POST['FilterLastname']))
		$where .= ' AND Lastname rlike "'.$_POST['FilterLastname'].'"';
	if(isset($_POST['FilterContractor']) && !empty($_POST['FilterContractor']))
		$where .= ' AND Contractor rlike "'.$_POST['FilterContractor'].'"';
	if(isset($_POST['FilterManagerID']) && !empty($_POST['FilterManagerID']))
		$where .= ' AND ManagerID = "'.(int)$_POST['FilterManagerID'].'"';
	if(isset($_POST['FilterAmount']) && !empty($_POST['FilterAmount']))
		$where .= ' AND Amount = "'.(int)$_POST['FilterAmount'].'"';
	
	
	$query = 'SELECT SUM(Amount) AmountOutcome FROM crm_bookkeeping '.$where;
	$result = $stmt->FetchRow($query);
	$AmountOutcome = $result['AmountOutcome'] ? $result['AmountOutcome'] : 0;
?>

<div class='row col-md-12'><strong>Итого приход: <span class='text-success'><?php echo $AmountIncome; ?></span></strong></div>
<div class='row col-md-12'><strong>Итого расход: <span class='text-danger'><?php echo $AmountOutcome; ?></span></strong></div>