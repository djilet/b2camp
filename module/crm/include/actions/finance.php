<?php 
require_once(dirname(__FILE__)."/../action.php");
es_include("mpdf60/mpdf.php");

class FinanceAction extends BaseAction
{
	function DoAction($request, $user)
	{
		switch($this->name)
		{
			case "AddContract": 
				$this->ActionAddContract($request, $user);
				break;
			case "AddContractExtended": 
				$this->ActionAddContractExtended($request, $user);
				break;
			case "AddInvoice": 
				$this->ActionAddInvoice($request, $user);
				break;
			case "AddPayment": 
				$this->ActionAddPayment($request, $user);
				break;
			case "AddPayback": 
				$this->ActionAddPayback($request, $user);
				break;
			case "AddAct":
				$this->ActionAddAct($request, $user);
				break;
			case "AddCommission":
				$this->ActionAddCommission($request);
				break;
			case "RemoveContract":
			case "RemoveInvoice":
			case "RemovePayment":
			case "RemovePayback":
			case "RemoveAct":
				$this->Remove($request, $user);
				break;
			case "GetContractPDF":
				$this->GetContractPDF($request, $user);
				break;
			case "GetPaymentPDF":
				$this->GetPaymentPDF($request, $user);
				break;
			case "GetStaffPaymentPDF":
				$this->GetStaffPaymentPDF($request, $user);
				break;
            case "GetStaffPaybackPDF":
                $this->GetStaffPaymentPDF($request, $user);
                break;
			case "GetInvoicePDF":
				$this->GetInvoicePDF($request, $user);
				break;
			case "GetActPDF":
				$this->GetActPDF($request, $user);
				break;
		}
	}

	private function ActionAddContractExtended($request, $user)
	{
		if(!$request->GetIntProperty($this->actionConfig["KeyField"]) > 0)
			$this->AddError("finance-contract-entity-id-required", "crm");
		if(isset($this->actionConfig["UseSeason"]) && $this->actionConfig["UseSeason"] && (!is_array($request->GetProperty("SeasonID")) || count(array_filter($request->GetProperty("SeasonID"))) == 0))
			$this->AddError("finance-contract-season-required", "crm");
		if($request->IsPropertySet("ContractType") && !in_array($request->GetProperty("ContractType"), array("discipular", "employment")))
			$this->AddError("finance-contract-type-required", "crm");
		if(!$request->ValidateInt('TourPrice'))
			$this->AddError("finance-contract-tour-price-required", "crm");
		if(!$request->ValidateInt('CoursePrice'))
			$this->AddError("finance-contract-course-price-required", "crm");
		if(!$request->ValidateInt('TourCount'))
			$this->AddError("finance-contract-tour-count-required", "crm");
		if(!$request->ValidateInt('CourseCount'))
			$this->AddError("finance-contract-course-count-required", "crm");
		if( count(explode(',',$request->GetProperty('childChoose')[0])) > $request->GetIntProperty('TourCount') )
			$this->AddError("finance-contract-count-not-true", "crm");
		if (!$request->GetProperty("ContactID")){
			$this->AddError("finance-contract-contact-is-empty", "crm");
		}

		$lastDayForPay = $request->GetProperty('LastDayForPay');
		if(empty($lastDayForPay))
			$this->AddError("finance-contract-lastday-required", "crm");

		$isNeedStamp = 0;
		if ($request->IsPropertySet("IsNeedStamp")) {
			$isNeedStamp = 1;
		}
		
		if($this->HasErrors())
			return false;
		
		$user = new User();
		$user->LoadBySession();
		$stmt = GetStatement();

		//Для ЮР лиц
		if (isset($this->actionConfig['ContactField'])) {
			$contactFielfCond = ", " . $this->actionConfig['ContactField'] . "=" . $request->GetProperty("ContactID");
		}

		$query = "INSERT INTO ".$this->actionConfig["Table"].
		" SET 	Created=".Connection::GetSQLString(date("Y-m-d H:i:s")).", 
				ManagerID=".$user->GetPropertyForSQL("UserID").", 
				ContractType=".$request->GetPropertyForSQL("ContractType").",
				TourPrice=".$request->GetPropertyForSQL("TourPrice").",
				TourCount=".$request->GetPropertyForSQL("TourCount").",
				CoursePrice=".$request->GetPropertyForSQL("CoursePrice").",
				CourseCount=".$request->GetPropertyForSQL("CourseCount").",
				LastDayForPay='".date('Y-m-d', strtotime($request->GetProperty("LastDayForPay")))."',
				IsNeedStamp=".$isNeedStamp.",
				ChildIDs='".json_encode(explode(',',$request->GetProperty('childChoose')[0]))."',
				".$this->actionConfig["KeyField"]."=".$request->GetPropertyForSQL($this->actionConfig["KeyField"]).", 
				Amount=".$request->GetIntProperty("Amount") . $contactFielfCond;
		if($request->GetProperty('entity')=='school'){
		    $query.=', SchoolID='.$request->GetProperty('SchoolID');
        }

		if($stmt->Execute($query))
		{
			$contractID = $stmt->GetLastInsertID();
			if(isset($this->actionConfig["UseSeason"]) && $this->actionConfig["UseSeason"])
			{
				foreach ($request->GetProperty("SeasonID") as $seasonID)
				{
					$query = "INSERT INTO ".$this->actionConfig["Table"]."2season SET ContractID=".Connection::GetSQLString($contractID).", 
																				SeasonID=".Connection::GetSQLString($seasonID);
					$stmt->Execute($query);
				}
			}
			$this->AddMessage("finance-contract-added", "crm");
		}
		else
		{
			$this->AddError("sql-error");	
		}
	}
	
