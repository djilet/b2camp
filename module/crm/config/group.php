<?php 
$GLOBALS['entityConfig']['group'] = array(
	'Access' => array(INTEGRATOR, ADMINISTRATOR),
	'Hidden' => true,
	'Table' => 'crm_group',
	'ID' => 'GroupID',
	'ItemsPerPage' => 20,
	'ItemsOrderBy' => 't.GroupID',
	'ListConfig' => array(
		'Fields' => array(
			array(
				'Name' => 'Title',
				'Type' => 'field',
			),
		),
	),
	'EditConfig' => array(
		'Fields' => array(
			array(
				'Name' => 'Title',
				'Type' => 'field',
				'Validate' => 'empty',
			),
		),
	),
)

?>