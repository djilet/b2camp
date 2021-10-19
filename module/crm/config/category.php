<?php 
$GLOBALS['entityConfig']['category'] = array(
	'Access' => array(INTEGRATOR, ADMINISTRATOR),
	'Hidden' => true,
	'Table' => 'crm_category',
	'ID' => 'CategoryID',
	'ItemsPerPage' => 20,
	'ItemsOrderBy' => 't.CategoryID',
	'ListTemplate' => 'category_list.html',
	'ListConfig' => array(
		'Fields' => array(
			array(
				'Name' => 'Title',
				'Type' => 'field',
			),
		),
	),
	'EditTemplate' => 'category_edit.html',
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