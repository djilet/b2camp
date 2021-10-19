<?php 
$GLOBALS['entityConfig']['bookkeepingout'] = array(
	'Access' => array(INTEGRATOR, ADMINISTRATOR, MANAGER),
	'AdminMenuIcon' => 'fa fa-book',
	'Table' => 'crm_bookkeeping',
	'ID' => 'BookkeepingID',
	'ItemsPerPage' => 20,
	'ItemsOrderBy' => 't.Date DESC',
	'ShowExportButton' => true,
	'ShowReassignButton' => false,
	'ShowClearPanelButton' => true,
	'Hidden' => true,
	'ListTemplate' => 'bookkeeping_list.html',
	'ListConfig' => array(
		'Fields' => array(
			array(
				'Name' => 'Date',
				'Type' => 'field',	
			),
			array(
				'Name' => 'DocumentNumber',
				'Type' => 'field',
			),
			array(
				'Name' => 'Base',
				'Type' => 'field'
			),
			array(
				'Name' => 'Bill',
				'Type' => 'component',
				'File' => 'components/directory.php',
				'Class' => 'DirectoryViewComponent',
				'Config' => array(
// 					'Table' => 'crm_child_phone',
// 					'KeyField' => 'ChildID',
					'DirectoryType' => 1,
					'ViewId' => 'BillID',
					'ViewName' => 'BillName',
				),
			),
			array(
				'Name' => 'IncomeType',
				'Type' => 'component',
				'File' => 'components/directory.php',
				'Class' => 'DirectoryViewComponent',
				'Config' => array(
					'DirectoryType' => 2,
					'ViewId' => 'BillID',
					'ViewName' => 'IncomeName',
				),
			),
			array(
				'Name' => 'OutcomeType',
				'Type' => 'component',
				'File' => 'components/directory.php',
				'Class' => 'DirectoryViewComponent',
				'Config' => array(
					'DirectoryType' => 3,
					'ViewId' => 'BillID',
					'ViewName' => 'IncomeName',
				),
			),	
			array(
				'Name' => 'FIO',
				'Type' => 'sql',
				'SQL' => 'CONCAT(t.Lastname," ",t.Firstname," ",t.Secondname)',
			),
			array(
				'Name' => 'Contractor',
				'Type' => 'field',
			),
			array(
				'Name' => 'Manager',
				'Type' => 'component',
				'File' => 'components/linked.php',
				'Class' => 'LinkedViewComponent',
				'Config' => array(
					'Table' => 'user',
					'FromField' => 'ManagerID',
					'ToField' => 'UserID',
					'ViewField' => 'Title',
					'ViewSQL' => 'CONCAT(LastName, \' \', FirstName) AS Name',
				),
			),
				
			array(
				'Name' => '',
				'Type' => 'component',
				'File' => 'components/bookkeeping.php',
				'Class' => 'BookkeepingAmountViewComponent',
				'Config' => array(
				// 					'Table' => 'crm_child_phone',
				// 					'KeyField' => 'ChildID',
// 						'DirectoryType' => 1,
// 						'ViewId' => 'BillID',
// 						'ViewName' => 'BillName',
				),
			),
		),
		'Filters' => array(
			array(
				'Name' => 'FilterDateFrom',
				'File' => 'filters/date.php',
				'Class' => 'DateFilter',
				'Config' => array(
					'Name' => 'FilterDateFrom',
					'Fields' => array(
						'Date',
					),
					'Operation' => '>='
				),
			),
			array(
				'Name' => 'FilterDateTo',
				'File' => 'filters/date.php',
				'Class' => 'DateFilter',
				'Config' => array(
					'Name' => 'FilterDateTo',
					'Fields' => array(
						'Date',
					),
					'Operation' => '<='
				),
			),
			array(
				'Name' => 'FilteCheck',
				'Field' => 'Check'
			),
			array(
				'Name' => 'FilteArticleType',
				'Field' => 'ArticleType'
			),
			array(
				'Name' => 'FilterDirectoryCheckID',
				'File' => 'filters/directory.php',
				'Class' => 'DirectoryFilter',
				'Config' => array(
					'DirectoryType' => 1,
					// 					'ViewId' => 'ArticleID',
			// 					'ViewName' => 'ArticleName',
					'Name' => 'FilterDirectoryCheckID',
					'ArrayName' => 'FilterDirectoryCheckList',
					'Table' => 'crm_directory',
					'FromField' => 'Check',
					'ToField' => 'DirectoryID',
					'ViewField' => 'Name',
				),
			),
			array(
				'Name' => 'FilterDirectoryIncomeID',
				'File' => 'filters/directory.php',
				'Class' => 'DirectoryFilter',
				'Config' => array(
					'DirectoryType' => 2,
// 					'ViewId' => 'ArticleID',
// 					'ViewName' => 'ArticleName',
					'Name' => 'FilterDirectoryIncomeID',
					'ArrayName' => 'FilterDirectoryIncomeList',
					'Table' => 'crm_directory',
					'FromField' => 'ArticleID',
					'ToField' => 'DirectoryID',
					'ViewField' => 'Name',	
				),
			),
			array(
				'Name' => 'FilterDirectoryOutcomeID',
				'File' => 'filters/directory.php',
				'Class' => 'DirectoryFilter',
				'Config' => array(
					'DirectoryType' => 3,
// 					'ViewId' => 'ArticleID',
// 					'ViewName' => 'ArticleName',
					'Name' => 'FilterDirectoryOutcomeID',
					'ArrayName' => 'FilterDirectoryOutcomeList',
					'Table' => 'crm_directory',
					'FromField' => 'ArticleID',
					'ToField' => 'DirectoryID',
					'ViewField' => 'Name',
				),
			),
				
			array(
				'Name' => 'FilterDocumentNumber',
				'Field' => 'DocumentNumber'
			),
			array(
				'Name' => 'FilterLastname',
				'Field' => 'Lastname'
			),
			array(
				'Name' => 'FilterContractor',
				'Field' => 'Contractor'
			),
			array(
				'Name' => 'FilterManagerID',
				'File' => 'filters/manager.php',
				'Class' => 'ManagerFilter',
				'Config' => array(
					'Name' => 'FilterManagerID',
					'ArrayName' => 'FilterManagerList',
					'Table' => 'user',
					'FromField' => 'ManagerID',
					'ToField' => 'UserID',
					'ViewField' => 'LastName, FirstName',
				)
			),
			array(
				'Name' => 'FilterAmount',
				'Field' => 'Amount'
			),
		),
	),
	'ViewTemplate' => 'bookkeeping_view.html',
	'ViewConfig' => array(
		'Fields' => array(
			array(
				'Name' => 'Base',
				'Type' => 'field',
			),
			array(
				'Name' => 'Date',
				'Type' => 'field',
			),
			array(
				'Name' => 'FIO',
				'Type' => 'sql',
				'SQL' => 'CONCAT(t.Lastname," ",t.Firstname," ",t.Secondname)',
			),
			array(
				'Name' => 'Contractor',
				'Type' => 'field',
			),
			array(
				'Name' => 'RealComment',
				'Type' => 'field',
			),
			array(
				'Name' => 'ArticleType',
				'Type' => 'field',
			),
			array(
				'Name' => 'Manager',
				'Type' => 'component',
				'File' => 'components/linked.php',
				'Class' => 'LinkedViewComponent',
				'Config' => array(
					'Table' => 'user',
					'FromField' => 'ManagerID',
					'ToField' => 'UserID',
					'ViewField' => 'Title',
					'ViewSQL' => 'CONCAT(LastName, \' \', FirstName)',
				),
			),
			array(
				'Name' => 'DocumentNumber',
				'Type' => 'field',
			),
			array(
				'Name' => 'Bill',
				'Type' => 'component',
				'File' => 'components/directory.php',
				'Class' => 'DirectoryViewComponent',
				'Config' => array(
					'DirectoryType' => 1,
					'ViewId' => 'BillID',
					'ViewName' => 'BillName',
				),
			),
			array(
				'Name' => 'IncomeType',
				'Type' => 'component',
				'File' => 'components/directory.php',
				'Class' => 'DirectoryViewComponent',
				'Config' => array(
					'DirectoryType' => 2,
					'ViewId' => 'IncomeTypeID',
					'ViewName' => 'IncomeTypeName',
				),
			),
			array(
				'Name' => 'OutcomeType',
				'Type' => 'component',
				'File' => 'components/directory.php',
				'Class' => 'DirectoryViewComponent',
				'Config' => array(
					'DirectoryType' => 3,
					'ViewId' => 'OutcomeTypeID',
					'ViewName' => 'OutcomeTypeName',
				),
			),
		),
	),
	'PrintListTemplate' => 'bookkeeping_print.html',
	'PrintListConfig' => array(
		'Fields' => array(
// 			array(
// 				'Name' => 'FIO',
// 				'Type' => 'sql',
// 				'SQL' => 'CONCAT(t.LastName," ",t.FirstName)',
// 			),		
			array(
				'Name' => 'Amount',
				'Type' => 'field',
			),
// 			array(
// 				'Name' => 'Age',
// 				'Type' => 'component',
// 				'File' => 'components/age.php',
// 				'Class' => 'AgeViewComponent',
// 				'Config' => array(
// 					'DOBField' => 'DOB',
// 				),
// 			),
// 			array(
// 				'Name' => 'School',
// 				'Type' => 'component',
// 				'File' => 'components/linked.php',
// 				'Class' => 'LinkedViewComponent',
// 				'Config' => array(
// 					'Table' => 'crm_school',
// 					'FromField' => 'SchoolID',
// 					'ToField' => 'SchoolID',
// 					'ViewField' => 'Title',
// 				),
// 			),
// 			array(
// 				'Name' => 'Class',
// 				'Type' => 'field',
// 			),
// 			array(
// 				'Name' => 'Friends',
// 				'Type' => 'component',
// 				'File' => 'components/linked.php',
// 				'Class' => 'LinkedMultipleSelectViewComponent',
// 				'Config' => array(
// 					'Table' => 'crm_child',
// 					'KeyField' => 'ChildID',
// 					'LinkTable' => 'crm_child2child',
// 					'LinkFromField' => 'ChildID',
// 					'LinkToField' => 'FriendID',
// 					'ViewSQL' => 'CONCAT(t.LastName," ",t.FirstName) AS Title',
// 					'Symmetric' => true,
// 				),
// 			),
// 			array(
// 				'Name' => 'Staff',
// 				'Type' => 'component',
// 				'File' => 'components/linked.php',
// 				'Class' => 'LinkedMultipleSelectViewComponent',
// 				'Config' => array(
// 					'Table' => 'crm_staff',
// 					'KeyField' => 'StaffID',
// 					'LinkTable' => 'crm_child2staff',
// 					'LinkFromField' => 'ChildID',
// 					'LinkToField' => 'StaffID',
// 					'ViewSQL' => 'CONCAT(t.LastName," ",t.FirstName) AS Title',
// 				),
// 			),
// 			array(
// 				'Name' => 'Season',
// 				'Type' => 'component',
// 				'File' => 'components/linked.php',
// 				'Class' => 'LinkedMultipleSelectViewComponent',
// 				'Config' => array(
// 					'Table' => 'crm_season',
// 					'KeyField' => 'SeasonID',
// 					'LinkTable' => 'crm_child2season',
// 					'LinkFromField' => 'ChildID',
// 					'LinkToField' => 'SeasonID',
// 					'ViewSQL' => 't.Title',
// 				),
// 			),
// 			array(
// 				'Name' => 'Base',
// 				'Type' => 'field',
// 			),
		),
	),
	'EditTemplate' => 'bookkeepingout_edit.html',
	'EditConfig' => array(
		'Fields' => array(
			array(
				'Name' => 'DocumentNumber',
				'Type' => 'field',		
			),
			array(
				'Name' => '',
				'Type' => 'component',
				'File' => 'components/bookkeeping.php',
				'Class' => 'BookkeepingViewComponent',
				'Config' => array(
// 						'DirectoryType' => 2,
					'ViewId' => 'BillID',
					'ViewName' => 'IncomeName',
					'FromField' => 'ArticleID',
				),
			),
			array(
				'Name' => 'Lastname',
				'Type' => 'field',
			),
			array(
				'Name' => 'Firstname',
				'Type' => 'field',
			),
			array(
				'Name' => 'Secondname',
				'Type' => 'field',
			),
			array(
				'Name' => 'Base',
				'Type' => 'field',
				'Required' => true,
				'Validate' => 'empty',
			),					
			array(
				'Name' => 'Date',
				'Type' => 'date',
				'Required' => true,
				'Validate' => 'date'
			),
// 			array(
// 				'Name' => 'ArticleIncome',
// 				'Type' => 'component',
// 				'File' => 'components/linked.php',
// 				'Class' => 'LinkedEditComponent',
// 				'Config' => array(
// 					'Table' => 'crm_directory',
// 					'FromField' => 'ArticleID',
// 					'ToField' => 'DirectoryID',
// 					'ViewField' => 'Title',
// 				),
// 			),
// 			array(
// 				'Name' => 'IncomeType',
// 				'Type' => 'component',
// 				'File' => 'components/directory.php',
// 				'Class' => 'DirectoryViewComponent',
// 				'Config' => array(
// 					'DirectoryType' => 2,
// 					'ViewId' => 'IncomeTypeID',
// 					'ViewName' => 'IncomeTypeName',
// 				),
// 			),
			array(
				'Name' => 'ArticleType',
				'Type' => 'field',
			),
			array(
				'Name' => 'Check',
				'Type' => 'component',
				'File' => 'components/directory.php',
				'Class' => 'DirectoryEditComponent',
				'Required' => true,	
				'Config' => array(
					'DirectoryType' => 1,
					'ViewId' => '`Check`',
					'ViewName' => 'CheckName',	
					'FromField' => '`Check`',
					'Required' => true,	
				),
			),
// 				array(
// 						'Name' => 'Check',
// 						'Type' => 'component',
// 						'File' => 'components/linked.php',
// 						'Class' => 'LinkedEditComponent',
// 						'Config' => array(
// 								'Table' => 'crm_directory',
// 								'FromField' => 'Check',
// 								'ToField' => 'DirectoryID',
// 								'ViewField' => 'Title',
// 						),
// 				),
			array(
				'Name' => 'ArticleID',
				'Type' => 'component',
				'File' => 'components/directory.php',
				'Class' => 'DirectoryEditComponent',
				'Config' => array(
					'DirectoryType' => isset($_GET['ArticleType']) && $_GET['ArticleType'] == 2 ? 3 : 2,
					'ViewId' => 'ArticleID',
					'ViewName' => 'ArticleName',
					'FromField' => 'ArticleID',
					'Required' => true,	
				),
			),

		
			array(
				'Name' => 'Amount',
				'Type' => 'field',
				'Required' => true,
				'Validate' => 'empty',
			),
			array(
				'Name' => 'Contractor',
				'Type' => 'field',
			),
			array(
				'Name' => 'RealComment',
				'Type' => 'field',
			),
// 			array(
// 				'Name' => 'ManagerID',
// 				'Type' => 'component',
// 				'File' => 'components/manager.php',
// 				'Class' => 'ManagerEditComponent',
// 				'Config' => array(
// 					'Table' => 'user',
// 					'FromField' => 'ManagerID',
// 					'ToField' => 'UserID',
// 					'ViewField' => 'CONCAT(LastName, \' \', FirstName)',
// 				),
// 			),
			array(
				'Name' => 'Manager',
				'Type' => 'component',
				'File' => 'components/linked.php',
				'Class' => 'ManagerEditSecondComponent',
				'Required' => true,	
				'Config' => array(
					'Table' => 'user',
					'FromField' => 'ManagerID',
					'ToField' => 'UserID',
					'ViewField' => 'CONCAT(LastName, \' \', FirstName) AS Name',
					'Required' => true,
				),
			),
		),
	),
	'ActionConfig' => array(

		'Export' => array(
			'Access' => array(INTEGRATOR, ADMINISTRATOR),	
			'File' => 'actions/export.php',
			'Class' => 'ExportAction',
			'Config' => array(
				'Template' => 'bookkeeping_export.html',
				'Entity' => 'bookkeeping',
				'Table' => 'crm_bookkeeping',
				'ID' => 'BookkeepingID',
				'ItemsPerPage' => 0, 
				'ItemsOrderBy' => 't.BookkeepingID',
				'ListConfig' => array(
					'Fields' => array(
						array(
							'Name' => 'FIO',
							'Type' => 'sql',
							'SQL' => 'CONCAT(t.Lastname," ",t.Firstname," ",t.Secondname)',
						),
						array(
							'Name' => 'Contractor',
							'Type' => 'field'
						),
// 						array(
// 							'Name' => 'Lastname',
// 							'Type' => 'field'
// 						),
// 						array(
// 							'Name' => 'Firstname',
// 							'Type' => 'field'
// 						),
// 						array(
// 							'Name' => 'Secondname',
// 							'Type' => 'field'
// 						),
						array(
							'Name' => 'Date',
							'Type' => 'field'
						),
						array(
							'Name' => 'DocumentNumber',
							'Type' => 'field'
						),
						array(
							'Name' => 'Base',
							'Type' => 'field'
						),
						array(
							'Name' => 'Date',
							'Type' => 'field'
						),
						array(
							'Name' => 'Bill',
							'Type' => 'component',
							'File' => 'components/directory.php',
							'Class' => 'DirectoryViewComponent',
							'Config' => array(
							// 					'Table' => 'crm_child_phone',
							// 					'KeyField' => 'ChildID',
								'DirectoryType' => 1,
								'ViewId' => 'BillID',
								'ViewName' => 'BillName',
							),
						),
						array(
							'Name' => 'IncomeType',
							'Type' => 'component',
							'File' => 'components/directory.php',
							'Class' => 'DirectoryViewComponent',
							'Config' => array(
								'DirectoryType' => 2,
								'ViewId' => 'BillID',
								'ViewName' => 'IncomeName',
							),
						),
						array(
							'Name' => 'OutcomeType',
							'Type' => 'component',
							'File' => 'components/directory.php',
							'Class' => 'DirectoryViewComponent',
							'Config' => array(
								'DirectoryType' => 3,
								'ViewId' => 'BillID',
								'ViewName' => 'IncomeName',
							),
						),
						array(
							'Name' => 'Manager',
							'Type' => 'component',
							'File' => 'components/linked.php',
							'Class' => 'LinkedViewComponent',
							'Config' => array(
								'Table' => 'user',
								'FromField' => 'ManagerID',
								'ToField' => 'UserID',
								'ViewField' => 'Title',
								'ViewSQL' => 'CONCAT(LastName, \' \', FirstName) AS Name',
							),
						),
					),
				),
			), 
		)
	),
)

?>
