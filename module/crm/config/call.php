<?php 
$GLOBALS['entityConfig']['call'] = array(
	'Access' => array(INTEGRATOR, ADMINISTRATOR),
	'Hidden' => true,
	'Table' => 'crm_call',
	'ID' => 'CallID',
	'ItemsPerPage' => 0,
	'ItemsOrderBy' => 't.CallID DESC',
	'ListTemplate' => '',
	'ListConfig' => array(
		
	),
	'EditTemplate' => '',
	'EditConfig' => array(
		'Fields' => array(
			array(
				'Name' => 'Created',
				'Type' => 'generated',
				'Value' => 'current_datetime'
			),
			array(
				'Name' => 'UserID',
				'Type' => 'field',
				'Required' => true,
				'Validate' => 'int',
			),
			array(
				'Name' => 'LinkedEntity',
				'Type' => 'field',
				'Required' => true,
				'Validate' => 'empty',
			),
			array(
				'Name' => 'LinkedEntityID',
				'Type' => 'field',
				'Required' => true,
				'Validate' => 'int',
			),
			array(
				'Name' => 'CallType',
				'Type' => 'field',
				'Required' => true,
				'Validate' => 'option',
				'Options' => array('in', 'out')
			),
			array(
				'Name' => 'Duration',
				'Type' => 'field',
				'Required' => false,
			),
			array(
				'Name' => 'RecordURL',
				'Type' => 'field',
				'Required' => false,
			),
			array(
				'Name' => 'CallKey',
				'Type' => 'field',
				'Required' => true,
				'Validate' => 'empty',
			),
		),
	),
)

?>