<?php
$GLOBALS['entityConfig']['child'] = array(
	'Access' => array(INTEGRATOR, ADMINISTRATOR, MANAGER, GUIDE),
	'AdminMenuIcon' => 'fa fa-child',
	'AdminSubmenu' => array(
        array(
            "Title" => GetTranslation("admin-menu-crm-child-active"),
            "Link" => "module.php?load=crm&entity=child&FilterArchive=N",
            "Selected" => !isset($_REQUEST["FilterArchive"]) || (isset($_REQUEST["FilterArchive"]) && $_REQUEST["FilterArchive"] == "N")
        ),
        array(
            "Title" => GetTranslation("admin-menu-crm-child-archive"),
            "Link" => "module.php?load=crm&entity=child&FilterArchive=Y",
            "Selected" => isset($_REQUEST["FilterArchive"]) && $_REQUEST["FilterArchive"] == "Y"
        ),
    ),
	'Table' => 'crm_child',
	'ID' => 'ChildID',
	'ItemsPerPage' => 20,
	'ItemsOrderBy' => 't.LastName,t.FirstName',
	'ShowExportButton' => true,
	'ShowPrintButton' => true,
	'ShowReassignButton' => true,
	'ShowClearPanelButton' => false,
    'ShowSendToArchiveButton' => true,
    'ShowRemoveFromArchiveButton' => false,
	'ListDuplicateTemplate' => 'child_list_duplicate.html',
	'ListTemplate' => 'child_list.html',
	'ListConfig' => array(
		'Fields' => array(
			array(
				'Name' => 'Image',
				'Type' => 'component',
				'File' => 'components/image.php',
				'Class' => 'ImageViewComponent',
				'Config' => array(
					'Path' => 'child',
					'Image' => '50x50|8|Small',
				),
			),
			array(
				'Name' => 'FIO',
				'Type' => 'sql',
				'SQL' => 'CONCAT(t.LastName," ",t.FirstName)',
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
                    'IncludeKeys' => array(1,2,3,7,12,13)
				),
			),
			array(
				'Name' => 'Age',
				'Type' => 'component',
				'File' => 'components/age.php',
				'Class' => 'AgeViewComponent',
				'Config' => array(
					'DOBField' => 'DOB',
				),
			),
			array(
				'Name' => 'School',
				'Type' => 'component',
				'File' => 'components/linked.php',
				'Class' => 'LinkedViewComponent',
				'Config' => array(
					'Table' => 'crm_school',
					'FromField' => 'SchoolID',
					'ToField' => 'SchoolID',
					'ViewField' => 'Title',
				),
			),
			array(
				'Name' => "Document",
				'Type' => "component",
				'File' => "components/document.php",
				'Class' => "DocumentViewComponent",
				'Config' => array(
					'Table' => "crm_child_document",
					'KeyField' => "ChildID"
				)
			),
			array(
				'Name' => 'Phone',
				'Type' => 'component',
				'File' => 'components/phone.php',
				'Class' => 'PhoneViewComponent',
				'Config' => array(
					'Table' => 'crm_child_phone',
					'KeyField' => 'ChildID',
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
				'Name' => 'Status',
				'Type' => 'component',
				'File' => 'components/status.php',
				'Class' => 'StatusViewComponent',
				'Config' => array(
					'KeyField' => 'ChildID',
					'StatusTable' => 'crm_child_status'
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
				'Field' => 'FirstName'
			),
			array(
				'Name' => 'FilterMiddleName',
				'Field' => 'MiddleName'
			),
			array(
				'Name' => 'FilterLastName',
				'Field' => 'LastName'
			),
			array(
				'Name' => 'FilterCategoryID',
				'File' => 'filters/linked.php',
				'Class' => 'LinkedFilter',
				'Config' => array(
					'Name' => 'FilterCategoryID',
                    'FromField' => 'CategoryID',
                    'ArrayName' => 'FilterCategoryList',
                    'Table' => 'crm_category',
					'ToField' => 'CategoryID',
					'ViewField' => 'Title',
                    'IncludeKeys' => array(1,2,3,12,13)
				)
			),
			array(
				'Name' => 'FilterParentLastName',
				'File' => 'filters/linked.php',
				'Class' => 'CustomLinkedFilter',
				'Config' => array(
					'Name' => 'FilterParentLastName',
					'Path' => array(
						array(
							'Table' => 'crm_parent',
							'FromField' => 'ChildID',
							'ToField' => 'ChildID',
							'Field' => 'LastName',
							'SQLTemplate' => 'INSTR(#Field#,',
							'OperationTemplate' => ' #Value#)'
						),
					),
					'Autocomplete' => true,
					'AutocompleteTable' => 'crm_parent',
					'AutocompleteField' => 'LastName'
				)
			),
			array(
				'Name' => 'FilterSchoolID',
				'File' => 'filters/linked.php',
				'Class' => 'LinkedFilter',
				'Config' => array(
					'Name' => 'FilterSchoolID',
					'ArrayName' => 'FilterSchoolList',
					'Table' => 'crm_school',
					'FromField' => 'SchoolID',
					'ToField' => 'SchoolID',
					'ViewField' => 'Title',
				)
			),
			array(
				'Name' => 'FilterClass',
				'Field' => 'Class'
			),
			array(
				'Name' => 'FilterAge',
				'File' => 'filters/age.php',
				'Class' => 'AgeFilter',
				'Config' => array(
					'Name' => 'FilterAge',
					'DOBField' => 'DOB'
				)
			),
			array(
				'Name' => 'FilterStaffID',
				'File' => 'filters/linked.php',
				'Class' => 'LinkedMultipleSelectFilter',
				'Config' => array(
					'Name' => 'FilterStaffID',
					'ArrayName' => 'FilterStaffList',
					'Table' => 'user JOIN crm_staff USING(UserID)',
					'LinkTable' => 'crm_child2staff',
					'FromField' => 'ChildID',
					'LinkFromField' => 'ChildID',
					'LinkToField' => 'StaffID',
					'ToField' => 'UserID',
					'ToFieldAlias' => "StaffID",
					'ViewField' => 'user.LastName, user.FirstName',
				)
			),
			array(
				'Name' => 'FilterPhone',
				'File' => 'filters/phone.php',
				'Class' => 'PhoneViewFilter',
				'Config' => array(
					'Name' => 'FilterPhone',
					'Table' => 'crm_child_phone',
					'FromField' => 'ChildID',
					'ToField' => 'ChildID',
					'CodeField' => 'Prefix',
					'NumberField' => 'Number',
					'AlternativePath' => array(
						array(
							'Table' => 'crm_parent',
							'FromField' => 'ChildID',
							'ToField' => 'ChildID'
						),
						array(
							'Table' => 'crm_parent_phone',
							'FromField' => 'ParentID',
							'ToField' => 'ParentID'
						),
					)
				)
			),
			array(
				'Name' => 'FilterEmail',
				'File' => 'filters/email.php',
				'Class' => 'EmailFilter',
				'Config' => array(
					'Name' => 'FilterEmail',
					'Table' => 'crm_child_email',
					'FromField' => 'ChildID',
					'ToField' => 'ChildID',
					'AlternativePath' => array(
						array(
							'Table' => 'crm_parent',
							'FromField' => 'ChildID',
							'ToField' => 'ChildID'
						),
						array(
							'Table' => 'crm_parent_email',
							'FromField' => 'ParentID',
							'ToField' => 'ParentID'
						),
					)
				)
			),
			array(
				'Name' => 'FilterChildID',
				'File' => 'filters/linked.php',
				'Class' => 'LinkedMultipleSelectFilter',
				'Config' => array(
					'Name' => 'FilterChildID',
					'ArrayName' => 'FilterFriendList',
					'Table' => 'crm_child',
					'LinkTable' => 'crm_child2child',
					'FromField' => 'ChildID',
					'LinkFromField' => 'ChildID',
					'LinkToField' => 'FriendID',
					'ToField' => 'ChildID',
					'Symmetric' => true,
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
					'Key' => 'ChildID',
					'StatusTable' => 'crm_child_status',
					'EntitySeasonTable' => 'crm_child_status'
				)
			),
			array(
				'Name' => 'ApprovFilter',
				'File' => 'filters/approv.php',
				'Class' => 'ApprovFilter',
				'Config' => array(
					'ApprovArrayName' => 'FilterApprovList',
					'ApprovName' => 'FilterApprovName',
					'Table' => 'crm_child2mailing',
                    'FromField' => 'ChildID',
                    'ToField' => 'ChildID',
                    'Name1' => 'FilteronSending',
                    'Where1' => 'onSending',
                    'Name2' => 'FilteronSMS',
                    'Where2' => 'onSMS',
                    'Name3' => 'FilteronPhoto',
                    'Where3' => 'onPhoto',
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
							'Table' => 'crm_parent',
							'FromField' => 'ChildID',
							'ToField' => 'ChildID'
						),
						array(
							'Table' => 'crm_parent_contract',
							'FromField' => 'ParentID',
							'ToField' => 'ParentID',
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
							'Table' => 'crm_parent',
							'FromField' => 'ChildID',
							'ToField' => 'ChildID'
						),
						array(
							'Table' => 'crm_parent_contract',
							'FromField' => 'ParentID',
							'ToField' => 'ParentID',
						),
						array(
							'Table' => 'crm_parent_invoice',
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
							'Table' => 'crm_parent',
							'FromField' => 'ChildID',
							'ToField' => 'ChildID'
						),
						array(
							'Table' => 'crm_parent_contract',
							'FromField' => 'ParentID',
							'ToField' => 'ParentID',
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
							'Table' => 'crm_parent',
							'FromField' => 'ChildID',
							'ToField' => 'ChildID'
						),
						array(
							'Table' => 'crm_parent_contract',
							'FromField' => 'ParentID',
							'ToField' => 'ParentID',
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
							'Table' => 'crm_parent',
							'FromField' => 'ChildID',
							'ToField' => 'ChildID'
						),
						array(
							'Table' => 'crm_parent_contract',
							'FromField' => 'ParentID',
							'ToField' => 'ParentID',
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
							'Table' => 'crm_parent',
							'FromField' => 'ChildID',
							'ToField' => 'ChildID'
						),
						array(
							'Table' => 'crm_parent_contract',
							'FromField' => 'ParentID',
							'ToField' => 'ParentID'
						),
						array(
							'Table' => 'crm_parent_contract2season',
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
							'Table' => 'crm_parent',
							'FromField' => 'ChildID',
							'ToField' => 'ChildID'
						),
						array(
							'Table' => 'crm_parent_contract',
							'FromField' => 'ParentID',
							'ToField' => 'ParentID',
							'Field' => 'ManagerID',
							'Operation' => '='
						)
					)
				)
			),
            array(
                "Name" => 'FilterSource',
                'Type' => "component",
                'File' => "filters/directory.php",
                'Class' => "DirectoryFilter",
                'Config' => array(
                    'ArrayName' => 'FilterSourceValueList',
                    'Table' => 'crm_directory',
                    'FromField' => 'Source',
                    'ToField' => 'DirectoryID',
                    'DirectoryType' => "6",
                    'ViewField' => 'Name',
					"Name" => 'FilterSource',
                )
            ),
			array(
				'Name' => 'AccessFilter',
				'File' => "filters/child.php",
				'Class' => "Child2StaffFilter",
				'Config' => array(
					'Name' => "StaffID",
					'Table' => "crm_child2staff",
					'FromField' => "ChildID",
					'ToField' => "ChildID"
				)
			)
		),
	),
	'ViewTemplate' => 'child_view.html',
	'ViewConfig' => array(
		'Fields' => array(
			array(
				'Name' => 'Image',
				'Type' => 'component',
				'File' => 'components/image.php',
				'Class' => 'ImageViewComponent',
				'Config' => array(
					'Path' => 'child',
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
				'SQL' => 'CONCAT(t.LastName," ",t.FirstName)',
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
				'Name' => 'Squad',
				'Type' => 'field',
			),
			array(
				'Name' => 'Age',
				'Type' => 'component',
				'File' => 'components/age.php',
				'Class' => 'AgeViewComponent',
				'Config' => array(
					'DOBField' => 'DOB',
				),
			),
			array(
				'Name' => 'Phone',
				'Type' => 'component',
				'File' => 'components/phone.php',
				'Class' => 'PhoneViewComponent',
				'Config' => array(
					'Table' => 'crm_child_phone',
					'KeyField' => 'ChildID',
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
									'ToField' => 'ChildID'
								)
							),
							array(
								'Field' => 'LinkedEntity',
								'Operation' => '=',
								'Value' => 'child',
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
					'Table' => 'crm_child_email',
					'KeyField' => 'ChildID',
				),
			),
			array(
				'Name' => 'School',
				'Type' => 'component',
				'File' => 'components/linked.php',
				'Class' => 'LinkedViewComponent',
				'Config' => array(
					'Table' => 'crm_school',
					'FromField' => 'SchoolID',
					'ToField' => 'SchoolID',
					'ViewField' => 'Title',
				),
			),
			array(
				'Name' => 'Class',
				'Type' => 'field',
			),
			array(
				'Name' => 'Social',
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
					'LinkTable' => 'crm_child2season',
					'LinkFromField' => 'ChildID',
					'LinkToField' => 'SeasonID',
                    'ViewSQL' => 't.Title, t.TypeID',
                    'OrderSQL' => 't.TypeID ASC, t.SeasonID ASC',
				),
			),
			array(
				'Name' => 'Staff',
				'Type' => 'component',
				'File' => 'components/linked.php',
				'Class' => 'LinkedMultipleSelectViewComponent',
				'Config' => array(
					'Table' => 'user',
					'KeyField' => 'UserID',
					'LinkTable' => 'crm_child2staff',
					'LinkFromField' => 'ChildID',
					'LinkToField' => 'StaffID',
					'ViewSQL' => 'CONCAT(t.LastName," ",t.FirstName) AS Title, (SELECT StaffID FROM crm_staff WHERE UserID=t.UserID) AS StaffID',
				),
			),
			array(
				'Name' => 'Friends',
				'Type' => 'component',
				'File' => 'components/linked.php',
				'Class' => 'LinkedMultipleSelectViewComponent',
				'Config' => array(
					'Table' => 'crm_child',
					'KeyField' => 'ChildID',
					'LinkTable' => 'crm_child2child',
					'LinkFromField' => 'ChildID',
					'LinkToField' => 'FriendID',
					'ViewSQL' => 'CONCAT(t.LastName," ",t.FirstName) AS Title',
					'Symmetric' => true,
				),
			),
			array(
				'Name' => 'File',
				'Type' => 'component',
				'File' => 'components/file.php',
				'Class' => 'FileViewComponent',
				'Config' => array(
					'EntityType' => 'child',
					'Path' => 'storage',
				),
			),
			array(
				'Name' => "Questionnaire",
				'Type' => "component",
				'File' => "components/questionnaire.php",
				'Class'=> "QuestionnaireComponent",
				'Config' => array(
					'KeyField' => "ChildID",
					'View' => "viewer"
				)
			),
			array(
				'Name' => 'Status',
				'Type' => 'component',
				'File' => 'components/status.php',
				'Class' => 'StatusEditComponent',
				'Config' => array(
					'StatusTable' => 'crm_child_status',
					'KeyField' => 'ChildID',
					'CustomQuantity' => false,
					'Entity' => "child"
				),
			),
            array(
                'Name' => 'Characteristic',
                'Type' => 'component',
                'File' => 'components/comment.php',
                'Class' => 'CharacteristicViewComponent',
                'Config' => array(
                    'Table' => 'crm_child_characteristic',
                    'KeyField' => 'ChildID',
                    'Image' => '50x50|8|Small',
                    'FileComponent' => array(
                        'Name' => 'File',
                        'File' => 'components/file.php',
                        'Class' => 'FileViewComponent',
                        'Config' => array(
                            'EntityType' => 'child_comment',
                            'Path' => 'storage',
                        ),
                    ),
                    'FilterAccess' => [INTEGRATOR, ADMINISTRATOR, MANAGER, GUIDE],
                    'FilterAppend' => [INTEGRATOR, ADMINISTRATOR, MANAGER, GUIDE],
					'FilterEdit' => [INTEGRATOR],
                ),
            ),
			array(
				'Name' => 'Parent',
				'Type' => 'component',
				'File' => 'components/itemlist.php',
				'Class' => 'ItemListViewComponent',
				'Config' => array(
					'Table' => 'crm_parent',
					'ID' => 'ParentID',
					'ItemsOrderBy' => 't.LastName,t.FirstName',
					'KeyField' => 'ChildID',
					'ListConfig' => array(
						'Fields' => array(
							array(
								'Name' => 'FIO',
								'Type' => 'sql',
								'SQL' => 'CONCAT(t.LastName," ",t.FirstName," ",COALESCE(t.MiddleName,""))',
							),
							array(
								'Name' => 'ParentStatus',
								'Type' => 'field',
							),
							array(
								'Name' => 'Phone',
								'Type' => 'component',
								'File' => 'components/phone.php',
								'Class' => 'PhoneViewComponent',
								'Config' => array(
									'Table' => 'crm_parent_phone',
									'KeyField' => 'ParentID',
								),
							),
							array(
								'Name' => 'Email',
								'Type' => 'component',
								'File' => 'components/email.php',
								'Class' => 'EmailViewComponent',
								'Config' => array(
									'Table' => 'crm_parent_email',
									'KeyField' => 'ParentID',
								),
							),
							array(
								'Name' => 'Address',
								'Type' => 'sql',
								'SQL' => 'CONCAT("г. ",t.AddressCity,", ул. ",t.AddressStreet,", ",t.AddressHome,", кв. ",t.AddressFlat)',
							),
							array(
								'Name' => 'Work',
								'Type' => 'field',
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
									'FromField' => 'ChildID',
									'ToField' => 'ChildID'
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
					'Table' => 'crm_child_comment',
					'KeyField' => 'ChildID',
					'Image' => '50x50|8|Small',
					'FileComponent' => array(
						'Name' => 'File',
						'File' => 'components/file.php',
						'Class' => 'FileViewComponent',
						'Config' => array(
							'EntityType' => 'child_comment',
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
				'Class' => 'FinanceViewComponentForChild',
				'Config' => array(
					'Type' => 'linked',
					'LinkTable' => 'crm_parent',
					'FromField' => 'ParentID',
					'ToField' => 'ParentID',
					'TargetField' => 'ChildID',
					'ContractTable' => 'crm_parent_contract',
					'InvoiceTable' => 'crm_parent_invoice',
					'PaymentTable' => 'crm_parent_payment',
					'PaybackTable' => 'crm_parent_payback',
                    'ActTable' => 'crm_parent_act',
                    'UseSeason' => true,
                    'LegalContract' => 'crm_legal_contract',
                    'LegalInvoiceTable' => 'crm_legal_invoice',
                    'LegalPaymentTable' => 'crm_legal_payment',
                    'LegalPaybackTable' => 'crm_legal_payback',
                    'LegalActTable' => 'crm_legal_act',
                    'SchoolPaybackTable' => 'crm_school_contact_payback',
                    'SchoolInvoiceTable' => 'crm_school_contact_invoice',
                    'SchoolPaymentTable' => 'crm_school_contact_payment',
                    'SchoolContract' => 'crm_school_contact_contract',
                    'SchoolActTable' => 'crm_school_contact_act',
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
	'PrintTemplate' => 'child_print.html',
	'PrintListItemsPerPage' => 20,
	'PrintConfig' => array(
		'Fields' => array(
			array(
				'Name' => 'FIO',
				'Type' => 'sql',
				'SQL' => 'CONCAT(t.LastName," ",t.FirstName)',
			),
			array(
				'Name' => 'Squad',
				'Type' => 'field',
			),
			array(
				'Name' => 'Age',
				'Type' => 'component',
				'File' => 'components/age.php',
				'Class' => 'AgeViewComponent',
				'Config' => array(
					'DOBField' => 'DOB',
				),
			),
			array(
				'Name' => 'School',
				'Type' => 'component',
				'File' => 'components/linked.php',
				'Class' => 'LinkedViewComponent',
				'Config' => array(
					'Table' => 'crm_school',
					'FromField' => 'SchoolID',
					'ToField' => 'SchoolID',
					'ViewField' => 'Title',
				),
			),
			array(
				'Name' => "Document",
				'Type' => "component",
				'File' => "components/document.php",
				'Class' => "DocumentViewComponent",
				'Config' => array(
					'Table' => "crm_child_document",
					'KeyField' => "ChildID"
				)
			),
			array(
				'Name' => 'Class',
				'Type' => 'field',
			),
			array(
				'Name' => 'Friends',
				'Type' => 'component',
				'File' => 'components/linked.php',
				'Class' => 'LinkedMultipleSelectViewComponent',
				'Config' => array(
					'Table' => 'crm_child',
					'KeyField' => 'ChildID',
					'LinkTable' => 'crm_child2child',
					'LinkFromField' => 'ChildID',
					'LinkToField' => 'FriendID',
					'ViewSQL' => 'CONCAT(t.LastName," ",t.FirstName) AS Title',
					'Symmetric' => true,
				),
			),
			array(
				'Name' => 'Staff',
				'Type' => 'component',
				'File' => 'components/linked.php',
				'Class' => 'LinkedMultipleSelectViewComponent',
				'Config' => array(
					'Table' => 'user',
					'KeyField' => 'UserID',
					'LinkTable' => 'crm_child2staff',
					'LinkFromField' => 'ChildID',
					'LinkToField' => 'StaffID',
					'ViewSQL' => 'CONCAT(t.LastName," ",t.FirstName) AS Title',
				),
			),
			array(
				'Name' => 'Season',
				'Type' => 'component',
				'File' => 'components/linked.php',
				'Class' => 'LinkedMultipleSelectViewComponent',
				'Config' => array(
					'Table' => 'crm_season',
					'KeyField' => 'SeasonID',
					'LinkTable' => 'crm_child2season',
					'LinkFromField' => 'ChildID',
					'LinkToField' => 'SeasonID',
					'ViewSQL' => 't.Title',
				),
			),
			array(
				'Name' => 'Comment',
				'Type' => 'field',
			),
			array(
				'Name' => 'Parent',
				'Type' => 'component',
				'File' => 'components/itemlist.php',
				'Class' => 'ItemListViewComponent',
				'Config' => array(
					'Table' => 'crm_parent',
					'ID' => 'ParentID',
					'ItemsOrderBy' => 't.LastName,t.FirstName',
					'KeyField' => 'ChildID',
					'ListConfig' => array(
						'Fields' => array(
							array(
								'Name' => 'FIO',
								'Type' => 'sql',
								'SQL' => 'CONCAT(t.LastName," ",t.FirstName," ",COALESCE(t.MiddleName,""))',
							),
							array(
								'Name' => 'ParentStatus',
								'Type' => 'field',
							),
							array(
								'Name' => 'Phone',
								'Type' => 'component',
								'File' => 'components/phone.php',
								'Class' => 'PhoneViewComponent',
								'Config' => array(
									'Table' => 'crm_parent_phone',
									'KeyField' => 'ParentID',
								),
							),
							array(
								'Name' => 'Address',
								'Type' => 'sql',
								'SQL' => 'CONCAT("г. ",t.AddressCity,", ул. ",t.AddressStreet,", ",t.AddressHome,", кв. ",t.AddressFlat)',
							),
							array(
								'Name' => 'Work',
								'Type' => 'field',
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
									'FromField' => 'ChildID',
									'ToField' => 'ChildID'
								)
							)
						)
					),
				),
			),
			array(
				'Name' => 'Phone',
				'Type' => 'component',
				'File' => 'components/phone.php',
				'Class' => 'PhoneViewComponent',
				'Config' => array(
					'Table' => 'crm_child_phone',
					'KeyField' => 'ChildID',
				),
			),
		),
	),
	'PrintListTemplate' => 'child_printlist.html',
	'PrintListConfig' => array(
		'Fields' => array(
			array(
				'Name' => 'FIO',
				'Type' => 'sql',
				'SQL' => 'CONCAT(t.LastName," ",t.FirstName)',
			),
			array(
				'Name' => 'Squad',
				'Type' => 'field',
			),
			array(
				'Name' => 'Age',
				'Type' => 'component',
				'File' => 'components/age.php',
				'Class' => 'AgeViewComponent',
				'Config' => array(
					'DOBField' => 'DOB',
				),
			),
			array(
				'Name' => 'School',
				'Type' => 'component',
				'File' => 'components/linked.php',
				'Class' => 'LinkedViewComponent',
				'Config' => array(
					'Table' => 'crm_school',
					'FromField' => 'SchoolID',
					'ToField' => 'SchoolID',
					'ViewField' => 'Title',
				),
			),
			array(
				'Name' => "Document",
				'Type' => "component",
				'File' => "components/document.php",
				'Class' => "DocumentViewComponent",
				'Config' => array(
					'Table' => "crm_child_document",
					'KeyField' => "ChildID"
				)
			),
			array(
				'Name' => 'Class',
				'Type' => 'field',
			),
			array(
				'Name' => 'Friends',
				'Type' => 'component',
				'File' => 'components/linked.php',
				'Class' => 'LinkedMultipleSelectViewComponent',
				'Config' => array(
					'Table' => 'crm_child',
					'KeyField' => 'ChildID',
					'LinkTable' => 'crm_child2child',
					'LinkFromField' => 'ChildID',
					'LinkToField' => 'FriendID',
					'ViewSQL' => 'CONCAT(t.LastName," ",t.FirstName) AS Title',
					'Symmetric' => true,
				),
			),
			array(
				'Name' => 'Staff',
				'Type' => 'component',
				'File' => 'components/linked.php',
				'Class' => 'LinkedMultipleSelectViewComponent',
				'Config' => array(
					'Table' => 'crm_staff',
					'KeyField' => 'StaffID',
					'LinkTable' => 'crm_child2staff',
					'LinkFromField' => 'ChildID',
					'LinkToField' => 'StaffID',
					'ViewSQL' => 'CONCAT(t.LastName," ",t.FirstName) AS Title',
				),
			),
			array(
				'Name' => 'Season',
				'Type' => 'component',
				'File' => 'components/linked.php',
				'Class' => 'LinkedMultipleSelectViewComponent',
				'Config' => array(
					'Table' => 'crm_season',
					'KeyField' => 'SeasonID',
					'LinkTable' => 'crm_child2season',
					'LinkFromField' => 'ChildID',
					'LinkToField' => 'SeasonID',
					'ViewSQL' => 't.Title',
				),
			),
			array(
				'Name' => 'Comment',
				'Type' => 'field',
			),
			array(
				'Name' => 'Parent',
				'Type' => 'component',
				'File' => 'components/itemlist.php',
				'Class' => 'ItemListViewComponent',
				'Config' => array(
					'Table' => 'crm_parent',
					'ID' => 'ParentID',
					'ItemsOrderBy' => 't.LastName,t.FirstName',
					'KeyField' => 'ChildID',
					'ListConfig' => array(
						'Fields' => array(
							array(
								'Name' => 'FIO',
								'Type' => 'sql',
								'SQL' => 'CONCAT(t.LastName," ",t.FirstName," ",COALESCE(t.MiddleName,""))',
							),
							array(
								'Name' => 'ParentStatus',
								'Type' => 'field',
							),
							array(
								'Name' => 'Phone',
								'Type' => 'component',
								'File' => 'components/phone.php',
								'Class' => 'PhoneViewComponent',
								'Config' => array(
									'Table' => 'crm_parent_phone',
									'KeyField' => 'ParentID',
								),
							),
							array(
								'Name' => 'Address',
								'Type' => 'sql',
								'SQL' => 'CONCAT("г. ",t.AddressCity,", ул. ",t.AddressStreet,", ",t.AddressHome,", кв. ",t.AddressFlat)',
							),
							array(
								'Name' => 'Work',
								'Type' => 'field',
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
									'FromField' => 'ChildID',
									'ToField' => 'ChildID'
								)
							)
						)
					),
				),
			),
			array(
				'Name' => 'Phone',
				'Type' => 'component',
				'File' => 'components/phone.php',
				'Class' => 'PhoneViewComponent',
				'Config' => array(
					'Table' => 'crm_child_phone',
					'KeyField' => 'ChildID',
				),
			),
		),
	),
	'EditTemplate' => 'child_edit.html',
	'EditConfig' => array(
		'Fields' => array(
			array(
				'Name' => 'Image',
				'Type' => 'component',
				'File' => 'components/image.php',
				'Class' => 'ImageEditComponent',
				'Config' => array(
					'Path' => 'child',
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
                'Name' => 'Archive',
                'Type' => 'field',
            ),
			array(
				'Name' => 'Squad',
				'Type' => 'field',
			),
			array(
				'Name' => 'Sex',
				'Type' => 'field',
				'Validate' => 'option',
				'Options' => array('M', 'F')
			),
			array(
				'Name' => 'DOB',
				'Type' => 'date',
				'Validate' => 'date'
			),
			array(
				'Name' => 'Phone',
				'Type' => 'component',
				'File' => 'components/phone.php',
				'Class' => 'PhoneEditComponent',
				'Config' => array(
					'Table' => 'crm_child_phone',
					'KeyField' => 'ChildID',
				),
			),
			array(
				'Name' => 'Email',
				'Type' => 'component',
				'File' => 'components/email.php',
				'Class' => 'EmailEditComponent',
				'Config' => array(
					'Table' => 'crm_child_email',
					'KeyField' => 'ChildID',
				),
			),
			array(
				'Name' => 'School',
				'Type' => 'component',
				'File' => 'components/linked.php',
				'Class' => 'LinkedEditComponent',
				'Config' => array(
					'Table' => 'crm_school',
					'FromField' => 'SchoolID',
					'ToField' => 'SchoolID',
					'ViewField' => 'Title',
				),
			),
			array(
				'Name' => 'Class',
				'Type' => 'field',
				'Required' => false,
				'Validate' => 'int',
				'Min' => 1,
				'Max' => 11
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
			),array(
				'Name' => 'Document',
				'Type' => 'component',
				'File' => "components/document.php",
				'Class'=> "DocumentEditComponent",
				'Config'=> array(
					'Table' => 'crm_child_document',
					'KeyField' => 'ChildID',
				)
			),
			array(
				'Name' => 'Mailing',
				'Type' => 'component',
				'File' => 'components/mailingcheckbox.php',
				'Class' => 'MailingCheckboxComponent',
				'Config' => array(
					'Table' => 'crm_child2mailing',
				),
			),
			array(
				'Name' => 'Season',
				'Type' => 'component',
				'File' => 'components/linked.php',
				'Class' => 'EditItemSelectComponent',
				'Config' => array(
					'Table' => 'crm_season',
					'KeyField' => 'SeasonID',
					'LinkTable' => 'crm_child2season',
					'LinkFromField' => 'ChildID',
					'LinkToField' => 'SeasonID',
					'ViewSQL' => 't.Title, t.TypeID',
					'OrderSQL' => 't.TypeID ASC, t.SeasonID ASC',
				),
			),
			array(
				'Name' => 'Staff',
				'Type' => 'component',
				'File' => 'components/linked.php',
				'Class' => 'EditItemSelectComponent',
				'Config' => array(
					'Table' => 'user',
					'KeyField' => 'UserID',
					'LinkTable' => 'crm_child2staff',
					'LinkFromField' => 'ChildID',
					'LinkToField' => 'StaffID',
					'ViewSQL' => 'CONCAT(t.LastName," ",t.FirstName) AS Title',
					'WhereSql'=> "t.Role='guide'"
				),
			),
			array(
				'Name' => 'Friends',
				'Type' => 'component',
				'File' => 'components/linked.php',
				'Class' => 'LinkedMultipleSelectEditComponent',
				'Config' => array(
					'Table' => 'crm_child',
					'KeyField' => 'ChildID',
					'LinkTable' => 'crm_child2child',
					'LinkFromField' => 'ChildID',
					'LinkToField' => 'FriendID',
					'ViewSQL' => 'CONCAT(t.LastName," ",t.FirstName) AS Title',
					'Symmetric' => true,
					'MinInput' => '2',
				),
			),
			array(
				'Name' => 'File',
				'Type' => 'component',
				'File' => 'components/file.php',
				'Class' => 'FileEditComponent',
				'Config' => array(
					'EntityType' => 'child',
					'Path' => 'storage',
				),
			),
            array(
                'Name' => "Source",
                'Type' => "component",
                'File' => "components/linked.php",
                'Class' => "LinkedEditComponent",
                'Config' => array(
                    'Table' => 'crm_directory',
                    'FromField' => 'Source',
                    'ToField' => 'DirectoryID',
                    'Condition' => "DirectoryType=6",
                    'ViewField' => 'Name',
                )
            ),
            array(
                'Name' => "Contact",
                'Type' => "field"
            ),
			array(
				'Name' => "Questionnaire",
				'Type' => "component",
				'File' => "components/questionnaire.php",
				'Class'=> "QuestionnaireComponent",
				'Config' => array(
					'KeyField' => "ChildID",
					'View' => "editor"
				)
			),
			array(
				'Name' => 'Parent',
				'Type' => 'component',
				'File' => 'components/itemlist.php',
				'Class' => 'ItemListEditComponent',
				'Config' => array(
					'Entity' => 'parent',
					'Table' => 'crm_parent',
					'ID' => 'ParentID',
					'ItemsOrderBy' => 't.LastName,t.FirstName',
					'Table' => 'crm_parent',
					'KeyField' => 'ChildID',
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
								'Name' => 'ParentStatus',
								'Type' => 'field',
								'Validate' => 'option',
								'Options' => array('father', 'mother', 'grandfather', 'grandmother')
							),
							array(
								'Name' => 'Phone',
								'Type' => 'component',
								'File' => 'components/phone.php',
								'Class' => 'PhoneEditComponent',
								'Config' => array(
									'Table' => 'crm_parent_phone',
									'KeyField' => 'ParentID',
								),
							),
							array(
								'Name' => 'Email',
								'Type' => 'component',
								'File' => 'components/email.php',
								'Class' => 'EmailEditComponent',
								'Config' => array(
									'Table' => 'crm_parent_email',
									'KeyField' => 'ParentID',
								),
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
								'Name' => 'Work',
								'Type' => 'field',
							),
							array(
								'Name' => 'Passport',
								'Type' => 'field',
							),
							array(
								'Name' => 'ChildID',
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
									'FromField' => 'ChildID',
									'ToField' => 'ChildID'
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
					'Entity' => 'child',
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
                'Table' => 'crm_child',
                'KeyField' => 'ChildID',
                'ArchiveField' => 'Archive',
            ),
        ),
        'RemoveFromArchive' => array(
            'File' => 'actions/archive.php',
            'Class' => 'ArchiveAction',
            'Config' => array(
                'Table' => 'crm_child',
                'KeyField' => 'ChildID',
                'ArchiveField' => 'Archive',
            ),
        ),
		'Reassign' => array(
			'File' => 'actions/assign.php',
			'Class' => 'AssignAction',
			'Config' => array(
				'Table' => 'crm_child',
				'KeyField' => 'ChildID',
				'ManagerField' => 'ManagerID'
			),
		),
		'ClearPanel' => array(
			'File' => 'actions/status.php',
			'Class' => 'StatusAction',
			'Config' => array(
				'Table' => 'crm_child_status',
				'KeyField' => 'ChildID',
			),
		),
		'SaveStatus' => array(
			'File' => 'actions/status.php',
			'Class' => 'StatusAction',
			'Config' => array(
				'Name' => 'Status',
				'StatusTable' => 'crm_child_status',
				'Entity2SeasonTable' => 'crm_child2season'
			),
		),
		'AddComment' => array(
			'File' => 'actions/comment.php',
			'Class' => 'CommentAction',
			'Config' => array(
				'Table' => 'crm_child_comment',
				'KeyField' => 'ChildID',
				'FileComponent' => array(
					'Name' => 'File',
					'File' => 'components/file.php',
					'Class' => 'FileEditComponent',
					'Config' => array(
						'EntityType' => 'child_comment',
						'Path' => 'storage'
					)
				)
			),
		),
        'AddCharacteristic' => array(
            'File' => 'actions/comment.php',
            'Class' => 'CommentAction',
            'Config' => array(
                'Table' => 'crm_child_characteristic',
                'KeyField' => 'ChildID',
                'FileComponent' => array(
                    'Name' => 'File',
                    'File' => 'components/file.php',
                    'Class' => 'FileEditComponent',
                    'Config' => array(
                        'EntityType' => 'child_comment',
                        'Path' => 'storage'
                    )
                )
            ),
        ),
		'RemoveCharacteristic' => array(
			'File' => "actions/comment.php",
			'Class' => "CommentAction",
			'Config' => array(
				'Table' => "crm_child_characteristic",
				'KeyField' => "CommentID",
				'KeyValue' => "CommentID"
			)
		),
		'UpdateCharacteristic' => array(
			'File' => "actions/comment.php",
			'Class' => "CommentAction",
			'Config' => array(
				'Table' => "crm_child_characteristic",
				'KeyValue' => "CommentID",
				'PropertyName' => "CommentID",
				'EntityType' => 'child_comment',
				'Path' => 'storage',
				'Type' => "characteristic"
			)
		),
		'AddContract' => array(
			'File' => 'actions/finance.php',
			'Class' => 'FinanceAction',
			'Config' => array(
				'Table' => 'crm_parent_contract',
				'KeyField' => 'ParentID',
				'UseSeason' => true
			),
		),
		'AddInvoice' => array(
			'File' => 'actions/finance.php',
			'Class' => 'FinanceAction',
			'Config' => array(
				'Table' => 'crm_parent_invoice',
				'ContractTable' => 'crm_parent_contract'
			),
		),
		'AddPayment' => array(
			'File' => 'actions/finance.php',
			'Class' => 'FinanceAction',
			'Config' => array(
				'Table' => 'crm_parent_payment',
				'ContractTable' => 'crm_parent_contract',
				'PaybackTable' => 'crm_parent_payback',
			),
		),
		'AddPayback' => array(
			'File' => 'actions/finance.php',
			'Class' => 'FinanceAction',
			'Config' => array(
				'Table' => 'crm_parent_payback',
				'ContractTable' => 'crm_parent_contract',
				'PaymentTable' => 'crm_parent_payment'
			),
		),
		'AddAct' => array(
			'File' => 'actions/finance.php',
			'Class' => 'FinanceAction',
			'Config' => array(
				'Table' => 'crm_parent_act'
			),
		),
		'GetPaymentPDF' => array(
			'File' => 'actions/finance.php',
			'Class' => 'FinanceAction',
			'Config' => array(
				'Table' => 'crm_parent_payment',
				'ContractTable' => 'crm_parent_contract',
				'ClientTable' => 'crm_parent',
				'ClientPhoneTable' => 'crm_parent_phone',
				'ClientKey' => 'ParentID',
				'ClientPhoneKey' => 'ParentID',
				'Template' => 'payment_pdf.html',
			),
		),
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
                    array(
                        'Name' => 'ParentPhoneString',
                        'Type' => 'field',
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
                                'SQLSelect' => 'CONCAT (d.Prefix, d.Number)'
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
					array(
						'Name' => 'LastDayForPay',
						'Type' => 'field',
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
		'RemoveContract' => array(
			'File' => 'actions/finance.php',
			'Class' => 'FinanceAction',
			'Config' => array(
				'PaymentTable' => 'crm_parent_payment',
				'PaybackTable' => 'crm_parent_payback',
				'Remove' => array(
					array('Table' => 'crm_parent_contract', 'Key' => 'ContractID'),
					array('Table' => 'crm_parent_contract2season', 'Key' => 'ContractID'),
					array('Table' => 'crm_parent_invoice', 'Key' => 'ContractID'),
					array('Table' => 'crm_parent_payment', 'Key' => 'ContractID'),
					array('Table' => 'crm_parent_act', 'Key' => 'ContractID'),
				),
			),
		),
		'RemoveInvoice' => array(
			'File' => 'actions/finance.php',
			'Class' => 'FinanceAction',
			'Config' => array(
				'Remove' => array(
					array('Table' => 'crm_parent_invoice', 'Key' => 'InvoiceID'),
				),
			),
		),
		'RemovePayment' => array(
			'File' => 'actions/finance.php',
			'Class' => 'FinanceAction',
			'Config' => array(
				'Remove' => array(
					array('Table' => 'crm_parent_payment', 'Key' => 'PaymentID'),
				),
			),
		),
		'RemovePayback' => array(
			'File' => 'actions/finance.php',
			'Class' => 'FinanceAction',
			'Config' => array(
				'Remove' => array(
					array('Table' => 'crm_parent_payback', 'Key' => 'PaybackID'),
				),
			),
		),
		'RemoveAct' => array(
			'File' => 'actions/finance.php',
			'Class' => 'FinanceAction',
			'Config' => array(
				'Remove' => array(
					array('Table' => 'crm_parent_act', 'Key' => 'ActID'),
				),
			),
		),
		'Export' => array(
			'Access' => array(INTEGRATOR, ADMINISTRATOR),
			'File' => 'actions/export.php',
			'Class' => 'ExportAction',
			'Config' => array(
				'Template' => 'child_export.html',
				'Entity' => 'child',
				'Table' => 'crm_child',
				'ID' => 'ChildID',
				'ItemsPerPage' => 0,
				'ItemsOrderBy' => 't.LastName,t.FirstName',
				'ListConfig' => array(
					'Fields' => array(
						array(
							'Name' => 'LastName',
							'Type' => 'field'
						),
						array(
							'Name' => 'FirstName',
							'Type' => 'field'
						),
						array(
							'Name' => 'MiddleName',
							'Type' => 'field'
						),
						array(
							'Name' => 'DOB',
							'Type' => 'field'
						),
						array(
							'Name' => 'School',
							'Type' => 'component',
							'File' => 'components/linked.php',
							'Class' => 'LinkedViewComponent',
							'Config' => array(
								'Table' => 'crm_school',
								'FromField' => 'SchoolID',
								'ToField' => 'SchoolID',
								'ViewField' => 'Title',
							),
						),
						array(
							'Name' => "Document",
							'Type' => "component",
							'File' => "components/document.php",
							'Class' => "DocumentViewComponent",
							'Config' => array(
								'Table' => "crm_child_document",
								'KeyField' => "ChildID"
							)
						),
						array(
							'Name' => 'Phone',
							'Type' => 'component',
							'File' => 'components/phone.php',
							'Class' => 'PhoneViewComponent',
							'Config' => array(
								'Table' => 'crm_child_phone',
								'KeyField' => 'ChildID',
							),
						),
						array(
							'Name' => 'Email',
							'Type' => 'component',
							'File' => 'components/email.php',
							'Class' => 'EmailViewComponent',
							'Config' => array(
								'Table' => 'crm_child_email',
								'KeyField' => 'ChildID',
							),
						),
						array(
							'Name' => 'Parent',
							'Type' => 'component',
							'File' => 'components/itemlist.php',
							'Class' => 'ItemListViewComponent',
							'Config' => array(
								'Table' => 'crm_parent',
								'ID' => 'ParentID',
								'ItemsOrderBy' => 't.LastName,t.FirstName',
								'KeyField' => 'ChildID',
								'ListConfig' => array(
									'Fields' => array(
										array(
											'Name' => 'LastName',
											'Type' => 'field',
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
											'Name' => 'Phone',
											'Type' => 'component',
											'File' => 'components/phone.php',
											'Class' => 'PhoneViewComponent',
											'Config' => array(
												'Table' => 'crm_parent_phone',
												'KeyField' => 'ParentID',
											),
										),
										array(
											'Name' => 'Email',
											'Type' => 'component',
											'File' => 'components/email.php',
											'Class' => 'EmailViewComponent',
											'Config' => array(
												'Table' => 'crm_parent_email',
												'KeyField' => 'ParentID',
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
												'FromField' => 'ChildID',
												'ToField' => 'ChildID'
											)
										)
									)
								),
							),
						),
					),
				),
			),
		)
	),
)
?>
