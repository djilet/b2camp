<?php
$GLOBALS['entityConfig']['staff'] = array(
	'Access' => array(INTEGRATOR, ADMINISTRATOR, MANAGER),
	'AdminMenuIcon' => 'fa fa-user',
	'AdminSubmenu' => array(
		array(
			"Title" => GetTranslation("admin-menu-crm-staff-active"),
			"Link" => "module.php?load=crm&entity=staff&FilterArchive=N",
			"Selected" => !isset($_REQUEST["FilterArchive"]) || (isset($_REQUEST["FilterArchive"]) && $_REQUEST["FilterArchive"] == "N")
		),
		array(
			"Title" => GetTranslation("admin-menu-crm-staff-archive"),
			"Link" => "module.php?load=crm&entity=staff&FilterArchive=Y",
			"Selected" => isset($_REQUEST["FilterArchive"]) && $_REQUEST["FilterArchive"] == "Y"
		),
	),
	'Table' => 'crm_staff',
	'Join' => 'user USING(UserID)',
	'ID' => 'StaffID',
	'ItemsPerPage' => 20,
	'ItemsOrderBy' => 'user.LastName,user.FirstName',
	'ShowSendToArchiveButton' => true,
	'ShowRemoveFromArchiveButton' => true,
	'ListDuplicateTemplate' => 'staff_list_duplicate.html',
	'ListTemplate' => 'staff_list.html',
	'ListConfig' => array(
		'Fields' => array(
            array(
                'Name' => 'StaffUser',
                'Type' => 'component',
                'File' => 'components/staff_user.php',
                'Class' => 'StaffUserComponent',
                'Config' => array(
                    'Table' => 'crm_staff',
                    'KeyField' => "StaffID"
                )
            ),
			array(
				'Name' => 'Image',
				'Type' => 'component',
				'File' => 'components/image.php',
				'Class' => 'ImageViewComponent',
				'Config' => array(
					'Path' => 'staff',
					'Image' => '50x50|8|Small',
				),
			),
			array(
				'Name' => 'FIO',
				'Type' => 'sql',
				'SQL' => 'CONCAT(user.LastName," ",user.FirstName)',
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
					'Table' => 'crm_group',
					'FromField' => 'GroupID',
					'ToField' => 'GroupID',
					'ViewField' => 'Title',
				),
			),
			array(
				'Name' => 'Age',
				'Type' => 'component',
				'File' => 'components/age.php',
				'Class' => 'AgeViewComponent',
				'Config' => array(
					'DOBField' => 'DOB',
					'TablePrefix' => 'user'
				),
			),
			array(
				'Name' => 'University',
				'Type' => 'field',
			),
			array(
				'Name' => 'Course',
				'Type' => 'field',
			),
			array(
				'Name' => 'Phone',
				'Type' => 'component',
				'File' => 'components/phone.php',
				'Class' => 'PhoneUserComponent',
				'Config' => array(
					'Table' => 'user',
					'KeyField' => 'UserID',
				),
			),
			array(
				'Name' => 'Email',
				'Type' => 'component',
				'File' => 'components/email.php',
				'Class' => 'EmailUserComponent',
				'Config' => array(
					'Table' => 'user',
					'KeyField' => 'UserID',
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
				'Name' => 'FilterArchive',
				'Field' => 'Archive'
			),
			array(
				'Name' => 'FilterFirstName',
				'Field' => 'FirstName',
				'TablePrefix' => "user"
			),
			array(
				'Name' => 'FilterMiddleName',
				'Field' => 'MiddleName',
                'TablePrefix' => "user"
			),
			array(
				'Name' => 'FilterLastName',
				'Field' => 'LastName',
                'TablePrefix' => "user"
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
				'Name' => 'FilterGroupID',
				'File' => 'filters/linked.php',
				'Class' => 'LinkedFilter',
				'Config' => array(
					'Name' => 'FilterGroupID',
					'ArrayName' => 'FilterGroupList',
					'Table' => 'crm_group',
					'FromField' => 'GroupID',
					'ToField' => 'GroupID',
					'ViewField' => 'Title',
				)
			),
			array(
				'Name' => 'FilterUniversity',
				'Field' => 'University'
			),
			array(
				'Name' => 'FilterCourse',
				'Field' => 'Course'
			),
			array(
				'Name' => 'FilterAge',
				'File' => 'filters/age.php',
				'Class' => 'AgeFilter',
				'Config' => array(
					'Name' => 'FilterAge',
					'DOBField' => 'DOB',
					'TablePrefix' => 'user'
				)
			),
			array(
				'Name' => 'FilterSeasonID',
				'File' => 'filters/linked.php',
				'Class' => 'LinkedMultipleSelectFilter',
				'Config' => array(
					'Name' => 'FilterSeasonID',
					'ArrayName' => 'FilterSeasonList',
					'Table' => 'crm_season',
					'LinkTable' => 'crm_staff2season',
					'Field' => 'SeasonID',
					'FromField' => 'StaffID',
					'LinkFromField' => 'StaffID',
					'LinkToField' => 'SeasonID',
					'ToField' => 'SeasonID',
					'ViewField' => 'TypeID, Title',
                    'OrderSQL' => 'TypeID ASC, SeasonID ASC',
				)
			),
			array(
				'Name' => 'FilterPhone',
				'File' => 'filters/phone.php',
				'Class' => 'PhoneUserFilter',
				'Config' => array(
					'Name' => 'FilterPhone',
					'Table' => 'user',
					'FromField' => 'UserID',
					'ToField' => 'UserID',
					'Field' => 'Phone'
				)
			),
			array(
				'Name' => 'FilterEmail',
				'File' => 'filters/email.php',
				'Class' => 'EmailFilter',
				'Config' => array(
					'Name' => 'FilterEmail',
					'Table' => 'user',
					'FromField' => 'UserID',
					'ToField' => 'UserID',
				)
			),
			array(
				'Name' => 'FilterStaffID',
				'File' => 'filters/linked.php',
				'Class' => 'LinkedMultipleSelectFilter',
				'Config' => array(
					'Name' => 'FilterStaffID',
					'ArrayName' => 'FilterStaffList',
					'Table' => 'crm_staff t LEFT JOIN user USING(UserID)',
					'LinkTable' => 'crm_staff2staff',
					'FromField' => 'StaffID',
					'LinkFromField' => 'StaffID',
					'LinkToField' => 'PartnerID',
					'ToField' => 'StaffID',
					'Symmetric' => true,
					'ViewField' => 'user.LastName, user.FirstName',
					'TablePrefix' => "t"
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
							'Table' => 'crm_staff_contract',
							'FromField' => 'StaffID',
							'ToField' => 'StaffID',
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
							'Table' => 'crm_staff_contract',
							'FromField' => 'StaffID',
							'ToField' => 'StaffID',
						),
						array(
							'Table' => 'crm_staff_invoice',
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
							'Table' => 'crm_staff_contract',
							'FromField' => 'StaffID',
							'ToField' => 'StaffID',
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
							'Table' => 'crm_staff_contract',
							'FromField' => 'StaffID',
							'ToField' => 'StaffID',
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
							'Table' => 'crm_staff_contract',
							'FromField' => 'StaffID',
							'ToField' => 'StaffID',
							'Field' => 'Created',
							'SQLTemplate' => 'DATE(#Field#)',
							'Operation' => '<='
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
							'Table' => 'crm_staff_contract',
							'FromField' => 'StaffID',
							'ToField' => 'StaffID',
							'Field' => 'ManagerID',
							'Operation' => '='
						)
					)
				)
			),
		),
	),
	'ViewTemplate' => 'staff_view.html',
	'ViewConfig' => array(
		'Fields' => array(
            array(
                'Name' => 'StaffUser',
                'Type' => 'component',
                'File' => 'components/staff_user.php',
                'Class' => 'StaffUserComponent',
                'Config' => array(
                    'Table' => 'crm_staff',
                    'KeyField' => "StaffID"
                )
            ),
			array(
				'Name' => 'Image',
				'Type' => 'component',
				'File' => 'components/image.php',
				'Class' => 'ImageViewComponent',
				'Config' => array(
					'Path' => 'staff',
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
				'Name' => 'FIO',
				'Type' => 'sql',
				'SQL' => 'CONCAT(user.LastName," ",user.FirstName)',
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
					'Table' => 'crm_group',
					'FromField' => 'GroupID',
					'ToField' => 'GroupID',
					'ViewField' => 'Title',
				),
			),
			array(
				'Name' => 'Age',
				'Type' => 'component',
				'File' => 'components/age.php',
				'Class' => 'AgeViewComponent',
				'Config' => array(
					'DOBField' => 'DOB',
					'TablePrefix' => "user"
				),
			),
			array(
				'Name' => 'Phone',
				'Type' => 'component',
				'File' => 'components/phone.php',
				'Class' => 'PhoneUserComponent',
				'Config' => array(
					'Table' => 'user',
					'KeyField' => 'UserID',
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
									'ToField' => 'StaffID'
								)
							),
							array(
								'Field' => 'LinkedEntity',
								'Operation' => '=',
								'Value' => 'staff',
							),
						)
					),
				),
			),
			array(
				'Name' => 'Email',
				'Type' => 'component',
				'File' => 'components/email.php',
				'Class' => 'EmailUserComponent',
				'Config' => array(
					'Table' => 'user',
					'KeyField' => 'UserID',
				),
			),
			array(
				'Name' => 'University',
				'Type' => 'field',
			),
			array(
				'Name' => 'Course',
				'Type' => 'field',
			),
			array(
				'Name' => 'Social',
				'Type' => 'field',
				'TablePrefix' => "user"
			),
			array(
				'Name' => 'Address',
				'Type' => 'sql',
				'SQL' => 'CONCAT("г. ",user.City,", ул. ",user.Street,", ",user.House,", кв. ",user.Flat)',
			),
			array(
				'Name' => 'Season',
				'Type' => 'component',
				'File' => 'components/linked.php',
				'Class' => 'LinkedMultipleSelectViewComponent',
				'Config' => array(
					'Table' => 'crm_season',
					'KeyField' => 'SeasonID',
					'LinkTable' => 'crm_staff2season',
					'LinkFromField' => 'StaffID',
					'LinkToField' => 'SeasonID',
                    'ViewSQL' => 't.Title, t.TypeID',
                    'OrderSQL' => 't.TypeID ASC, t.SeasonID ASC',
				),
			),
			array(
				'Name' => 'Partner',
				'Type' => 'component',
				'File' => 'components/linked.php',
				'Class' => 'LinkedMultipleSelectViewComponent',
				'Config' => array(
					'Table' => 'crm_staff',
					'KeyField' => 'StaffID',
					'LinkTable' => 'crm_staff2staff',
					'LinkFromField' => 'StaffID',
					'LinkToField' => 'PartnerID',
					'ViewSQL' => '(SELECT CONCAT(LastName," ",FirstName) FROM user WHERE UserID=t.UserID) AS Title',
					'Symmetric' => true,
				),
			),
			array(
				'Name' => 'Passport',
				'Type' => 'field',
			),
			array(
				'Name' => 'File',
				'Type' => 'component',
				'File' => 'components/file.php',
				'Class' => 'FileViewComponent',
				'Config' => array(
					'EntityType' => 'staff',
					'Path' => 'storage',
				),
			),
			array(
				'Name' => 'Comment',
				'Type' => 'component',
				'File' => 'components/comment.php',
				'Class' => 'CommentViewComponent',
				'Config' => array(
					'Table' => 'crm_staff_comment',
					'KeyField' => 'StaffID',
					'Image' => '50x50|8|Small',
					'FileComponent' => array(
						'Name' => 'File',
						'File' => 'components/file.php',
						'Class' => 'FileViewComponent',
						'Config' => array(
							'EntityType' => 'staff_comment',
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
					'KeyField' => 'StaffID',
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
				'Class' => 'FinanceViewComponent',
				'Config' => array(
					'TargetField' => 'StaffID',
					'ContractTable' => 'crm_staff_contract',
					'InvoiceTable' => 'crm_staff_invoice',
					'PaymentTable' => 'crm_staff_payment',
					'PaybackTable' => 'crm_staff_payback',
					'ActTable' => 'crm_staff_act',
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
	'EditTemplate' => 'staff_edit.html',
	'EditConfig' => array(
		'Fields' => array(
            array(
                'Name' => 'StaffUser',
                'Type' => 'component',
                'File' => 'components/staff_user.php',
                'Class' => 'StaffUserComponent',
                'Config' => array(
                    'Table' => 'crm_staff',
                    'KeyField' => "StaffID"
                )
            ),
			array(
				'Name' => 'Image',
				'Type' => 'component',
				'File' => 'components/image.php',
				'Class' => 'ImageEditComponent',
				'Config' => array(
					'Path' => 'staff',
					'Image' => '100x100|8|Small',
				),
			),
			array(
				'Name' => 'LastName',
				'Type' => 'none',
				'Required' => true,
				'Validate' => 'empty',
				'TablePrefix' => "user"
			),
			array(
				'Name' => 'FirstName',
				'Type' => 'none',
				'Required' => true,
				'Validate' => 'empty',
				'TablePrefix' => "user"
			),
			array(
				'Name' => 'MiddleName',
				'Type' => 'none',
                'TablePrefix' => "user"
			),
			array(
				'Name' => 'NickName',
				'Type' => 'field',
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
					'Required' => true,
                    'IncludeKeys' => array(1,2,3,7,12,13)
				),
			),
			array(
				'Name' => 'Group',
				'Type' => 'component',
				'File' => 'components/linked.php',
				'Class' => 'LinkedEditComponent',
				'Config' => array(
					'Table' => 'crm_group',
					'FromField' => 'GroupID',
					'ToField' => 'GroupID',
					'ViewField' => 'Title',
					'Required' => true,
					'IncludeKeys' => array(1,2,3,4)
				),
			),
			array(
				'Name' => 'Sex',
				'Type' => 'none',
				'Required' => true,
				'Validate' => 'option',
				'Options' => array('M', 'F'),
				'TablePrefix' => "user"
			),
			array(
				'Name' => 'DOB',
				'Required' => true,
				'Type' => 'none',
				'Validate' => 'date',
				'TablePrefix' => "user"
			),
			array(
				'Name' => 'Phone',
				'Type' => 'component',
				'File' => 'components/phone.php',
				'Class' => 'PhoneUserComponent',
				'Config' => array(
					'Table' => 'user',
					'KeyField' => 'UserID',
					'Required' => true,
				),
			),
			array(
				'Name' => 'Email',
				'Type' => 'component',
				'File' => 'components/email.php',
				'Class' => 'EmailUserComponent',
				'Config' => array(
					'Table' => 'user',
					'KeyField' => 'UserID',
					'Required' => true
				),
			),
			array(
				'Name' => 'University',
				'Type' => 'field',
			),
			array(
				'Name' => 'Course',
				'Type' => 'field',
			),
			array(
				'Name' => 'Social',
				'Type' => 'none',
				'TablePrefix' => "user"
			),
			array(
				'Name' => 'City',
				'Type' => 'none',
				'TablePrefix' => "user"
			),
			array(
				'Name' => 'Street',
				'Type' => 'none',
                'TablePrefix' => "user"
			),
			array(
				'Name' => 'House',
				'Type' => 'none',
                'TablePrefix' => "user"
			),
			array(
				'Name' => 'Flat',
				'Type' => 'none',
                'TablePrefix' => "user"
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
				'Name' => 'Seasons',
				'Type' => 'component',
				'File' => 'components/linked.php',
				'Class' => 'LinkedMultipleSelectEditComponent',
				'Config' => array(
					'Table' => 'crm_season',
					'KeyField' => 'SeasonID',
					'LinkTable' => 'crm_staff2season',
					'LinkFromField' => 'StaffID',
					'LinkToField' => 'SeasonID',
                    'ViewSQL' => 't.Title, t.TypeID',
                    'OrderSQL' => 't.TypeID ASC, t.SeasonID ASC',
				),
			),
			array(
				'Name' => 'Partners',
				'Type' => 'component',
				'File' => 'components/linked.php',
				'Class' => 'LinkedMultipleSelectEditComponent',
				'Config' => array(
					'Table' => 'crm_staff',
					'KeyField' => 'StaffID',
					'LinkTable' => 'crm_staff2staff',
					'LinkFromField' => 'StaffID',
					'LinkToField' => 'PartnerID',
					'ViewSQL' => '(SELECT CONCAT(LastName," ",FirstName) FROM user WHERE UserID=t.UserID) AS Title',
					'Symmetric' => true,
				),
			),
			array(
				'Name' => 'Passport',
				'Type' => 'field',
			),
			array(
				'Name' => 'File',
				'Type' => 'component',
				'File' => 'components/file.php',
				'Class' => 'FileEditComponent',
				'Config' => array(
					'EntityType' => 'staff',
					'Path' => 'storage',
				),
			),
			array(
				'Name' => 'ManagerID',
				'Type' => 'field',
			),
			array(
				'Name' => 'Duplicate',
				'Type' => 'component',
				'File' => 'components/duplicate.php',
				'Class' => 'DuplicateEditComponent',
				'Config' => array(
					'Entity' => 'staff',
					'DuplicateParams' => array(
						array('Field' => 'LastName', 'Filter' => 'FilterLastName')
					)
				),
			),
		),
	),
    'OldEditTemplate' => 'old_staff_edit.html',
    'OldEditConfig' => array(
        'Fields' => array(
            array(
                'Name' => 'Image',
                'Type' => 'component',
                'File' => 'components/image.php',
                'Class' => 'ImageEditComponent',
                'Config' => array(
                    'Path' => 'staff',
                    'Image' => '100x100|8|Small',
                ),
            ),
            array(
                'Name' => 'LastName',
                'Type' => 'field',
                'Required' => true,
                'Validate' => 'empty',
            ),
            array(
                'Name' => 'FirstName',
                'Type' => 'field',
                'Required' => true,
                'Validate' => 'empty',
            ),
            array(
                'Name' => 'MiddleName',
                'Type' => 'field',
            ),
            array(
                'Name' => 'NickName',
                'Type' => 'field',
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
                    'Required' => true,
                    'IncludeKeys' => array(1,2,3,7,12,13)
                ),
            ),
            array(
                'Name' => 'Group',
                'Type' => 'component',
                'File' => 'components/linked.php',
                'Class' => 'LinkedEditComponent',
                'Config' => array(
                    'Table' => 'crm_group',
                    'FromField' => 'GroupID',
                    'ToField' => 'GroupID',
                    'ViewField' => 'Title',
                    'Required' => true,
                    'IncludeKeys' => array(1,2,3,4)
                ),
            ),
            array(
                'Name' => 'Sex',
                'Type' => 'field',
                'Required' => true,
                'Validate' => 'option',
                'Options' => array('M', 'F')
            ),
            array(
                'Name' => 'DOB',
                'Required' => true,
                'Type' => 'date',
                'Validate' => 'date'
            ),
            array(
                'Name' => 'Phone',
                'Type' => 'component',
                'File' => 'components/phone.php',
                'Class' => 'PhoneEditComponent',
                'Config' => array(
                    'Table' => 'crm_staff_phone',
                    'KeyField' => 'StaffID',
                    'Required' => true,
                ),
            ),
            array(
                'Name' => 'Email',
                'Type' => 'component',
                'File' => 'components/email.php',
                'Class' => 'EmailEditComponent',
                'Config' => array(
                    'Table' => 'crm_staff_email',
                    'KeyField' => 'StaffID',
                ),
            ),
            array(
                'Name' => 'University',
                'Type' => 'field',
            ),
            array(
                'Name' => 'Course',
                'Type' => 'field',
            ),
            array(
                'Name' => 'Social',
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
                'Name' => 'Seasons',
                'Type' => 'component',
                'File' => 'components/linked.php',
                'Class' => 'LinkedMultipleSelectEditComponent',
                'Config' => array(
                    'Table' => 'crm_season',
                    'KeyField' => 'SeasonID',
                    'LinkTable' => 'crm_staff2season',
                    'LinkFromField' => 'StaffID',
                    'LinkToField' => 'SeasonID',
                    'ViewSQL' => 't.Title',
                ),
            ),
            array(
                'Name' => 'Partners',
                'Type' => 'component',
                'File' => 'components/linked.php',
                'Class' => 'LinkedMultipleSelectEditComponent',
                'Config' => array(
                    'Table' => 'crm_staff',
                    'KeyField' => 'StaffID',
                    'LinkTable' => 'crm_staff2staff',
                    'LinkFromField' => 'StaffID',
                    'LinkToField' => 'PartnerID',
                    'ViewSQL' => 'CONCAT(t.LastName," ",t.FirstName) AS Title',
                    'Symmetric' => true,
                ),
            ),
            array(
                'Name' => 'Passport',
                'Type' => 'field',
            ),
            array(
                'Name' => 'File',
                'Type' => 'component',
                'File' => 'components/file.php',
                'Class' => 'FileEditComponent',
                'Config' => array(
                    'EntityType' => 'staff',
                    'Path' => 'storage',
                ),
            ),
            array(
                'Name' => 'ManagerID',
                'Type' => 'field',
            ),
            array(
                'Name' => 'Duplicate',
                'Type' => 'component',
                'File' => 'components/duplicate.php',
                'Class' => 'DuplicateEditComponent',
                'Config' => array(
                    'Entity' => 'staff',
                    'DuplicateParams' => array(
                        array('Field' => 'LastName', 'Filter' => 'FilterLastName')
                    )
                ),
            ),
        ),
    ),
	'ActionConfig' => array(
		'SendToArchive' => array(
			'File' => 'actions/archive.php',
			'Class' => 'ArchiveAction',
			'Config' => array(
				'Table' => 'crm_staff',
				'KeyField' => 'StaffID',
				'ArchiveField' => 'Archive',
			),
		),
		'RemoveFromArchive' => array(
			'File' => 'actions/archive.php',
			'Class' => 'ArchiveAction',
			'Config' => array(
				'Table' => 'crm_staff',
				'KeyField' => 'StaffID',
				'ArchiveField' => 'Archive',
			),
		),
		'AddComment' => array(
			'File' => 'actions/comment.php',
			'Class' => 'CommentAction',
			'Config' => array(
				'Table' => 'crm_staff_comment',
				'KeyField' => 'StaffID',
				'FileComponent' => array(
					'Name' => 'File',
					'File' => 'components/file.php',
					'Class' => 'FileEditComponent',
					'Config' => array(
						'EntityType' => 'staff_comment',
						'Path' => 'storage'
					)
				)
			),
		),
		'AddContract' => array(
			'File' => 'actions/finance.php',
			'Class' => 'FinanceAction',
			'Config' => array(
				'Table' => 'crm_staff_contract',
				'KeyField' => 'StaffID',
			),
		),
		'AddInvoice' => array(
			'File' => 'actions/finance.php',
			'Class' => 'FinanceAction',
			'Config' => array(
				'Table' => 'crm_staff_invoice',
				'ContractTable' => 'crm_staff_contract'
			),
		),
		'AddPayment' => array(
			'File' => 'actions/finance.php',
			'Class' => 'FinanceAction',
			'Config' => array(
				'Table' => 'crm_staff_payment',
				'ContractTable' => 'crm_staff_contract',
				'PaybackTable' => 'crm_staff_payback',
			),
		),
		'AddPayback' => array(
			'File' => 'actions/finance.php',
			'Class' => 'FinanceAction',
			'Config' => array(
				'Table' => 'crm_staff_payback',
				'ContractTable' => 'crm_staff_contract',
				'PaymentTable' => 'crm_staff_payment',
			),
		),
		'AddAct' => array(
			'File' => 'actions/finance.php',
			'Class' => 'FinanceAction',
			'Config' => array(
				'Table' => 'crm_staff_act'
			),
		),
		'GetStaffPaymentPDF' => array(
			'File' => 'actions/finance.php',
			'Class' => 'FinanceAction',
			'Config' => array(
				'Table' => 'crm_staff_payment',
				'TableKey' => 'PaymentID',
				'ContractTable' => 'crm_staff_contract',
				'ClientTable' => 'crm_staff',
				'ClientPhoneTable' => 'user',
                'ClientAddressTable' => 'user',
                'ClientDOBTable' => 'user',
				'ClientKey' => 'StaffID',
				'ClientPhoneKey' => 'UserID',
				'ClientAddressKey' => 'UserID' ,
                'ClientDOBKey' => 'UserID' ,
				'Template' => 'staff_payment_pdf.html',
			), 
		),
        'GetStaffPaybackPDF' => array(
            'File' => 'actions/finance.php',
            'Class' => 'FinanceAction',
            'Config' => array(
                'Table' => 'crm_staff_payback',
                'TableKey' => 'PaybackID',
                'ContractTable' => 'crm_staff_contract',
                'ClientTable' => 'crm_staff',
                'ClientPhoneTable' => 'user',
                'ClientAddressTable' => 'user',
                'ClientDOBTable' => 'user',
                'ClientKey' => 'StaffID',
                'ClientPhoneKey' => 'UserID',
                'ClientAddressKey' => 'UserID' ,
                'ClientDOBKey' => 'UserID' ,
                'Template' => 'staff_payment_pdf.html',
            ),
        ),
		'GetContractPDF' => array(
			'File' => 'actions/finance.php',
			'Class' => 'FinanceAction',
			'Config' => array(
				'Table' => 'crm_staff_contract',
				'Template' => 'staff_contract_pdf.html',
				'Data' => array(
					array(
						'Name' => 'StaffName',	
						'Type' => 'field',
						'Path' => array(
							array(
								'Table' => '(SELECT t.StaffID, user.LastName, user.MiddleName, user.FirstName FROM crm_staff t LEFT JOIN user USING(UserID))',
								'FromField' => 'StaffID',
								'ToField' => 'StaffID',
								'SQLSelect' => 'CONCAT(d.LastName, \' \', d.FirstName, \' \', d.MiddleName)'
							),
						)
					),
					array(
						'Name' => 'StaffBirthYear',	
						'Type' => 'field',
						'Path' => array(
							array(
								'Table' => '(SELECT t.StaffID, user.DOB FROM crm_staff t LEFT JOIN user USING(UserID))',
								'FromField' => 'StaffID',
								'ToField' => 'StaffID',
								'SQLSelect' => 'YEAR(d.DOB)'
							),
						)
					),
					array(
						'Name' => 'Passport',
						'Type' => 'field',
						'Path' => array(
							array(
								'Table' => 'crm_staff',
								'FromField' => 'StaffID',
								'ToField' => 'StaffID',
								'SQLSelect' => 'Passport'
							)
						)
					),
					array(
						'Name' => 'Address',
						'Type' => 'field',
						'Path' => array(
							array(
								'Table' => '(SELECT t.StaffID, user.City, user.Street, user.House, user.Flat FROM crm_staff t LEFT JOIN user USING(UserID))',
								'FromField' => 'StaffID',
								'ToField' => 'StaffID',
								'SQLSelect' => 'CONCAT("г. ",d.City,", ул. ",d.Street,", ",d.House,", кв. ",d.Flat)'
							),
						)
					),
					array(
						'Name' => 'EmailList',
						'Type' => 'field',
						'Path' => array(
							array(
								'Table' => 'crm_staff',
								'FromField' => 'StaffID',
								'ToField' => 'StaffID',
							),
							array(
								'Table' => 'user',
								'FromField' => 'UserID',
								'ToField' => 'UserID',
								'SQLSelect' => 'd.Email'
							)
						)
					),
					array(
						'Name' => 'PhoneList',
						'Type' => 'field',
						'Path' => array(
							array(
								'Table' => 'crm_staff',
								'FromField' => 'StaffID',
								'ToField' => 'StaffID',
							),
							array(
								'Table' => 'user',
								'FromField' => 'UserID',
								'ToField' => 'UserID',
								'SQLSelect' => 'd.Phone'
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
				'Table' => 'crm_staff_invoice',
				'ContractTable' => 'crm_staff_contract',
				'Template' => 'invoice_pdf.html',
				'InvoiceSubjectKey' => 'finance-invoice-subject-education',
				'Data' => array(
					array(
						'Name' => 'ClientName',
						'Type' => 'field',
						'Path' => array(
							array(
								'Table' => 'crm_staff_contract',
								'FromField' => 'ContractID',
								'ToField' => 'ContractID',
							),		
							array(
								'Table' => 'crm_staff',
								'FromField' => 'StaffID',
								'ToField' => 'StaffID',
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
				'Table' => 'crm_staff_act',
				'ContractTable' => 'crm_staff_contract',
				'Template' => 'act_pdf.html',
				'ActSubjectKey' => 'finance-act-subject-education',
				'Data' => array(
					array(
						'Name' => 'ClientName',
						'Type' => 'field',
						'Path' => array(
							array(
								'Table' => 'crm_staff_contract',
								'FromField' => 'ContractID',
								'ToField' => 'ContractID',
							),
							array(
								'Table' => 'crm_staff',
								'FromField' => 'StaffID',
								'ToField' => 'StaffID',
								'SQLSelect' => 'CONCAT(d.LastName, \' \', d.FirstName, \' \', d.MiddleName)'
							)
						)
					),
					array(
						'Name' => 'ClientInfo',
						'Type' => 'field',
						'Path' => array(
							array(
								'Table' => 'crm_staff_contract',
								'FromField' => 'ContractID',
								'ToField' => 'ContractID',
							),
							array(
								'Table' => 'crm_staff',
								'FromField' => 'StaffID',
								'ToField' => 'StaffID',
								'SQLSelect' => 'd.Passport'
							)
						)
					),
					array(
						'Name' => 'ContractCreated',
						'Type' => 'field',
						'Path' => array(
							array(
								'Table' => 'crm_staff_contract',
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
				'PaymentTable' => 'crm_staff_payment',
				'PaybackTable' => 'crm_staff_payback',
				'Remove' => array(
					array('Table' => 'crm_staff_contract', 'Key' => 'ContractID'),
					array('Table' => 'crm_staff_invoice', 'Key' => 'ContractID'),
					array('Table' => 'crm_staff_payment', 'Key' => 'ContractID'),
					array('Table' => 'crm_staff_act', 'Key' => 'ContractID'),					
				),
			), 
		),
		'RemoveInvoice' => array(
			'File' => 'actions/finance.php',
			'Class' => 'FinanceAction',
			'Config' => array(
				'Remove' => array(
					array('Table' => 'crm_staff_invoice', 'Key' => 'InvoiceID'),
				),
			), 
		),
		'RemovePayment' => array(
			'File' => 'actions/finance.php',
			'Class' => 'FinanceAction',
			'Config' => array(
				'Remove' => array(
					array('Table' => 'crm_staff_payment', 'Key' => 'PaymentID'),
				),
			), 
		),
		'RemovePayback' => array(
			'File' => 'actions/finance.php',
			'Class' => 'FinanceAction',
			'Config' => array(
				'Remove' => array(
					array('Table' => 'crm_staff_payback', 'Key' => 'PaybackID'),
				),
			), 
		),
		'RemoveAct' => array(
			'File' => 'actions/finance.php',
			'Class' => 'FinanceAction',
			'Config' => array(
				'Remove' => array(
					array('Table' => 'crm_staff_act', 'Key' => 'ActID'),					
				),
			), 
		)
	),
	'OldConfig' => array(
        'Fields' => array(
        	array(
        		'Name' => "SavedImage",
				'Type' => "sql",
				'SQL' => "t.Image"
			),
            array(
                'Name' => 'FirstName',
                'Type' => 'field',
            ),
            array(
                'Name' => 'MiddleName',
                'Type' => 'field',
            ),
            array(
                'Name' => 'LastName',
                'Type' => 'field',
            ),
            array(
                'Name' => 'GroupID',
                'Type' => 'component',
                'File' => 'components/linked.php',
                'Class' => 'LinkedViewComponent',
                'Config' => array(
                    'Table' => 'crm_group',
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
                    'Table' => 'crm_staff_phone',
                    'KeyField' => 'StaffID',
                ),
            ),
            array(
                'Name' => 'Email',
                'Type' => 'component',
                'File' => 'components/email.php',
                'Class' => 'EmailViewComponent',
                'Config' => array(
                    'Table' => 'crm_staff_email',
                    'KeyField' => 'StaffID',
                ),
            ),
			array(
				'Name' => "Sex",
				'Type' => "field"
			),
			array(
				'Name' => "City",
				'Type' => "sql",
				'SQL' => "t.AddressCity"
			),
            array(
            	'Name' => "Street",
                'Type' => "sql",
                'SQL' => "t.AddressStreet"
            ),
            array(
            	'Name' => "House",
                'Type' => "sql",
                'SQL' => "t.AddressHome"
            ),
			array(
				'Name' => "Flat",
				'Type' => "sql",
                'SQL' => "t.AddressFlat"
			),
			array(
				'Name' => "DOB",
				'Type' => "date"
			),
			array(
				'Name' => "Social",
				'Type' => "field"
			),

            array(
                'Name' => 'Image',
                'Type' => 'field',
            ),
            array(
                'Name' => 'NickName',
                'Type' => 'field',
            ),
            array(
                'Name' => 'Category',
                'Type' => 'sql',
                'SQL' => 't.CategoryID'
            ),
            array(
                'Name' => '`Group`',
                'Type' => 'sql',
                'SQL' => 't.GroupID'
            ),

            array(
                'Name' => 'University',
                'Type' => 'field',
            ),
            array(
                'Name' => 'Course',
                'Type' => 'field',
            ),
            array(
                'Name' => 'Comment',
                'Type' => 'field',
            ),
            array(
                'Name' => 'Mailing',
                'Type' => 'field',
            ),


            array(
                'Name' => 'Passport',
                'Type' => 'field',
            ),

            array(
                'Name' => 'ManagerID',
                'Type' => 'field',
            ),
			array(
				'Name' => "UserID",
				'Type' => "field"
			)

        ),
		'Filters' => array(
			array(
				'Name' => "OldStaff",
				'File' => "filters/staff.php",
				'Class' => "OldStaffFilter",
				'Config' => array(
					'OldIDs' => array(),
				)
			),
		)

	)
)

?>
