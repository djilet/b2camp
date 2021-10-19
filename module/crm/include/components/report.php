<?php 
require_once(dirname(__FILE__)."/../component.php");

class ReportViewComponent extends BaseComponent
{
	function GetSelectPrefixForSQL()
	{
		if($this->config["Type"] == "Entity")
			return "t.".$this->config["Field"];
	}
	
	function PrepareBeforeShow(&$item, $user)
	{
		switch ($this->config["Type"])
		{
			//Отчет по менеджерам
			case "DashboardManager":
				$stmt = GetStatement();
				$userList = new UserList();
				$userList->LoadManagerList(new LocalObject(), true);
			
				
				$dateFrom = null;
				$dateTo   = null;

				if (isset($_POST['ManagerStDateFrom']) && $_POST['ManagerStDateFrom'])
					$dateFrom = date('Y-m-d', strtotime($_POST['ManagerStDateFrom']));

				if (isset($_POST['ManagerStDateTo']) && $_POST['ManagerStDateTo'])
					$dateTo = date('Y-m-d', strtotime($_POST['ManagerStDateTo']));

				$dateCondition = "";
				if($dateFrom)
					$dateCondition .= " AND s.Updated >= '".$dateFrom." 00:00:00' ";
				if($dateTo)
					$dateCondition .= " AND s.Updated <= '".$dateTo." 23:59:59' ";
				
				//$dateCondition = "";
				
				for($i = 0; $i < $userList->GetCountItems(); $i++)
				{
					$userList->_items[$i]["CallCount"] = 0;
					$userList->_items[$i]["ReservedCount"] = 0;
					$userList->_items[$i]["BoughtCount"] = 0;
				}

				//Статусы Звонили(Думают), Отказ, Не звонить
				$callStatuses = array(1,4,5);

				foreach ($this->config["SourceList"] as $source)
				{
					$query = "SELECT 
								    t.ManagerID,
								    s.StatusID,
								    COUNT(s.".$source["Key"].") AS StatusCount, 
								    SUM(s.Quantity) AS TotalQuantity 
								FROM
								    `".$source["Table"]."` AS t
								        LEFT JOIN
								    `".$source["Table"]."_status` AS s ON s.".$source["Key"]." = t.".$source["Key"]."
								WHERE
								    s.StatusID IN (1, 3, 4, 5, 6)".$dateCondition."
								GROUP BY t.ManagerID , s.StatusID
								ORDER BY t.ManagerID ASC";

					$stat = $stmt->FetchList($query);

					$total[$this->name]["CallCount"] = 0;
					$total[$this->name]["ReservedCount"] = 0;
					$total[$this->name]["BoughtCount"] = 0;

					for($i = 0; $i < $userList->GetCountItems(); $i++)
					{
						foreach ($stat as $row)
						{
							if($row["ManagerID"] == $userList->_items[$i]["UserID"])
							{
								if(in_array($row["StatusID"], $callStatuses)) {
									$userList->_items[$i]["CallCount"] += $row["StatusCount"];
									$total[$this->name]["CallCount"] += $row["StatusCount"];
								}

								if($row["StatusID"] == 3) {
									$userList->_items[$i]["ReservedCount"] += $row["TotalQuantity"];
									$total[$this->name]["ReservedCount"] += $row["TotalQuantity"];
								}

								if($row["StatusID"] == 6) {
									$userList->_items[$i]["BoughtCount"] += $row["TotalQuantity"];
									$total[$this->name]["BoughtCount"] += $row["TotalQuantity"];
								}
							}
						}
					}
				}

				$userList->LoadFromArray(MultiSort($userList->GetItems(), 'BoughtCount', false, 2));
				$item[$this->name."List"] = $userList->GetItems();
				$item["Total".$this->name."List"][] = $total[$this->name];

				break;
			// Будущие сезоны
			case "DashboardSeason":

				$stmt = GetStatement();

				$sortSeasonDate = 1;
				$sortSales = 0;

				if (isset($_POST['SortSeasonDate'])) {
					$sortSeasonDate = 1;
					$sortSales = 0;
				}

				if (isset($_POST['SortSales'])) {
					$sortSales = 1;
					$sortSeasonDate = 0;
				}
				
				$query = "SELECT Title, PlaceCount, SeasonID, Image, DateFrom FROM `crm_season` 
							WHERE Archive='N' AND TypeID = ".$this->config['TypeID']."
							ORDER BY DateFrom ASC";

				$seasonList = $stmt->FetchList($query);
				
				$seasonIDs = array();
				$imageParams = LoadImageConfig("Image", "data", "50x50|8|Small");
				for($i = 0; $i < count($seasonList); $i++)
				{
					$seasonList[$i]["PreOrder"] = 0;
					$seasonList[$i]["ReservedCount"] = 0;
					$seasonList[$i]["BoughtCount"] = 0;
					$seasonList[$i]["FreeCount"] = intval($seasonList[$i]["PlaceCount"]);
					$seasonIDs[] = $seasonList[$i]["SeasonID"];
					for ($j = 0; $j < count($imageParams); $j++)
					{
						$v = $imageParams[$j];
						$seasonList[$i][$v["Name"]."Path"] = $v["Path"]."season/".$seasonList[$i]["Image"];
					}
				}
				
				$total = array($this->name => array("PreOrder" => 0, "ReservedCount" => 0, "BoughtCount" => 0, "FreeCount" => 0));
				
				if(count($seasonIDs) > 0)
				{
					foreach ($this->config["SourceList"] as $source)
					{
						$query = "SELECT 
									    s.StatusID, SUM(s.Quantity) AS TotalQuantity, s.SeasonID
									FROM
									    `".$source["Table"]."` AS t
									        LEFT JOIN
									    `".$source["Table"]."_status` AS s ON s.".$source["Key"]." = t.".$source["Key"]." 
									WHERE
									    s.StatusID IN (3 , 6, 7)
									        AND s.SeasonID IN (".implode(", ", $seasonIDs).")
									GROUP BY s.SeasonID, s.StatusID
									ORDER BY s.SeasonID ASC";
	
						$stat = $stmt->FetchList($query);
						
						for($i = 0; $i < count($seasonList); $i++)
						{
							foreach ($stat as $row)
							{
								if($row["SeasonID"] == $seasonList[$i]["SeasonID"])
								{
									if($row["StatusID"] == 3)
									{
										$seasonList[$i]["ReservedCount"] += $row["TotalQuantity"];
										$seasonList[$i]["FreeCount"] -= $row["TotalQuantity"];
										$total[$this->name]['ReservedCount'] += $row["TotalQuantity"];
									}
									if($row["StatusID"] == 6)
									{
										$seasonList[$i]["BoughtCount"] += $row["TotalQuantity"];
										$seasonList[$i]["FreeCount"] -= $row["TotalQuantity"];
										$total[$this->name]['BoughtCount'] += $row["TotalQuantity"];
									}
									if($row["StatusID"] == 7)
									{
										$seasonList[$i]["PreOrder"] += $row["TotalQuantity"];
										$seasonList[$i]["FreeCount"] -= $row["TotalQuantity"];
										$total[$this->name]['PreOrder'] += $row["TotalQuantity"];
									}
								}
							}
						}
					}
				}

				foreach ($seasonList as $season) {
					$total[$this->name]['FreeCount'] += $season["FreeCount"];
				}

				if ($sortSales == 1)
				//По продажам
					$seasonList = MultiSort($seasonList, 'BoughtCount', false, 2);
				elseif ($sortSeasonDate == 1)
				//По дате начала
					$seasonList = MultiSort($seasonList, 'DateFrom', true, 2);

				$item[$this->name."List"] = $seasonList;
				$item['Total'.$this->name.'List'][] = $total[$this->name];

				break;
			case "DashboardCall":
				$stmt = GetStatement();
				$userList = new UserList();
				$userList->LoadManagerList(new LocalObject(), true);
				
				$dateFrom = null;
				$dateTo   = null;

				if (isset($_POST['ManagerStDateFrom']) && $_POST['ManagerStDateFrom'])
					$dateFrom = date('Y-m-d', strtotime($_POST['ManagerStDateFrom']));

				if (isset($_POST['ManagerStDateTo']) && $_POST['ManagerStDateTo'])
					$dateTo = date('Y-m-d', strtotime($_POST['ManagerStDateTo']));

				$where = array();
				if($dateFrom)
					$where[] = "DATE(c.Created) >= ".Connection::GetSQLString($dateFrom);
				if($dateTo)
					$where[] = "DATE(c.Created) <= ".Connection::GetSQLString($dateTo);
				
				for($i = 0; $i < $userList->GetCountItems(); $i++)
				{
					$userList->_items[$i]["OutgoingCallCount"] = 0;
					$userList->_items[$i]["IncomingCallCount"] = 0;
				}

				$query = "SELECT c.UserID, SUM(IF(c.CallType = 'out', 1, 0)) AS OutgoingCallCount, SUM(IF(c.CallType = 'in', 1, 0)) AS IncomingCallCount 
							FROM `crm_call` AS c 
							".(count($where) > 0 ? " WHERE ".implode(" AND ", $where) : "").
							" GROUP BY c.UserID";
				$stat = $stmt->FetchList($query);

				$total = array();
				$total["OutgoingCallCount"] = 0;
				$total["IncomingCallCount"] = 0;

				for($i = 0; $i < $userList->GetCountItems(); $i++)
				{
					foreach ($stat as $row)
					{
						if($row["UserID"] == $userList->_items[$i]["UserID"])
						{
							$userList->_items[$i]["OutgoingCallCount"] += $row["OutgoingCallCount"];
							$total["OutgoingCallCount"] += $row["OutgoingCallCount"];
							$userList->_items[$i]["IncomingCallCount"] += $row["IncomingCallCount"];
							$total["IncomingCallCount"] += $row["IncomingCallCount"];
						}
					}
				}

				$userList->LoadFromArray(MultiSort($userList->GetItems(), 'OutgoingCallCount', false, 2));
				$item[$this->name."List"] = $userList->GetItems();
				$item["Total".$this->name."List"] = array($total);

				break;
			case "Entity":
				$request = new LocalObject(array_merge($_GET, $_POST));
				$stmt = GetStatement();
				
				if($request->GetProperty("Action") == "Generate")
				{
					$resultList = array();
					$item["Generated"] = 1;
					$reportType = $item[$this->config["Field"]];
					switch ($reportType) 
					{
						case 1: //Отчет по работе менеджеров
							$query = "SELECT StatusID, Title, 0 AS Value FROM `crm_status` ORDER BY StatusID";
							$statusList = $stmt->FetchList($query);
							$item["StatusList"] = $statusList;
							$userList = $item["ReportManagerList"];
							$count = count($userList);
							for($i = 0; $i < $count; $i++)
							{
								$userList[$i]["StatusList"] = $statusList;
								if($request->GetProperty("ManagerID"))
								{
									if(!in_array($userList[$i]["ID"], $request->GetProperty("ManagerID")))
										unset($userList[$i]);
								}
							}
							$userList = array_values($userList);
							
							$where = array();
							if($request->GetProperty("DateFrom"))
								$where[] = "DATE(s.Updated) >= ".Connection::GetSQLString(ToSQLDate($request->GetProperty("DateFrom")));
							if($request->GetProperty("DateTo"))
								$where[] = "DATE(s.Updated) <= ".Connection::GetSQLString(ToSQLDate($request->GetProperty("DateTo")));

							foreach ($this->config[$reportType]["SourceList"] as $source)
							{
								$query = 
									"SELECT t.ManagerID, s.StatusID, 
										COUNT(s.".$source["Key"].") AS StatusCount, 
										SUM(s.Quantity) AS TotalQuantity 
									FROM
									    `".$source["Table"]."` AS t
									        LEFT JOIN
									    `".$source["Table"]."_status` AS s ON s.".$source["Key"]." = t.".$source["Key"]." 
									".(count($where) > 0 ? "WHERE ".implode(" AND ", $where) : "")."
									GROUP BY t.ManagerID , s.StatusID
									ORDER BY t.ManagerID ASC";

								$stat = $stmt->FetchList($query);

								for($i = 0; $i < count($userList); $i++)
								{
									foreach ($stat as $row)
									{
										if($row["ManagerID"] == $userList[$i]["ID"])
										{
											for($j = 0; $j < count($userList[$i]["StatusList"]); $j++)
											{
												if($row["StatusID"] == $userList[$i]["StatusList"][$j]["StatusID"]) {
													if (in_array($row["StatusID"], array(3,6))) {
														$userList[$i]["StatusList"][$j]["Value"] += $row["TotalQuantity"];
													} else {
														$userList[$i]["StatusList"][$j]["Value"] += $row["StatusCount"];
													}
												}
											}
											for($j = 0; $j < count($statusList); $j++)
											{
												if($row["StatusID"] == $statusList[$j]["StatusID"]) {
													if (in_array($row["StatusID"], array(3,6))) {
														$statusList[$j]["Value"] += $row["TotalQuantity"];
													} else {
														$statusList[$j]["Value"] += $row["StatusCount"];
													}
												}
											}
										}
									}
								}
							}

							$total = array("StatusList" => $statusList);
							$userList[] = $total;
							$resultList = $userList;
							break;
						case 2: //Отчет по выкупленным и забронированным путевкам (Финансовый отчет. Приход и дебиторская задолженность)
							$where = array();
							if($request->GetProperty("SeasonID"))
							{
								$where[] = "c2s.SeasonID IN(".implode(", ", Connection::GetSQLArray($request->GetProperty("SeasonID"))).")";
							}
							else
							{
								if($request->GetProperty("DateFrom"))
									$where[] = "DATE(c.Created) >= ".Connection::GetSQLString(ToSQLDate($request->GetProperty("DateFrom")));
								if($request->GetProperty("DateTo"))
									$where[] = "DATE(c.Created) <= ".Connection::GetSQLString(ToSQLDate($request->GetProperty("DateTo")));
							}
							//договор, созданный через физ лицо
							$query = "SELECT ch.ChildID, ch.LastName, ch.FirstName, c.ContractID, c.Created, c.Amount, ANY_VALUE(s.Title) AS SeasonTitle, 
											TIMESTAMPDIFF(YEAR, ch.DOB, NOW()) AS Age, 
											SUM(IF(p.PaymentID IS NOT NULL, p.Amount, 0)) AS PaidAmount, 
											c.Amount - SUM(IF(p.PaymentID IS NOT NULL, p.Amount, 0)) AS UnpaidAmount 
										FROM `crm_parent_contract` AS c 
											LEFT JOIN `crm_parent_contract2season` AS c2s ON c2s.ContractID=c.ContractID 
											LEFT JOIN `crm_season` AS s ON s.SeasonID=c2s.SeasonID 
											LEFT JOIN `crm_parent_payment` AS p ON c.ContractID=p.ContractID 
											JOIN `crm_parent` AS pr ON c.ParentID=pr.ParentID
											JOIN `crm_child` AS ch ON ch.ChildID=pr.ChildID  
										".(count($where) > 0 ? "WHERE ".implode(" AND ", $where) : "")." 
										GROUP BY c.ContractID 
										ORDER BY ch.LastName ASC, ch.FirstName ASC, s.DateFrom ASC";
							$resultList = $stmt->FetchList($query);

                            $query = "SELECT c.ChildIDs FROM `crm_legal_contract` AS c ".(count($where) > 0 ? "WHERE ".implode(" AND ", $where) : "");
							$childIDs = $stmt->FetchList($query);
							$childList = "";
							foreach ($childIDs as $value) {
							    $value["ChildIDs"] = str_replace('["', '', $value["ChildIDs"]);
                                $value["ChildIDs"] = str_replace('"]', '', $value["ChildIDs"]);
                                if ($childList != "" && $value["ChildIDs"] != "")
                                    $childList .= ", ";
                                $childList .= $value["ChildIDs"];
                            }

                            //договор, созданный через юр лицо
                            $query = "SELECT ch.ChildID, ch.LastName, ch.FirstName, c.ContractID, c.Created, c.Amount, ANY_VALUE(s.Title) AS SeasonTitle, 
											TIMESTAMPDIFF(YEAR, ch.DOB, NOW()) AS Age, 
											SUM(IF(p.PaymentID IS NOT NULL, p.Amount, 0)) AS PaidAmount, 
											c.Amount - SUM(IF(p.PaymentID IS NOT NULL, p.Amount, 0)) AS UnpaidAmount 
										FROM `crm_legal_contract` AS c 
											LEFT JOIN `crm_legal_contract2season` AS c2s ON c2s.ContractID=c.ContractID 
											LEFT JOIN `crm_season` AS s ON s.SeasonID=c2s.SeasonID 
											LEFT JOIN `crm_legal_payment` AS p ON c.ContractID=p.ContractID 
											JOIN `crm_legal` AS lg ON c.LegalID=lg.LegalID
											JOIN `crm_child` AS ch ON ch.ChildID IN (".$childList.")  
										".(count($where) > 0 ? "WHERE ".implode(" AND ", $where) : "")." 
										GROUP BY c.ContractID 
										ORDER BY ch.LastName ASC, ch.FirstName ASC, s.DateFrom ASC";
                            $childList = $stmt->FetchList($query);
                            $resultList= array_merge($resultList, $childList);

							if(count($resultList) > 0)
							{
								$total = array("ContractCount" => 0, "Amount" => 0, "PaidAmount" => 0, "UnpaidAmount" => 0);
								for($i = 0; $i < count($resultList); $i++)
								{
									$total["ContractCount"]++;
									$total["Amount"] += $resultList[$i]["Amount"];
									$total["PaidAmount"] += $resultList[$i]["PaidAmount"];
									$total["UnpaidAmount"] += $resultList[$i]["UnpaidAmount"];
								}
								$resultList[] = $total;
							}
							break;
						case 3: //Реестр
							$where = array();
							$join = array();
							$group = "";
							$paramField = "";
							
							if($request->GetProperty("SeasonID"))
								$where[] = "s.SeasonID IN(".implode(", ", Connection::GetSQLArray($request->GetProperty("SeasonID"))).")";
							if($request->GetProperty("DateFrom"))
								$where[] = "cpc.Created >= ".Connection::GetSQLString(ToSQLDate($request->GetProperty("DateFrom")) . " 00:00:00");
							if($request->GetProperty("DateTo"))
								$where[] = "cpc.Created <= ".Connection::GetSQLString(ToSQLDate($request->GetProperty("DateTo")) . "23:59:59");
							
							if($request->GetProperty("CategoryID"))
							{
								$group = "c.CategoryID";
								$paramField = "p.Title";
								$join[] = "LEFT JOIN `crm_category` AS p ON p.CategoryID=c.CategoryID";
								$item["GroupTitle"] = GetTranslation("report-group-category", "crm");
							}
							if($request->GetProperty("Squad"))
							{
								$group = "c.Squad";
								$paramField = "c.Squad";
								$item["GroupTitle"] = GetTranslation("report-group-squad", "crm");
							}
							if($request->GetProperty("ManagerID"))
							{
								$group = "c.ManagerID";
								$paramField = "CONCAT(p.LastName, ' ', p.FirstName)";
								$join[] = "LEFT JOIN `user` AS p ON p.UserID=c.ManagerID";
								$item["GroupTitle"] = GetTranslation("report-group-manager", "crm");
							}
							if($request->GetProperty("SchoolID"))
							{
								$group = "c.SchoolID";
								$paramField = "p.Title";
								$join[] = "LEFT JOIN `crm_school` AS p ON p.SchoolID=c.SchoolID";
								$item["GroupTitle"] = GetTranslation("report-group-school", "crm");
							}
							if($request->GetProperty("Age"))
							{
								$group = "TIMESTAMPDIFF(YEAR, c.DOB, NOW())";
								$paramField = "TIMESTAMPDIFF(YEAR, c.DOB, NOW())";
								$item["GroupTitle"] = GetTranslation("report-group-age", "crm");
							}	
							if($request->GetProperty("StatusID"))
							{
								$group = "fst.StatusID";
								$paramField = "p.Title";
								$join[] = "JOIN `crm_child_status` AS fst ON fst.ChildID=c2s.ChildID AND fst.SeasonID=s.SeasonID";
								$join[] = "LEFT JOIN `crm_status` AS p ON p.StatusID=fst.StatusID";
								$item["GroupTitle"] = GetTranslation("report-group-status", "crm");
							}

							$query = "SELECT s.Title, COUNT(DISTINCT(c.ChildID)) AS ChildCount, ".$paramField." AS GroupTitle 
										FROM `crm_child2season` AS c2s 
											JOIN `crm_season` AS s ON s.SeasonID=c2s.SeasonID 
											JOIN `crm_child` AS c ON c.ChildID=c2s.ChildID 
											JOIN `crm_parent` AS pr ON c.ChildID=pr.ChildID
											JOIN `crm_parent_contract` as cpc ON cpc.ParentID=pr.ParentID
											".(count($join) > 0 ? implode(" ", $join) : "")." 
											".(count($where) > 0 ? " WHERE ".implode(" AND ", $where) : "")." 
											GROUP BY s.SeasonID, ".$group."  
											ORDER BY s.Title ASC, ".$paramField." ASC";

							$resultList = $stmt->FetchList($query);
							$total = 0;

							foreach ($resultList as $result) {
								$total += $result['ChildCount'];
							}

							$totalList[] = array('Title' => ' ИТОГО', 'ChildCount' => $total, 'GroupTitle' => '');

							break;
						case 4: //Отчет по количеству проданных путевок за сезон
							$where = array();
							if($request->GetProperty("SeasonID"))
								$where[] = "SeasonID IN(".implode(", ", Connection::GetSQLArray($request->GetProperty("SeasonID"))).")";
							$query = "SELECT SeasonID, Title, PlaceCount FROM `crm_season` 
										".(count($where) > 0 ? "WHERE ".implode(" AND ", $where) : "")." 
										ORDER BY Title ASC";
							$seasonList = $stmt->FetchList($query);
							for($i = 0; $i < count($seasonList); $i++)
							{
								$seasonList[$i]["ReservedCount"] = 0;
								$seasonList[$i]["BoughtCount"] = 0;
								$seasonList[$i]["FreeCount"] = intval($seasonList[$i]["PlaceCount"]);
							}
							
							$where = array();
							$where[] = "st.StatusID IN (3,6)";
							if($request->GetProperty("DateFrom"))
								$where[] = "DATE(st.Updated) >= ".Connection::GetSQLString(ToSQLDate($request->GetProperty("DateFrom")));
							if($request->GetProperty("DateTo"))
								$where[] = "DATE(st.Updated) <= ".Connection::GetSQLString(ToSQLDate($request->GetProperty("DateTo")));
							foreach ($this->config[$reportType]["SourceList"] as $source)
							{
								$query = "SELECT st.SeasonID, st.StatusID, COUNT(st.HistoryStatusID) AS StatusCount, SUM(st.Quantity) AS TotalQuantity 
											FROM `".$source["Table"]."_status` AS st  
											".(count($where) > 0 ? "WHERE ".implode(" AND ", $where) : "")."
											GROUP BY st.SeasonID, st.StatusID";
								$stat = $stmt->FetchList($query);

								for($i = 0; $i < count($seasonList); $i++)
								{
									foreach ($stat as $row)
									{
										if($row["SeasonID"] == $seasonList[$i]["SeasonID"])
										{
											if($row["StatusID"] == 3)
											{
												$seasonList[$i]["ReservedCount"] += $row["TotalQuantity"];
												$seasonList[$i]["FreeCount"] -= $row["TotalQuantity"];
											}
											if($row["StatusID"] == 6)
											{
												$seasonList[$i]["BoughtCount"] += $row["TotalQuantity"];
												$seasonList[$i]["FreeCount"] -= $row["TotalQuantity"];
											}
										}
									}
								}
							}
							if(count($seasonList) > 0)
							{
								$total = array("Title" => "", "BoughtCount" => 0, "ReservedCount" => 0, "FreeCount" => 0);
								$count = count($seasonList);
								for($i = 0; $i < $count; $i++)
								{
									if($seasonList[$i]["BoughtCount"] > 0 || $seasonList[$i]["ReservedCount"] > 0)
									{
										$total["BoughtCount"] += $seasonList[$i]["BoughtCount"];
										$total["ReservedCount"] += $seasonList[$i]["ReservedCount"];
										$total["FreeCount"] += $seasonList[$i]["FreeCount"];
									}
									else
									{
										unset($seasonList[$i]);
									}
								}	
								$seasonList[] = $total;
							}
							$seasonList = array_values($seasonList);
							$resultList = $seasonList;
							break;
						case 5: // Динамика продаж
							$where = array();
							if($request->GetProperty("SeasonID"))
								$where[] = "SeasonID IN(".implode(", ", Connection::GetSQLArray($request->GetProperty("SeasonID"))).")";
							$query = "SELECT SeasonID, Title, PlaceCount FROM `crm_season` 
										".(count($where) > 0 ? "WHERE ".implode(" AND ", $where) : "")." 
										ORDER BY Title ASC";
							$seasonList = $stmt->FetchList($query);
							for($i = 0; $i < count($seasonList); $i++)
							{
								$seasonList[$i]["ChildCount"] = 0;
								$seasonList[$i]["Amount"] = 0;
							}
							
							$where = array();
							if($request->GetProperty("DateFrom"))
								$where[] = "DATE(c.Created) >= ".Connection::GetSQLString(ToSQLDate($request->GetProperty("DateFrom")));
							if($request->GetProperty("DateTo"))
								$where[] = "DATE(c.Created) <= ".Connection::GetSQLString(ToSQLDate($request->GetProperty("DateTo")));
							foreach ($this->config[$reportType]["SourceList"] as $source)
							{
								$query = "SELECT COUNT(c.ContractID) AS ChildCount, SUM(c.Amount) AS Amount, c2s.SeasonID 
											FROM `".$source["Table"]."` AS c  
												JOIN `".$source["Table"]."2season` AS c2s ON c2s.ContractID=c.ContractID
											".(count($where) > 0 ? "WHERE ".implode(" AND ", $where) : "")." 
											GROUP BY c2s.SeasonID";
								$stat = $stmt->FetchList($query);

								for($i = 0; $i < count($seasonList); $i++)
								{
									foreach ($stat as $row)
									{
										if($row["SeasonID"] == $seasonList[$i]["SeasonID"])
										{
											$seasonList[$i]["ChildCount"] += $row["ChildCount"];
											$seasonList[$i]["Amount"] += $row["Amount"];
										}
									}
								}
							}
							if(count($seasonList) > 0)
							{
								$total = array("Title" => "", "ChildCount" => 0, "Amount" => 0);
								$count = count($seasonList);
								for($i = 0; $i < $count; $i++)
								{
									if($seasonList[$i]["ChildCount"] > 0)
									{
										$total["ChildCount"] += $seasonList[$i]["ChildCount"];
										$total["Amount"] += $seasonList[$i]["Amount"];
									}
									else
									{
										unset($seasonList[$i]);
									}
								}	
								$seasonList[] = $total;
							}
							$seasonList = array_values($seasonList);
							$resultList = $seasonList;
							break;
						case 6: //Статистика оплат за путевки по менеджерам
							$item["DisableSorting"] = 1;
							$userList = $item["ReportManagerList"];
							$count = count($userList);
							for($i = 0; $i < $count; $i++)
							{
								$userList[$i]["ContractList"] = array();;
								if($request->GetProperty("ManagerID"))
								{
									if(!in_array($userList[$i]["ID"], $request->GetProperty("ManagerID")))
										unset($userList[$i]);
								}
							}
							$userList = array_values($userList);
							
							$where = array();
							if($request->GetProperty("SeasonID"))
							{
								$where[] = "c2s.SeasonID IN(".implode(", ", Connection::GetSQLArray($request->GetProperty("SeasonID"))).")";
							}
							else
							{
								if($request->GetProperty("DateFrom"))
									$where[] = "DATE(c.Created) >= ".Connection::GetSQLString(ToSQLDate($request->GetProperty("DateFrom")));
								if($request->GetProperty("DateTo"))
									$where[] = "DATE(c.Created) <= ".Connection::GetSQLString(ToSQLDate($request->GetProperty("DateTo")));
							}
							foreach ($this->config[$reportType]["SourceList"] as $source)
							{
								$query = "SELECT c.ContractID, GROUP_CONCAT(s.Title) AS SeasonTitle, c.Amount, 
												SUM(IF(p.PaymentID IS NOT NULL, p.Amount, 0)) AS PaidAmount,
												c.Amount - SUM(IF(p.PaymentID IS NOT NULL, p.Amount, 0)) AS UnpaidAmount, 
												".(isset($source["LinkTable"]) ? "l.ManagerID" : "t.".$source["KeyField"])." AS ManagerID 
											FROM `".$source["Table"]."_contract` AS c
												LEFT JOIN `".$source["Table"]."` AS t ON t.".$source["KeyField"]."=c.".$source["KeyField"]."  
												".(isset($source["LinkTable"]) ? "LEFT JOIN ".$source["LinkTable"]." AS l ON l.".$source["LinkField"]."=t.".$source["LinkField"] : "")."
												LEFT JOIN `".$source["Table"]."_payment` AS p ON p.ContractID=c.ContractID 
												LEFT JOIN `".$source["Table"]."_contract2season` AS c2s ON c2s.ContractID=c.ContractID 
												LEFT JOIN `crm_season` AS s ON s.SeasonID=c2s.SeasonID
											".(count($where) > 0 ? "WHERE ".implode(" AND ", $where) : "")." 
											GROUP BY c.ContractID 
											ORDER BY c.ContractID ";

								$stat = $stmt->FetchList($query);
								
								for($i = 0; $i < count($userList); $i++)
								{
									foreach ($stat as $row)
									{
										if($row["ManagerID"] == $userList[$i]["ID"])
										{
											$userList[$i]["ContractList"][] = $row;
										}
									}
								}
							}
							for($i = 0; $i < count($userList); $i++)
							{
								$total = array("ContractCount" => 0, "Amount" => 0, "PaidAmount" => 0, "UnpaidAmount" => 0);
								foreach ($userList[$i]["ContractList"] as $row)
								{
									$total["ContractCount"]++;
									$total["Amount"] += $row["Amount"];
									$total["PaidAmount"] += $row["PaidAmount"];
									$total["UnpaidAmount"] += $row["UnpaidAmount"];
								}
								$userList[$i]["ContractList"][] = $total;
							}
							
							$resultList = $userList;
							break;
						case 7: //Отчет по количеству выкупленных и забронированных путевок
							$item["DisableSorting"] = 1;
							$userList = $item["ReportManagerList"];
							$count = count($userList);
							for($i = 0; $i < $count; $i++)
							{
								$userList[$i]["SeasonList"] = array();
								$userList[$i]["SeasonIDs"] = array();
								if($request->GetProperty("ManagerID"))
								{
									if(!in_array($userList[$i]["ID"], $request->GetProperty("ManagerID")))
										unset($userList[$i]);
								}
							}
							$userList = array_values($userList);
							
							$where = array();
							if($request->GetProperty("SeasonID"))
							{
								$where[] = "s.SeasonID IN(".implode(", ", Connection::GetSQLArray($request->GetProperty("SeasonID"))).")";
							}
							else
							{
								if($request->GetProperty("DateFrom"))
									$where[] = "DATE(s.Updated) >= ".Connection::GetSQLString(ToSQLDate($request->GetProperty("DateFrom")));
								if($request->GetProperty("DateTo"))
									$where[] = "DATE(s.Updated) <= ".Connection::GetSQLString(ToSQLDate($request->GetProperty("DateTo")));
							}
							$where[] = "s.StatusID IN (3,6)";
							foreach ($this->config[$reportType]["SourceList"] as $source)
							{
								$query = "SELECT COUNT(s.HistoryStatusID) AS StatusCount, SUM(s.Quantity) AS TotalQuantity, s.SeasonID, t.ManagerID, ss.Title, s.StatusID 
												FROM `".$source["Table"]."_status` AS s 
												JOIN `crm_season` AS ss ON ss.SeasonID=s.SeasonID
												JOIN `".$source["Table"]."` AS t ON t.".$source["KeyField"]."=s.".$source["KeyField"]."  
											".(count($where) > 0 ? "WHERE ".implode(" AND ", $where) : "")." 
											GROUP BY t.ManagerID, ss.SeasonID, s.StatusID  
											ORDER BY t.ManagerID, ss.Title";
								$stat = $stmt->FetchList($query);
								
								for($i = 0; $i < count($userList); $i++)
								{
									foreach ($stat as $row)
									{
										if($row["ManagerID"] == $userList[$i]["ID"])
										{
											if(!in_array($row["SeasonID"], $userList[$i]["SeasonIDs"]))
											{
												$userList[$i]["SeasonIDs"][] = $row["SeasonID"];
												$userList[$i]["SeasonList"][] = array("SeasonID" => $row["SeasonID"], "Title" => $row["Title"], "ReservedCount" => 0, "BoughtCount" => 0);
											}
											
											for($j = 0; $j < count($userList[$i]["SeasonList"]); $j++)
											{
												if($row["SeasonID"] == $userList[$i]["SeasonList"][$j]["SeasonID"])
												{
													if($row["StatusID"] == 3)
														$userList[$i]["SeasonList"][$j]["ReservedCount"] += $row["TotalQuantity"];
													if($row["StatusID"] == 6)
														$userList[$i]["SeasonList"][$j]["BoughtCount"] += $row["TotalQuantity"];
												}
											}
										}
									}
								}
							}
							for($i = 0; $i < count($userList); $i++)
							{
								unset($userList[$i]["SeasonIDs"]);	
							}
							if(count($userList) > 0)
							{
								$total = array("ReservedCount" => 0, "BoughtCount" => 0);
								for($i = 0; $i < count($userList); $i++)
								{
									if(count($userList[$i]["SeasonList"]) > 0)
									{
										$subtotal = array("ReservedCount" => 0, "BoughtCount" => 0);
										for($j = 0; $j < count($userList[$i]["SeasonList"]); $j++)
										{
											$subtotal["ReservedCount"] += $userList[$i]["SeasonList"][$j]["ReservedCount"];
											$subtotal["BoughtCount"] += $userList[$i]["SeasonList"][$j]["BoughtCount"];
											$total["ReservedCount"] += $userList[$i]["SeasonList"][$j]["ReservedCount"];
											$total["BoughtCount"] += $userList[$i]["SeasonList"][$j]["BoughtCount"];
										}
										$userList[$i]["SeasonList"][] = $subtotal;
									}
								}
								$userList[] = $total;
							}
							$resultList = $userList;
							break;
					}

					$item["ResultList"] = $resultList;
					$item["TotalList"]  = (isset($totalList)) ? $totalList : array();
					
					switch ($request->GetProperty("Output"))
					{
						case "Print":
							$popupPage = new PopupPage("crm", true);
							$content = $popupPage->Load("report_view_result.html");
							$content->SetLoop("ResultList", $resultList);
							$content->LoadFromArray($item);
							$content->LoadFromObject($request, array('Output'));
							$popupPage->Output($content);
							exit();
							break;
						case "Excel":
							es_include("phpexcel/PHPExcel.php");
							es_include("phpexcel/PHPExcel/Writer/Excel5.php");
		
							$popupPage = new PopupPage("crm", true);
							$content = $popupPage->Load("report_view_result.html");
							$content->SetLoop("ResultList", $resultList);
							$content->LoadFromArray($item);
							$content->LoadFromObject($request, array('Output'));
							$html = $popupPage->Grab($content);
							$html = str_replace("utf-8", "windows-1251", $html);
							$html = mb_convert_encoding($html, "cp1251", "utf-8");
							
							$tmpfile = PROJECT_DIR."var/log/".date("U").rand(0, 999).'.html';
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
							header("Content-Disposition: attachment; filename='report.xlsx'" );
							
							$objWriter = PHPExcel_IOFactory::createWriter($content, 'Excel2007');
							$objWriter->save('php://output');
							
							// Delete temporary file
							unlink($tmpfile);
							exit();
							break;
					}
				}
				break;
		}
	}
}

?>