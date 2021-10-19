<?php 
$GLOBALS['entityConfig']['storage'] = array(
	'Access' => array(INTEGRATOR, ADMINISTRATOR, MANAGER),
	'RemoveAccess' => array(),
	'AdminMenuIcon' => 'fa fa-database',
	'Table' => 'crm_file',
	'ID' => 'FileID',
	'ItemsPerPage' => 20,
	'ItemsOrderBy' => 't.Created',
	'ListTemplate' => 'storage_list.html',
	'ListConfig' => array(
		'Fields' => array(
			array(
				'Name' => 'FileTitle',
				'Type' => 'field',
			),
			array(
				'Name' => 'FilePath',
				'Type' => 'sql',
				'SQL' => 'CONCAT("'.CRM_DATA_PATH.'storage/",t.FileName)',
			),
			array(
				'Name' => 'Created',
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
		),
		'Filters' => array(
			array(
				'Name' => 'FilterType',
				'Field' => 'FileType'
			),
			array(
				'Name' => 'FilterTitle',
				'Field' => 'FileTitle'
			),
		)
	),
)

?>
