<?php 

$submenus = array();
if(isset($user))
{
	$submenus = array(
		array("Title" => GetTranslation("admin-menu-crm-season-active"),
			"Link" => "module.php?load=crm&entity=season&FilterArchive=N",
			"Selected" => !isset($_REQUEST["FilterArchive"]) || (isset($_REQUEST["FilterArchive"]) && $_REQUEST["FilterArchive"] == "N")),		
		array("Title" => GetTranslation("admin-menu-crm-season-archive"),
			"Link" => "module.php?load=crm&entity=season&FilterArchive=Y",
			"Selected" => isset($_REQUEST["FilterArchive"]) && $_REQUEST["FilterArchive"] == "Y"),
	);
}

$GLOBALS['entityConfig']['season'] = array(
	'Access' => array(INTEGRATOR, ADMINISTRATOR, MANAGER),
	'AdminMenuIcon' => 'fa fa-calendar',
	'AdminSubmenu' => $submenus,
	'Table' => 'crm_season',
	'ID' => 'SeasonID',
	'ItemsPerPage' => 20,
	'ItemsOrderBy' => 't.DateFrom DESC',
	'ShowSendToArchiveButton' => true,
	'ListDuplicateTemplate' => 'season_list_duplicate.html',
	'ListTemplate' => 'season_list.html',
	'ListConfig' => array(
		'Fields' => array(
			array(
				'Name' => 'Image',
				'Type' => 'component',
				'File' => 'components/image.php',
				'Class' => 'ImageViewComponent',
				'Config' => array(
					'Path' => 'season',
					'Image' => '50x50|8|Small',
				),
			),
			array(
				'Name' => 'Title',
				'Type' => 'field',
			),
			array(
				'Name' => 'DateFrom',
				'Type' => 'field',
			),
			array(
				'Name' => 'DateTo',
				'Type' => 'field',
			),
			array(
				'Name' => 'ChildCount',
				'Type' => 'component',
				'File' => 'components/linked.php',
				'Class' => 'CountViewComponent',
				'Config' => array(
					'Table' => 'crm_child2season',
					'KeyField' => 'SeasonID',
					'TargetField' => 'ChildID',
				),
			),
			array(
				'Name' => 'Type',
				'Type' => 'component',
				'File' => 'components/linked.php',
				'Class' => 'LinkedViewComponent',
				'Config' => array(
					'Table' => 'crm_season_type',
					'FromField' => 'TypeID',
					'ToField' => 'TypeID',
					'ViewField' => 'Title',
				),
			),
		),
		'Filters' => array(
			array(
				'Name' => 'FilterArchive',
				'Field' => 'Archive'
			),
			array(
				'Name' => 'FilterTitle',
				'Field' => 'Title'
			),
			array(
				'Name' => 'FilterDateFrom',
				'File' => 'filters/date.php',
				'Class' => 'DateFilter',
				'Config' => array(
					'Name' => 'FilterDateFrom',	
					'Fields' => array(
						'DateFrom',
						'DateTo'
					),
					'Operation' => '>='
				),
			),
			array(
				'Name' => 'FilterDateTo',
				'File' => 'filters/date.php',
				'Class' => 'DateFilter',
				'Config' => array(
					'Name' => 'FilterDateTo',
					'Fields' => array(
						'DateFrom',
						'DateTo'
					),
					'Operation' => '<='
				),
			),
			array(
				'Name' => 'FilterChildrenCount',
				'File' => 'filters/linked.php',
				'Class' => 'CountFilter',
				'Config' => array(
					'Name' => 'FilterChildrenCount',
					'LinkTable' => 'crm_child2season',
					'FromField' => 'SeasonID', 
					'ToField' => 'SeasonID',
					'Operation' => '<='
				),
			),
			array(
				'Name' => 'FilterTypeID',
				'File' => 'filters/linked.php',
				'Class' => 'LinkedFilter',
				'Config' => array(
					'Name' => 'FilterTypeID',
					'ArrayName' => 'FilterTypeList',
					'Table' => 'crm_season_type',
					'FromField' => 'TypeID',
					'ToField' => 'TypeID',
					'ViewField' => 'Title',
				)
			),
		)
	),
	'ViewTemplate' => 'season_view.html',
	'ViewConfig' => array(
		'Fields' => array(
			array(
				'Name' => 'Image',
				'Type' => 'component',
				'File' => 'components/image.php',
				'Class' => 'ImageViewComponent',
				'Config' => array(
					'Path' => 'season',
					'Image' => '300x300|8|Small',
				),
			),
			array(
				'Name' => 'Title',
				'Type' => 'field',
			),
			array(
				'Name' => 'DateFrom',
				'Type' => 'field',
			),
			array(
				'Name' => 'DateTo',
				'Type' => 'field',
			),
			array(
				'Name' => 'PlaceCount',
				'Type' => 'field',
			),
			array(
				'Name' => 'Social',
				'Type' => 'field',
			),
			array(
				'Name' => 'Comment',
				'Type' => 'field',
			),
			array(
				'Name' => 'Staff',
				'Type' => 'component',
				'File' => 'components/itemlist.php',
				'Class' => 'ItemListViewComponent',
				'Config' => array(
					'Table' => 'crm_staff',
                    'Join' => 'user USING(UserID)',
					'ID' => 'StaffID',
					'ItemsOrderBy' => 'user.LastName,user.FirstName',
					'KeyField' => 'SeasonID',
					'LinkTable' => 'crm_staff2season',
					'LinkField' => 'StaffID',
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
								'Name' => 'University',
								'Type' => 'field',
							),
							array(
								'Name' => 'Course',
								'Type' => 'field',
							),
							array(
								'Name' => 'NickName',
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
						),
						'Filters' => array(
							array(
								'Name' => 'EntityID',
								'File' => 'filters/linked.php',
								'Class' => 'LinkedMultipleSelectFilter',
								'Config' => array(
									'Name' => 'EntityID',
									'Table' => 'crm_staff',
									'LinkTable' => 'crm_staff2season',
									'FromField' => 'StaffID',
									'LinkFromField' => 'StaffID',
									'LinkToField' => 'SeasonID',
									'ToField' => 'SeasonID',
								)
							),
						)
					),
				),
			),
			array(
				'Name' => 'Child',
				'Type' => 'component',
				'File' => 'components/itemlist.php',
				'Class' => 'ItemListViewComponent',
				'Config' => array(
					'Table' => 'crm_child',
					'ID' => 'ChildID',
					'ItemsOrderBy' => 't.LastName,t.FirstName',
					'KeyField' => 'SeasonID',
					'LinkTable' => 'crm_child2season',
					'LinkField' => 'ChildID',
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
						),
						'Filters' => array(
							array(
								'Name' => 'EntityID',
								'File' => 'filters/linked.php',
								'Class' => 'LinkedMultipleSelectFilter',
								'Config' => array(
									'Name' => 'EntityID',
									'Table' => 'crm_child',
									'LinkTable' => 'crm_child2season',
									'FromField' => 'ChildID',
									'LinkFromField' => 'ChildID',
									'LinkToField' => 'SeasonID',
									'ToField' => 'SeasonID',
								)
							),
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
					'Table' => 'crm_season_comment',
					'KeyField' => 'SeasonID',
					'Image' => '50x50|8|Small',
					'FileComponent' => array(
						'Name' => 'File',
						'File' => 'components/file.php',
						'Class' => 'FileViewComponent',
						'Config' => array(
							'EntityType' => 'season_comment',
							'Path' => 'storage',
						),
					)
				),
			),
			array(
				'Name' => 'File',
				'Type' => 'component',
				'File' => 'components/file.php',
				'Class' => 'FileViewComponent',
				'Config' => array(
					'EntityType' => 'season',
					'Path' => 'storage',
				),
			),
		),
	),
	'EditTemplate' => 'season_edit.html',
	'EditConfig' => array(
		'Fields' => array(
			array(
				'Name' => 'Image',
				'Type' => 'component',
				'File' => 'components/image.php',
				'Class' => 'ImageEditComponent',
				'Config' => array(
					'Path' => 'season',
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
				'Name' => 'Type',
				'Type' => 'component',
				'File' => 'components/linked.php',
				'Class' => 'LinkedEditComponent',
				'Config' => array(
					'Table' => 'crm_season_type',
					'FromField' => 'TypeID',
					'ToField' => 'TypeID',
					'ViewField' => 'Title',
// 					'Required' => true,
// 					'IncludeKeys' => array(1,2,3)
				),
			),
// 			array(
// 				'Name' => 'Type',
// 				'Type' => 'field',
// 			),
			array(
				'Name' => 'DateFrom',
				'Type' => 'date',
				'Required' => true,
				'Validate' => 'date',
			),
			array(
				'Name' => 'DateTo',
				'Type' => 'date',
				'Required' => true,
				'Validate' => 'date',
			),
			array(
				'Name' => 'PlaceCount',
				'Type' => 'field',
				'Required' => true,
				'Validate' => 'int'
			),
			array(
				'Name' => 'Social',
				'Type' => 'field',
			),
			array(
				'Name' => 'File',
				'Type' => 'component',
				'File' => 'components/file.php',
				'Class' => 'FileEditComponent',
				'Config' => array(
					'EntityType' => 'season',
					'Path' => 'storage',
				),
			),
			array(
				'Name' => 'Staff',
				'Type' => 'component',
				'File' => 'components/linked.php',
				'Class' => 'LinkedMultipleSelectEditComponent',
				'Config' => array(
					'Table' => 'crm_staff',
					'Join' => "user USING (UserID)",
					'KeyField' => 'StaffID',
					'LinkTable' => 'crm_staff2season',
					'LinkFromField' => 'SeasonID',
					'LinkToField' => 'StaffID',
					'ViewSQL' => 'CONCAT(user.LastName," ",user.FirstName) AS Title',
				),
			),
            array(
                'Name' => "TransferThereConditions",
                'Type' => "field"
            ),
            array(
                'Name' => "TransferBackConditions",
                'Type' => "field"
            ),
			array(
				'Name' => 'Comment',
				'Type' => 'field',
			),
			array(
				'Name' => 'Duplicate',
				'Type' => 'component',
				'File' => 'components/duplicate.php',
				'Class' => 'DuplicateEditComponent',
				'Config' => array(
					'Entity' => 'season',
					'DuplicateParams' => array(
						array('Field' => 'Title', 'Filter' => 'FilterTitle')
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
				'Table' => 'crm_season',
				'KeyField' => 'SeasonID',
				'ArchiveField' => 'Archive',
			),
		),
		'AddComment' => array(
			'File' => 'actions/comment.php',
			'Class' => 'CommentAction',
			'Config' => array(
				'Table' => 'crm_season_comment',
				'KeyField' => 'SeasonID',
				'FileComponent' => array(
					'Name' => 'File',
					'File' => 'components/file.php',
					'Class' => 'FileEditComponent',
					'Config' => array(
						'EntityType' => 'season_comment',
						'Path' => 'storage'
					)
				)
			),
		),
        'PrintChildCards' => array(
            'File' => "actions/print.php",
            'Class' => "MassPrintAction",
            'Config' => array(
                'Entity' => "child",
                'EntityName' => "Child",
                'Table' => "crm_child",
                'Template' => "child_mass_print.html",
                'ItemsOrderBy' => 't.LastName,t.FirstName',
                'ID' => 'ChildID',
                'KeyField' => "EntityViewID",
                'Key' => "EntityID",
                'ItemsPerPage' => 0,
                'ListConfig' => array(
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
                                                'KeyField' => 'ParentID'
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
                    'Filters' => array(
                        array(
                            'Name' => 'EntityID',
                            'File' => 'filters/linked.php',
                            'Class' => 'LinkedMultipleSelectFilter',
                            'Config' => array(
                                'Name' => 'EntityID',
                                'Table' => 'crm_child',
                                'LinkTable' => 'crm_child2season',
                                'FromField' => 'ChildID',
                                'LinkFromField' => 'ChildID',
                                'LinkToField' => 'SeasonID',
                                'ToField' => 'SeasonID',
                            )
                        ),
                    )
                )
            )
        )
	),
)

?>
