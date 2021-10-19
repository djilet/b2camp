<?php 
require_once(dirname(__FILE__)."/../component.php");

class FinanceViewComponent extends BaseComponent
{
	function PrepareBeforeShow(&$item, $user)
	{
		if(isset($item["EntityID"]))
		{
			$stmt = GetStatement();
			$query = "SELECT c.*, 
							CONCAT(u.LastName, ' ', u.FirstName) AS ManagerTitle,  
							SUM(IF(p.PaymentID IS NOT NULL, p.Amount, 0)) AS PaidAmount,
							c.Amount - SUM(IF(p.PaymentID IS NOT NULL, p.Amount, 0)) AS UnpaidAmount
						FROM ".$this->config["ContractTable"]." AS c 
							LEFT JOIN ".$this->config["PaymentTable"]." AS p ON c.ContractID=p.ContractID 
							LEFT JOIN `user` AS u ON c.ManagerID=u.UserID ";

			if(isset($this->config["Type"]) && $this->config["Type"] == "linked")
			{
				$query .= "LEFT JOIN ".$this->config["LinkTable"]." AS l ON c.".$this->config["FromField"]."=l.".$this->config["ToField"]." 
						WHERE l.".$this->config["TargetField"]."=".Connection::GetSQLString((isset($this->config["ID"]) && $this->config["ID"] ? $item[$this->config["ID"]] : $item["EntityID"]));
			}
			else
			{
				$query .= "WHERE c.".$this->config["TargetField"]."=".Connection::GetSQLString((isset($this->config["ID"]) && $this->config["ID"] ? $item[$this->config["ID"]] : $item["EntityID"]));
			}
			$query .= "GROUP BY c.ContractID ORDER BY c.Created DESC";
			$contractList = $stmt->FetchList($query);

			//В прошлом запросе джоином не выдает пэйбэки нормально
			foreach ($contractList as $key => $contract) {
				$query = 'SELECT SUM(Amount) as PaybackAmount FROM '.$this->config['PaybackTable']
                    .' WHERE ContractID = ' . $contract['ContractID'];

				$row = $stmt->FetchRow($query);
				$contractList[$key]['PaybackAmount'] = $row['PaybackAmount'];
				$contractList[$key]['PaidAmount'] = $contractList[$key]['PaidAmount'] - $row['PaybackAmount'];
				$contractList[$key]['UnpaidAmount'] = $contractList[$key]['Amount'] - abs($contractList[$key]['PaidAmount']);
			}
			
			for($i = 0; $i < count($contractList); $i++)
			{
				$query = "SELECT i.InvoiceID, i.Created, i.ManagerID, CONCAT(u.LastName, ' ', u.FirstName) AS ManagerTitle, i.Amount
							FROM ".$this->config["InvoiceTable"]." AS i 
								LEFT JOIN `user` AS u ON i.ManagerID=u.UserID 
							WHERE i.ContractID=".Connection::GetSQLString($contractList[$i]["ContractID"])." 
							ORDER BY i.Created ASC";
				$contractList[$i]["InvoiceList"] = $stmt->FetchList($query);
				
				$query = "SELECT p.PaymentID, p.Created, p.ManagerID, CONCAT(u.LastName, ' ', u.FirstName) AS ManagerTitle, p.Amount
							FROM ".$this->config["PaymentTable"]." AS p 
								LEFT JOIN `user` AS u ON p.ManagerID=u.UserID 
							WHERE p.ContractID=".Connection::GetSQLString($contractList[$i]["ContractID"])." 
							ORDER BY p.Created ASC";
				$contractList[$i]["PaymentList"] = $stmt->FetchList($query);

				$query = "SELECT p.PaybackID, p.Created, p.ManagerID, CONCAT(u.LastName, ' ', u.FirstName) AS ManagerTitle, p.Amount
							FROM ".$this->config["PaybackTable"]." AS p 
								LEFT JOIN `user` AS u ON p.ManagerID=u.UserID 
							WHERE p.ContractID=".Connection::GetSQLString($contractList[$i]["ContractID"])." 
							ORDER BY p.Created ASC";
				$contractList[$i]["PaybackList"] = $stmt->FetchList($query);
				
				$query = "SELECT a.ActID, a.Created, a.ManagerID, CONCAT(u.LastName, ' ', u.FirstName) AS ManagerTitle 
							FROM ".$this->config["ActTable"]." AS a 
								LEFT JOIN `user` AS u ON a.ManagerID=u.UserID 
							WHERE a.ContractID=".Connection::GetSQLString($contractList[$i]["ContractID"])." 
							ORDER BY a.Created ASC";
				$contractList[$i]["ActList"] = $stmt->FetchList($query);
				
				if(isset($this->config["UseSeason"]) && $this->config["UseSeason"])
				{
					$query = "SELECT s.Title FROM `crm_season` AS s 
									JOIN ".$this->config["ContractTable"]."2season AS c2s ON c2s.SeasonID=s.SeasonID 
								WHERE c2s.ContractID=".Connection::GetSQLString($contractList[$i]["ContractID"]);
					$contractList[$i]["SeasonList"] = $stmt->FetchList($query);
				}
			}

			$item[$this->name."List"] = $contractList;
			
			$script = '<script type="text/javascript">$(document).ready(function(){';
			$script .= 'InitContractTableControl($("#'.$this->name.'"));';
			$script .= "});</script>";
			$item[$this->name."ControlHTML"] = $script;
			
			$script = '<script type="text/javascript">$(document).ready(function(){';
			$script .= '$("#SeasonID").select2({placeholder:"",allowClear: true}).on("select2-open", function() {$(this).data("select2").results.addClass("overflow-hidden").perfectScrollbar();});';
			$script .= "});</script>";
			$item["SeasonIDControlHTML"] = $script;
		}
	}
}

