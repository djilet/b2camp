<?php 

$submenus = array(
	array("Title" => GetTranslation("admin-menu-crm-directory-billings"),
		"Link" => "module.php?load=crm&entity=directory&FilterType=1",
		"Selected" => !isset($_REQUEST["FilterType"]) || (isset($_REQUEST["FilterType"]) && $_REQUEST["FilterType"] == "1")),
	array("Title" => GetTranslation("admin-menu-crm-directory-article-arrival"),
		"Link" => "module.php?load=crm&entity=directory&FilterType=2",
		"Selected" => isset($_REQUEST["FilterType"]) && $_REQUEST["FilterType"] == "2"),
	array("Title" => GetTranslation("admin-menu-crm-directory-article-consumption"),
		"Link" => "module.php?load=crm&entity=directory&FilterType=3",
		"Selected" => isset($_REQUEST["FilterType"]) && $_REQUEST["FilterType"] == "3"),
    array(
        "Title" => GetTranslation("admin-menu-crm-directory-sources-of-child"),
        "Link" => "module.php?load=crm&entity=directory&FilterType=6",
        "Selected" => isset($_REQUEST["FilterType"]) && $_REQUEST["FilterType"] == "6"
    ),
);
	
$GLOBALS['entityConfig']['directory'] = array(
	'Access' => array(INTEGRATOR, ADMINISTRATOR),
	'AdminMenuIcon' => 'fa fa-book',
	'AdminSubmenu' => $submenus,	
	'Table' => 'crm_directory',
	'ID' => 'DirectoryID',
	'ItemsPerPage' => 20,
	'ItemsOrderBy' => 't.DirectoryID',
	'ListTemplate' => 'directory_list.php',
	'ListConfig' => array(
		'Fields' => array(),
		'Filters' => array(),
	),
);

?>
