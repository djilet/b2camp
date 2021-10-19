<?php 
$GLOBALS['entityConfig']['legal'] = array(
	'Access' => array(INTEGRATOR, ADMINISTRATOR, MANAGER),
	'AdminMenuIcon' => 'fa fa-building',
	'Table' => 'crm_legal',
	'ID' => 'LegalID',
	'ItemsPerPage' => 20,
	'ItemsOrderBy' => 't.Title',
	'ShowClearPanelButton' => true,
	'ListDuplicateTemplate' => 'legal_list_duplicate.html',
	'ListTemplate' => 'legal_list.html',
	'ListConfig' => array(
		'Fields' => array(
			array(
				'Name' => 'Image',
				'Type' => 'component',
				'File' => 'components/image.php',
				'Class' => 'ImageViewComponent',
				'Config' => array(
					'Path' => 'legal',
					'Image' => '50x50|8|Small',
				),
			),
			array(
				'Name' => 'Title',
				'Type' => 'field',
			),
			array(
				'Name' => 'Category',
				'Type' => 'component',
				'File' => 'components/linked.php',
				'Class' => 'LinkedViewComponent',
				'Config' => array(
					'Table' => 'crm_category',
					'FromField' => 'CategoryID',
					'ToField' => 'CategoryID',
					'ViewField' => 'Title',
				),
			),
			array(
				'Name' => 'Group',
				'Type' => 'component',
				'File' => 'components/linked.php',
				'Class' => 'LinkedViewComponent',
				'Config' => array(
					'Table' => 'crm_legalgroup',
					'FromField' => 'GroupID',
					'ToField' => 'GroupID',
					'ViewField' => 'Title',
				),
			),
			array(
				'Name' => 'Phone',
				'Type' => 'component',
				'File' => 'components/phone.php',
				'Class' => 'PhoneViewComponent',
				'Config' => array(
					'Table' => 'crm_legal_phone',
					'KeyField' => 'LegalID',
				),
			),
			array(
				'Name' => 'Contact',
				'Type' => 'component',
				'File' => 'components/itemlist.php',
				'Class' => 'ItemListViewComponent',
				'Config' => array(
					'Table' => 'crm_legal_contact',
					'ID' => 'ContactID',
					'ItemsOrderBy' => 't.LastName,t.FirstName',
					'KeyField' => 'LegalID',
					'ListConfig' => array(
						'Fields' => array(
							array(
								'Name' => 'FIO',
								'Type' => 'sql',
								'SQL' => 'CONCAT(t.LastName," ",t.FirstName," ",COALESCE(t.MiddleName,""))',
							),
						),
						'Filters' => array(
							array(
								'Name' => 'EntityID',
								'File' => 'filters/linked.php',
								'Class' => 'LinkedFilter',
								'Config' => array(
									'Name' => 'EntityID',
									'FromField' => 'LegalID',
									'ToField' => 'LegalID'
								)
							)
						)
					),
				),
			),
			array(
				'Name' => 'Status',
				'Type' => 'component',
				'File' => 'components/status.php',
				'Class' => 'StatusViewComponent',
				'Config' => array(
					'KeyField' => 'LegalID',
					'StatusTable' => 'crm_legal_status'
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
		'Filters' => array(
			array(
				'Name' => 'FilterTitle', 
				'Field' => 'Title'
			),
			array(
				'Name' => 'FilterGroupID',
				'File' => 'filters/linked.php',
				'Class' => 'LinkedFilter',
				'Config' => array(
					'Name' => 'FilterGroupID',
					'ArrayName' => 'FilterGroupList',
					'Table' => 'crm_legalgroup',
					'FromField' => 'GroupID',
					'ToField' => 'GroupID',
					'ViewField' => 'Title',
				)
			),
			array(
				'Name' => 'FilterCategoryID',
				'File' => 'filters/linked.php',
				'Class' => 'LinkedFilter',
				'Config' => array(
					'Name' => 'FilterCategoryID',
					'ArrayName' => 'FilterCategoryList',
					'Table' => 'crm_category',
					'FromField' => 'CategoryID',
					'ToField' => 'CategoryID',
					'ViewField' => 'Title',
                    'IncludeKeys' => array(1,2,3,7,12,13)
				)
			),
			array(
				'Name' => 'FilterPhone',
				'File' => 'filters/phone.php',
				'Class' => 'PhoneViewFilter',
				'Config' => array(
					'Name' => 'FilterPhone',
					'Table' => 'crm_legal_phone',
					'FromField' => 'LegalID',
					'ToField' => 'LegalID',
					'CodeField' => 'Prefix',
					'NumberField' => 'Number'
				)
			),
			array(
				'Name' => 'FilterEmail',
				'File' => 'filters/email.php',
				'Class' => 'EmailFilter',
				'Config' => array(
					'Name' => 'FilterEmail',
					'Table' => 'crm_legal_email',
					'FromField' => 'LegalID',
					'ToField' => 'LegalID',
				)
			),
			array(
				'Name' => 'FilterContactID',
				'File' => 'filters/linked.php',
				'Class' => 'LinkedMultipleSelectFilter',
				'Config' => array(
					'Name' => 'FilterContactID',
					'ArrayName' => 'FilterContactList',
					'Table' => 'crm_legal_contact',
					'LinkTable' => 'crm_legal_contact',
					'FromField' => 'LegalID',
					'LinkFromField' => 'LegalID',
					'LinkToField' => 'ContactID',
					'ToField' => 'ContactID',
					'ViewField' => 'LastName, FirstName',
				)
			),
			array(
				'Name' => 'FilterStatusID',
				'File' => 'filters/status.php',
				'Class' => 'StatusFilter',
				'Config' => array(
					'StatusName' => 'FilterStatusID',
					'StatusArrayName' => 'FilterStatusList',
					'SeasonName' => 'FilterStatusSeasonID',
					'SeasonArrayName' => 'FilterStatusSeasonList',
					'Key' => 'LegalID',
					'StatusTable' => 'crm_legal_status',
					'EntitySeasonTable' => 'crm_legal2season'
				)
			),
			array(
				'Name' => 'FilterFinanceContractID',
				'File' => 'filters/linked.php',
				'Class' => 'CustomLinkedFilter',
				'Config' => array(
					'Name' => 'FilterFinanceContractID',
					'Path' => array(
						array(
							'Table' => 'crm_legal_contract',
							'FromField' => 'LegalID',
							'ToField' => 'LegalID',
							'Field' => 'ContractID',
							'Operation' => '='
						),
					)
				)
			),
			array(
				'Name' => 'FilterFinanceInvoiceID',
				'File' => 'filters/linked.php',
				'Class' => 'CustomLinkedFilter',
				'Config' => array(
					'Name' => 'FilterFinanceInvoiceID',
					'Path' => array(
						array(
							'Table' => 'crm_legal_contract',
							'FromField' => 'LegalID',
							'ToField' => 'LegalID',
						),
						array(
							'Table' => 'crm_legal_invoice',
							'FromField' => 'ContractID',
							'ToField' => 'ContractID',
							'Field' => 'InvoiceID',
							'Operation' => '='
						),
					)
				)
			),
			array(
				'Name' => 'FilterFinanceAmount',
				'File' => 'filters/linked.php',
				'Class' => 'CustomLinkedFilter',
				'Config' => array(
					'Name' => 'FilterFinanceAmount',
					'Path' => array(
						array(
							'Table' => 'crm_legal_contract',
							'FromField' => 'LegalID',
							'ToField' => 'LegalID',
							'Field' => 'Amount',
							'Operation' => '='
						)
					)
				)
			),
			array(
				'Name' => 'FilterFinanceDateFrom',
				'File' => 'filters/linked.php',
				'Class' => 'CustomLinkedFilter',
				'Config' => array(
					'Name' => 'FilterFinanceDateFrom',
					'Type' => 'date',
					'Path' => array(
						array(
							'Table' => 'crm_legal_contract',
							'FromField' => 'LegalID',
							'ToField' => 'LegalID',
							'Field' => 'Created',
							'SQLTemplate' => 'DATE(#Field#)',
							'Operation' => '>='
						)
					)
				)
			),
			array(
				'Name' => 'FilterFinanceDateTo',
				'File' => 'filters/linked.php',
				'Class' => 'CustomLinkedFilter',
				'Config' => array(
					'Name' => 'FilterFinanceDateTo',
					'Type' => 'date',
					'Path' => array(
						array(
							'Table' => 'crm_legal_contract',
							'FromField' => 'LegalID',
							'ToField' => 'LegalID',
							'Field' => 'Created',
							'SQLTemplate' => 'DATE(#Field#)',
							'Operation' => '<='
						)
					)
				)
			),
			array(
				'Name' => 'FilterFinanceSeasonID',
				'File' => 'filters/linked.php',
				'Class' => 'CustomLinkedFilter',
				'Config' => array(
					'Name' => 'FilterFinanceSeasonID',
					'Table' => 'crm_season',
					'ToField' => 'SeasonID',
					'ViewField' => 'Title',
					'ArrayName' => 'FilterFinanceSeasonList',
					'Path' => array(
						array(
							'Table' => 'crm_legal_contract',
							'FromField' => 'LegalID',
							'ToField' => 'LegalID',
						),
						array(
							'Table' => 'crm_legal_contract2season',
							'FromField' => 'ContractID',
							'ToField' => 'ContractID',
							'Field' => 'SeasonID',
							'Operation' => '='
						)
					)
				)
			),
			// 			array(
			// 				'Name' => 'FilterFinanceManagerID',
			// 				'File' => 'filters/linked.php',
			// 				'Class' => 'CustomLinkedFilter',
			// 				'Config' => array(
			// 					'Name' => 'FilterFinanceManagerID',
			// 					'Table' => 'user',
			// 					'ToField' => 'UserID',
			// 					'ViewField' => 'LastName, FirstName',
			// 					'ArrayName' => 'FilterFinanceManagerList',
			// 					'Path' => array(
			// 						array(
			// 							'Table' => 'crm_legal_contract',
			// 							'FromField' => 'LegalID',
			// 							'ToField' => 'LegalID',
			// 							'Field' => 'ManagerID',
			// 							'Operation' => '='
			// 						)
			// 					)
			// 				)
			// 			),
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
		)
	),
	'ViewTemplate' => 'legal_view.html',
	'ViewConfig' => array(
		'Fields' => array(
			array(
				'Name' => 'Image',
				'Type' => 'component',
				'File' => 'components/image.php',
				'Class' => 'ImageViewComponent',
				'Config' => array(
					'Path' => 'legal',
					'Image' => '300x300|8|Small',
				),
			),
			array(
				'Name' => 'Comment',
				'Type' => 'field',
			),
			array(
				'Name' => 'CreateDate',
				'Type' => 'field',
			),
			array(
				'Name' => 'Title',
				'Type' => 'field',
			),
			array(
				'Name' => 'Commission',
				'Type' => 'field',
			),
			array(
				'Name' => 'Group',
				'Type' => 'component',
				'File' => 'components/linked.php',
				'Class' => 'LinkedViewComponent',
				'Config' => array(
					'Table' => 'crm_legalgroup',
					'FromField' => 'GroupID',
					'ToField' => 'GroupID',
					'ViewField' => 'Title',
				),
			),
			array(
				'Name' => 'Category',
				'Type' => 'component',
				'File' => 'components/linked.php',
				'Class' => 'LinkedViewComponent',
				'Config' => array(
					'Table' => 'crm_category',
					'FromField' => 'CategoryID',
					'ToField' => 'CategoryID',
					'ViewField' => 'Title',
				),
			),
			array(
				'Name' => 'Phone',
				'Type' => 'component',
				'File' => 'components/phone.php',
				'Class' => 'PhoneViewComponent',
				'Config' => array(
					'Table' => 'crm_legal_phone',
					'KeyField' => 'LegalID',
				),
			),
			array(
				'Name' => 'Call',
				'Type' => 'component',
				'File' => 'components/itemlist.php',
				'Class' => 'ItemListViewComponent',
				'Config' => array(
					'Table' => 'crm_call',
					'ID' => 'CallID',
					'ItemsOrderBy' => 't.Created DESC',
					'KeyField' => 'EntityID',
					'ListConfig' => array(
						'Fields' => array(
							array(
								'Name' => 'Created',
								'Type' => 'field',
							),
							array(
								'Name' => 'Duration',
								'Type' => 'field',
							),
							array(
								'Name' => 'Manager',
								'Type' => 'component',
								'File' => 'components/linked.php',
								'Class' => 'LinkedViewComponent',
								'Config' => array(
									'Table' => 'user',
									'FromField' => 'UserID',
									'ToField' => 'UserID',
									'ViewField' => 'Name',
									'ViewSQL' => 'CONCAT(LastName, \' \', FirstName) AS Name',
								),
							),
							array(
								'Name' => 'RecordURL',
								'Type' => 'field',
							),
						),
						'Filters' => array(
							array(
								'Name' => 'LinkedEntityID',
								'File' => 'filters/linked.php',
								'Class' => 'LinkedFilter',
								'Config' => array(
									'Name' => 'EntityID',
									'FromField' => 'LinkedEntityID',
									'ToField' => 'LegalID'
								)
							),
							array(
								'Field' => 'LinkedEntity',
								'Operation' => '=',
								'Value' => 'legal',
							),
						)
					),
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
			array(
				'Name' => 'Email',
				'Type' => 'component',
				'File' => 'components/email.php',
				'Class' => 'EmailViewComponent',
				'Config' => array(
					'Table' => 'crm_legal_email',
					'KeyField' => 'LegalID',
				),
			),
			array(
				'Name' => 'Website',
				'Type' => 'field',
			),
			array(
				'Name' => 'Address',
				'Type' => 'sql',
				'SQL' => 'CONCAT("г. ",t.AddressCity,", ул. ",t.AddressStreet,", ",t.AddressHome,", кв. ",t.AddressFlat)',
			),
			array(
				'Name' => 'Season',
				'Type' => 'component',
				'File' => 'components/linked.php',
				'Class' => 'LinkedMultipleSelectViewComponent',
				'Config' => array(
					'Table' => 'crm_season',
					'KeyField' => 'SeasonID',
					'LinkTable' => 'crm_legal2season',
					'LinkFromField' => 'LegalID',
					'LinkToField' => 'SeasonID',
					'ViewSQL' => 't.Title',
				),
			),
			array(
				'Name' => 'ActiveSeason',
				'Type' => 'component',
				'File' => 'components/itemlist.php',
				'Class' => 'ItemListViewComponent',
				'Config' => array(
					'Table' => 'crm_season',
					'ID' => 'SeasonID',
					'ItemsOrderBy' => 't.TypeID, t.Title',
					'KeyField' => 'LegalID',
					'ListConfig' => array(
						'Fields' => array(
							array(
								'Name' => 'Title',
								'Type' => 'field',
							),
                            array(
                                'Name' => 'TypeID',
                                'Type' => 'field',
                            ),
						),
						'Filters' => array(
							array(
								'Field' => 'Archive',
								'Value' => 'N',	
								'Operation' => '=',	
							)
						)
					),
				),
			),
			array(
				'Name' => 'File',
				'Type' => 'component',
				'File' => 'components/file.php',
				'Class' => 'FileViewComponent',
				'Config' => array(
					'EntityType' => 'legal',
					'Path' => 'storage',
				),
			),
			array(
				'Name' => 'Requisites',
				'Type' => 'field',
			),
			array(
				'Name' => 'Status',
				'Type' => 'component',
				'File' => 'components/status.php',
				'Class' => 'StatusEditComponent',
				'Config' => array(
					'StatusTable' => 'crm_legal_status',
					'KeyField' => 'LegalID',
					'CustomQuantity' => true,
					'Entity' => "legal"
				),
			),
			array(
				'Name' => 'Contact',
				'Type' => 'component',
				'File' => 'components/itemlist.php',
				'Class' => 'ItemListViewComponent',
				'Config' => array(
					'Table' => 'crm_legal_contact',
					'ID' => 'ContactID',
					'ItemsOrderBy' => 't.LastName,t.FirstName',
					'KeyField' => 'LegalID',
					'ListConfig' => array(
						'Fields' => array(
							array(
								'Name' => 'FIO',
								'Type' => 'sql',
								'SQL' => 'CONCAT(t.LastName," ",t.FirstName," ",COALESCE(t.MiddleName,""))',
							),
							array(
								'Name' => 'Post',
								'Type' => 'field',
							),
							array(
								'Name' => 'Phone',
								'Type' => 'component',
								'File' => 'components/phone.php',
								'Class' => 'PhoneViewComponent',
								'Config' => array(
									'Table' => 'crm_legal_contact_phone',
									'KeyField' => 'ContactID',
								),
							),
							array(
								'Name' => 'Email',
								'Type' => 'component',
								'File' => 'components/email.php',
								'Class' => 'EmailViewComponent',
								'Config' => array(
									'Table' => 'crm_legal_contact_email',
									'KeyField' => 'ContactID',
								),
							),
							array(
								'Name' => 'Passport',
								'Type' => 'field',
							),
						),
						'Filters' => array(
							array(
								'Name' => 'EntityID',
								'File' => 'filters/linked.php',
								'Class' => 'LinkedFilter',
								'Config' => array(
									'Name' => 'EntityID',
									'FromField' => 'LegalID',
									'ToField' => 'LegalID'
								)
							)
						)
					),
				),
			),
			array(
				'Name' => 'Comment',
				'Type' => 'component',
				'File' => 'components/comment.php',
				'Class' => 'CommentViewComponent',
				'Config' => array(
					'Table' => 'crm_legal_comment',
					'KeyField' => 'LegalID',
					'Image' => '50x50|8|Small',
					'FileComponent' => array(
						'Name' => 'File',
						'File' => 'components/file.php',
						'Class' => 'FileViewComponent',
						'Config' => array(
							'EntityType' => 'legal_comment',
							'Path' => 'storage',
						),
					)
				),
			),
			array(
				'Name' => 'FullSeason',
				'Type' => 'component',
				'File' => 'components/itemlist.php',
				'Class' => 'ItemListViewComponent',
				'Config' => array(
					'Table' => 'crm_season',
					'ID' => 'SeasonID',
					'ItemsOrderBy' => 't.Title',
					'KeyField' => 'LegalID',
					'ListConfig' => array(
						'Fields' => array(
							array(
								'Name' => 'Title',
								'Type' => 'field',
							),
						),
					),
				),
			),
			array(
				'Name' => 'Contract',
				'Type' => 'component',
				'File' => 'components/finance.php',
				'Class' => 'FinanceExtendedViewComponent',
				'Config' => array(
					'TargetField' => 'LegalID',
					'ContractTable' => 'crm_legal_contract',
					'InvoiceTable' => 'crm_legal_invoice',
					'PaymentTable' => 'crm_legal_payment',
					'PaybackTable' => 'crm_legal_payback',
					'ActTable' => 'crm_legal_act',
					'UseSeason' => true
				),
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
				'Name' => 'IncomeID',
				'Type' => 'component',
				'File' => 'components/directory.php',
				'Class' => 'DirectoryViewComponent',
				'Config' => array(
					'DirectoryType' => 2,
					'ViewId' => 'ArticleID',
					'ViewName' => 'ArticleName',
				),
			),
			array(
				'Name' => 'OutcomeID',
				'Type' => 'component',
				'File' => 'components/directory.php',
				'Class' => 'DirectoryViewComponent',
				'Config' => array(
					'DirectoryType' => 3,
					'ViewId' => 'ArticleID',
					'ViewName' => 'ArticleName',
				),
			),
			array(
				'Name' => 'Childlist',
				'Type' => 'component',
				'File' => 'components/childlist.php',
				'Class' => 'ChildlistComponent',
				'Config' => array(
					//'Name' => 'Childlist',
				),
			),
		),
	),
	'EditTemplate' => 'legal_edit.html',
	'EditConfig' => array(
		'Fields' => array(
			array(
				'Name' => 'Image',
				'Type' => 'component',
				'File' => 'components/image.php',
				'Class' => 'ImageEditComponent',
				'Config' => array(
					'Path' => 'legal',
					'Image' => '100x100|8|Small',
				),
			),
			array(
				'Name' => 'Title',
				'Type' => 'field',
				'Required' => true,
				'Validate' => 'empty',
			),
			array(
				'Name' => 'Category',
				'Type' => 'component',
				'File' => 'components/linked.php',
				'Class' => 'LinkedEditComponent',
				'Config' => array(
					'Table' => 'crm_category',
					'FromField' => 'CategoryID',
					'ToField' => 'CategoryID',
					'ViewField' => 'Title',
                    'IncludeKeys' => array(1,2,3,7,12,13)
				),
			),
			array(
				'Name' => 'Group',
				'Type' => 'component',
				'File' => 'components/linked.php',
				'Class' => 'LinkedEditComponent',
				'Config' => array(
					'Table' => 'crm_legalgroup',
					'FromField' => 'GroupID',
					'ToField' => 'GroupID',
					'ViewField' => 'Title',
					'Required' => true
				),
			),
			array(
				'Name' => 'Email',
				'Type' => 'component',
				'File' => 'components/email.php',
				'Class' => 'EmailEditComponent',
				'Config' => array(
					'Table' => 'crm_legal_email',
					'KeyField' => 'LegalID',
				),
			),
			array(
				'Name' => 'Phone',
				'Type' => 'component',
				'File' => 'components/phone.php',
				'Class' => 'PhoneEditComponent',
				'Config' => array(
					'Table' => 'crm_legal_phone',
					'KeyField' => 'LegalID',
					'Required' => true,
				),
			),
			array(
				'Name' => 'Website',
				'Type' => 'field',
			),
			array(
				'Name' => 'Seasons',
				'Type' => 'component',
				'File' => 'components/linked.php',
				'Class' => 'LinkedMultipleSelectEditComponent',
				'Config' => array(
					'Table' => 'crm_season',
					'KeyField' => 'SeasonID',
					'LinkTable' => 'crm_legal2season',
					'LinkFromField' => 'LegalID',
					'LinkToField' => 'SeasonID',
                    'ViewSQL' => 't.Title, t.TypeID',
                    'OrderSQL' => 't.TypeID ASC, t.SeasonID ASC',
					// 'WhereSql' => 't.Archive = "N"',	
				),
			),
			array(
				'Name' => 'Requisites',
				'Type' => 'field',
			),
			array(
				'Name' => 'AddressCity',
				'Type' => 'field',
			),
			array(
				'Name' => 'AddressStreet',
				'Type' => 'field',
			),
			array(
				'Name' => 'AddressHome',
				'Type' => 'field',
			),
			array(
				'Name' => 'AddressFlat',
				'Type' => 'field',
			),
			array(
				'Name' => 'INN',
				'Type' => 'field',
			),
			array(
				'Name' => 'KPP',
				'Type' => 'field',
			),
			array(
				'Name' => 'BankName',
				'Type' => 'field',
			),
			array(
				'Name' => 'PC',
				'Type' => 'field',
			),
			array(
				'Name' => 'KC',
				'Type' => 'field',
			),
			array(
				'Name' => 'BIK',
				'Type' => 'field',
			),
			array(
				'Name' => 'LegalPostIndex',
				'Type' => 'field',
			),
			array(
				'Name' => 'LegalCountry',
				'Type' => 'field',
			),
			array(
				'Name' => 'LegalCity',
				'Type' => 'field',
			),
			array(
				'Name' => 'LegalStreet',
				'Type' => 'field',
			),
			array(
				'Name' => 'LegalHome',
				'Type' => 'field',
			),
			array(
				'Name' => 'LegalBuilding',
				'Type' => 'field',
			),
			array(
				'Name' => 'LegalOffice',
				'Type' => 'field',
			),
			array(
				'Name' => 'Comment',
				'Type' => 'field',
				'Required' => true,
				'Validate' => 'empty'
			),
			array(
				'Name' => 'Mailing',
				'Type' => 'field',
				'Validate' => 'option',
				'Options' => array('Y', 'N')
			),
			array(
				'Name' => 'File',
				'Type' => 'component',
				'File' => 'components/file.php',
				'Class' => 'FileEditComponent',
				'Config' => array(
					'EntityType' => 'legal',
					'Path' => 'storage',
				),
			),
			array(
				'Name' => 'Contact',
				'Type' => 'component',
				'File' => 'components/itemlist.php',
				'Class' => 'ItemListEditComponent',
				'Config' => array(
					'Entity' => 'contact',
					'Table' => 'crm_legal_contact',
					'ID' => 'ContactID',
					'ItemsOrderBy' => 't.LastName,t.FirstName',
					'KeyField' => 'LegalID',
					'ListConfig' => array(
						'Fields' => array(
							array(
								'Name' => 'LastName',
								'Type' => 'field',
								'Required' => true,
								'Validate' => 'empty'
							),
							array(
								'Name' => 'FirstName',
								'Type' => 'field',
								'Required' => true,
								'Validate' => 'empty'
							),
							array(
								'Name' => 'MiddleName',
								'Type' => 'field',
							),
							array(
								'Name' => 'Post',
								'Type' => 'field',
							),
							array(
								'Name' => 'Phone',
								'Type' => 'component',
								'File' => 'components/phone.php',
								'Class' => 'PhoneEditComponent',
								'Config' => array(
									'Table' => 'crm_legal_contact_phone',
									'KeyField' => 'ContactID',
									'Required' => true,
								),
							),
							array(
								'Name' => 'Email',
								'Type' => 'component',
								'File' => 'components/email.php',
								'Class' => 'EmailEditComponent',
								'Config' => array(
									'Table' => 'crm_legal_contact_email',
									'KeyField' => 'ContactID',
								),
							),
							array(
								'Name' => 'Passport',
								'Type' => 'field',
							),
							array(
								'Name' => 'LegalID',
								'Type' => 'field',
							),
						),
						'Filters' => array(
							array(
								'Name' => 'EntityID',
								'File' => 'filters/linked.php',
								'Class' => 'LinkedFilter',
								'Config' => array(
									'Name' => 'EntityID',
									'FromField' => 'LegalID',
									'ToField' => 'LegalID'
								)
							)
						)
					),
				),
			),
			array(
				'Name' => 'ManagerID',
				'Type' => 'component',
				'File' => 'components/manager.php',
				'Class' => 'ManagerEditComponent',
				'Config' => array(
					'Table' => 'user',
					'FromField' => 'ManagerID',
					'ToField' => 'UserID',
					'ViewField' => 'CONCAT(LastName, \' \', FirstName)',
				),
			),
			array(
				'Name' => 'Duplicate',
				'Type' => 'component',
				'File' => 'components/duplicate.php',
				'Class' => 'DuplicateEditComponent',
				'Config' => array(
					'Entity' => 'legal',
					'DuplicateParams' => array(
						array('Field' => 'Title', 'Filter' => 'FilterTitle')
					)
				),
			),
		),
	),
	'ActionConfig' => array(
		'SaveStatus' => array(
			'File' => 'actions/status.php',
			'Class' => 'StatusAction',
			'Config' => array(
				'Name' => 'Status',
				'StatusTable' => 'crm_legal_status',
				'Entity2SeasonTable' => 'crm_legal2season'
			),
		),
		'ClearPanel' => array(
			'File' => 'actions/status.php',
			'Class' => 'StatusAction',
			'Config' => array(
				'Table' => 'crm_legal_status',
				'KeyField' => 'LegalID',
			),
		),
		'AddComment' => array(
			'File' => 'actions/comment.php',
			'Class' => 'CommentAction',
			'Config' => array(
				'Table' => 'crm_legal_comment',
				'KeyField' => 'LegalID',
				'FileComponent' => array(
					'Name' => 'File',
					'File' => 'components/file.php',
					'Class' => 'FileEditComponent',
					'Config' => array(
						'EntityType' => 'legal_comment',
						'Path' => 'storage'
					)
				)
			),
		),
		'AddContractExtended' => array(
			'File' => 'actions/finance.php',
			'Class' => 'FinanceAction',
			'Config' => array(
				'Table' => 'crm_legal_contract',
				'KeyField' => 'LegalID',
				'ContactField' => 'ContactID',
				'UseSeason' => true,
			),
		),
		'AddInvoice' => array(
			'File' => 'actions/finance.php',
			'Class' => 'FinanceAction',
			'Config' => array(
				'Table' => 'crm_legal_invoice',
				'ContractTable' => 'crm_legal_contract'
			),
		),
		'AddPayment' => array(
			'File' => 'actions/finance.php',
			'Class' => 'FinanceAction',
			'Config' => array(
				'Table' => 'crm_legal_payment',
				'ContractTable' => 'crm_legal_contract',
				'PaybackTable' => 'crm_legal_payback'
			),
		),
		'AddPayback' => array(
			'File' => 'actions/finance.php',
			'Class' => 'FinanceAction',
			'Config' => array(
				'Table' => 'crm_legal_payback',
				'ContractTable' => 'crm_legal_contract',
				'PaymentTable' => 'crm_legal_payment',
			),
		),
		'AddAct' => array(
			'File' => 'actions/finance.php',
			'Class' => 'FinanceAction',
			'Config' => array(
				'Table' => 'crm_legal_act'
			),
		),
		'AddCommission' => array(
			'File' => 'actions/finance.php',
			'Class' => 'FinanceAction',
			'Config' => array(
				'Table' => 'crm_legal'
			),
		),
		'GetPaymentPDF' => array(
			'File' => 'actions/finance.php',
			'Class' => 'FinanceAction',
			'Config' => array(
				'Table' => 'crm_legal_payment',
				'ContractTable' => 'crm_legal_contract',
				'ClientTable' => 'crm_legal_contact',
				'ClientPhoneTable' => 'crm_legal_contact_phone',
				'ClientKey' => 'LegalID',
				'ClientPhoneKey' => 'ContactID',
				'Template' => 'payment_pdf.html',
			),
		),
		'GetContractPDF' => array(
			'File' => 'actions/finance.php',
			'Class' => 'FinanceAction',
			'Config' => array(
				'Table' => 'crm_legal_contract',
				'Contract2SeasonTable' => 'crm_legal_contract2season',
				'ContractTable' => 'crm_legal_contract',
				'ContactPhoneTable' => 'crm_legal_contact_phone',
				'Template' => 'legal_contract_pdf.html',
				'Data' => array(
					array(
						'Name' => 'Amount',
						'Type' => 'field',
					),
					array(
						'Name' => 'TourPrice',
						'Type' => 'field',
					),
					array(
						'Name' => 'TourCount',
						'Type' => 'field',
					),
					array(
						'Name' => 'Amount',
						'Type' => 'field',
					),
					array(
						'Name' => 'Amount',
						'Type' => 'field',
					),
					array(
						'Name' => 'LastDayForPay',
						'Type' => 'field',
					),
					array(
						'Name' => 'IsNeedStamp',
						'Type' => 'field',
					),
					array(
						'Name' => 'CompanyName',	
						'Type' => 'field',
						'Path' => array(
							array(
								'Table' => 'crm_legal',
								'FromField' => 'LegalID',
								'ToField' => 'LegalID',
								'SQLSelect' => 'd.Title'
							),
						)
					),
					array(
						'Name' => 'INN',	
						'Type' => 'field',
						'Path' => array(
							array(
								'Table' => 'crm_legal',
								'FromField' => 'LegalID',
								'ToField' => 'LegalID',
								'SQLSelect' => 'd.INN'
							),
						)
					),
					array(
						'Name' => 'KPP',	
						'Type' => 'field',
						'Path' => array(
							array(
								'Table' => 'crm_legal',
								'FromField' => 'LegalID',
								'ToField' => 'LegalID',
								'SQLSelect' => 'd.KPP'
							),
						)
					),
					array(
						'Name' => 'BankName',	
						'Type' => 'field',
						'Path' => array(
							array(
								'Table' => 'crm_legal',
								'FromField' => 'LegalID',
								'ToField' => 'LegalID',
								'SQLSelect' => 'd.BankName'
							),
						)
					),
					array(
						'Name' => 'PC',	
						'Type' => 'field',
						'Path' => array(
							array(
								'Table' => 'crm_legal',
								'FromField' => 'LegalID',
								'ToField' => 'LegalID',
								'SQLSelect' => 'd.PC'
							),
						)
					),
					array(
						'Name' => 'KC',	
						'Type' => 'field',
						'Path' => array(
							array(
								'Table' => 'crm_legal',
								'FromField' => 'LegalID',
								'ToField' => 'LegalID',
								'SQLSelect' => 'd.KC'
							),
						)
					),
					array(
						'Name' => 'BIK',
						'Type' => 'field',
						'Path' => array(
							array(
								'Table' => 'crm_legal',
								'FromField' => 'LegalID',
								'ToField' => 'LegalID',
								'SQLSelect' => 'd.BIK'
							),
						)
					),
					array(
						'Name' => 'LegalPostIndex',	
						'Type' => 'field',
						'Path' => array(
							array(
								'Table' => 'crm_legal',
								'FromField' => 'LegalID',
								'ToField' => 'LegalID',
								'SQLSelect' => 'd.LegalPostIndex'
							),
						)
					),
					array(
						'Name' => 'LegalCountry',	
						'Type' => 'field',
						'Path' => array(
							array(
								'Table' => 'crm_legal',
								'FromField' => 'LegalID',
								'ToField' => 'LegalID',
								'SQLSelect' => 'd.LegalCountry'
							),
						)
					),
					array(
						'Name' => 'LegalCity',	
						'Type' => 'field',
						'Path' => array(
							array(
								'Table' => 'crm_legal',
								'FromField' => 'LegalID',
								'ToField' => 'LegalID',
								'SQLSelect' => 'd.LegalCity'
							),
						)
					),
					array(
						'Name' => 'LegalStreet',	
						'Type' => 'field',
						'Path' => array(
							array(
								'Table' => 'crm_legal',
								'FromField' => 'LegalID',
								'ToField' => 'LegalID',
								'SQLSelect' => 'd.LegalStreet'
							),
						)
					),
					array(
						'Name' => 'LegalHome',	
						'Type' => 'field',
						'Path' => array(
							array(
								'Table' => 'crm_legal',
								'FromField' => 'LegalID',
								'ToField' => 'LegalID',
								'SQLSelect' => 'd.LegalHome'
							),
						)
					),
					array(
						'Name' => 'LegalBuilding',	
						'Type' => 'field',
						'Path' => array(
							array(
								'Table' => 'crm_legal',
								'FromField' => 'LegalID',
								'ToField' => 'LegalID',
								'SQLSelect' => 'd.LegalBuilding'
							),
						)
					),
					array(
						'Name' => 'LegalOffice',	
						'Type' => 'field',
						'Path' => array(
							array(
								'Table' => 'crm_legal',
								'FromField' => 'LegalID',
								'ToField' => 'LegalID',
								'SQLSelect' => 'd.LegalOffice'
							),
						)
					),
					array(
						'Name' => 'Requisites',	
						'Type' => 'field',
						'Path' => array(
							array(
								'Table' => 'crm_legal',
								'FromField' => 'LegalID',
								'ToField' => 'LegalID',
								'SQLSelect' => 'd.Requisites'
							),
						)
					),
					array(
						'Name' => 'SeasonDateFrom',
						'Type' => 'field',
						'Path' => array(
							array(
								'Table' => 'crm_legal_contract2season',
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
								'Table' => 'crm_legal_contract2season',
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
		'GetInvoicePDF' => array(
			'File' => 'actions/finance.php',
			'Class' => 'FinanceAction',
			'Config' => array(
				'Table' => 'crm_legal_invoice',
				'ContractTable' => 'crm_legal_contract',
				'Template' => 'invoice_pdf.html',
				'UseSeason' => true,
				'InvoiceSubjectKey' => 'finance-invoice-subject-rest',
				'Data' => array(
					array(
						'Name' => 'ClientName',
						'Type' => 'field',
						'Path' => array(
							array(
								'Table' => 'crm_legal_contract',
								'FromField' => 'ContractID',
								'ToField' => 'ContractID',
							),
							array(
								'Table' => 'crm_legal',
								'FromField' => 'LegalID',
								'ToField' => 'LegalID',
								'SQLSelect' => 'd.Title'
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
				'Table' => 'crm_legal_act',
				'ContractTable' => 'crm_legal_contract',
				'Template' => 'act_pdf.html',
				'UseSeason' => true,
				'ActSubjectKey' => 'finance-act-subject-rest',
				'Data' => array(
					array(
						'Name' => 'ClientName',
						'Type' => 'field',
						'Path' => array(
							array(
								'Table' => 'crm_legal_contract',
								'FromField' => 'ContractID',
								'ToField' => 'ContractID',
							),
							array(
								'Table' => 'crm_legal',
								'FromField' => 'LegalID',
								'ToField' => 'LegalID',
								'SQLSelect' => 'd.Title'
							)
						)
					),
					array(
						'Name' => 'ClientInfo',
						'Type' => 'field',
						'Path' => array(
							array(
								'Table' => 'crm_legal_contract',
								'FromField' => 'ContractID',
								'ToField' => 'ContractID',
							),
							array(
								'Table' => 'crm_legal',
								'FromField' => 'LegalID',
								'ToField' => 'LegalID',
								'SQLSelect' => 'd.Requisites'
							)
						)
					),
					array(
						'Name' => 'ContractCreated',
						'Type' => 'field',
						'Path' => array(
							array(
								'Table' => 'crm_legal_contract',
								'FromField' => 'ContractID',
								'ToField' => 'ContractID',
								'SQLSelect' => 'd.Created'
							),
						)
					),
				)
			), 
		),
		'RemoveContract' => array(
			'File' => 'actions/finance.php',
			'Class' => 'FinanceAction',
			'Config' => array(
				'PaymentTable' => 'crm_legal_payment',
				'PaybackTable' => 'crm_legal_payback',
				'Remove' => array(
					array('Table' => 'crm_legal_contract', 'Key' => 'ContractID'),
					array('Table' => 'crm_legal_contract2season', 'Key' => 'ContractID'),
					array('Table' => 'crm_legal_invoice', 'Key' => 'ContractID'),
					array('Table' => 'crm_legal_payment', 'Key' => 'ContractID'),
					array('Table' => 'crm_legal_act', 'Key' => 'ContractID'),
				),
			), 
		),
		'RemoveInvoice' => array(
			'File' => 'actions/finance.php',
			'Class' => 'FinanceAction',
			'Config' => array(
				'Remove' => array(
					array('Table' => 'crm_legal_invoice', 'Key' => 'InvoiceID'),
				),
			), 
		),
		'RemovePayment' => array(
			'File' => 'actions/finance.php',
			'Class' => 'FinanceAction',
			'Config' => array(
				'Remove' => array(
					array('Table' => 'crm_legal_payment', 'Key' => 'PaymentID'),
				),
			), 
		),
		'RemovePayback' => array(
			'File' => 'actions/finance.php',
			'Class' => 'FinanceAction',
			'Config' => array(
				'Remove' => array(
					array('Table' => 'crm_legal_payback', 'Key' => 'PaybackID'),
				),
			), 
		),
		'RemoveAct' => array(
			'File' => 'actions/finance.php',
			'Class' => 'FinanceAction',
			'Config' => array(
				'Remove' => array(
					array('Table' => 'crm_legal_act', 'Key' => 'ActID'),
				),
			), 
		)
	),
)

?>
