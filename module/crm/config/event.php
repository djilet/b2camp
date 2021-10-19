<?php 
$GLOBALS['entityConfig']['event'] = array(
	'Access' => array(INTEGRATOR, ADMINISTRATOR, MANAGER),
	'Hidden' => true,
	'AdminMenuIcon' => 'fa fa-child',
	'Table' => 'crm_event',
	'ID' => 'EventID',
	'ItemsPerPage' => 0,
	'ItemsOrderBy' => 't.EventDateFrom',
	'ListTemplate' => '',
	'ListPopupTemplate' => 'event_list_popup.html',
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
				'Type' => 'field',
			),
			array(
				'Name' => 'Past',
				'Type' => 'component',
				'File' => 'components/date.php',
				'Class' => 'CurrentDateViewComponent',
				'Config' => array(
					'Name' => 'Future',
					'Conditions' => array(
						array('Field' => 'EventDateFrom', 'Operation' => '<'),
						array('Field' => 'EventDateTo', 'Operation' => '<'),
					)
				),
			),
		),
		'Filters' => array(
			array(
				'Name' => 'FilterManagerID',
				'File' => 'filters/linked.php',
				'Class' => 'LinkedFilter',
				'Config' => array(
					'Name' => 'FilterManagerID',
					'ArrayName' => 'EventManagerList',
					'Table' => 'crm_user',
					'FromField' => 'ManagerID',
					'ToField' => 'UserID',
					'ViewField' => 'CONCAT(LastName, \' \', FirstName)',
				)
			),
			array(
				'Name' => 'FilterEventType',
				'Field' => 'EventType',
			),
			array(
				'Name' => 'FilterEventDate',
				'File' => 'filters/date.php',
				'Class' => 'DateFilter',
				'Config' => array(
					'Name' => 'FilterEventDate',	
					'Fields' => array(
						'EventDateFrom'
					),
					'Operation' => '<='
				),
			),
			array(
				'Name' => 'FilterEventDate',
				'File' => 'filters/date.php',
				'Class' => 'DateFilter',
				'Config' => array(
					'Name' => 'FilterEventDate',
					'Fields' => array(
						'EventDateTo'
					),
					'Operation' => '>='
				),
			),
		)
	),
	'ViewTemplate' => '',
	'ViewConfig' => array(
		'Fields' => array(
			
		),
	),
	'EditTemplate' => '',
	'EditConfig' => array(
		'Fields' => array(
			
		),
	),
	'EditPopupTemplate' => 'event_edit_popup.html',
	'EditPopupConfig' => array(
		'Fields' => array(
			array(
				'Name' => 'Title',
				'Type' => 'field',
				'Required' => true,
				'Validate' => 'empty',
			),
			array(
				'Name' => 'ManagerID',
				'Type' => 'field',
				'Required' => true,
			),
			array(
				'Name' => 'EventDateFrom',
				'Type' => 'datetime',
				'Required' => true,
				'Validate' => 'datetime'
			),
			array(
				'Name' => 'EventDateTo',
				'Type' => 'datetime',
				'Required' => true,
				'Validate' => 'datetime'
			),
			array(
				'Name' => 'EventType',
				'Type' => 'field',
				'Validate' => 'option',
				'Options' => array('private', 'public'),
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
		),
	),
	'ActionConfig' => array(
		
	),
)

?>
