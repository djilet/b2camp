<?php 
$readComponent = Utilities::GetComponent(
	"UnreadTaskCount", 
	"components/read.php", 
	"ReadViewComponent", 
	array(
		"EntityIDField" => "TaskID", 
		"Table" => "crm_task", 
		"UserField" => "ExecutorManagerID", 
		"ReadField" => "Read",
		"LinkTable" => "crm_task2user",
		"LinkUserField" => "UserID", 
		"LinkReadField" => "Read"
	)
);
$unreadTaskCount = $readComponent ? $readComponent->GetUnreadEntityCount() : "";

$GLOBALS['entityConfig']['task'] = array(
	'Access' => array(INTEGRATOR, ADMINISTRATOR, MANAGER, GUIDE),
	'RemoveAccess' => array(INTEGRATOR, ADMINISTRATOR),
	'AdminMenuIcon' => 'fa fa-tasks',
	'AdminMenuInfo' => $unreadTaskCount,
	'AdminMenuInfoClass' => "unread-task-count",
	'AdminSubmenu' => array(
		array("Title" => GetTranslation("admin-menu-crm-task-out"),
			"Link" => "module.php?load=crm&entity=task&FilterShow=out&FilterStatus=opened",
			"Selected" => isset($_REQUEST["FilterShow"]) && $_REQUEST["FilterShow"] == "out" && isset($_REQUEST["FilterStatus"]) && $_REQUEST["FilterStatus"] == "opened"),
		array("Title" => GetTranslation("admin-menu-crm-task-in"),
			"Link" => "module.php?load=crm&entity=task&FilterShow=in&FilterStatus=opened",
			"Info" => $unreadTaskCount,
			"InfoClass" => "unread-task-count",
			"Selected" => isset($_REQUEST["FilterStatus"]) && $_REQUEST["FilterStatus"] == "opened" && (!isset($_REQUEST["FilterShow"]) || (isset($_REQUEST["FilterShow"]) && $_REQUEST["FilterShow"] == "in"))),
		array("Title" => GetTranslation("admin-menu-crm-task-closed"),
			"Link" => "module.php?load=crm&entity=task&FilterStatus=closed",
			"Selected" => !isset($_REQUEST["FilterShow"]) && isset($_REQUEST["FilterStatus"]) && $_REQUEST["FilterStatus"] == "closed"),
	),
	'ShowExportButton' => true,
	'Table' => 'crm_task',
	'ID' => 'TaskID',
	'ItemsPerPage' => 20,
	'ItemsOrderBy' => 't.Created DESC',
	'ListTemplate' => 'task_list.html',
	'ListConfig' => array(
		'Fields' => array(
			array(
				'Name' => 'Title',
				'Type' => 'field'
			),
			array(
				'Name' => 'Status',
				'Type' => 'field'
			),
			array(
				'Name' => 'Priority',
				'Type' => 'field'
			),
			array(
				'Name' => 'Created',
				'Type' => 'field'
			),
			array(
				'Name' => 'ExecutionDateFrom',
				'Type' => 'field'
			),
			array(
				'Name' => 'ExecutionDateTo',
				'Type' => 'field'
			),
			array(
				'Name' => 'CreatedManager',
				'Type' => 'component',
				'File' => 'components/linked.php',
				'Class' => 'LinkedViewComponent',
				'Config' => array(
					'Table' => 'user',
					'FromField' => 'CreatedManagerID',
					'ToField' => 'UserID',
					'ViewField' => 'Title',
					'ViewSQL' => 'CONCAT(LastName, \' \', FirstName) AS Name',
				),
			),
			array(
				'Name' => 'ExecutorManager',
				'Type' => 'component',
				'File' => 'components/linked.php',
				'Class' => 'LinkedViewComponent',
				'Config' => array(
					'Table' => 'user',
					'FromField' => 'ExecutorManagerID',
					'ToField' => 'UserID',
					'ViewField' => 'Title',
					'ViewSQL' => 'CONCAT(LastName, \' \', FirstName) AS Name',
				),
			),
			array(
				'Name' => 'Read',
				'Type' => 'field'
			),
			array(
				'Name' => 'Link',
				'Type' => 'component',
				'File' => 'components/read.php',
				'Class' => 'ReadListViewComponent',
				'Config' => array(
					'Table' => 'crm_task2user',
					'FromField' => 'TaskID',
					'ToField' => 'TaskID',
					'UserField' => 'UserID',
					'ViewField' => 'Read'
				),
			),
			array(
				'Name' => 'Executor',
				'Type' => 'component',
				'File' => 'components/linked.php',
				'Class' => 'UserLinkedViewComponent',
				'Config' => array(
					'Field' => array('ExecutorManagerID')
				),
			),
		),
		'Filters' => array(
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
					'Fields' => array('Created'),
					'Operation' => '>='
				),
			),
			array(
				'Name' => 'FilterDateTo',
				'File' => 'filters/date.php',
				'Class' => 'DateFilter',
				'Config' => array(
					'Name' => 'FilterDateTo',	
					'Fields' => array('Created'),
					'Operation' => '<='
				),
			),
			array(
				'Name' => 'FilterStatus',
				'Field' => 'Status'
			),
			array(
				'Name' => 'FilterCreatedManagerID',
				'File' => 'filters/linked.php',
				'Class' => 'LinkedFilter',
				'Config' => array(
					'Name' => 'FilterCreatedManagerID',
					'ArrayName' => 'FilterCreatedManagerList',
					'Table' => 'user',
					'FromField' => 'CreatedManagerID',
					'ToField' => 'UserID',
					'ViewField' => 'FirstName, LastName',
				)
			),
			array(
				'Name' => 'FilterExecutorManagerID',
				'File' => 'filters/linked.php',
				'Class' => 'LinkedFilter',
				'Config' => array(
					'Name' => 'FilterExecutorManagerID',
					'ArrayName' => 'FilterExecutorManagerList',
					'Table' => 'user',
					'FromField' => 'ExecutorManagerID',
					'ToField' => 'UserID',
					'ViewField' => 'FirstName, LastName',
				)
			),
			array(
				'Name' => 'FilterPriority',
				'Field' => 'Priority'
			),
			array(
				'Name' => 'FilterShow',
				'File' => 'filters/subscribe.php',
				'Class' => 'SubscribeFilter',
				'Config' => array(
					'Name' => 'FilterShow',
					'OwnerField' => 'CreatedManagerID',
					'MainSubField' => 'ExecutorManagerID',
					'LinkTable' => 'crm_task2user',
					'FromField' => 'TaskID',
					'ToField' => 'TaskID',
					'LinkSubField' => 'UserID'
				)
			),
            /*array(
                'Name' => 'FilterShow',
                'Type' => 'component',
                'File' => 'components/linked.php',
                'Class' => 'LinkedMultipleSelectEditComponent',
                'Config' => array(
                    'Table' => 'user',
                    'KeyField' => 'UserID',
                    'LinkTable' => 'crm_task2user',
                    'LinkFromField' => 'TaskID',
                    'LinkToField' => 'UserID',
                    'ViewSQL' => 'CONCAT(t.LastName," ",t.FirstName) AS Title',
                ),
            ),*/
		),
	),
	'ViewTemplate' => 'task_view.html',
	'ViewConfig' => array(
		'Fields' => array(
			array(
				'Name' => 'Title',
				'Type' => 'field',
			),
			array(
				'Name' => 'Created',
				'Type' => 'field',
			),
			array(
				'Name' => 'ExecutionDateFrom',
				'Type' => 'field'
			),
			array(
				'Name' => 'ExecutionDateTo',
				'Type' => 'field'
			),
			array(
				'Name' => 'CreatedManager',
				'Type' => 'component',
				'File' => 'components/linked.php',
				'Class' => 'LinkedViewComponent',
				'Config' => array(
					'Table' => 'user',
					'FromField' => 'CreatedManagerID',
					'ToField' => 'UserID',
					'ViewField' => 'Title',
					'ViewSQL' => 'CONCAT(LastName, \' \', FirstName) AS Name',
				),
			),
			array(
				'Name' => 'ExecutorManager',
				'Type' => 'component',
				'File' => 'components/linked.php',
				'Class' => 'LinkedViewComponent',
				'Config' => array(
					'Table' => 'user',
					'FromField' => 'ExecutorManagerID',
					'ToField' => 'UserID',
					'ViewField' => 'Title',
					'ViewSQL' => 'CONCAT(LastName, \' \', FirstName) AS Name',
				),
			),
			array(
				'Name' => 'Priority',
				'Type' => 'field',
			),
			array(
				'Name' => 'Status',
				'Type' => 'field',
			),
			array(
				'Name' => 'Description',
				'Type' => 'field',
			),
			array(
				'Name' => 'Comment',
				'Type' => 'component',
				'File' => 'components/comment.php',
				'Class' => 'CommentViewComponent',
				'Config' => array(
					'Table' => 'crm_task_comment',
					'KeyField' => 'TaskID',
					'Image' => '50x50|8|Small',
					'FileComponent' => array(
						'Name' => 'File',
						'File' => 'components/file.php',
						'Class' => 'FileViewComponent',
						'Config' => array(
							'EntityType' => 'task_comment',
							'Path' => 'storage',
						),
					)
				),
			),
			array(
				'Name' => 'SubscriberID',
				'Type' => 'component',
				'File' => 'components/linked.php',
				'Class' => 'LinkedMultipleSelectEditComponent',
				'Config' => array(
					'Table' => 'user',
					'KeyField' => 'UserID',
					'LinkTable' => 'crm_task2user',
					'LinkFromField' => 'TaskID',
					'LinkToField' => 'UserID',
					'ViewSQL' => 'CONCAT(t.LastName," ",t.FirstName) AS Title',
                    'WhereSql'=> "InManagerStat=1",
					'Concatenation' => "true",
					'NotSeason' => true,
				),
			),
			array(
				'Name' => 'StatusAccess',
				'Type' => 'component',
				'File' => 'components/linked.php',
				'Class' => 'UserLinkedViewComponent',
				'Config' => array(
					'Field' => array('CreatedManagerID','ExecutorManagerID')
				),
			),
			array(
				'Name' => '',
				'Type' => 'component',
				'File' => 'components/read.php',
				'Class' => 'ReadViewComponent',
				'Config' => array(
					'Table' => 'crm_task',
					'EntityField' => 'TaskID',
					'MainUserField' => 'ExecutorManagerID',
					'MainReadField' => 'Read',
					'LinkTable' => 'crm_task2user',
					'LinkUserField' => 'UserID',
					'LinkReadField' => 'Read'
				),
			),
			array(
				'Name' => 'File',
				'Type' => 'component',
				'File' => 'components/file.php',
				'Class' => 'FileViewComponent',
				'Config' => array(
					'EntityType' => 'task',
					'Path' => 'storage',
				),
			),
		),
	),
	'EditTemplate' => 'task_edit.html',
	'EditConfig' => array(
		'EditAccess' => array(""),
		'Fields' => array(
			array(
				'Name' => 'Title',
				'Type' => 'field',
				'Required' => true,
				'Validate' => 'empty',
			),
			array(
				'Name' => 'Priority',
				'Type' => 'field',
				'Validate' => 'option',
				'Options' => array('low', 'normal', 'high')
			),
			array(
				'Name' => 'Created',
				'Type' => 'generated',
				'Value' => 'current_datetime'
			),
			array(
				'Name' => 'ExecutionDateFrom',
				'Type' => 'date',
				'Validate' => 'date',
			),
			array(
				'Name' => 'ExecutionDateTo',
				'Type' => 'date',
				'Validate' => 'date',
			),
			array(
				'Name' => 'CreatedManagerID',
				'Type' => 'field',
				'Required' => true,
				'Validate' => 'empty',
			),
			array(
				'Name' => 'ExecutorManagerID',
				'Type' => 'component',
				'File' => 'components/linked.php',
				'Class' => 'LinkedEditComponent',
				'Config' => array(
					'Table' => 'user',
					'FromField' => 'ExecutorManagerID',
					'ToField' => 'UserID',
					'ViewField' => 'CONCAT(LastName, \' \', FirstName)',
					'Required' => true,
				),
			),
			array(
				'Name' => 'File',
				'Type' => 'component',
				'File' => 'components/file.php',
				'Class' => 'FileEditComponent',
				'Config' => array(
					'EntityType' => 'task',
					'Path' => 'storage',
				),
			),
			array(
				'Name' => 'Description',
				'Type' => 'field',
			),
		),
	),
	'ActionConfig' => array(
		'AddComment' => array(
			'File' => 'actions/comment.php',
			'Class' => 'CommentAction',
			'Config' => array(
				'Table' => 'crm_task_comment',
				'KeyField' => 'TaskID',
				'FileComponent' => array(
					'Name' => 'File',
					'File' => 'components/file.php',
					'Class' => 'FileEditComponent',
					'Config' => array(
						'EntityType' => 'task_comment',
						'Path' => 'storage'
					)
				)
			),
		),
		'SaveSubscribers' => array(
			'File' => 'actions/subscribe.php',
			'Class' => 'SubscribeAction',
			'Config' => array(
				'LinkTable' => 'crm_task2user',
				'SubID' => 'UserID',
			),
		),
		'SaveProperty' => array(
			'File' => 'actions/property.php',
			'Class' => 'PropertyAction',
			'Config' => array(
				'Name' => 'Status',
				'Field' => 'Status'
			),
		),
		'Export' => array(
			'Access' => array(INTEGRATOR, ADMINISTRATOR),	
			'File' => 'actions/export.php',
			'Class' => 'ExportAction',
			'Config' => array(
				'Template' => 'task_export.html',
				'Entity' => 'task',
				'Table' => 'crm_task',
				'ID' => 'TaskID',
				'ItemsPerPage' => 0, 
				'ItemsOrderBy' => 't.Created DESC',
				'ListConfig' => array(
					'Fields' => array(
						array(
							'Name' => 'Title',
							'Type' => 'field'
						),
						array(
							'Name' => 'Description',
							'Type' => 'field'
						),
						array(
							'Name' => 'Status',
							'Type' => 'field'
						),
						array(
							'Name' => 'Priority',
							'Type' => 'field'
						),
						array(
							'Name' => 'Created',
							'Type' => 'field'
						),
						array(
							'Name' => 'ExecutionDateFrom',
							'Type' => 'field'
						),
						array(
							'Name' => 'ExecutionDateTo',
							'Type' => 'field'
						),
						array(
							'Name' => 'CreatedManager',
							'Type' => 'component',
							'File' => 'components/linked.php',
							'Class' => 'LinkedViewComponent',
							'Config' => array(
								'Table' => 'user',
								'FromField' => 'CreatedManagerID',
								'ToField' => 'UserID',
								'ViewField' => 'Title',
								'ViewSQL' => 'CONCAT(LastName, \' \', FirstName) AS Name',
							),
						),
						array(
							'Name' => 'ExecutorManager',
							'Type' => 'component',
							'File' => 'components/linked.php',
							'Class' => 'LinkedViewComponent',
							'Config' => array(
								'Table' => 'user',
								'FromField' => 'ExecutorManagerID',
								'ToField' => 'UserID',
								'ViewField' => 'Title',
								'ViewSQL' => 'CONCAT(LastName, \' \', FirstName) AS Name',
							),
						),
						array(
							'Name' => 'Read',
							'Type' => 'field'
						),
						array(
							'Name' => 'Link',
							'Type' => 'component',
							'File' => 'components/read.php',
							'Class' => 'ReadListViewComponent',
							'Config' => array(
								'Table' => 'crm_task2user',
								'FromField' => 'TaskID',
								'ToField' => 'TaskID',
								'UserField' => 'UserID',
								'ViewField' => 'Read'
							),
						),
						array(
							'Name' => 'Executor',
							'Type' => 'component',
							'File' => 'components/linked.php',
							'Class' => 'UserLinkedViewComponent',
							'Config' => array(
								'Field' => array('ExecutorManagerID')
							),
						),
					),
				),
			), 
		)
	),
)

?>
