<?php 
$GLOBALS['entityConfig']['report'] = array(
	'Access' => array(ADMINISTRATOR, INTEGRATOR),
	'AdminMenuIcon' => 'fa fa-pie-chart',
	'Table' => 'crm_report',
	'ID' => 'ReportID',
	'ItemsPerPage' => 0,
	'ItemsOrderBy' => 't.SortOrder',
	'ListTemplate' => 'report_list.html',
	'ListConfig' => array(
		'Fields' => array(
			array(
				'Name' => 'Title',
				'Type' => 'field',
			),
		),
	),
	'ViewTemplate' => 'report_view.html',
	'ViewConfig' => array(
		'Fields' => array(
			array(
				'Name' => 'Title',
				'Type' => 'field',
			),
			array(
				'Name' => 'DateFrom',
				'Type' => 'component',
				'File' => 'components/data.php',
				'Class' => 'DataComponent',
				'Config' => array(
					'RequestField' => 'DateFrom',
				),
			),
			array(
				'Name' => 'DateTo',
				'Type' => 'component',
				'File' => 'components/data.php',
				'Class' => 'DataComponent',
				'Config' => array(
					'RequestField' => 'DateTo',
				),
			),
			array(
				'Name' => 'ReportManagerList',
				'Type' => 'component',
				'File' => 'components/data.php',
				'Class' => 'DataComponent',
				'Config' => array(
					'RequestField' => 'ManagerID',
					'Table' => 'user',
					'OrderBy' => 'Title',
					'KeyField' => 'UserID',
					'IsManager' => 1,
					'ViewSQL' => 'CONCAT(LastName, \' \', FirstName)',
				),
			),
			array(
				'Name' => 'ReportSeasonList',
				'Type' => 'component',
				'File' => 'components/data.php',
				'Class' => 'DataComponent',
				'Config' => array(
					'RequestField' => 'SeasonID',
					'Table' => 'crm_season',
					'OrderBy' => 'TypeID ASC, Title ASC',
					'KeyField' => 'SeasonID',
					'ViewField' => 'TypeID, Title',
				),
			),
			array(
				'Name' => 'ManagerID',
				'Type' => 'component',
				'File' => 'components/data.php',
				'Class' => 'DataComponent',
				'Config' => array(
					'RequestField' => 'ManagerID',
				),
			),
			array(
				'Name' => 'CategoryID',
				'Type' => 'component',
				'File' => 'components/data.php',
				'Class' => 'DataComponent',
				'Config' => array(
					'RequestField' => 'CategoryID',
				),
			),
			array(
				'Name' => 'Squad',
				'Type' => 'component',
				'File' => 'components/data.php',
				'Class' => 'DataComponent',
				'Config' => array(
					'RequestField' => 'Squad',
				),
			),
			array(
				'Name' => 'Age',
				'Type' => 'component',
				'File' => 'components/data.php',
				'Class' => 'DataComponent',
				'Config' => array(
					'RequestField' => 'Age',
				),
			),
			array(
				'Name' => 'SchoolID',
				'Type' => 'component',
				'File' => 'components/data.php',
				'Class' => 'DataComponent',
				'Config' => array(
					'RequestField' => 'SchoolID',
				),
			),
			array(
				'Name' => 'StatusID',
				'Type' => 'component',
				'File' => 'components/data.php',
				'Class' => 'DataComponent',
				'Config' => array(
					'RequestField' => 'StatusID',
				),
			),
			array(
				'Name' => 'ReportType',
				'Type' => 'component',
				'File' => 'components/report.php',
				'Class' => 'ReportViewComponent',
				'Config' => array(
					'Type' => 'Entity',
					'Field' => 'ReportType',
					'1' => array(
						'SourceList' => array(
							array('Table' => 'crm_child', 'Key' => 'ChildID'),
							array('Table' => 'crm_legal', 'Key' => 'LegalID'),
							array('Table' => 'crm_school', 'Key' => 'SchoolID'),
						),
					),
					'4' => array(
						'SourceList' => array(
							array('Table' => 'crm_child'),
							array('Table' => 'crm_legal'),
							array('Table' => 'crm_school'),
						),
					),
					'5' => array(
						'SourceList' => array(
							array('Table' => 'crm_parent_contract'),
						),
					),
					'6' => array(
						'SourceList' => array(
							array('Table' => 'crm_parent', 'KeyField' => 'ParentID', 'LinkTable' => 'crm_child', 'LinkField' => 'ChildID'),
							array('Table' => 'crm_school_contact', 'KeyField' => 'ContactID', 'LinkTable' => 'crm_school', 'LinkField' => 'SchoolID'),
							array('Table' => 'crm_legal', 'KeyField' => 'LegalID'),
						),
					),
					'7' => array(
						'SourceList' => array(
							array('Table' => 'crm_child', 'KeyField' => 'ChildID'),
							array('Table' => 'crm_school', 'KeyField' => 'SchoolID'),
							array('Table' => 'crm_legal', 'KeyField' => 'LegalID'),
						),
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