class FinanceViewComponentForChild extends BaseComponent
{
    function PrepareBeforeShow(&$item, $user)
    {
        if(isset($item["EntityID"]))
        {
            $stmt = GetStatement();
            $query = "SELECT c.*, 
							CONCAT(u.LastName, ' ', u.FirstName) AS ManagerTitle,  
							SUM(IF(p.PaymentID IS NOT NULL, p.Amount, 0)) AS PaidAmount,
							c.Amount - SUM(IF(p.PaymentID IS NOT NULL, p.Amount, 0)) AS UnpaidAmount
						FROM ".$this->config["ContractTable"]." AS c 
							LEFT JOIN ".$this->config["PaymentTable"]." AS p ON c.ContractID=p.ContractID 
							LEFT JOIN `user` AS u ON c.ManagerID=u.UserID ";
            if(isset($this->config["Type"]) && $this->config["Type"] == "linked")
            {
                $query .= "LEFT JOIN ".$this->config["LinkTable"]." AS l ON c.".$this->config["FromField"]."=l.".$this->config["ToField"]." 
						WHERE l.".$this->config["TargetField"]."=".Connection::GetSQLString((isset($this->config["ID"]) && $this->config["ID"] ? $item[$this->config["ID"]] : $item["EntityID"]));
            }
            else
            {
                $query .= "WHERE c.".$this->config["TargetField"]."=".Connection::GetSQLString((isset($this->config["ID"]) && $this->config["ID"] ? $item[$this->config["ID"]] : $item["EntityID"]));
            }
            $query .= "GROUP BY c.ContractID ORDER BY c.Created DESC";
            $contractList = $stmt->FetchList($query);
            foreach ($contractList as $key => $contract) {
                $query = 'SELECT SUM(Amount) as PaybackAmount FROM '.$this->config['PaybackTable']
                    .' WHERE ContractID = ' . $contract['ContractID'];

                $row = $stmt->FetchRow($query);
                $contractList[$key]['PaybackAmount'] = $row['PaybackAmount'];
                $contractList[$key]['PaidAmount'] = $contractList[$key]['PaidAmount'] - $row['PaybackAmount'];
                $contractList[$key]['UnpaidAmount'] = $contractList[$key]['Amount'] - $contractList[$key]['PaidAmount'];
            }
            for($i = 0; $i < count($contractList); $i++)
            {
                // Счета
                $query = "SELECT i.InvoiceID, i.Created, i.ManagerID, CONCAT(u.LastName, ' ', u.FirstName) AS ManagerTitle, i.Amount
							FROM ".$this->config["InvoiceTable"]." AS i 
								LEFT JOIN `user` AS u ON i.ManagerID=u.UserID 
							WHERE i.ContractID=".Connection::GetSQLString($contractList[$i]["ContractID"])." 
							ORDER BY i.Created ASC";
                $contractList[$i]["InvoiceList"] = $stmt->FetchList($query);
                // Оплаты
                $query = "SELECT p.PaymentID, p.Created, p.ManagerID, CONCAT(u.LastName, ' ', u.FirstName) AS ManagerTitle, p.Amount
							FROM ".$this->config["PaymentTable"]." AS p 
								LEFT JOIN `user` AS u ON p.ManagerID=u.UserID 
							WHERE p.ContractID=".Connection::GetSQLString($contractList[$i]["ContractID"])." 
							ORDER BY p.Created ASC";
                $contractList[$i]["PaymentList"] = $stmt->FetchList($query);
                // Возвраты
                $query = "SELECT p.PaybackID, p.Created, p.ManagerID, CONCAT(u.LastName, ' ', u.FirstName) AS ManagerTitle, p.Amount
							FROM ".$this->config["PaybackTable"]." AS p 
								LEFT JOIN `user` AS u ON p.ManagerID=u.UserID 
							WHERE p.ContractID=".Connection::GetSQLString($contractList[$i]["ContractID"])." 
							ORDER BY p.Created ASC";
                $contractList[$i]["PaybackList"] = $stmt->FetchList($query);
                // Акты
                $query = "SELECT a.ActID, a.Created, a.ManagerID, CONCAT(u.LastName, ' ', u.FirstName) AS ManagerTitle 
							FROM ".$this->config["ActTable"]." AS a 
								LEFT JOIN `user` AS u ON a.ManagerID=u.UserID 
							WHERE a.ContractID=".Connection::GetSQLString($contractList[$i]["ContractID"])." 
							ORDER BY a.Created ASC";
                $contractList[$i]["ActList"] = $stmt->FetchList($query);

                if(isset($this->config["UseSeason"]) && $this->config["UseSeason"])
                {
                    $query = "SELECT s.Title FROM `crm_season` AS s 
									JOIN ".$this->config["ContractTable"]."2season AS c2s ON c2s.SeasonID=s.SeasonID 
								WHERE c2s.ContractID=".Connection::GetSQLString($contractList[$i]["ContractID"]);
                    $contractList[$i]["SeasonList"] = $stmt->FetchList($query);
                }
            }

            // подгржуаем договора ЮР ЛИЦ в которых учавствуют дети
            $query = "SELECT c.*, k.Title, p.Amount AS PaidAmount,"
                ."c.Amount - p.Amount AS UnpaidAmount"
                . " FROM crm_legal_contract AS c"
                . " LEFT JOIN crm_legal AS k ON c.LegalID=k.LegalID"
                . " LEFT JOIN crm_legal_payment AS p ON c.ContractID=p.ContractID"
                . " WHERE ChildIDs LIKE '%"."\"".$item["EntityID"]."\""."%'";
            $contractListLegal = $stmt->FetchList($query);
            foreach ($contractListLegal as $key => $contract) {
                $query = 'SELECT SUM(Amount) as PaybackAmount FROM '.$this->config['LegalPaybackTable']
                    .' WHERE ContractID = ' . $contract['ContractID'];
                $row = $stmt->FetchRow($query);
                $contractListLegal[$key]['PaybackAmount'] = $row['PaybackAmount'];
                $contractListLegal[$key]['PaidAmount'] = $contractListLegal[$key]['PaidAmount'] - $row['PaybackAmount'];
                $contractListLegal[$key]['UnpaidAmount'] = $contractListLegal[$key]['Amount'] - $contractListLegal[$key]['PaidAmount'];
            }
            for($i = 0; $i < count($contractListLegal); $i++){
                $query = "SELECT i.InvoiceID, i.Created, i.ManagerID, CONCAT(u.LastName, ' ', u.FirstName) AS ManagerTitle, i.Amount
							FROM ".$this->config["LegalInvoiceTable"]." AS i 
								LEFT JOIN `user` AS u ON i.ManagerID=u.UserID 
							WHERE i.ContractID=".Connection::GetSQLString($contractListLegal[$i]["ContractID"])." 
							ORDER BY i.Created ASC";
                $contractListLegal[$i]["InvoiceList"] = $stmt->FetchList($query);

                $query = "SELECT p.PaymentID, p.Created, p.ManagerID, CONCAT(u.LastName, ' ', u.FirstName) AS ManagerTitle, p.Amount
							FROM ".$this->config["LegalPaymentTable"]." AS p 
								LEFT JOIN `user` AS u ON p.ManagerID=u.UserID 
							WHERE p.ContractID=".Connection::GetSQLString($contractList[$i]["ContractID"])." 
							ORDER BY p.Created ASC";
                $contractListLegal[$i]["PaymentList"] = $stmt->FetchList($query);

                $query = "SELECT s.Title FROM `crm_season` AS s JOIN ".$this->config["LegalContract"]
                    ."2season AS c2s ON c2s.SeasonID=s.SeasonID WHERE c2s.ContractID="
                    .Connection::GetSQLString($contractListLegal[$i]["ContractID"]);
                $contractListLegal[$i]["SeasonList"] = $stmt->FetchList($query);

                $query = "SELECT p.PaybackID, p.Created, p.ManagerID, CONCAT(u.LastName, ' ', u.FirstName) AS ManagerTitle, p.Amount
							FROM ".$this->config["LegalPaybackTable"]." AS p 
								LEFT JOIN `user` AS u ON p.ManagerID=u.UserID 
							WHERE p.ContractID=".Connection::GetSQLString($contractListLegal[$i]["ContractID"])." 
							ORDER BY p.Created ASC";
                $contractListLegal[$i]["PaybackList"] = $stmt->FetchList($query);

                $query = "SELECT a.ActID, a.Created, a.ManagerID, CONCAT(u.LastName, ' ', u.FirstName) AS ManagerTitle 
							FROM ".$this->config["LegalActTable"]." AS a 
								LEFT JOIN `user` AS u ON a.ManagerID=u.UserID 
							WHERE a.ContractID=".Connection::GetSQLString($contractListLegal[$i]["ContractID"])." 
							ORDER BY a.Created ASC";
                $contractListLegal[$i]["ActList"] = $stmt->FetchList($query);
            }

            // подгржуаем договора ШКОЛ в которых учавствуют дети
            $query = "SELECT c.*, k.Title, p.Amount AS PaidAmount,"
                ."c.Amount - p.Amount AS UnpaidAmount"
                . " FROM crm_school_contact_contract AS c"
                . " LEFT JOIN crm_school AS k ON c.SchoolID=k.SchoolID"
                . " LEFT JOIN crm_school_contact_payment AS p ON c.ContractID=p.ContractID"
                . " WHERE ChildIDs LIKE '%"."\"".$item["EntityID"]."\""."%'";
            $contractListSchool = $stmt->FetchList($query);
            foreach ($contractListSchool as $key => $contract) {
                $query = 'SELECT SUM(Amount) as PaybackAmount FROM '.$this->config['SchoolPaybackTable']
                    .' WHERE ContractID = ' . $contract['ContractID'];
                $row = $stmt->FetchRow($query);
                $contractListSchool[$key]['PaybackAmount'] = $row['PaybackAmount'];
                $contractListSchool[$key]['PaidAmount'] = $contractListSchool[$key]['PaidAmount'] - $row['PaybackAmount'];
                $contractListSchool[$key]['UnpaidAmount'] = $contractListSchool[$key]['Amount'] - $contractListSchool[$key]['PaidAmount'];
            }
            for($i = 0; $i < count($contractListSchool); $i++){
                $query = "SELECT i.InvoiceID, i.Created, i.ManagerID, CONCAT(u.LastName, ' ', u.FirstName) AS ManagerTitle, i.Amount
							FROM ".$this->config["SchoolInvoiceTable"]." AS i 
								LEFT JOIN `user` AS u ON i.ManagerID=u.UserID 
							WHERE i.ContractID=".Connection::GetSQLString($contractListSchool[$i]["ContractID"])." 
							ORDER BY i.Created ASC";
                $contractListSchool[$i]["InvoiceList"] = $stmt->FetchList($query);

                $query = "SELECT p.PaymentID, p.Created, p.ManagerID, CONCAT(u.LastName, ' ', u.FirstName) AS ManagerTitle, p.Amount
							FROM ".$this->config["SchoolPaymentTable"]." AS p 
								LEFT JOIN `user` AS u ON p.ManagerID=u.UserID 
							WHERE p.ContractID=".Connection::GetSQLString($contractListSchool[$i]["ContractID"])." 
							ORDER BY p.Created ASC";
                $contractListSchool[$i]["PaymentList"] = $stmt->FetchList($query);

                $query = "SELECT s.Title FROM `crm_season` AS s JOIN ".$this->config["SchoolContract"]
                    ."2season AS c2s ON c2s.SeasonID=s.SeasonID WHERE c2s.ContractID="
                    .Connection::GetSQLString($contractListSchool[$i]["ContractID"]);
                $contractListSchool[$i]["SeasonList"] = $stmt->FetchList($query);

                $query = "SELECT p.PaybackID, p.Created, p.ManagerID, CONCAT(u.LastName, ' ', u.FirstName) AS ManagerTitle, p.Amount
							FROM ".$this->config["SchoolPaybackTable"]." AS p 
								LEFT JOIN `user` AS u ON p.ManagerID=u.UserID 
							WHERE p.ContractID=".Connection::GetSQLString($contractListSchool[$i]["ContractID"])." 
							ORDER BY p.Created ASC";
                $contractListSchool[$i]["PaybackList"] = $stmt->FetchList($query);

                $query = "SELECT a.ActID, a.Created, a.ManagerID, CONCAT(u.LastName, ' ', u.FirstName) AS ManagerTitle 
							FROM ".$this->config["SchoolActTable"]." AS a 
								LEFT JOIN `user` AS u ON a.ManagerID=u.UserID 
							WHERE a.ContractID=".Connection::GetSQLString($contractListSchool[$i]["ContractID"])." 
							ORDER BY a.Created ASC";
                $contractListSchool[$i]["ActList"] = $stmt->FetchList($query);
            }

            $contractList = array_merge($contractList, $contractListLegal, $contractListSchool);

            $item[$this->name."List"] = $contractList;

            $script = '<script type="text/javascript">$(document).ready(function(){';
            $script .= 'InitContractTableControl($("#'.$this->name.'"));';
            $script .= "});</script>";
            $item[$this->name."ControlHTML"] = $script;

            $script = '<script type="text/javascript">$(document).ready(function(){';
            $script .= '$("#SeasonID").select2({placeholder:"",allowClear: true}).on("select2-open", function() {$(this).data("select2").results.addClass("overflow-hidden").perfectScrollbar();});';
            $script .= "});</script>";
            $item["SeasonIDControlHTML"] = $script;
        }
    }
}

