<?php
//currently only for load finance docs to mailing attachments 
$GLOBALS['entityConfig']['parent'] = array(
	'Access' => array(INTEGRATOR, ADMINISTRATOR, MANAGER),
	'Hidden' => true,
	//'AdminMenuIcon' => 'fa fa-building',
	'Table' => 'crm_parent',
	'ID' => 'ParentID',
	'ItemsPerPage' => 20,
	'ItemsOrderBy' => 't.LastName,t.FirstName',
	'ListTemplate' => '',
	'ListConfig' => array(
		'Fields' => array(
			
		),
		'Filters' => array(
			
		)
	),
	'ViewTemplate' => '',
	'ViewConfig' => array(
		'Fields' => array(
			array(
				'Name' => 'ChildID',
				'Type' => 'field'
			),
			array(
				'Name' => 'Contract',
				'Type' => 'component',
				'File' => 'components/finance.php',
				'Class' => 'FinanceViewComponent',
				'Config' => array(
					'Type' => 'linked',
					'ID' => 'ChildID',
					'LinkTable' => 'crm_parent',
					'FromField' => 'ParentID',
					'ToField' => 'ParentID', 
					'TargetField' => 'ChildID',
					'ContractTable' => 'crm_parent_contract',
					'InvoiceTable' => 'crm_parent_invoice',
					'PaymentTable' => 'crm_parent_payment',
					'ActTable' => 'crm_parent_act',
					'UseSeason' => true
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
		'GetContractPDF' => array(
			'File' => 'actions/finance.php',
			'Class' => 'FinanceAction',
			'Config' => array(
				'Table' => 'crm_parent_contract',
				'Template' => 'parent_contract_pdf.html',
				'Data' => array(
					array(
						'Name' => 'ChildName',	
						'Type' => 'field',
						'Path' => array(
							array(
								'Table' => 'crm_parent',
								'FromField' => 'ParentID',
								'ToField' => 'ParentID'
							),
							array(
								'Table' => 'crm_child',
								'FromField' => 'ChildID',
								'ToField' => 'ChildID',
								'SQLSelect' => 'CONCAT(d.LastName, \' \', d.FirstName, \' \', d.MiddleName)'
							),
						)
					),
					array(
						'Name' => 'ChildBirthYear',	
						'Type' => 'field',
						'Path' => array(
							array(
								'Table' => 'crm_parent',
								'FromField' => 'ParentID',
								'ToField' => 'ParentID'
							),
							array(
								'Table' => 'crm_child',
								'FromField' => 'ChildID',
								'ToField' => 'ChildID',
								'SQLSelect' => 'YEAR(d.DOB)'
							),
						)
					),
					array(
						'Name' => 'ParentName',
						'Type' => 'field',
						'Path' => array(
							array(
								'Table' => 'crm_parent',
								'FromField' => 'ParentID',
								'ToField' => 'ParentID',
								'SQLSelect' => 'CONCAT(d.LastName, \' \', d.FirstName, \' \', d.MiddleName)'
							)
						)
					),
					array(
						'Name' => 'ParentPassport',
						'Type' => 'field',
						'Path' => array(
							array(
								'Table' => 'crm_parent',
								'FromField' => 'ParentID',
								'ToField' => 'ParentID',
								'SQLSelect' => 'Passport'
							)
						)
					),
					array(
						'Name' => 'SeasonTitle',
						'Type' => 'field',
						'Path' => array(
							array(
								'Table' => 'crm_parent_contract2season',
								'FromField' => 'ContractID',
								'ToField' => 'ContractID',
							),
							array(
								'Table' => 'crm_season',
								'FromField' => 'SeasonID',
								'ToField' => 'SeasonID',
								'SQLSelect' => 'Title'
							)
						)
					),
					array(
						'Name' => 'SeasonDateTo',
						'Type' => 'field',
						'Path' => array(
							array(
								'Table' => 'crm_parent_contract2season',
								'FromField' => 'ContractID',
								'ToField' => 'ContractID',
							),
							array(
								'Table' => 'crm_season',
								'FromField' => 'SeasonID',
								'ToField' => 'SeasonID',
								'SQLSelect' => 'DateTo'
							)
						)
					),
					array(
						'Name' => 'ParentPhoneList',
						'Type' => 'list',
						'Path' => array(
							array(
								'Table' => 'crm_parent',
								'FromField' => 'ParentID',
								'ToField' => 'ParentID',
							),
							array(
								'Table' => 'crm_parent_phone',
								'FromField' => 'ParentID',
								'ToField' => 'ParentID',
								'SQLSelect' => 'd.Prefix, d.Number'
							)
						)
					),
					array(
						'Name' => 'ParentEmailList',
						'Type' => 'list',
						'Path' => array(
							array(
								'Table' => 'crm_parent',
								'FromField' => 'ParentID',
								'ToField' => 'ParentID',
							),
							array(
								'Table' => 'crm_parent_email',
								'FromField' => 'ParentID',
								'ToField' => 'ParentID',
								'SQLSelect' => 'd.Email'
							)
						)
					),
					array(
						'Name' => 'ParentAddress',
						'Type' => 'field',
						'Path' => array(
							array(
								'Table' => 'crm_parent',
								'FromField' => 'ParentID',
								'ToField' => 'ParentID',
								'SQLSelect' => 'CONCAT("г. ",d.AddressCity,", ул. ",d.AddressStreet,", ",d.AddressHome,", кв. ",d.AddressFlat)'
							),
						)
					),
				)
			), 
		),
		'GetInvoicePDF' => array(
			'File' => 'actions/finance.php',
			'Class' => 'FinanceAction',
			'Config' => array(
				'Table' => 'crm_parent_invoice',
				'ContractTable' => 'crm_parent_contract',
				'Template' => 'invoice_pdf.html',
				'UseSeason' => true,
				'InvoiceSubjectKey' => 'finance-invoice-subject-rest',
				'Data' => array(
					array(
						'Name' => 'ClientName',
						'Type' => 'field',
						'Path' => array(
							array(
								'Table' => 'crm_parent_contract',
								'FromField' => 'ContractID',
								'ToField' => 'ContractID',
							),
							array(
								'Table' => 'crm_parent',
								'FromField' => 'ParentID',
								'ToField' => 'ParentID',
								'SQLSelect' => 'CONCAT(d.LastName, \' \', d.FirstName, \' \', d.MiddleName)'
							)
						)
					),
					array(
						'Name' => 'ClientAddress',
						'Type' => 'field',
						'Path' => array(
							array(
								'Table' => 'crm_parent_contract',
								'FromField' => 'ContractID',
								'ToField' => 'ContractID',
							),
							array(
								'Table' => 'crm_parent',
								'FromField' => 'ParentID',
								'ToField' => 'ParentID',
								'SQLSelect' => 'CONCAT("г. ",d.AddressCity,", ул. ",d.AddressStreet,", ",d.AddressHome,", кв. ",d.AddressFlat)'
							),
						)
					),
					array(
						'Name' => 'ClientPhoneList',
						'Type' => 'list',
						'Path' => array(
							array(
								'Table' => 'crm_parent_contract',
								'FromField' => 'ContractID',
								'ToField' => 'ContractID',
							),
							array(
								'Table' => 'crm_parent',
								'FromField' => 'ParentID',
								'ToField' => 'ParentID',
							),
							array(
								'Table' => 'crm_parent_phone',
								'FromField' => 'ParentID',
								'ToField' => 'ParentID',
								'SQLSelect' => 'd.Prefix, d.Number'
							)
						)
					),
					array(
						'Name' => 'ClientEmailList',
						'Type' => 'list',
						'Path' => array(
							array(
								'Table' => 'crm_parent_contract',
								'FromField' => 'ContractID',
								'ToField' => 'ContractID',
							),
							array(
								'Table' => 'crm_parent',
								'FromField' => 'ParentID',
								'ToField' => 'ParentID',
							),
							array(
								'Table' => 'crm_parent_email',
								'FromField' => 'ParentID',
								'ToField' => 'ParentID',
								'SQLSelect' => 'd.Email'
							)
						)
					),
					array(
						'Name' => 'SeasonTitle',
						'Type' => 'field',
						'Path' => array(
							array(
								'Table' => 'crm_parent_contract2season',
								'FromField' => 'ContractID',
								'ToField' => 'ContractID',
							),
							array(
								'Table' => 'crm_season',
								'FromField' => 'SeasonID',
								'ToField' => 'SeasonID',
								'SQLSelect' => 'Title'
							)
						)
					),
					array(
						'Name' => 'SeasonDateFrom',
						'Type' => 'field',
						'Path' => array(
							array(
								'Table' => 'crm_parent_contract2season',
								'FromField' => 'ContractID',
								'ToField' => 'ContractID',
							),
							array(
								'Table' => 'crm_season',
								'FromField' => 'SeasonID',
								'ToField' => 'SeasonID',
								'SQLSelect' => 'DateFrom'
							)
						)
					),
					array(
						'Name' => 'SeasonDateTo',
						'Type' => 'field',
						'Path' => array(
							array(
								'Table' => 'crm_parent_contract2season',
								'FromField' => 'ContractID',
								'ToField' => 'ContractID',
							),
							array(
								'Table' => 'crm_season',
								'FromField' => 'SeasonID',
								'ToField' => 'SeasonID',
								'SQLSelect' => 'DateTo'
							)
						)
					),
				)
			), 
		),
		'GetActPDF' => array(
			'File' => 'actions/finance.php',
			'Class' => 'FinanceAction',
			'Config' => array(
				'Table' => 'crm_parent_act',
				'ContractTable' => 'crm_parent_contract',
				'Template' => 'act_pdf.html',
				'UseSeason' => true,
				'ActSubjectKey' => 'finance-act-subject-rest',
				'Data' => array(
					array(
						'Name' => 'ClientName',
						'Type' => 'field',
						'Path' => array(
							array(
								'Table' => 'crm_parent_contract',
								'FromField' => 'ContractID',
								'ToField' => 'ContractID',
							),
							array(
								'Table' => 'crm_parent',
								'FromField' => 'ParentID',
								'ToField' => 'ParentID',
								'SQLSelect' => 'CONCAT(d.LastName, \' \', d.FirstName, \' \', d.MiddleName)'
							)
						)
					),
					array(
						'Name' => 'ClientInfo',
						'Type' => 'field',
						'Path' => array(
							array(
								'Table' => 'crm_parent_contract',
								'FromField' => 'ContractID',
								'ToField' => 'ContractID',
							),
							array(
								'Table' => 'crm_parent',
								'FromField' => 'ParentID',
								'ToField' => 'ParentID',
								'SQLSelect' => 'd.Passport'
							)
						)
					),
					array(
						'Name' => 'ContractCreated',
						'Type' => 'field',
						'Path' => array(
							array(
								'Table' => 'crm_parent_contract',
								'FromField' => 'ContractID',
								'ToField' => 'ContractID',
								'SQLSelect' => 'd.Created'
							),
						)
					),
					array(
						'Name' => 'SeasonTitle',
						'Type' => 'field',
						'Path' => array(
							array(
								'Table' => 'crm_parent_contract2season',
								'FromField' => 'ContractID',
								'ToField' => 'ContractID',
							),
							array(
								'Table' => 'crm_season',
								'FromField' => 'SeasonID',
								'ToField' => 'SeasonID',
								'SQLSelect' => 'Title'
							)
						)
					),
				)
			), 
		),
	),
)

?>