	private function ActionAddContract($request, $user)
	{
		if(!$request->GetIntProperty($this->actionConfig["KeyField"]) > 0)
			$this->AddError("finance-contract-entity-id-required", "crm");
		if(!$request->GetIntProperty("Amount") > 0)
			$this->AddError("finance-contract-amount-required", "crm");
		if(isset($this->actionConfig["UseSeason"]) && $this->actionConfig["UseSeason"] && (!is_array($request->GetProperty("SeasonID")) || count(array_filter($request->GetProperty("SeasonID"))) == 0))
			$this->AddError("finance-contract-season-required", "crm");
		if($request->IsPropertySet("ContractType") && !in_array($request->GetProperty("ContractType"), array("discipular", "employment")))
			$this->AddError("finance-contract-type-required", "crm");
		
		if($this->HasErrors())
			return false;
		
		$user = new User();
		$user->LoadBySession();
		$stmt = GetStatement();
		$isNeedStamp = $request->IsPropertySet("IsNeedStamp") ? ", IsNeedStamp=".$request->GetIntProperty("IsNeedStamp"):"";
		$lastDayForPay = $request->IsPropertySet("LastDayForPay") ? ", LastDayForPay=".Connection::GetSQLString(date("Y-m-d", strtotime($request->GetProperty("LastDayForPay")))): "";
		$query = "INSERT INTO ".$this->actionConfig["Table"].
		" SET 	Created=".Connection::GetSQLString(date("Y-m-d H:i:s")).", 
				ManagerID=".$user->GetPropertyForSQL("UserID").", 
				ContractType=".$request->GetPropertyForSQL("ContractType").
				$isNeedStamp.
                $lastDayForPay.
				",".$this->actionConfig["KeyField"]."=".$request->GetPropertyForSQL($this->actionConfig["KeyField"]).", 
				Amount=".$request->GetIntProperty("Amount");

		if($stmt->Execute($query))
		{
			$contractID = $stmt->GetLastInsertID();
			if(isset($this->actionConfig["UseSeason"]) && $this->actionConfig["UseSeason"])
			{
				foreach ($request->GetProperty("SeasonID") as $seasonID)
				{
					$query = "INSERT INTO ".$this->actionConfig["Table"]."2season SET ContractID=".Connection::GetSQLString($contractID).", 
																				SeasonID=".Connection::GetSQLString($seasonID);
					$stmt->Execute($query);
				}
			}
			$this->AddMessage("finance-contract-added", "crm");
		}
		else
		{
			$this->AddError("sql-error");	
		}
	}
	
	private function ActionAddInvoice($request, $user)
	{
		$stmt = GetStatement();
		if(!$request->GetIntProperty("ContractID") > 0)
			$this->AddError("finance-invoice-contract-required", "crm");
		if(!$request->GetIntProperty("Amount") > 0)
			$this->AddError("finance-invoice-amount-required", "crm");
		
		if(!$this->HasErrors())
		{
			$query = "SELECT c.Amount - SUM(IF(i.InvoiceID IS NOT NULL, i.Amount, 0)) 
						FROM ".$this->actionConfig["ContractTable"]." AS c 
							LEFT JOIN ".$this->actionConfig["Table"]." AS i ON i.ContractID=c.ContractID
						WHERE c.ContractID=".$request->GetPropertyForSQL("ContractID")." 
						GROUP BY c.ContractID";
			$uninvoicedAmount = $stmt->FetchField($query);
			if($request->GetIntProperty("Amount") > $uninvoicedAmount)
				$this->AddError("finance-invoice-amount-exceed", "crm", array("UninvoicedAmount" => $uninvoicedAmount));
		}
			
		if($this->HasErrors())
			return false;
		
		$user = new User();
		$user->LoadBySession();
		$query = "INSERT INTO ".$this->actionConfig["Table"]." SET 	Created=".Connection::GetSQLString(date("Y-m-d H:i:s")).", 
																	ManagerID=".$user->GetPropertyForSQL("UserID").", 
																	ContractID=".$request->GetPropertyForSQL("ContractID").",
																	Amount=".$request->GetIntProperty("Amount");
		if($stmt->Execute($query))
		{
			$this->AddMessage("finance-invoice-added", "crm");
		}
		else
		{
			$this->AddError("sql-error");	
		}
	}
	
	private function ActionAddPayment($request, $user)
	{
		$stmt = GetStatement();
		if(!$request->GetIntProperty("ContractID") > 0)
			$this->AddError("finance-payment-contract-required", "crm");
		if(!$request->GetIntProperty("Amount") > 0)
			$this->AddError("finance-payment-amount-required", "crm");
		
		if(!$this->HasErrors())
		{
			$query = "SELECT Amount
						FROM ".$this->actionConfig["ContractTable"]."
						WHERE ContractID=".$request->GetPropertyForSQL("ContractID");

			$query1 = "SELECT SUM(Amount) 
						FROM ".$this->actionConfig['PaybackTable']." WHERE ContractID=".$request->GetPropertyForSQL("ContractID");

			$query2 = "SELECT SUM(Amount) 
						FROM ".$this->actionConfig['Table']." WHERE ContractID=".$request->GetPropertyForSQL("ContractID");

			$amount        = $stmt->FetchField($query);
			$paybackAmount = $stmt->FetchField($query1);
			$paidAmount    = $stmt->FetchField($query2);

			$paidAmount   = $paidAmount - $paybackAmount;
			$unpaidAmount = $amount - $paidAmount;

			/*if($request->GetIntProperty("Amount") > $unpaidAmount)
				$this->AddError("finance-payment-amount-exceed", "crm", array("UnpaidAmount" => $unpaidAmount));*/
		}
		
		if($this->HasErrors())
			return false;

		//Достаем ФИО или Контрагента
		$titleCondition = '';
		$entity = $request->GetProperty('entity');

		switch ($entity) {
			case 'child':
				$query = 'SELECT LastName, FirstName, MiddleName FROM crm_parent WHERE ChildID = ' . $request->GetProperty('EntityID');
				$row = $stmt->FetchRow($query);

				if (!is_null($row)) {
					$titleCondition .= 'Lastname = "' . $row['LastName'] . '", ';
					$titleCondition .= 'Firstname = "' . $row['FirstName'] . '", ';
					$titleCondition .= 'Secondname = "' . $row['MiddleName'] . '", ';
				}
				break;
			case 'staff':
				$query = 'SELECT user.LastName, user.FirstName, user.MiddleName FROM crm_staff AS t LEFT JOIN user USING(UserID) WHERE StaffID=' . $request->GetProperty('EntityID');
				$row = $stmt->FetchRow($query);

				if (!is_null($row)) {
					$titleCondition .= 'Lastname = "' . $row['LastName'] . '", ';
					$titleCondition .= 'Firstname = "' . $row['FirstName'] . '", ';
					$titleCondition .= 'Secondname = "' . $row['MiddleName'] . '", ';
				}
				break;
			case 'school':
				$query = 'SELECT Title FROM crm_school WHERE SchoolID = ' . $request->GetProperty('EntityID');
				$row = $stmt->FetchRow($query);

				if (!is_null($row)) {
					$titleCondition .= 'Contractor = "' . $row['Title'] . '", ';
				}
				break;
			case 'legal':
				$query = 'SELECT Title FROM crm_legal WHERE LegalID = ' . $request->GetProperty('EntityID');
				$row = $stmt->FetchRow($query);

				if (!is_null($row)) {
					$titleCondition .= 'Contractor = "' . $row['Title'] . '", ';
				}
				break;
			default:
				break;
		}

		$user = new User();
		$user->LoadBySession();
		$query = "INSERT INTO ".$this->actionConfig["Table"]." SET 	Created=".Connection::GetSQLString(date("Y-m-d H:i:s")).", 
																	ManagerID=".$user->GetPropertyForSQL("UserID").", 
																	ContractID=".$request->GetPropertyForSQL("ContractID").",
																	Amount=".$request->GetIntProperty("Amount");

		$bookQuery = 'INSERT INTO crm_bookkeeping SET '.$titleCondition.'
													Date='.Connection::GetSQLString(date("Y-m-d H:i:s")).', 
													DocumentNumber='.$request->GetPropertyForSQL("ContractID").',
													`Check`='.$request->GetPropertyForSQL("BillID").',
													ArticleType='.$request->GetPropertyForSQL("ArticleType").',
													ArticleID='.$request->GetPropertyForSQL("ArticleID").',
													ManagerID='.$user->GetPropertyForSQL("UserID").', 
													Base='.$request->GetPropertyForSQL("Reason").',
													Amount='.$request->GetIntProperty("Amount");

		if($stmt->Execute($query) && $stmt->Execute($bookQuery))
		{
			$this->AddMessage("finance-payment-added", "crm");
		}
		else
		{
			$this->AddError("sql-error");	
		}
	}

