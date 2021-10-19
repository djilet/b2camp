<?php 
$GLOBALS['entityConfig']['dashboard'] = array(
	'Access' => array(INTEGRATOR, ADMINISTRATOR, MANAGER),
	'Hidden' => true,
	'AdminMenuIcon' => 'fa fa-dashboard',
	'Table' => 'crm_dashboard',
	'ID' => 'DashboardID',
	'ItemsPerPage' => 20,
	'ItemsOrderBy' => 't.DashboardID',
	'ListTemplate' => '',
	'ListConfig' => array(
		'Fields' => array(
			
		),
		'Filters' => array(
			
		),
	),
	'ViewTemplate' => 'dashboard_view.html',
	'ViewConfig' => array(
		'Fields' => array(
			array(
				'Name' => 'PublicEvent',
				'Type' => 'component',
				'File' => 'components/itemlist.php',
				'Class' => 'ItemListViewComponent',
				'Config' => array(
					'Table' => 'crm_event',
					'ID' => 'EventID',
					'ItemsOrderBy' => 't.EventDateFrom',
					'ListConfig' => array(
						'Fields' => array(
							array(
								'Name' => 'Title',
								'Type' => 'field',
							),
							array(
								'Name' => 'EventDateFrom',
								'Type' => 'field',
							),
							array(
								'Name' => 'EventDateTo',
								'Type' => 'field',
							),
							array(
								'Name' => 'EventType',
								'Type' => 'Field',
							),
						),
						'Filters' => array(
							array(
								'Name' => 'EventType',
								'File' => 'filters/predefined.php',
								'Class' => 'PredefinedFilter',
								'Config' => array(
									'Name' => 'EventType',
									'Field' => 'EventType',
									'Value' => 'public',
								),
							),
							array(
								'Name' => 'FilterDateFrom',
								'File' => 'filters/date.php',
								'Class' => 'DateFilter',
								'Config' => array(
									'Name' => 'FilterDateFrom',
									'Fields' => array(
											'DateFrom',
											'DateTo'
									),
									'Operation' => '>='
								),
							),
						)
					),
				),
			),
			array(
				'Name' => 'ChildBirthday',
				'Type' => 'component',
				'File' => 'components/itemlist.php',
				'Class' => 'ItemListViewComponent',
				'Config' => array(
					'Table' => 'crm_child',
					'ID' => 'ChildID',
					'ItemsOrderBy' => 't.LastName, t.FirstName',
					'ListConfig' => array(
						'Fields' => array(
							array(
								'Name' => 'LastName',
								'Type' => 'field',
							),
							array(
								'Name' => 'FirstName',
								'Type' => 'field',
							),
							array(
								'Name' => 'Image',
								'Type' => 'component',
								'File' => 'components/image.php',
								'Class' => 'ImageViewComponent',
								'Config' => array(
									'Path' => 'child',
									'Image' => '23x23|8|Small',
								),
							),
							array(
								'Name' => 'Age',
								'Type' => 'component',
								'File' => 'components/age.php',
								'Class' => 'AgeViewComponent',
								'Config' => array(
									'DOBField' => 'DOB',
								),
							),
						),
						'Filters' => array(
							array(
								'Name' => 'DOB',
								'File' => 'filters/age.php',
								'Class' => 'DOBFilter',
								'Config' => array(
									'Name' => 'DOB',
									'DOBField' => 'DOB',
								),
							),
						)
					),
				),
			),
			array(
				'Name' => 'StaffBirthday',
				'Type' => 'component',
				'File' => 'components/itemlist.php',
				'Class' => 'ItemListViewComponent',
				'Config' => array(
					'Table' => 'crm_staff',
					'Join' => "user USING(UserID)",
					'ID' => 'StaffID',
					'ItemsOrderBy' => 'user.LastName, user.FirstName',
					'ListConfig' => array(
						'Fields' => array(
							array(
								'Name' => 'LastName',
								'Type' => 'field',
								'TablePrefix' => "user"
							),
							array(
								'Name' => 'FirstName',
								'Type' => 'field',
                                				'TablePrefix' => "user"
							),
							array(
								'Name' => 'Image',
								'Type' => 'component',
								'File' => 'components/image.php',
								'Class' => 'ImageViewComponent',
								'Config' => array(
									'Path' => 'staff',
									'Image' => '23x23|8|Small',
								),
							),
							array(
								'Name' => 'Age',
								'Type' => 'component',
								'File' => 'components/age.php',
								'Class' => 'AgeViewComponent',
								'Config' => array(
									'DOBField' => 'DOB',
                                    'TablePrefix' => "user"
								),
							),
						),
						'Filters' => array(
							array(
								'Name' => 'DOB',
								'File' => 'filters/age.php',
								'Class' => 'DOBFilter',
								'Config' => array(
									'Name' => 'DOB',
									'DOBField' => 'DOB',
                                    'TablePrefix' => "user"
								),
							),
						)
					),
				),
			),
			array(
				'Name' => 'BirthdayCount',
				'Type' => 'component',
				'File' => 'components/count.php',
				'Class' => 'ListCountViewComponent',
				'Config' => array(
					'Fields' => array('ChildBirthdayList', 'StaffBirthdayList')
				),
			),
			array(
				'Name' => 'ManagerStatChild',
				'Type' => 'component',
				'File' => 'components/report.php',
				'Class' => 'ReportViewComponent',
				'Config' => array(
					'Type' => 'DashboardManager',
					'SourceList' => array(
						array('Table' => 'crm_child', 'Key' => 'ChildID'),
					),
				),
			),
			array(
				'Name' => 'ManagerStatSchool',
				'Type' => 'component',
				'File' => 'components/report.php',
				'Class' => 'ReportViewComponent',
				'Config' => array(
					'Type' => 'DashboardManager',
					'SourceList' => array(
						array('Table' => 'crm_school', 'Key' => 'SchoolID'),
					),
				),
			),
			array(
				'Name' => 'ManagerStatLegal',
				'Type' => 'component',
				'File' => 'components/report.php',
				'Class' => 'ReportViewComponent',
				'Config' => array(
					'Type' => 'DashboardManager',
					'SourceList' => array(
						array('Table' => 'crm_legal', 'Key' => 'LegalID'),
					),
				),
			),
			array(
				'Name' => 'ManagerStatCall',
				'Type' => 'component',
				'File' => 'components/report.php',
				'Class' => 'ReportViewComponent',
				'Config' => array(
					'Type' => 'DashboardCall',
				),
			),
			array(
				'Name' => 'SeasonStatCamp',
				'Type' => 'component',
				'File' => 'components/report.php',
				'Class' => 'ReportViewComponent',
				'Config' => array(
					'Type' => 'DashboardSeason',
					'TypeID' => 1,
					'SourceList' => array(
						array('Table' => 'crm_child', 'Key' => 'ChildID'),
						array('Table' => 'crm_legal', 'Key' => 'LegalID'),
						array('Table' => 'crm_school', 'Key' => 'SchoolID'),
					),
				),
			),
			array(
				'Name' => 'SeasonStatPlayground',
				'Type' => 'component',
				'File' => 'components/report.php',
				'Class' => 'ReportViewComponent',
				'Config' => array(
					'Type' => 'DashboardSeason',
					'TypeID' => 2,
					'SourceList' => array(
						array('Table' => 'crm_child', 'Key' => 'ChildID'),
						array('Table' => 'crm_legal', 'Key' => 'LegalID'),
						array('Table' => 'crm_school', 'Key' => 'SchoolID'),
					),
				),
			),
			array(
				'Name' => 'SeasonStatHoliday',
				'Type' => 'component',
				'File' => 'components/report.php',
				'Class' => 'ReportViewComponent',
				'Config' => array(
					'Type' => 'DashboardSeason',
					'TypeID' => 3,
					'SourceList' => array(
						array('Table' => 'crm_child', 'Key' => 'ChildID'),
						array('Table' => 'crm_legal', 'Key' => 'LegalID'),
						array('Table' => 'crm_school', 'Key' => 'SchoolID'),
					),
				),
			),
		),
	),
	'EditTemplate' => '',
	'EditConfig' => array(
		'Fields' => array(
			
		),
	),
	'ActionConfig' => array(
		
	),
)
?>
