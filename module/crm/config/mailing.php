<?php 
$GLOBALS['entityConfig']['mailing'] = array(
	'Access' => array(INTEGRATOR, ADMINISTRATOR, MANAGER),
	'RemoveAccess' => array(INTEGRATOR, ADMINISTRATOR),
	'AdminMenuIcon' => 'fa fa-envelope-o',
	'AdminSubmenu' => array(
		array("Title" => GetTranslation("admin-menu-crm-mailing-create"),
			"Link" => "module.php?load=crm&entity=mailing&FilterShow=create",
			"Selected" => !isset($_REQUEST["FilterShow"]) || $_REQUEST["FilterShow"] == "create"),	
		array("Title" => GetTranslation("admin-menu-crm-mailing-history"),
			"Link" => "module.php?load=crm&entity=mailing&FilterShow=history",
			"Selected" => isset($_REQUEST["FilterShow"]) && $_REQUEST["FilterShow"] == "history")
	),
	'Table' => 'crm_mailing',
	'ID' => 'MailingID',
	'ItemsPerPage' => 20,
	'ItemsOrderBy' => 't.Created DESC',
	'ListTemplate' => 'mailing_list.html',
	'ListConfig' => array(
		'Fields' => array(
			array(
				'Name' => 'User',
				'Type' => 'component',
				'File' => 'components/linked.php',
				'Class' => 'LinkedViewComponent',
				'Config' => array(
					'Table' => 'user',
					'FromField' => 'UserID',
					'ToField' => 'UserID',
					'ViewField' => 'Title',
					'ViewSQL' => 'CONCAT(LastName, \' \', FirstName) AS Name',
				),
			),
			array(
				'Name' => 'Sender',
				'Type' => 'component',
				'File' => 'components/linked.php',
				'Class' => 'LinkedViewComponent',
				'Config' => array(
					'Table' => 'crm_mailing_sender',
					'FromField' => 'SenderID',
					'ToField' => 'SenderID',
					'ViewField' => 'Email'
				),
			),
			array(
				'Name' => 'Subject',
				'Type' => 'field',
			),
			array(
				'Name' => 'CountReciever',
				'Type' => 'component',
				'File' => 'components/linked.php',
				'Class' => 'CountViewComponent',
				'Config' => array(
					'Table' => 'crm_mailing_dispatch',
					'KeyField' => 'MailingID',
					'TargetField' => 'DispatchID',
				),
			),
			array(
				'Name' => 'Reciever',
				'Type' => 'component',
				'File' => 'components/email.php',
				'Class' => 'EmailRecieverComponent',
				'Config' => array(
					'Table' => 'crm_mailing_dispatch',
					'KeyField' => 'MailingID',
				),
			),
			array(
				'Name' => 'Created',
				'Type' => 'field',
			),
		),
		'Filters' => array(
			array(
				'Name' => 'FilterShow',
				'File' => 'filter.php',
				'Class' => 'BaseFilter',
				'Config' => array(
					'Name' => 'FilterShow'
				)
			),
			array(
				'Name' => 'FilterShowErrors',
				'File' => 'filter.php',
				'Class' => 'BaseFilter',
				'Config' => array(
					'Name' => 'FilterShowErrors'
				)
			),
			array(
				'Name' => 'FilterEmail',
				'File' => 'filters/mailing.php',
				'Class' => 'MailingFilter',
				'Config' => array(
					'Name' => 'FilterEmail',
				)
			),
			//only for loading data to mailing create form 
			array(
				'Name' => 'load_child',
				'File' => 'filter.php',
				'Class' => 'BaseFilter',
				'Config' => array(
					'Name' => 'load_child'
				)
			),
			array(
				'Name' => 'load_school',
				'File' => 'filter.php',
				'Class' => 'BaseFilter',
				'Config' => array(
					'Name' => 'load_school'
				)
			),
			array(
				'Name' => 'load_legal',
				'File' => 'filter.php',
				'Class' => 'BaseFilter',
				'Config' => array(
					'Name' => 'load_legal'
				)
			),
			array(
				'Name' => 'load_staff',
				'File' => 'filter.php',
				'Class' => 'BaseFilter',
				'Config' => array(
					'Name' => 'load_staff'
				)
			),
			array(
				'Name' => 'ChildCategoryID',
				'Type' => 'component',
				'File' => 'filters/linked.php',
				'Class' => 'LinkedFilter',
				'Config' => array(
					'Disabled' => true,
					'Name' => 'ChildCategoryID',
					'Table' => 'crm_category',
					'ArrayName' => 'ChildCategoryList',
					'FromField' => 'CategoryID',
					'ToField' => 'CategoryID',
					'ViewField' => 'Title',
                    'IncludeKeys' => array(1,2,3,7,12,13)
				),
			),
			array(
				'Name' => 'ChildAge',
				'File' => 'filters/age.php',
				'Class' => 'AgeFilter',
				'Config' => array(
					'Disabled' => true,
					'Name' => 'ChildAge',
					'DOBField' => 'DOB'
				),
			),
			array(
				'Name' => 'ChildClass',
				'File' => 'filter.php',
				'Class' => 'BaseFilter',
				'Config' => array(
					'Name' => 'ChildClass'
				)
			),
			array(
				'Name' => 'ChildStatusID',
				'Type' => 'component',
				'File' => 'filters/linked.php',
				'Class' => 'LinkedFilter',
				'Config' => array(
					'Disabled' => true,
					'Name' => 'ChildStatusID',
					'ArrayName' => 'ChildStatusList',
					'Table' => 'crm_status',
					'FromField' => 'StatusID',
					'ToField' => 'StatusID',
					'ViewField' => 'Title',
				),
			),
			array(
				'Name' => 'ChildSeasonID',
				'File' => 'filters/linked.php',
				'Class' => 'LinkedMultipleSelectFilter',
				'Config' => array(
					'Disabled' => true,
					'Name' => 'ChildSeasonID',
					'ArrayName' => 'ChildSeasonList',
					'Table' => 'crm_season',
					'LinkTable' => 'crm_child2season',
					'Field' => 'SeasonID',
					'FromField' => 'ChildID',
					'LinkFromField' => 'ChildID',
					'LinkToField' => 'SeasonID',
					'ToField' => 'SeasonID',
					'ViewField' => 'TypeID, Title',
				)
			),
			array(
				'Name' => 'SchoolCategoryID',
				'Type' => 'component',
				'File' => 'filters/linked.php',
				'Class' => 'LinkedFilter',
				'Config' => array(
					'Disabled' => true,
					'Name' => 'SchoolCategoryID',
					'ArrayName' => 'SchoolCategoryList',
					'Table' => 'crm_category',
					'FromField' => 'CategoryID',
					'ToField' => 'CategoryID',
					'ViewField' => 'Title',
                    'IncludeKeys' => array(1,2,3,7,12,13)
				),
			),
			array(
				'Name' => 'LegalCategoryID',
				'Type' => 'component',
				'File' => 'filters/linked.php',
				'Class' => 'LinkedFilter',
				'Config' => array(
					'Disabled' => true,
					'Name' => 'LegalCategoryID',
					'ArrayName' => 'LegalCategoryList',
					'Table' => 'crm_category',
					'FromField' => 'CategoryID',
					'ToField' => 'CategoryID',
					'ViewField' => 'Title',
                    'IncludeKeys' => array(1,2,3,7,12,13)
				),
			),
			array(
				'Name' => 'LegalGroupID',
				'Type' => 'component',
				'File' => 'filters/linked.php',
				'Class' => 'LinkedFilter',
				'Config' => array(
						'Disabled' => true,
						'Name' => 'LegalGroupID',
						'ArrayName' => 'LegalGroupList',
						'Table' => 'crm_legalgroup',
						'FromField' => 'GroupID',
						'ToField' => 'GroupID',
						'ViewField' => 'Title',
// 						'IncludeKeys' => array(1, 2, 3)
				),
			),
			array(
				'Name' => 'StaffCategoryID',
				'Type' => 'component',
				'File' => 'filters/linked.php',
				'Class' => 'LinkedFilter',
				'Config' => array(
					'Disabled' => true,
					'Name' => 'StaffCategoryID',
					'ArrayName' => 'StaffCategoryList',
					'Table' => 'crm_category',
					'FromField' => 'CategoryID',
					'ToField' => 'CategoryID',
					'ViewField' => 'Title',
                    'IncludeKeys' => array(1,2,3,7,12,13)
				),
			),
			array(
				'Name' => 'SenderID',
				'Type' => 'component',
				'File' => 'filters/linked.php',
				'Class' => 'LinkedFilter',
				'Config' => array(
					'Disabled' => true,
					'Name' => 'SenderID',
					'ArrayName' => 'SenderList',
					'Table' => 'crm_mailing_sender',
					'FromField' => 'SenderID',
					'ToField' => 'SenderID',
					'ViewField' => 'Email',
				),
			),
			array(
				'Name' => 'Subject',
				'File' => 'filter.php',
				'Class' => 'BaseFilter',
				'Config' => array(
					'Name' => 'Subject'
				)
			),
			array(
				'Name' => 'Content',
				'File' => 'filter.php',
				'Class' => 'BaseFilter',
				'Config' => array(
					'Name' => 'Content'
				)
			),
			array(
				'Name' => 'TargetEmail',
				'File' => 'filter.php',
				'Class' => 'BaseFilter',
				'Config' => array(
					'Name' => 'TargetEmail'
				)
			),
			array(
				'Name' => 'TargetName',
				'File' => 'filter.php',
				'Class' => 'BaseFilter',
				'Config' => array(
					'Name' => 'TargetName'
				)
			),
			array(
				'Name' => 'TargetEntityType',
				'File' => 'filter.php',
				'Class' => 'BaseFilter',
				'Config' => array(
					'Name' => 'TargetEntityType'
				)
			),
			array(
				'Name' => 'TargetEntityID',
				'File' => 'filter.php',
				'Class' => 'BaseFilter',
				'Config' => array(
					'Name' => 'TargetEntityID'
				)
			),
		)
	),
	'ViewTemplate' => 'mailing_view.html',
	'ViewConfig' => array(
		'Fields' => array(
			array(
				'Name' => 'Subject', 
				'Type' => 'field'
			),
			array(
				'Name' => 'Content',
				'Type' => 'component',
				'File' => 'components/content.php',
				'Class' => 'ContentViewComponent',
				'Config' => array(
					'Field' => 'Content'
				),
			),
			array(
				'Name' => 'Attachment',
				'Type' => 'component',
				'File' => 'components/attachment.php',
				'Class' => 'AttachmentViewComponent',
				'Config' => array(
					'Table' => 'crm_mailing_attachment',
					'KeyField' => 'MailingID',
				),
			),
			array(
				'Name' => 'Reciever',
				'Type' => 'component',
				'File' => 'components/linked.php',
				'Class' => 'LinkedMultipleSelectViewComponent',
				'Config' => array(
					'Table' => 'crm_mailing_dispatch',
					'KeyField' => 'MailingID',
					'LinkTable' => 'crm_mailing',
					'LinkFromField' => 'MailingID',
					'LinkToField' => 'MailingID',
					'ViewSQL' => 'Email',
				),
			),
			array(
				'Name' => 'Sender',
				'Type' => 'component',
				'File' => 'components/linked.php',
				'Class' => 'LinkedViewComponent',
				'Config' => array(
					'Table' => 'crm_mailing_sender',
					'FromField' => 'SenderID',
					'ToField' => 'SenderID',
					'ViewField' => 'Email',
				),
			),
			array(
				'Name' => 'DispatchCount',
				'Type' => 'component',
				'File' => 'components/linked.php',
				'Class' => 'CountViewComponent',
				'Config' => array(
					'Table' => 'crm_mailing_dispatch',
					'KeyField' => 'MailingID',
					'TargetField' => 'DispatchID',
				),
			),
			array(
				'Name' => 'DispatchSuccessCount',
				'Type' => 'component',
				'File' => 'components/linked.php',
				'Class' => 'CountViewComponent',
				'Config' => array(
					'Table' => 'crm_mailing_dispatch',
					'KeyField' => 'MailingID',
					'TargetField' => 'DispatchID',
					'Condition' => array(
						'Field' => 'Sent',
						'Value' => 'Y'
					),
				),
			),
			array(
				'Name' => 'DispatchError',
				'Type' => 'component',
				'File' => 'components/itemlist.php',
				'Class' => 'ItemListViewComponent',
				'Config' => array(
					'Table' => 'crm_mailing_dispatch',
					'ID' => 'DispatchID',
					'ItemsOrderBy' => 't.DispatchID',
					'KeyField' => 'MailingID',
					'ListConfig' => array(
						'Fields' => array(
							array(
								'Name' => 'EntityType',
								'Type' => 'field',
							),
							array(
								'Name' => 'RecieverEntityID',
								'Type' => 'field',
							),
							array(
								'Name' => 'Email',
								'Type' => 'field',
							),
							array(
								'Name' => 'ErrorInfo',
								'Type' => 'field',
							),
							array(
								'Name' => 'Phone',
								'Type' => 'component',
								'File' => 'components/phone.php',
								'Class' => 'PhoneMorphViewComponent',
								'Config' => array(
									'EntityTypeField' => 'EntityType',
									'EntityIDField' => 'RecieverEntityID'
								),
							),
							array(
								'Name' => 'Manager',
								'Type' => 'component',
								'File' => 'components/linked.php',
								'Class' => 'ExternalLinkedMorphViewComponent',
								'Config' => array(
									'Name' => 'Manager',
									'TargetTable' => 'user',
									'FromField' => 'UserID',
									'ToField' => 'ManagerID',
									'EntityTypeField' => 'EntityType',
									'EntityIDField' => 'RecieverEntityID',
									'ViewField' => 'Name',
									'ViewSQL' => 'CONCAT(t.LastName, \' \', t.FirstName)',
									'DisabledEntities' => array('parent')
								),
							),
							array(
								'Name' => 'Child',
								'Type' => 'component',
								'File' => 'components/linked.php',
								'Class' => 'LinkedViewComponent',
								'Config' => array(
									'Table' => 'crm_child',
									'FromField' => 'RecieverEntityID',
									'ToField' => 'ChildID',
									'ViewField' => 'Title',
									'ViewSQL' => 'CONCAT(LastName, \' \', FirstName)',
								),
							),
							array(
								'Name' => 'Staff',
								'Type' => 'component',
								'File' => 'components/linked.php',
								'Class' => 'LinkedViewComponent',
								'Config' => array(
									'Table' => 'crm_staff',
									'FromField' => 'RecieverEntityID',
									'ToField' => 'StaffID',
									'ViewField' => 'Title',
									'ViewSQL' => 'CONCAT(LastName, \' \', FirstName)',
								),
							),
							array(
								'Name' => 'School',
								'Type' => 'component',
								'File' => 'components/linked.php',
								'Class' => 'LinkedViewComponent',
								'Config' => array(
									'Table' => 'crm_school',
									'FromField' => 'RecieverEntityID',
									'ToField' => 'SchoolID',
									'ViewField' => 'Title'
								),
							),
							array(
								'Name' => 'Legal',
								'Type' => 'component',
								'File' => 'components/linked.php',
								'Class' => 'LinkedViewComponent',
								'Config' => array(
									'Table' => 'crm_legal',
									'FromField' => 'RecieverEntityID',
									'ToField' => 'LegalID',
									'ViewField' => 'Title'
								),
							),
							array(
								'Name' => 'Parent',
								'Type' => 'component',
								'File' => 'components/linked.php',
								'Class' => 'LinkedViewComponent',
								'Config' => array(
									'Table' => 'crm_parent',
									'FromField' => 'RecieverEntityID',
									'ToField' => 'ParentID',
									'ViewField' => 'Title',
									'ViewSQL' => 'CONCAT(LastName, \' \', FirstName)',
								),
							),
							array(
								'Name' => 'SchoolContact',
								'Type' => 'component',
								'File' => 'components/linked.php',
								'Class' => 'LinkedViewComponent',
								'Config' => array(
									'Table' => 'crm_school_contact',
									'FromField' => 'RecieverEntityID',
									'ToField' => 'ContactID',
									'ViewField' => 'Title',
									'ViewSQL' => 'CONCAT(LastName, \' \', FirstName)',
								),
							),
							array(
								'Name' => 'LegalContact',
								'Type' => 'component',
								'File' => 'components/linked.php',
								'Class' => 'LinkedViewComponent',
								'Config' => array(
									'Table' => 'crm_legal_contact',
									'FromField' => 'RecieverEntityID',
									'ToField' => 'ContactID',
									'ViewField' => 'Title',
									'ViewSQL' => 'CONCAT(LastName, \' \', FirstName)',
								),
							),
							array(
								'Name' => 'Parent',
								'Type' => 'component',
								'File' => 'components/linked.php',
								'Class' => 'LinkedViewComponent',
								'Config' => array(
									'Table' => 'crm_parent',
									'FromField' => 'RecieverEntityID',
									'ToField' => 'ParentID',
									'ViewField' => 'ChildID',
								),
							),
							array(
								'Name' => 'Contact',
								'Type' => 'component',
								'File' => 'components/linked.php',
								'Class' => 'LinkedViewComponent',
								'Config' => array(
									'Table' => 'crm_school_contact',
									'FromField' => 'RecieverEntityID',
									'ToField' => 'ContactID',
									'ViewField' => 'SchoolID',
								),
							),
							array(
								'Name' => 'Contact',
								'Type' => 'component',
								'File' => 'components/linked.php',
								'Class' => 'LinkedViewComponent',
								'Config' => array(
									'Table' => 'crm_legal_contact',
									'FromField' => 'RecieverEntityID',
									'ToField' => 'ContactID',
									'ViewField' => 'LegalID',
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
									'FromField' => 'MailingID',
									'ToField' => 'MailingID'
								)
							),
							array(
								'Name' => 'Sent',
								'File' => 'filters/predefined.php',
								'Class' => 'PredefinedFilter',
								'Config' => array(
									'Name' => 'Sent',
									'Field' => 'Sent',
									'Value' => 'N'
								)
							)
						)
					),
				),
			),
		),
	),
	'EditTemplate' => 'mailing_edit.html',
	'EditConfig' => array(
		'Fields' => array(
			
		),
	),
	'ActionConfig' => array(
		'SetEmail' => array(
			'File' => 'actions/mailing.php',
			'Class' => 'MailingAction',
			'Config' => array(
				'Name' => 'SetEmail'
			)
		),
		'Resend' => array(
			'File' => 'actions/mailing.php',
			'Class' => 'MailingAction',
			'Config' => array(
				'Name' => 'Resend'
			)
		),
		'Send' => array(
			'File' => 'actions/mailing.php',
			'Class' => 'MailingAction',
			'Config' => array(
				'EntityList' => array(
					'child' => array(
						'Table' => 'crm_child',
						'ID' => 'ChildID',
						'ItemsPerPage' => 0,
						'ItemsOrderBy' => 't.LastName,t.FirstName',
						'ListConfig' => array(
							'Fields' => array(
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
							),
							'Filters' => array(
								array(
									'Name' => 'ChildCategoryID',
									'Type' => 'component',
									'File' => 'filters/linked.php',
									'Class' => 'LinkedFilter',
									'Config' => array(
										'Name' => 'ChildCategoryID',
										'Table' => 'crm_category',
										'ArrayName' => 'ChildCategoryList',
										'FromField' => 'CategoryID',
										'ToField' => 'CategoryID',
										'ViewField' => 'Title',
									),
								),
								array(
									'Name' => 'ChildAge',
									'File' => 'filters/age.php',
									'Class' => 'AgeFilter',
									'Config' => array(
										'Name' => 'ChildAge',
										'DOBField' => 'DOB'
									),
								),
								array(
									'Name' => 'ChildClass',
									'Field' => 'Class'
								),
								array(
									'Name' => 'ChildStatusID',
									'Type' => 'component',
									'File' => 'filters/linked.php',
									'Class' => 'LinkedFilter',
									'Config' => array(
										'Name' => 'ChildStatusID',
										'ArrayName' => 'ChildStatusList',
										'Table' => 'crm_status',
										'FromField' => 'StatusID',
										'ToField' => 'StatusID',
										'ViewField' => 'Title',
									),
								),
								array(
									'Name' => 'ChildSeasonID',
									'File' => 'filters/linked.php',
									'Class' => 'LinkedMultipleSelectFilter',
									'Config' => array(
										'Name' => 'ChildSeasonID',
										'ArrayName' => 'ChildSeasonList',
										'Table' => 'crm_season',
										'LinkTable' => 'crm_child2season',
										'Field' => 'SeasonID',
										'FromField' => 'ChildID',
										'LinkFromField' => 'ChildID',
										'LinkToField' => 'SeasonID',
										'ToField' => 'SeasonID',
										'ViewField' => 'TypeID, Title',
									)
								),	
								array(
									'Name' => 'Mailing',
									'Type' => 'component',
									'File' => 'filters/predefined.php',
									'Class' => 'PredefinedFilterSending',
									'Config' => array(
										'Name' => 'Mailing',
										'Field' => 'onSending',
										'Value' => 'Y',
										'MailingTable' => 'crm_child2mailing',
										'Using' => 'ChildID',
									),
								),
							),
						),
					),
					'parent' => array(
						'Table' => 'crm_parent',
						'ID' => 'ParentID',
						'ItemsPerPage' => 0,
						'ItemsOrderBy' => 't.ParentID',	
						'ListConfig' => array(
							'Fields' => array(
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
									'Name' => 'ChildID',
									'Type' => 'field',
								),
							),	
							'Filters' => array(
								array(
									'Name' => 'ChildID',
									'File' => 'filters/linked.php',
									'Class' => 'LinkedFilter',
									'Config' => array(
										'Name' => 'ChildID',
										'FromField' => 'ChildID',
										'ToField' => 'ChildID'
									)
								)
							)
						),
					),
					'school' => array(
						'Table' => 'crm_school',
						'ID' => 'SchoolID',
						'ItemsPerPage' => 0,
						'ItemsOrderBy' => 't.Title',
						'ListConfig' => array(
							'Fields' => array(
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
							),
							'Filters' => array(
								array(
									'Name' => 'SchoolCategoryID',
									'Type' => 'component',
									'File' => 'filters/linked.php',
									'Class' => 'LinkedFilter',
									'Config' => array(
										'Name' => 'SchoolCategoryID',
										'Table' => 'crm_category',
										'ArrayName' => 'SchoolCategoryList',
										'FromField' => 'CategoryID',
										'ToField' => 'CategoryID',
										'ViewField' => 'Title',
									),
								),
								array(
									'Name' => 'Mailing',
									'Type' => 'component',
									'File' => 'filters/predefined.php',
									'Class' => 'PredefinedFilter',
									'Config' => array(
										'Name' => 'Mailing',
										'Field' => 'Mailing',
										'Value' => 'Y',
									),
								),
							),
						),
					),
					'legal' => array(
						'Table' => 'crm_legal',
						'ID' => 'LegalID',
						'ItemsPerPage' => 0,
						'ItemsOrderBy' => 't.Title',
						'ListConfig' => array(
							'Fields' => array(
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
							),
							'Filters' => array(
								array(
									'Name' => 'LegalCategoryID',
									'Type' => 'component',
									'File' => 'filters/linked.php',
									'Class' => 'LinkedFilter',
									'Config' => array(
										'Name' => 'LegalCategoryID',
										'Table' => 'crm_category',
										'ArrayName' => 'LegalCategoryList',
										'FromField' => 'CategoryID',
										'ToField' => 'CategoryID',
										'ViewField' => 'Title',
									),
								),
								array(
									'Name' => 'LegalGroupID',
									'Type' => 'component',
									'File' => 'filters/linked.php',
									'Class' => 'LinkedFilter',
									'Config' => array(
										'Name' => 'LegalGroupID',
										'ArrayName' => 'LegalGroupList',
										'Table' => 'crm_legalgroup',
										'FromField' => 'GroupID',
										'ToField' => 'GroupID',
										'ViewField' => 'Title',
// 						'IncludeKeys' => array(1, 2, 3)
									),
								),
								array(
									'Name' => 'Mailing',
									'Type' => 'component',
									'File' => 'filters/predefined.php',
									'Class' => 'PredefinedFilter',
									'Config' => array(
										'Name' => 'Mailing',
										'Field' => 'Mailing',
										'Value' => 'Y',
									),
								),
							),
						),
					),
					'staff' => array(
						'Table' => 'crm_staff',
						'ID' => 'StaffID',
						'ItemsPerPage' => 0,
						'ItemsOrderBy' => 't.LastName, t.FirstName',
						'ListConfig' => array(
							'Fields' => array(
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
							),
							'Filters' => array(
								array(
									'Name' => 'StaffCategoryID',
									'Type' => 'component',
									'File' => 'filters/linked.php',
									'Class' => 'LinkedFilter',
									'Config' => array(
										'Name' => 'StaffCategoryID',
										'Table' => 'crm_category',
										'ArrayName' => 'StaffCategoryList',
										'FromField' => 'CategoryID',
										'ToField' => 'CategoryID',
										'ViewField' => 'Title',
									),
								),
								array(
									'Name' => 'Mailing',
									'Type' => 'component',
									'File' => 'filters/predefined.php',
									'Class' => 'PredefinedFilter',
									'Config' => array(
										'Name' => 'Mailing',
										'Field' => 'Mailing',
										'Value' => 'Y',
									),
								),
							),
						),
					),
				),
			),
		),
	),
)

?>