	private function ActionAddPayback($request, $user)
	{
		$stmt = GetStatement();
		if(!$request->GetIntProperty("ContractID") > 0)
			$this->AddError("finance-payment-contract-required", "crm");
		if(!$request->GetIntProperty("Amount") > 0)
			$this->AddError("finance-payment-amount-required", "crm");
		
		if(!$this->HasErrors())
		{
			$query1 = "SELECT SUM(Amount) 
						FROM ".$this->actionConfig['PaymentTable']." WHERE ContractID=".$request->GetPropertyForSQL("ContractID");

			$query2 = "SELECT SUM(Amount) 
						FROM ".$this->actionConfig['Table']." WHERE ContractID=".$request->GetPropertyForSQL("ContractID");

			$paidAmount    = $stmt->FetchField($query1);
			$paybackAmount = $stmt->FetchField($query2);
			$paidAmount    = $paidAmount - $paybackAmount;

			/*if($request->GetIntProperty("Amount") > $paidAmount)
				$this->AddError("finance-payback-amount-exceed", "crm", array("paidAmount" => $paidAmount));*/
		}
		
		if($this->HasErrors())
			return false;

		//Достаем ФИО или Контрагента
		$titleCondition = '';
		$entity = $request->GetProperty('entity');

		switch ($entity) {
			case 'child':
				$query = 'SELECT LastName, FirstName, MiddleName FROM crm_parent WHERE ChildID = ' . $request->GetProperty('EntityID');
				$row = $stmt->FetchRow($query);

				if (!is_null($row)) {
					$titleCondition .= 'Lastname = "' . $row['LastName'] . '", ';
					$titleCondition .= 'Firstname = "' . $row['FirstName'] . '", ';
					$titleCondition .= 'Secondname = "' . $row['MiddleName'] . '", ';
				}
				break;
			case 'staff':
				$query = 'SELECT user.LastName, user.FirstName, user.MiddleName FROM crm_staff AS t LEFT JOIN user USING(UserID) WHERE StaffID=' . $request->GetProperty('EntityID');
				$row = $stmt->FetchRow($query);

				if (!is_null($row)) {
					$titleCondition .= 'Lastname = "' . $row['LastName'] . '", ';
					$titleCondition .= 'Firstname = "' . $row['FirstName'] . '", ';
					$titleCondition .= 'Secondname = "' . $row['MiddleName'] . '", ';
				}
				break;
			case 'school':
				$query = 'SELECT Title FROM crm_school WHERE SchoolID = ' . $request->GetProperty('EntityID');
				$row = $stmt->FetchRow($query);

				if (!is_null($row)) {
					$titleCondition .= 'Contractor = "' . $row['Title'] . '", ';
				}
				break;
			case 'legal':
				$query = 'SELECT Title FROM crm_legal WHERE LegalID = ' . $request->GetProperty('EntityID');
				$row = $stmt->FetchRow($query);

				if (!is_null($row)) {
					$titleCondition .= 'Contractor = "' . $row['Title'] . '", ';
				}
				break;
			default:
				break;
		}

		$user = new User();
		$user->LoadBySession();

		$query = "INSERT INTO ".$this->actionConfig["Table"]." SET Created=".Connection::GetSQLString(date("Y-m-d H:i:s")).", 
																	ManagerID=".$user->GetPropertyForSQL("UserID").", 
																	ContractID=".$request->GetPropertyForSQL("ContractID").",
																	Amount=".$request->GetIntProperty("Amount");

		$bookQuery = 'INSERT INTO crm_bookkeeping SET '.$titleCondition.'
													Date='.Connection::GetSQLString(date("Y-m-d H:i:s")).', 
													DocumentNumber='.$request->GetPropertyForSQL("ContractID").',
													`Check`='.$request->GetPropertyForSQL("BillID").',
													ArticleType='.$request->GetPropertyForSQL("ArticleType").',
													ArticleID='.$request->GetPropertyForSQL("ArticleID").',
													ManagerID='.$user->GetPropertyForSQL("UserID").', 
													Base='.$request->GetPropertyForSQL("Reason").',
													Amount='.$request->GetIntProperty("Amount");

		if($stmt->Execute($query) && $stmt->Execute($bookQuery))
		{
		    if ($entity == 'staff')
                $this->AddMessage("finance-payback-staff-added", "crm");
            else
		        $this->AddMessage("finance-payback-added", "crm");
		}
		else
		{
			$this->AddError("sql-error");	
		}
	}
	
