<?php 
$GLOBALS['entityConfig']['school'] = array(
	'Access' => array(INTEGRATOR, ADMINISTRATOR, MANAGER),
	'AdminMenuIcon' => 'fa fa-university',	
	'Table' => 'crm_school',
	'ID' => 'SchoolID',
	'ItemsPerPage' => 20,
	'ItemsOrderBy' => 't.Title',
	'ShowClearPanelButton' => true,
	'ListDuplicateTemplate' => 'school_list_duplicate.html',
	'ListTemplate' => 'school_list.html',
	'ListConfig' => array(
		'Fields' => array(
			array(
				'Name' => 'Image',
				'Type' => 'component',
				'File' => 'components/image.php',
				'Class' => 'ImageViewComponent',
				'Config' => array(
					'Path' => 'school',
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
					'ViewField' => 'Title'
				),
			),
			array(
				'Name' => 'Phone',
				'Type' => 'component',
				'File' => 'components/phone.php',
				'Class' => 'PhoneViewComponent',
				'Config' => array(
					'Table' => 'crm_school_phone',
					'KeyField' => 'SchoolID',
				),
			),
			array(
				'Name' => 'Email',
				'Type' => 'component',
				'File' => 'components/email.php',
				'Class' => 'EmailViewComponent',
				'Config' => array(
					'Table' => 'crm_school_email',
					'KeyField' => 'SchoolID',
				),
			),
			array(
				'Name' => 'Contact',
				'Type' => 'component',
				'File' => 'components/itemlist.php',
				'Class' => 'ItemListViewComponent',
				'Config' => array(
					'Table' => 'crm_school_contact',
					'ID' => 'ContactID',
					'ItemsOrderBy' => 't.LastName,t.FirstName',
					'KeyField' => 'SchoolID',
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
									'FromField' => 'SchoolID',
									'ToField' => 'SchoolID'
								)
							)
						)
					),
				),
			),
			array(
				'Name' => 'ChildCount',
				'Type' => 'component',
				'File' => 'components/linked.php',
				'Class' => 'CountViewComponent',
				'Config' => array(
					'Table' => 'crm_child',
					'KeyField' => 'SchoolID',
					'TargetField' => 'ChildID',
				),
			),
			array(
				'Name' => 'Status',
				'Type' => 'component',
				'File' => 'components/status.php',
				'Class' => 'StatusViewComponent',
				'Config' => array(
					'KeyField' => 'SchoolID',
					'StatusTable' => 'crm_school_status'
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
					'Table' => 'crm_school_phone',
					'FromField' => 'SchoolID',
					'ToField' => 'SchoolID',
					'CodeField' => 'Prefix',
					'NumberField' => 'Number'
				)
			),
			array(
				'Name' => 'FilterContactID',
				'File' => 'filters/linked.php',
				'Class' => 'LinkedMultipleSelectFilter',
				'Config' => array(
					'Name' => 'FilterContactID',
					'ArrayName' => 'FilterContactList',
					'Table' => 'crm_school_contact',
					'LinkTable' => 'crm_school_contact',
					'FromField' => 'SchoolID',
					'LinkFromField' => 'SchoolID',
					'LinkToField' => 'ContactID',
					'ToField' => 'ContactID',
					'ViewField' => 'LastName, FirstName',
				)
			),
			array(
				'Name' => 'FilterEmail',
				'File' => 'filters/email.php',
				'Class' => 'EmailFilter',
				'Config' => array(
					'Name' => 'FilterEmail',
					'Table' => 'crm_school_email',
					'FromField' => 'SchoolID',
					'ToField' => 'SchoolID',
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
					'Key' => 'SchoolID',
					'StatusTable' => 'crm_school_status',
					'EntitySeasonTable' => 'crm_school2season'
				)
			),
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
			array(
				'Name' => 'FilterFinanceContractID',
				'File' => 'filters/linked.php',
				'Class' => 'CustomLinkedFilter',
				'Config' => array(
					'Name' => 'FilterFinanceContractID',
					'Path' => array(
						array(
							'Table' => 'crm_school_contact',
							'FromField' => 'SchoolID',
							'ToField' => 'SchoolID'
						),
						array(
							'Table' => 'crm_school_contact_contract',
							'FromField' => 'ContactID',
							'ToField' => 'ContactID',
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
							'Table' => 'crm_school_contact',
							'FromField' => 'SchoolID',
							'ToField' => 'SchoolID'
						),
						array(
							'Table' => 'crm_school_contact_contract',
							'FromField' => 'ContactID',
							'ToField' => 'ContactID',
						),
						array(
							'Table' => 'crm_school_contact_invoice',
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
							'Table' => 'crm_school_contact',
							'FromField' => 'SchoolID',
							'ToField' => 'SchoolID'
						),
						array(
							'Table' => 'crm_school_contact_contract',
							'FromField' => 'ContactID',
							'ToField' => 'ContactID',
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
							'Table' => 'crm_school_contact',
							'FromField' => 'SchoolID',
							'ToField' => 'SchoolID'
						),
						array(
							'Table' => 'crm_school_contact_contract',
							'FromField' => 'ContactID',
							'ToField' => 'ContactID',
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
							'Table' => 'crm_school_contact',
							'FromField' => 'SchoolID',
							'ToField' => 'SchoolID'
						),
						array(
							'Table' => 'crm_school_contact_contract',
							'FromField' => 'ContactID',
							'ToField' => 'ContactID',
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
							'Table' => 'crm_school_contact',
							'FromField' => 'SchoolID',
							'ToField' => 'SchoolID'
						),
						array(
							'Table' => 'crm_school_contact_contract',
							'FromField' => 'ContactID',
							'ToField' => 'ContactID',
						),
						array(
							'Table' => 'crm_school_contact_contract2season',
							'FromField' => 'ContractID',
							'ToField' => 'ContractID',
							'Field' => 'SeasonID',
							'Operation' => '='
						)
					)
				)
			),
			array(
				'Name' => 'FilterFinanceManagerID',
				'File' => 'filters/linked.php',
				'Class' => 'CustomLinkedFilter',
				'Config' => array(
					'Name' => 'FilterFinanceManagerID',
					'Table' => 'user',
					'ToField' => 'UserID',
					'ViewField' => 'LastName, FirstName',
					'ArrayName' => 'FilterFinanceManagerList',
					'Path' => array(
						array(
							'Table' => 'crm_school_contact',
							'FromField' => 'SchoolID',
							'ToField' => 'SchoolID'
						),
						array(
							'Table' => 'crm_school_contact_contract',
							'FromField' => 'ContactID',
							'ToField' => 'ContactID',
							'Field' => 'ManagerID',
							'Operation' => '='
						)
					)
				)
			),
		)
	),
	'ViewTemplate' => 'school_view.html',
	'ViewConfig' => array(
		'Fields' => array(
			array(
				'Name' => 'Image',
				'Type' => 'component',
				'File' => 'components/image.php',
				'Class' => 'ImageViewComponent',
				'Config' => array(
					'Path' => 'school',
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
					'Table' => 'crm_school_phone',
					'KeyField' => 'SchoolID',
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
									'ToField' => 'SchoolID'
								)
							),
							array(
								'Field' => 'LinkedEntity',
								'Operation' => '=',
								'Value' => 'school',
							),
						)
					),
				),
			),
			array(
				'Name' => 'Email',
				'Type' => 'component',
				'File' => 'components/email.php',
				'Class' => 'EmailViewComponent',
				'Config' => array(
					'Table' => 'crm_school_email',
					'KeyField' => 'SchoolID',
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
					'LinkTable' => 'crm_school2season',
					'LinkFromField' => 'SchoolID',
					'LinkToField' => 'SeasonID',
					'ViewSQL' => 't.Title',
				),
			),
			array(
				'Name' => 'File',
				'Type' => 'component',
				'File' => 'components/file.php',
				'Class' => 'FileViewComponent',
				'Config' => array(
					'EntityType' => 'school',
					'Path' => 'storage',
				),
			),
			array(
				'Name' => 'Status',
				'Type' => 'component',
				'File' => 'components/status.php',
				'Class' => 'StatusEditComponent',
				'Config' => array(
					'StatusTable' => 'crm_school_status',
					'KeyField' => 'SchoolID',
					'CustomQuantity' => true,
					'Entity' => "school"
				),
			),
			array(
				'Name' => 'Contact',
				'Type' => 'component',
				'File' => 'components/itemlist.php',
				'Class' => 'ItemListViewComponent',
				'Config' => array(
					'Table' => 'crm_school_contact',
					'ID' => 'ContactID',
					'ItemsOrderBy' => 't.LastName,t.FirstName',
					'KeyField' => 'SchoolID',
					'ListConfig' => array(
						'Fields' => array(
							array(
								'Name' => 'FIO',
								'Type' => 'sql',
								'SQL' => 'CONCAT(t.LastName," ",t.FirstName," ",COALESCE(t.MiddleName,""))',
							),
							array(
								'Name' => 'ContactStatus',
								'Type' => 'field',
							),
							array(
								'Name' => 'Passport',
								'Type' => 'field',
							),
							array(
								'Name' => 'Phone',
								'Type' => 'component',
								'File' => 'components/phone.php',
								'Class' => 'PhoneViewComponent',
								'Config' => array(
									'Table' => 'crm_school_contact_phone',
									'KeyField' => 'ContactID',
								),
							),
							array(
								'Name' => 'Email',
								'Type' => 'component',
								'File' => 'components/email.php',
								'Class' => 'EmailViewComponent',
								'Config' => array(
									'Table' => 'crm_school_contact_email',
									'KeyField' => 'ContactID',
								),
							),
						),
						'Filters' => array(
							array(
								'Name' => 'EntityID',
								'File' => 'filters/linked.php',
								'Class' => 'LinkedFilter',
								'Config' => array(
									'Name' => 'EntityID',
									'FromField' => 'SchoolID',
									'ToField' => 'SchoolID'
								)
							)
						)
					),
				),
			),
			array(
				'Name' => 'Visit',
				'Type' => 'component',
				'File' => 'components/visit.php',
				'Class' => 'VisitViewComponent',
				'Config' => array(
					'Table' => 'crm_school_visit',
					'KeyField' => 'SchoolID',
					'Image' => '50x50|8|Small',
				),
			),
			array(
				'Name' => 'Comment',
				'Type' => 'component',
				'File' => 'components/comment.php',
				'Class' => 'CommentViewComponent',
				'Config' => array(
					'Table' => 'crm_school_comment',
					'KeyField' => 'SchoolID',
					'Image' => '50x50|8|Small',
					'FileComponent' => array(
						'Name' => 'File',
						'File' => 'components/file.php',
						'Class' => 'FileViewComponent',
						'Config' => array(
							'EntityType' => 'school_comment',
							'Path' => 'storage',
						),
					)
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
                    'KeyField' => 'ChildID',
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
				'Name' => 'Contract',
				'Type' => 'component',
				'File' => 'components/finance.php',
				'Class' => 'FinanceExtendedViewComponent',
				'Config' => array(
					'Type' => 'linked',
					'LinkTable' => 'crm_school_contact',
					'FromField' => 'ContactID',
					'ToField' => 'ContactID', 
					'TargetField' => 'SchoolID',
					'ContractTable' => 'crm_school_contact_contract',
					'InvoiceTable' => 'crm_school_contact_invoice',
					'PaymentTable' => 'crm_school_contact_payment',
					'PaybackTable' => 'crm_school_contact_payback',
					'ActTable' => 'crm_school_contact_act',
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
		),
	),
	'EditTemplate' => 'school_edit.html',
	'EditConfig' => array(
		'Fields' => array(
			array(
				'Name' => 'Image',
				'Type' => 'component',
				'File' => 'components/image.php',
				'Class' => 'ImageEditComponent',
				'Config' => array(
					'Path' => 'school',
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
				'Name' => 'Phone',
				'Type' => 'component',
				'File' => 'components/phone.php',
				'Class' => 'PhoneEditComponent',
				'Config' => array(
					'Table' => 'crm_school_phone',
					'KeyField' => 'SchoolID',
					'Required' => true,
				),
			),
			array(
				'Name' => 'Email',
				'Type' => 'component',
				'File' => 'components/email.php',
				'Class' => 'EmailEditComponent',
				'Config' => array(
					'Table' => 'crm_school_email',
					'KeyField' => 'SchoolID',
				),
			),
			array(
				'Name' => 'Website',
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
				'Name' => 'Seasons',
				'Type' => 'component',
				'File' => 'components/linked.php',
				'Class' => 'LinkedMultipleSelectEditComponent',
				'Config' => array(
					'Table' => 'crm_season',
					'KeyField' => 'SeasonID',
					'LinkTable' => 'crm_school2season',
					'LinkFromField' => 'SchoolID',
					'LinkToField' => 'SeasonID',
                    'ViewSQL' => 't.Title, t.TypeID',
                    'OrderSQL' => 't.TypeID ASC, t.SeasonID ASC',
// 					'WhereSql' => 't.Archive = "N"',	
				),
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
					'EntityType' => 'school',
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
					'Table' => 'crm_school_contact',
					'ID' => 'ContactID',
					'ItemsOrderBy' => 't.LastName,t.FirstName',
					'KeyField' => 'SchoolID',
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
								'Name' => 'ContactStatus',
								'Type' => 'field',
								'Required' => true,
								'Validate' => 'option',
								'Options' => array('director', 'director_deputy', 'class_manager', 'parent_committee', 'other')
							),
							array(
								'Name' => 'Phone',
								'Type' => 'component',
								'File' => 'components/phone.php',
								'Class' => 'PhoneEditComponent',
								'Config' => array(
									'Table' => 'crm_school_contact_phone',
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
									'Table' => 'crm_school_contact_email',
									'KeyField' => 'ContactID',
								),
							),
							array(
								'Name' => 'Passport',
								'Type' => 'field',
							),
							array(
								'Name' => 'SchoolID',
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
									'FromField' => 'SchoolID',
									'ToField' => 'SchoolID'
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
					'Entity' => 'school',
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
				'StatusTable' => 'crm_school_status',
				'Entity2SeasonTable' => 'crm_school2season'
			),
		),
		'ClearPanel' => array(
			'File' => 'actions/status.php',
			'Class' => 'StatusAction',
			'Config' => array(
				'Table' => 'crm_school_status',
				'KeyField' => 'SchoolID',
			),
		),
		'AddComment' => array(
			'File' => 'actions/comment.php',
			'Class' => 'CommentAction',
			'Config' => array(
				'Table' => 'crm_school_comment',
				'KeyField' => 'SchoolID',
				'FileComponent' => array(
					'Name' => 'File',
					'File' => 'components/file.php',
					'Class' => 'FileEditComponent',
					'Config' => array(
						'EntityType' => 'school_comment',
						'Path' => 'storage'
					)
				)
			),
		),
		'AddVisit' => array(
			'File' => 'actions/visit.php',
			'Class' => 'VisitAction',
			'Config' => array(
				'Table' => 'crm_school_visit',
				'KeyField' => 'SchoolID',
			),
		),
		'AddContractExtended' => array(
			'File' => 'actions/finance.php',
			'Class' => 'FinanceAction',
			'Config' => array(
				'Table' => 'crm_school_contact_contract',
				'KeyField' => 'ContactID',
				'UseSeason' => true,
			),
		),
		'AddInvoice' => array(
			'File' => 'actions/finance.php',
			'Class' => 'FinanceAction',
			'Config' => array(
				'Table' => 'crm_school_contact_invoice',
				'ContractTable' => 'crm_school_contact_contract'
			),
		),
		'AddPayment' => array(
			'File' => 'actions/finance.php',
			'Class' => 'FinanceAction',
			'Config' => array(
				'Table' => 'crm_school_contact_payment',
				'ContractTable' => 'crm_school_contact_contract',
				'PaybackTable' => 'crm_school_contact_payback',
			),
		),
		'AddPayback' => array(
			'File' => 'actions/finance.php',
			'Class' => 'FinanceAction',
			'Config' => array(
				'Table' => 'crm_school_contact_payback',
				'ContractTable' => 'crm_school_contact_contract',
				'PaymentTable' => 'crm_school_contact_payment',
			),
		),
		'AddAct' => array(
			'File' => 'actions/finance.php',
			'Class' => 'FinanceAction',
			'Config' => array(
				'Table' => 'crm_school_contact_act'
			),
		),
		'GetPaymentPDF' => array(
			'File' => 'actions/finance.php',
			'Class' => 'FinanceAction',
			'Config' => array(
				'Table' => 'crm_school_contact_payment',
				'ContractTable' => 'crm_school_contact_contract',
				'ClientTable' => 'crm_school_contact',
				'ClientPhoneTable' => 'crm_school_contact_phone',
				'ClientKey' => 'SchoolID',
				'ClientPhoneKey' => 'ContactID',
				'Template' => 'payment_pdf.html',
			), 
		),
		'GetContractPDF' => array(
			'File' => 'actions/finance.php',
			'Class' => 'FinanceAction',
			'Config' => array(
				'Table' => 'crm_school_contact_contract',
				'ContactTable' => 'crm_school_contact',
                'ContractTable' => 'crm_school_contact_contract',
                'Contract2SeasonTable' => 'crm_school_contact_contract2season',
				'ContactPhoneTable' => 'crm_school_contact_phone',
				'Template' => 'school_contact_contract_pdf.html',
				'Data' => array(
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
						'Name' => 'LastDayForPay',
						'Type' => 'field',
					),
					array(
						'Name' => 'IsNeedStamp',
						'Type' => 'field',
					),
					array(
						'Name' => 'ContactName',
						'Type' => 'field',
						'Path' => array(
							array(
								'Table' => 'crm_school_contact',
								'FromField' => 'ContactID',
								'ToField' => 'ContactID',
								'SQLSelect' => 'CONCAT(d.LastName, \' \', d.FirstName, \' \', d.MiddleName)'
							)
						)
					),
					array(
						'Name' => 'SchoolAddressCity',	
						'Type' => 'field',
						'Path' => array(
							array(
								'Table' => 'crm_school_contact',
								'FromField' => 'ContactID',
								'ToField' => 'ContactID'
							),
							array(
								'Table' => 'crm_school',
								'FromField' => 'SchoolID',
								'ToField' => 'SchoolID',
								'SQLSelect' => 'd.AddressCity'
							),
						)
					),
					array(
						'Name' => 'SchoolAddressStreet',	
						'Type' => 'field',
						'Path' => array(
							array(
								'Table' => 'crm_school_contact',
								'FromField' => 'ContactID',
								'ToField' => 'ContactID'
							),
							array(
								'Table' => 'crm_school',
								'FromField' => 'SchoolID',
								'ToField' => 'SchoolID',
								'SQLSelect' => 'd.AddressStreet'
							),
						)
					),
					array(
						'Name' => 'SchoolAddressHome',	
						'Type' => 'field',
						'Path' => array(
							array(
								'Table' => 'crm_school_contact',
								'FromField' => 'ContactID',
								'ToField' => 'ContactID'
							),
							array(
								'Table' => 'crm_school',
								'FromField' => 'SchoolID',
								'ToField' => 'SchoolID',
								'SQLSelect' => 'd.AddressHome'
							),
						)
					),
					array(
						'Name' => 'SchoolAddressFlat',	
						'Type' => 'field',
						'Path' => array(
							array(
								'Table' => 'crm_school_contact',
								'FromField' => 'ContactID',
								'ToField' => 'ContactID'
							),
							array(
								'Table' => 'crm_school',
								'FromField' => 'SchoolID',
								'ToField' => 'SchoolID',
								'SQLSelect' => 'd.AddressFlat'
							),
						)
					),
					array(
						'Name' => 'SchoolTitle',	
						'Type' => 'field',
						'Path' => array(
							array(
								'Table' => 'crm_school_contact',
								'FromField' => 'ContactID',
								'ToField' => 'ContactID'
							),
							array(
								'Table' => 'crm_school',
								'FromField' => 'SchoolID',
								'ToField' => 'SchoolID',
								'SQLSelect' => 'd.Title'
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
				'Table' => 'crm_school_contact_invoice',
				'ContractTable' => 'crm_school_contact_contract',
				'Template' => 'invoice_pdf.html',
				'UseSeason' => true,
				'InvoiceSubjectKey' => 'finance-invoice-subject-rest',
				'Data' => array(
					array(
						'Name' => 'ClientName',
						'Type' => 'field',
						'Path' => array(
							array(
								'Table' => 'crm_school_contact_contract',
								'FromField' => 'ContractID',
								'ToField' => 'ContractID',
							),
							array(
								'Table' => 'crm_school_contact',
								'FromField' => 'ContactID',
								'ToField' => 'ContactID',
								'SQLSelect' => 'CONCAT(d.LastName, \' \', d.FirstName, \' \', d.MiddleName)'
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
				'Table' => 'crm_school_contact_act',
				'ContractTable' => 'crm_school_contact_contract',
				'Template' => 'act_pdf.html',
				'UseSeason' => true,
				'ActSubjectKey' => 'finance-act-subject-rest',
				'Data' => array(
					array(
						'Name' => 'ClientName',
						'Type' => 'field',
						'Path' => array(
							array(
								'Table' => 'crm_school_contact_contract',
								'FromField' => 'ContractID',
								'ToField' => 'ContractID',
							),
							array(
								'Table' => 'crm_school_contact',
								'FromField' => 'ContactID',
								'ToField' => 'ContactID',
								'SQLSelect' => 'CONCAT(d.LastName, \' \', d.FirstName, \' \', d.MiddleName)'
							)
						)
					),
					array(
						'Name' => 'ContractCreated',
						'Type' => 'field',
						'Path' => array(
							array(
								'Table' => 'crm_school_contact_contract',
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
				'PaymentTable' => 'crm_school_payment',
				'PaybackTable' => 'crm_school_payback',
				'Remove' => array(
					array('Table' => 'crm_school_contact_contract', 'Key' => 'ContractID'),
					array('Table' => 'crm_school_contact_contract2season', 'Key' => 'ContractID'),
					array('Table' => 'crm_school_contact_invoice', 'Key' => 'ContractID'),
					array('Table' => 'crm_school_contact_payment', 'Key' => 'ContractID'),
					array('Table' => 'crm_school_contact_act', 'Key' => 'ContractID'),					
				),
			), 
		),
		'RemoveInvoice' => array(
			'File' => 'actions/finance.php',
			'Class' => 'FinanceAction',
			'Config' => array(
				'Remove' => array(
					array('Table' => 'crm_school_contact_invoice', 'Key' => 'InvoiceID'),
				),
			), 
		),
		'RemovePayment' => array(
			'File' => 'actions/finance.php',
			'Class' => 'FinanceAction',
			'Config' => array(
				'Remove' => array(
					array('Table' => 'crm_school_contact_payment', 'Key' => 'PaymentID'),
				),
			), 
		),
		'RemovePayback' => array(
			'File' => 'actions/finance.php',
			'Class' => 'FinanceAction',
			'Config' => array(
				'Remove' => array(
					array('Table' => 'crm_school_contact_payback', 'Key' => 'PaybackID'),
				),
			), 
		),
		'RemoveAct' => array(
			'File' => 'actions/finance.php',
			'Class' => 'FinanceAction',
			'Config' => array(
				'Remove' => array(
					array('Table' => 'crm_school_contact_act', 'Key' => 'ActID'),					
				),
			), 
		)
	),
)

?>
