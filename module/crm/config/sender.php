<?php 
$GLOBALS['entityConfig']['sender'] = array(
	'Access' => array(INTEGRATOR, ADMINISTRATOR, MANAGER),
	'Hidden' => true,
	'Table' => 'crm_mailing_sender',
	'ID' => 'SenderID',
	'ItemsPerPage' => 20,
	'ItemsOrderBy' => 't.Email',
	'ListTemplate' => 'sender_list.html',
	'ListConfig' => array(
		'Fields' => array(
			array(
				'Name' => 'Email',
				'Type' => 'field',
			),
		),
	),
	'EditTemplate' => 'sender_edit.html',
	'EditConfig' => array(
		'Fields' => array(
			array(
				'Name' => 'Email',
				'Type' => 'field',
				'Required' => true,
				'Validate' => 'email',
			),
		),
	),
)

?>