	private function ActionAddAct($request, $user)
	{
		if(!$request->GetIntProperty("ContractID") > 0)
			$this->AddError("finance-act-contract-required", "crm");
		
		if($this->HasErrors())
			return false;
		
		$user = new User();
		$user->LoadBySession();
		$stmt = GetStatement();
		$query = "INSERT INTO ".$this->actionConfig["Table"]." SET 	Created=".Connection::GetSQLString(date("Y-m-d H:i:s")).", 
																	ManagerID=".$user->GetPropertyForSQL("UserID").", 
																	ContractID=".$request->GetPropertyForSQL("ContractID");
		if($stmt->Execute($query))
		{
			$this->AddMessage("finance-act-added", "crm");
		}
		else
		{
			$this->AddError("sql-error");
		}
	}

	private function ActionAddCommission($request)
	{

		if($this->HasErrors())
			return false;

		$stmt = GetStatement();
		$query = "UPDATE ".$this->actionConfig["Table"]." 
			SET Commission=".$request->GetPropertyForSQL("Commission")." 
			WHERE LegalID=".$request->GetProperty('EntityID');
		if($stmt->Execute($query))
		{
			$this->AddMessage("finance-act-commission", "crm");
		}
		else
		{
			$this->AddError("sql-error");
		}
	}
	
	private function Remove($request, $user)
	{
		if(isset($this->actionConfig["Remove"]) && is_array($this->actionConfig["Remove"]))
		{
			$stmt = GetStatement();
			
			//remove bookkeeping records "linked" with contract
			if($request->GetProperty("Action") == "RemoveContract")
			{
				if(isset($this->actionConfig['PaymentTable']))
				{
					$query = "SELECT Amount FROM ".$this->actionConfig['PaymentTable']." WHERE ContractID=".$request->GetPropertyForSQL("ContractID");
					$amountList = $stmt->FetchList($query);
					foreach ($amountList as $amount)
					{
						$query = "DELETE FROM crm_bookkeeping
									WHERE DocumentNumber = " . $request->GetPropertyForSQL("ContractID") . "
										AND Amount = " . Connection::GetSQLString($amount['Amount'])."
										AND ArticleType=1";
						$stmt->Execute($query);
					}
				}
				if(isset($this->actionConfig['PaybackTable']))
				{
					$query = "SELECT Amount FROM ".$this->actionConfig['PaybackTable']." WHERE ContractID=".$request->GetPropertyForSQL("ContractID");
					$amountList = $stmt->FetchList($query);
					foreach ($amountList as $amount)
					{
						$query = "DELETE FROM crm_bookkeeping
									WHERE DocumentNumber = " . $request->GetPropertyForSQL("ContractID") . "
										AND Amount = " . Connection::GetSQLString($amount['Amount'])."
										AND ArticleType=2";
						$stmt->Execute($query);
					}
				}
			}
			
			foreach ($this->actionConfig["Remove"] as $remove)
			{
				if (in_array($request->getProperty('Action'), array('RemovePayment', 'RemovePayback'))) {
					$query = "SELECT ContractID, Amount FROM ".$remove["Table"]." WHERE ".$remove["Key"]."=".$request->GetPropertyForSQL($remove["Key"]);
					$row = $stmt->FetchRow($query);
					if ($row) 
					{
						$articleType = ($request->getProperty('Action') == 'RemovePayment') ? 1 : 2;

						$query = "DELETE FROM crm_bookkeeping WHERE DocumentNumber = " . $row['ContractID'] . 
							" AND Amount = " . $row['Amount'] . " AND ArticleType = " . $articleType;

						$stmt->Execute($query);
					}
				}

				$query = "DELETE FROM ".$remove["Table"]." WHERE ".$remove["Key"]."=".$request->GetPropertyForSQL($remove["Key"]);
				$stmt->Execute($query);
			}
		}
	}

	private function GetStaffPaymentPDF($request, $user)
	{
		$page = new PopupPage("crm", true);
		$content = $page->Load($this->actionConfig["Template"]);

		$stmt = GetStatement();

		$query = "SELECT 
			client.".$this->actionConfig['ClientKey']." as ClientKey, 
			phone.Phone as Phone,
			CONCAT(phone.LastName, ' ', phone.FirstName, ' ', phone.MiddleName) as ClientName,
			CONCAT(phone.LastName, ' ',LEFT(phone.FirstName, 1), '. ', LEFT(phone.MiddleName, 1),'.') as ClientInitials,
			CONCAT('г. ',phone.City,', ул. ',phone.Street,', ',phone.House,', кв. ',phone.Flat) as ClientAddress,
			phone.DOB as ClientDOB,
			client.Passport as ClientPassport,
			payment.Amount as Amount,
			payment.ContractID as ContractId,
			payment.ManagerID as ManagerId,
			payment.Created as Paydate
				FROM ".$this->actionConfig['Table']." as payment
			LEFT JOIN ".$this->actionConfig['ContractTable']." as contract ON payment.ContractID = contract.ContractID
			LEFT JOIN ".$this->actionConfig['ClientTable']." as client ON client.".$this->actionConfig['ClientKey']." = contract.".$this->actionConfig['ClientKey']."
			LEFT JOIN ".$this->actionConfig['ClientPhoneTable']." as phone ON client.".$this->actionConfig['ClientPhoneKey']." = phone.".$this->actionConfig['ClientPhoneKey']."
		
			WHERE payment.".$this->actionConfig['TableKey']." = " . $request->GetProperty($this->actionConfig['TableKey']);

		$row = $stmt->FetchRow($query);

		$contractId = $row['ContractId'];
		$amount    = $row['Amount'];
		$managerId  = $row['ManagerId'];
		$paydate    = $row['Paydate'];

		$month = date('F', strtotime($paydate));
		$monthName = GetTranslation('date-'.$month);

		$content->setVar('PayDay', date('d', strtotime($paydate)));
		$content->setVar('PayMonth', $monthName);
		$content->setVar('PayYear', date('Y', strtotime($paydate)));
		$content->setVar('ClientName', $row['ClientName']);
        $content->setVar('ClientInitials', $row['ClientInitials']);
		$content->setVar('Phone', $row['Phone']);
        $content->setVar('ClientDOB', $row['ClientDOB']);
        $content->setVar('ClientPassport', $row['ClientPassport']);
        $content->setVar('ClientAddress', $row['ClientAddress']);
        $content->setVar('ClientDOB', $row['ClientDOB']);
		$content->setVar('Amount', $amount);
		$content->setVar('AmountString', NumToStr($amount));
		$content->setVar('ContractId', $row['ContractId']);
		$content->setVar('ManagerId', $row['ManagerId']);

		$query = "SELECT CONCAT(LastName, ' ', FirstName, ' ', MiddleName) as Manager
					FROM user
					WHERE UserID = " . $managerId;

		$row = $stmt->FetchRow($query);
		$content->setVar('Manager', $row['Manager']);

		$query = "SELECT name FROM crm_directory WHERE DirectoryID = (SELECT ArticleID FROM crm_bookkeeping 
					WHERE DocumentNumber = ".$contractId." AND Amount = ".$amount." AND ArticleType = 1)";