class FinanceExtendedViewComponent extends BaseComponent
{
	function PrepareBeforeShow(&$item, $user)
	{
		if(isset($item["EntityID"]))
		{
			$stmt = GetStatement();
			$query = "SELECT c.ContractID, c.Created, c.ContractType, c.ManagerID, CONCAT(u.LastName, ' ', u.FirstName) AS ManagerTitle, c.Amount,  
							SUM(IF(p.PaymentID IS NOT NULL, p.Amount, 0)) AS PaidAmount, c.Amount - SUM(IF(p.PaymentID IS NOT NULL, p.Amount, 0)) AS UnpaidAmount,
							LastDayForPay,
							IF(IsNeedStamp = 1, 'Да', 'Нет') as IsNeedStamp
						FROM ".$this->config["ContractTable"]." AS c 
							LEFT JOIN ".$this->config["PaymentTable"]." AS p ON c.ContractID=p.ContractID 
							LEFT JOIN `user` AS u ON c.ManagerID=u.UserID ";
			if(isset($this->config["Type"]) && $this->config["Type"] == "linked")
			{
				$query .= "LEFT JOIN ".$this->config["LinkTable"]." AS l ON c.".$this->config["FromField"]."=l.".$this->config["ToField"]." 
						WHERE l.".$this->config["TargetField"]."=".Connection::GetSQLString((isset($this->config["ID"]) && $this->config["ID"] ? $item[$this->config["ID"]] : $item["EntityID"]));
			}
			else
			{
				$query .= "WHERE c.".$this->config["TargetField"]."=".Connection::GetSQLString((isset($this->config["ID"]) && $this->config["ID"] ? $item[$this->config["ID"]] : $item["EntityID"]));
			}
			$query .= "GROUP BY c.ContractID ORDER BY c.Created DESC";
			$contractList = $stmt->FetchList($query);

			//В прошлом запросе джоином не выдает пэйбэки нормально
			foreach ($contractList as $key => $contract) {
				$query = 'SELECT SUM(Amount) as PaybackAmount FROM '.$this->config['PaybackTable'].' WHERE ContractID = ' . $contract['ContractID'];

				$row = $stmt->FetchRow($query);
				$contractList[$key]['PaybackAmount'] = $row['PaybackAmount'];
				$contractList[$key]['PaidAmount'] = $contractList[$key]['PaidAmount'] - $row['PaybackAmount'];
				$contractList[$key]['UnpaidAmount'] = $contractList[$key]['Amount'] - $contractList[$key]['PaidAmount'];
			}

			for($i = 0; $i < count($contractList); $i++)
			{
				$query = "SELECT i.InvoiceID, i.Created, i.ManagerID, CONCAT(u.LastName, ' ', u.FirstName) AS ManagerTitle, i.Amount
							FROM ".$this->config["InvoiceTable"]." AS i 
								LEFT JOIN `user` AS u ON i.ManagerID=u.UserID 
							WHERE i.ContractID=".Connection::GetSQLString($contractList[$i]["ContractID"])." 
							ORDER BY i.Created ASC";
				$contractList[$i]["InvoiceList"] = $stmt->FetchList($query);
				
				$query = "SELECT p.PaymentID, p.Created, p.ManagerID, CONCAT(u.LastName, ' ', u.FirstName) AS ManagerTitle, p.Amount
							FROM ".$this->config["PaymentTable"]." AS p 
								LEFT JOIN `user` AS u ON p.ManagerID=u.UserID 
							WHERE p.ContractID=".Connection::GetSQLString($contractList[$i]["ContractID"])." 
							ORDER BY p.Created ASC";
				$contractList[$i]["PaymentList"] = $stmt->FetchList($query);

				$query = "SELECT p.PaybackID, p.Created, p.ManagerID, CONCAT(u.LastName, ' ', u.FirstName) AS ManagerTitle, p.Amount
							FROM ".$this->config["PaybackTable"]." AS p 
								LEFT JOIN `user` AS u ON p.ManagerID=u.UserID 
							WHERE p.ContractID=".Connection::GetSQLString($contractList[$i]["ContractID"])." 
							ORDER BY p.Created ASC";
				$contractList[$i]["PaybackList"] = $stmt->FetchList($query);
				
				$query = "SELECT a.ActID, a.Created, a.ManagerID, CONCAT(u.LastName, ' ', u.FirstName) AS ManagerTitle 
							FROM ".$this->config["ActTable"]." AS a 
								LEFT JOIN `user` AS u ON a.ManagerID=u.UserID 
							WHERE a.ContractID=".Connection::GetSQLString($contractList[$i]["ContractID"])." 
							ORDER BY a.Created ASC";
				$contractList[$i]["ActList"] = $stmt->FetchList($query);
				
				if(isset($this->config["UseSeason"]) && $this->config["UseSeason"])
				{
					$query = "SELECT s.Title FROM `crm_season` AS s 
									JOIN ".$this->config["ContractTable"]."2season AS c2s ON c2s.SeasonID=s.SeasonID 
								WHERE c2s.ContractID=".Connection::GetSQLString($contractList[$i]["ContractID"]);
					$contractList[$i]["SeasonList"] = $stmt->FetchList($query);
				}
			}
			
			$item[$this->name."List"] = $contractList;
			
			$script = '<script type="text/javascript">$(document).ready(function(){';
			$script .= 'InitContractTableControl($("#'.$this->name.'"));';
			$script .= "});</script>";
			$item[$this->name."ControlHTML"] = $script;
			
			$script = '<script type="text/javascript">$(document).ready(function(){';
			$script .= '$("#SeasonID").select2({placeholder:"",allowClear: true}).on("select2-open", function() {$(this).data("select2").results.addClass("overflow-hidden").perfectScrollbar();});';
			$script .= "});</script>";
			$item["SeasonIDControlHTML"] = $script;
		}
	}
}

?>