		$article = $stmt->fetchField($query);
		$content->setVar('Article', $article);

		$mpdf = new mPDF('utf-8', 'A4', '11.5', '', 10, 10, 7, 7, 10, 10);
		$stylesheet = file_get_contents(PROJECT_DIR."module/crm/template/css/payment.css");

		if($request->GetProperty("Print"))
		{
			$mpdf->SetJS('this.print();');
		}
		$mpdf->WriteHTML($stylesheet, 1);
		$mpdf->list_indent_first_level = 0; 
		$mpdf->WriteHTML($page->Grab($content), 2);
		if($request->GetProperty("ToMailingAttachment"))
		{
			$mpdf->Output(PROJECT_DIR.'var/data/mailing/attachment/payment_'.$request->GetProperty("entity").'_'.$actInfo["ActID"].'.pdf', 'F');
		}
		else
		{
			$mpdf->Output('payment.pdf', 'I');	
		}
		exit();
	}

	private function GetPaymentPDF($request, $user)
	{
		$page = new PopupPage("crm", true);
		$content = $page->Load($this->actionConfig["Template"]);

		$stmt = GetStatement();

		if ($request->GetProperty('entity') == 'child') {
			$query = "SELECT 
				client.".$this->actionConfig['ClientKey']." as ClientKey, 
				CONCAT('7 (', phone.prefix, ') ', phone.number) as Phone,
				CONCAT(client.LastName, ' ', client.FirstName, ' ', client.MiddleName) as ClientName,
				payment.PaymentID as PaymentId,
				payment.Amount as Amount,
				payment.ContractID as ContractId,
				payment.ManagerID as ManagerId,
				payment.Created as Paydate
			FROM ".$this->actionConfig['Table']." as payment
				LEFT JOIN ".$this->actionConfig['ContractTable']." as contract ON payment.ContractID = contract.ContractID
				LEFT JOIN ".$this->actionConfig['ClientTable']." as 
				client ON client.".$this->actionConfig['ClientKey']." = contract.".$this->actionConfig['ClientKey']."
				LEFT JOIN ".$this->actionConfig['ClientPhoneTable']." as phone ON client.".$this->actionConfig['ClientKey']." = phone.".$this->actionConfig['ClientPhoneKey']."
				WHERE payment.PaymentID = " . $request->GetProperty('PaymentID');
		} elseif ($request->GetProperty('entity') == 'legal') {
			$query = "SELECT 
				client.".$this->actionConfig['ClientKey']." as ClientKey, 
				CONCAT('7 (', phone.prefix, ') ', phone.number) as Phone,
				CONCAT(client.LastName, ' ', client.FirstName, ' ', client.MiddleName) as ClientName,
				payment.PaymentID as PaymentId,
				payment.Amount as Amount,
				payment.ContractID as ContractId,
				payment.ManagerID as ManagerId,
				payment.Created as Paydate
			FROM ".$this->actionConfig['Table']." as payment
				LEFT JOIN ".$this->actionConfig['ContractTable']." as contract ON payment.ContractID = contract.ContractID
				LEFT JOIN ".$this->actionConfig['ClientTable']." as 
				client ON client.".$this->actionConfig['ClientKey']." = contract.".$this->actionConfig['ClientKey']."
				LEFT JOIN ".$this->actionConfig['ClientPhoneTable']." as phone ON client.".$this->actionConfig['ClientPhoneKey']." = phone.".$this->actionConfig['ClientPhoneKey']."
				WHERE payment.PaymentID = " . $request->GetProperty('PaymentID');
		} elseif ($request->GetProperty('entity') == 'school') {
			$query = "SELECT 
				client.".$this->actionConfig['ClientKey']." as ClientKey, 
				CONCAT('7 (', phone.prefix, ') ', phone.number) as Phone,
				CONCAT(client.LastName, ' ', client.FirstName, ' ', client.MiddleName) as ClientName,
				payment.PaymentID as PaymentId,
				payment.Amount as Amount,
				payment.ContractID as ContractId,
				payment.ManagerID as ManagerId,
				payment.Created as Paydate
			FROM ".$this->actionConfig['Table']." as payment
				LEFT JOIN ".$this->actionConfig['ContractTable']." as contract ON payment.ContractID = contract.ContractID
				LEFT JOIN ".$this->actionConfig['ClientTable']." as 
				client ON client.".$this->actionConfig['ClientPhoneKey']." = contract.".$this->actionConfig['ClientPhoneKey']."
				LEFT JOIN ".$this->actionConfig['ClientPhoneTable']." as phone ON client.".$this->actionConfig['ClientPhoneKey']." = phone.".$this->actionConfig['ClientPhoneKey']."
				WHERE payment.PaymentID = " . $request->GetProperty('PaymentID');
		}

		$row = $stmt->FetchRow($query);

		$paymentId  = $row['PaymentId'];
		$contractId = $row['ContractId'];
		$managerId  = $row['ManagerId'];
		$paydate    = $row['Paydate'];

		$month = date('F', strtotime($paydate));
		$monthName = GetTranslation('date-'.$month);

		$content->setVar('PayDay', date('d', strtotime($paydate)));
		$content->setVar('PayMonth', $monthName);
		$content->setVar('PayYear', date('Y', strtotime($paydate)));
		$content->setVar('ClientName', $row['ClientName']);
		$content->setVar('Phone', $row['Phone']);
		$content->setVar('Amount', $row['Amount']);
		$content->setVar('AmountString', NumToStr($row['Amount']));
		$content->setVar('ContractId', $row['ContractId']);
		$content->setVar('ManagerId', $row['ManagerId']);

		$query = "SELECT CONCAT(LastName, ' ', FirstName, ' ', MiddleName) as Manager
					FROM user
					WHERE UserID = " . $managerId;

		$row = $stmt->FetchRow($query);
		$content->setVar('Manager', $row['Manager']);

		$seasonTitle = "";
		$query = "SELECT s.Title, s.DateFrom, s.DateTo FROM ".$this->actionConfig["ContractTable"]." AS c  
						JOIN ".$this->actionConfig["ContractTable"]."2season AS c2s ON c2s.ContractID=c.ContractID 
						JOIN `crm_season` AS s ON s.SeasonID=c2s.SeasonID 
					WHERE c.ContractID=".$contractId." LIMIT 1";

		$row = $stmt->FetchRow($query);

		$content->setVar('SeasonTitle', $row['Title']);
		$content->setVar('DateFrom', $row['DateFrom']);
		$content->setVar('DateTo', $row['DateTo']);
		  
		$mpdf = new mPDF('utf-8', 'A4', '11.5', '', 10, 10, 7, 7, 10, 10);
		$stylesheet = file_get_contents(PROJECT_DIR."module/crm/template/css/payment.css");

		if($request->GetProperty("Print"))
		{
			$mpdf->SetJS('this.print();');
		}
		$mpdf->WriteHTML($stylesheet, 1);
		$mpdf->list_indent_first_level = 0; 
		$mpdf->WriteHTML($page->Grab($content), 2);
		if($request->GetProperty("ToMailingAttachment"))
		{
			$mpdf->Output(PROJECT_DIR.'var/data/mailing/attachment/payment_'.$request->GetProperty("entity").'_'.$paymentId.'.pdf', 'F');
		}
		else
		{
			$mpdf->Output('payment.pdf', 'I');	
		}
		exit();
	}
	
	private function GetContractPDF($request, $user)
	{
		$page = new PopupPage("crm", true);
		$content = $page->Load($this->actionConfig["Template"]);

		$stmt = GetStatement();
		if(isset($this->actionConfig["Data"]))
		{
			foreach($this->actionConfig["Data"] as $data)
			{
				$query = "";
				$prevAlias = "t";
				if (isset($data["Path"])) {
					foreach ($data["Path"] as $key => $chunk) {
						if ($key == count($data["Path"]) - 1) {
							$select = "SELECT " . $chunk["SQLSelect"] . " FROM " . $this->actionConfig["Table"] . " AS t ";
							$alias = "d";
						} else {
							$alias = "l" . $key;
						}
						$query .= " LEFT JOIN " . $chunk["Table"] . " AS " . $alias . " ON " . $prevAlias . "." . $chunk["FromField"] . "=" . $alias . "." . $chunk["ToField"];
						$prevAlias = $alias;
					}
					$query = $select . $query . " WHERE t.ContractID=" . $request->GetPropertyForSQL("ContractID");
					if ($data["Type"] == "field")
						$content->SetVar($data["Name"], $stmt->FetchField($query));
					else if ($data["Type"] == "list")
						$content->SetLoop($data["Name"], $stmt->FetchList($query));
				}
			}
		}

		$contractInfo = $stmt->FetchRow("SELECT * FROM ".$this->actionConfig["Table"]." WHERE ContractID=".$request->GetPropertyForSQL("ContractID"));
		$contractInfo["AmountString"] = NumToStr($contractInfo["Amount"], false);
		$content->LoadFromArray($contractInfo);

		//contact name
		if(isset($this->actionConfig["ContactTable"]) && isset($this->actionConfig["ContactPhoneTable"]))
		{
			$contactInfo = $stmt->FetchRow("SELECT ct.*, cpt.Prefix, cpt.Number
				FROM ".$this->actionConfig["ContactTable"]." ct
				LEFT JOIN ".$this->actionConfig["ContactPhoneTable"]." cpt ON ct.ContactID = cpt.ContactID 
				WHERE ct.ContactID =".$contractInfo["ContactID"]);
		}
		else
		{
			$contactInfo = null;
		}
		$contactPost = '';
		$contactName = '';
		$contactPhone = '';
		$contactPassport = '';
		if (!is_null($contactInfo)) {
			$contactPost = $contactInfo['Post'];
			$contactName = $contactInfo['LastName'] . ' ' . $contactInfo['FirstName'] . ' ' . $contactInfo['MiddleName'];
			$contactPhone = '+7 ' . $contactInfo['Prefix'] . ' ' . $contactInfo['Number'];
			$contactPassport = isset($contactInfo['Passport']) ? $contactInfo['Passport'] : '';
		}

		//LastDay for pay
		if ($contractInfo['LastDayForPay'] == '0000-00-00' || !$contactInfo['LastDayForPay']) {
			$lastDay = "_______________." . date("Y");
		} else {
			$lastDay = date('d.m.Y', strtotime($contractInfo['LastDayForPay']));
		}

		//Legal address
		$legalAddress = '';
		$l1 = $content->getVar('LegalCountry');
		if (!empty($l1)) 
			$legalAddress .= $l1;
		$l2 = $content->getVar('LegalCity');
		if (!empty($l2)) 
			$legalAddress .= ', г.' . $l2;
		$l3 = $content->getVar('LegalStreet');
		if (!empty($l3)) 
			$legalAddress .= ', ул.' . $l3;
		$l4 = $content->getVar('LegalHome');
		if (!empty($l4)) 
			$legalAddress .= ', д. ' . $content->getVar('LegalHome');
		$l5 = $content->getVar('LegalBuilding');
		if (!empty($l5)) 
			$legalAddress .= ', стр. ' . $l5;
		$l6 = $content->getVar('LegalOffice');
		if (!empty($l6)) 
			$legalAddress .= ', оф. ' . $l6;

		// список детей привязанных к контракту юр лица
        $config = $this->config['ActionConfig']['GetContractPDF']['Config'];
        $contractID = $request->GetProperty('ContractID');
        $seasonID = $stmt->FetchField("SELECT SeasonID FROM ".$config['Contract2SeasonTable']." WHERE ContractID=".$contractID);
        $seasonName = $stmt->FetchField("SELECT Title FROM crm_season WHERE SeasonID=".$seasonID);
        $query = "SELECT ChildIDs FROM ".$config['ContractTable']." WHERE ContractID=".$contractID;
        $childIDs = json_decode($stmt->FetchField($query));
        foreach ($childIDs as $k=>$v){
            $query = "SELECT CONCAT(c.LastName, ' ' , c.FirstName, ' ' , c.MiddleName) as FIO, DOB, d.*, Number as DocumentNumber,
            CONCAT('г. ', c.AddressCity, ', ул. ', c.AddressStreet, ', д. ', c.AddressHome, ', кв. ', c.AddressFlat) AS Address
            FROM crm_child as c "
                ."LEFT JOIN crm_child_document as d ON c.ChildID=d.ChildID"
                ." LEFT JOIN crm_parent as p ON c.ChildID=d.ChildID"
                ." WHERE d.ChildID=".$v." AND d.MAIN=1";
            if ($stmt->FetchRow($query)){
                $row = $stmt->FetchRow($query);
            } else {
                $query = "SELECT CONCAT(c.LastName, ' ' , c.FirstName, ' ' , c.MiddleName) as FIO, DOB, 
                    CONCAT('г. ', c.AddressCity, ', ул. ', c.AddressStreet, ', д. ', c.AddressHome, ', кв. ', c.AddressFlat) AS Address
                      FROM crm_child as c "
                    ." LEFT JOIN crm_parent as p ON c.ChildID=p.ChildID"
                    ." WHERE c.ChildID=".$v;
                $row = $stmt->FetchRow($query);
            }
            if (strlen($row['Address'])<=26){$row['Address']=null;}
            $parentList = $stmt->FetchList("SELECT * FROM crm_parent WHERE ChildID=".$v);
            foreach ($parentList as $k=>$v){
                $ID = $v['ParentID'];
                $phoneList = $stmt->FetchList("SELECT * FROM crm_parent_phone WHERE ParentID=".$ID);
                $parentList[$k]['PhoneList'] = $phoneList;
            }
            $row['ParentList'] = $parentList;
            $row['SeasonTitle'] = $seasonName;
            $childList[] = $row;
        }

        // Child Document filter by Main = 1
		if (count($content->_arrVars['ChildDocument'])>1){
			$content->_arrVars['ChildDocument'] = array_filter(
				$content->_arrVars['ChildDocument'],
				function ($val){
					if ($val['Main']==1) return true;
					else return false;
				},
				ARRAY_FILTER_USE_BOTH
			);
			foreach ($content->_arrVars['ChildDocument'] as $k=>$v){
				$content->_arrVars['ChildDocument'] = array($v);
			}
		}


		$content->SetLoop('ChildList', $childList);
		$content->setVar('TotalCount', $content->getVar('TourCount') + $content->getVar('CourseCount'));
		$content->setVar('LegalAddress', $legalAddress);
		$content->setVar('AmountStr', NumToStr($content->getVar('Amount')));
		$content->setVar('AmountStrService', NumToStr($content->getVar('Amount')-1000));
		$content->setVar('AmountNumbService', ($content->getVar('Amount')-1000));
		$content->setVar('ContactPost', $contactPost);
		$content->setVar('ContactName', $contactName);
		$content->setVar('ContactPhone', $contactPhone);
		$content->setVar('ContactPassport', $contactPassport);
		$content->SetVar("CurrentYear", date("Y"));
		$content->SetVar("LastDay", $lastDay);

		$mpdf = new mPDF('utf-8', 'A4', '8', '', 10, 10, 7, 7, 10, 10);
		$stylesheet = file_get_contents(PROJECT_DIR."module/crm/template/css/contract.css");
		$mpdf->WriteHTML($stylesheet, 1);
		$mpdf->list_indent_first_level = 0;

		if($request->GetProperty("Print"))
		{
			$mpdf->SetJS('this.print();');
		}
		$mpdf->WriteHTML($page->Grab($content), 2);
		if($request->GetProperty("ToMailingAttachment"))
		{
			$mpdf->Output(PROJECT_DIR.'var/data/mailing/attachment/contract_'.$request->GetProperty("entity").'_'.$contractInfo["ContractID"].'.pdf', 'F');
		}
		else
		{
			$mpdf->Output('contract.pdf', 'I');	
		}
		exit();
	}
	
	private function GetInvoicePDF($request, $user)
	{
		$page = new PopupPage("crm", true);
		$content = $page->Load($this->actionConfig["Template"]);

		$stmt = GetStatement();
		if(isset($this->actionConfig["Data"]))
		{
			foreach($this->actionConfig["Data"] as $data)
			{
				$query = "";
				$prevAlias = "t";
				foreach ($data["Path"] as $key => $chunk)
				{
					if($key == count($data["Path"]) - 1)
					{
						$select = "SELECT ".$chunk["SQLSelect"]." FROM ".$this->actionConfig["Table"]." AS t ";
						$alias = "d";
					}
					else
					{
						$alias = "l".$key;
					}
					$query .= " LEFT JOIN ".$chunk["Table"]." AS ".$alias." ON ".$prevAlias.".".$chunk["FromField"]."=".$alias.".".$chunk["ToField"];
					$prevAlias = $alias;
				}
				$query = $select . $query . " WHERE t.InvoiceID=".$request->GetPropertyForSQL("InvoiceID");
				if($data["Type"] == "field")
					$content->SetVar($data["Name"], $stmt->FetchField($query));
				else if($data["Type"] == "list")
					$content->SetLoop($data["Name"], $stmt->FetchList($query));
			}
		}
		$invoiceInfo = $stmt->FetchRow("SELECT * FROM ".$this->actionConfig["Table"]." WHERE InvoiceID=".$request->GetPropertyForSQL("InvoiceID"));
		$content->LoadFromArray($invoiceInfo);
		$content->SetVar("AmountString", NumToStr($invoiceInfo["Amount"], true));
		
		$seasonTitle = "";
		if(isset($this->actionConfig["UseSeason"]) && $this->actionConfig["UseSeason"])
		{
			$query = "SELECT s.Title FROM ".$this->actionConfig["ContractTable"]." AS c  
							JOIN ".$this->actionConfig["ContractTable"]."2season AS c2s ON c2s.ContractID=c.ContractID 
							JOIN `crm_season` AS s ON s.SeasonID=c2s.SeasonID 
						WHERE c.ContractID=".Connection::GetSQLString($invoiceInfo["ContractID"]);
			$seasonList = $stmt->FetchList($query);
			$seasonTitleList = array();
			if($seasonList)
			{
				foreach ($seasonList as $season)
				{
					$seasonTitleList[] = "\"".$season["Title"]."\"";	
				}
			}
			$seasonTitle = implode(", ", $seasonTitleList);
		}
		$content->SetVar("InvoiceSubject", GetTranslation($this->actionConfig["InvoiceSubjectKey"], "crm", array("SeasonTitle" => $seasonTitle)));
		  
		$mpdf = new mPDF('utf-8', 'A4', '11.5', '', 10, 10, 7, 7, 10, 10);
		$mpdf->list_indent_first_level = 0; 
		if($request->GetProperty("Print"))
		{
			$mpdf->SetJS('this.print();');
		}
		$mpdf->WriteHTML($page->Grab($content), 2);
		if($request->GetProperty("ToMailingAttachment"))
		{
			$mpdf->Output(PROJECT_DIR.'var/data/mailing/attachment/invoice_'.$request->GetProperty("entity").'_'.$invoiceInfo["InvoiceID"].'.pdf', 'F');
			//rename(PROJECT_DIR.'var/data/mailing/attachment/invoice_'.$request->GetProperty("entity").'_'.$invoiceInfo["InvoiceID"].'.pdf', iconv("utf-8", "cp1251", $request->GetProperty("FilePath")));
		}
		else
		{
			$mpdf->Output('invoice.pdf', 'I');	
		}
		exit();
	}
	
	private function GetActPDF($request, $user)
	{
		$page = new PopupPage("crm", true);
		$content = $page->Load($this->actionConfig["Template"]);

		$stmt = GetStatement();
		if(isset($this->actionConfig["Data"]))
		{
			foreach($this->actionConfig["Data"] as $data)
			{
				$query = "";
				$prevAlias = "t";
				foreach ($data["Path"] as $key => $chunk)
				{
					if($key == count($data["Path"]) - 1)
					{
						$select = "SELECT ".$chunk["SQLSelect"]." FROM ".$this->actionConfig["Table"]." AS t ";
						$alias = "d";
					}
					else
					{
						$alias = "l".$key;
					}
					$query .= " LEFT JOIN ".$chunk["Table"]." AS ".$alias." ON ".$prevAlias.".".$chunk["FromField"]."=".$alias.".".$chunk["ToField"];
					$prevAlias = $alias;
				}
				$query = $select . $query . " WHERE t.ActID=".$request->GetPropertyForSQL("ActID");
				if($data["Type"] == "field")
					$content->SetVar($data["Name"], $stmt->FetchField($query));
				else if($data["Type"] == "list")
					$content->SetLoop($data["Name"], $stmt->FetchList($query));
			}
		}
		$actInfo = $stmt->FetchRow("SELECT a.*, c.Amount FROM ".$this->actionConfig["Table"]." AS a 
											LEFT JOIN ".$this->actionConfig["ContractTable"]." AS c ON c.ContractID=a.ContractID 
										WHERE a.ActID=".$request->GetPropertyForSQL("ActID"));
		$content->LoadFromArray($actInfo);
		$content->SetVar("AmountString", NumToStr($actInfo["Amount"], true));
		
		$seasonTitle = "";
		if(isset($this->actionConfig["UseSeason"]) && $this->actionConfig["UseSeason"])
		{
			$query = "SELECT s.Title FROM ".$this->actionConfig["ContractTable"]." AS c  
							JOIN ".$this->actionConfig["ContractTable"]."2season AS c2s ON c2s.ContractID=c.ContractID 
							JOIN `crm_season` AS s ON s.SeasonID=c2s.SeasonID 
						WHERE c.ContractID=".Connection::GetSQLString($actInfo["ContractID"]);
			$seasonList = $stmt->FetchList($query);
			$seasonTitleList = array();
			if($seasonList)
			{
				foreach ($seasonList as $season)
				{
					$seasonTitleList[] = "\"".$season["Title"]."\"";	
				}
			}
			$seasonTitle = implode(", ", $seasonTitleList);
		}
		$content->SetVar("ActSubject", GetTranslation($this->actionConfig["ActSubjectKey"], "crm", array("SeasonTitle" => $seasonTitle)));
		  
		$mpdf = new mPDF('utf-8', 'A4', '11.5', '', 10, 10, 7, 7, 10, 10);
		$stylesheet = file_get_contents(PROJECT_DIR."module/crm/template/css/act.css");
		if($request->GetProperty("Print"))
		{
			$mpdf->SetJS('this.print();');
		}
		$mpdf->WriteHTML($stylesheet, 1);
		$mpdf->list_indent_first_level = 0; 
		$mpdf->WriteHTML($page->Grab($content), 2);
		if($request->GetProperty("ToMailingAttachment"))
		{
			$mpdf->Output(PROJECT_DIR.'var/data/mailing/attachment/act_'.$request->GetProperty("entity").'_'.$actInfo["ActID"].'.pdf', 'F');
			//rename(PROJECT_DIR.'var/data/mailing/attachment/act_'.$request->GetProperty("entity").'_'.$actInfo["ActID"].'.pdf', iconv("utf-8", "cp1251", $request->GetProperty("FilePath")));
		}
		else
		{
			$mpdf->Output('act.pdf', 'I');	
		}
		exit();
	}
}

?>