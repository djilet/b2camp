<?php

es_include("template.php");
es_include("voximplant.php");

class LocalPage
{
	var $includePaths;
	var $module;
	var $headerTmpl;
	var $footerTmpl;
	var $isAdmin;

	function LocalPage($module)
	{
		$this->includePaths = array();
		$this->module = $module;
	}

	function Load($file, $header = array(), $pageID = null)
	{
		$this->_InitHeader($header, $pageID);
		$this->_InitFooter($header, $pageID);
		return $this->_CreateTemplate($file, $header, $pageID);
	}

	function Output($contentTmpl)
	{
		$this->headerTmpl->pparse();
		$contentTmpl->pparse();
		$this->footerTmpl->pparse();
	}

	function Grab($contentTmpl)
	{
		$header = $this->headerTmpl->Grab();
		$content = $contentTmpl->Grab();
		$footer = $this->footerTmpl->Grab();
		return $header.$content.$footer;
	}

	function _InitHeader($header, $pageID)
	{
		if (isset($header['HeaderTemplate']))
			$this->headerTmpl = $this->_CreateTemplate($header['HeaderTemplate'], $header, $pageID);
		else
			$this->headerTmpl = $this->_CreateTemplate("_header.html", $header, $pageID);
		$this->headerTmpl->LoadFromArray($header);
		$this->headerTmpl->SetVar("PageID", $pageID);
	}

	function _InitFooter($header, $pageID)
	{
		if (isset($header['FooterTemplate']))
			$this->footerTmpl = $this->_CreateTemplate($header['FooterTemplate'], $header, $pageID);
		else
			$this->footerTmpl = $this->_CreateTemplate("_footer.html", $header, $pageID);
		$this->footerTmpl->LoadFromArray($header);
		$this->footerTmpl->SetVar("PageID", $pageID);
	}

	function _CreateTemplate($file, $header, $pageID)
	{
		$tmpl = new Template($file, array("INCLUDE_PATHS" => $this->includePaths));

		if ($this->isAdmin)
		{
			$tmpl->SetVar("PATH2MAIN", ADMIN_PATH."template/");
			if (!is_null($this->module))
			{
				$tmpl->SetVar("MODULE_NAME", $this->module);
				$tmpl->SetVar("MODULE_URL", GetCurrentProtocol().$_SERVER["HTTP_HOST"].ADMIN_PATH."module.php?load=".$this->module);
				$tmpl->SetVar("MODULE_PATH", PROJECT_PATH.'module/'.$this->module.'/');
				$tmpl->SetVar("PATH2MOD", PROJECT_PATH."module/".$this->module."/template/");
			}
			$tmpl->SetVar("CMS_BRAND", GetFromConfig("Brand"));
		}
		else
		{
			$tmpl->SetVar("PATH2MAIN", PROJECT_PATH."template/");
		}

		/*@var language Language */
		$language =& GetLanguage();
		$translation = $language->LoadForTempate($file, $this->module, $this->isAdmin);
		foreach ($translation as $key => $value)
		{
			if(isset($value["Value"]))
				$tmpl->SetVar("LNG_".$key, $value["Value"]);
		}
		return $tmpl;
	}
}

class AdminPage extends LocalPage
{
	function AdminPage($module = null)
	{
		parent::LocalPage($module);

		$this->isAdmin = true;

		if (!is_null($this->module))
		{
			array_push($this->includePaths, PROJECT_DIR."module/".$this->module."/template/");
		}
		array_push($this->includePaths, PROJECT_DIR."template/");
	}

	function _InitHeader($header, $pageID)
	{
		parent::_InitHeader($header, $pageID);

		$adminMenu = array();
		$user = new User();
		$user->LoadBySession();
		
		require_once(PROJECT_DIR."module/crm/include/utilities.php");
		
		$adminMenu = array();
		
		if($user->GetProperty("Role") != GUIDE)
		{
			$adminMenu[] = array(
				"Title" => GetTranslation("admin-menu-dashboard"),
				"Link" => "module.php?load=crm&entity=dashboard&EntityViewID=1",
				"AdminMenuIcon" => "fa fa-dashboard",
				"Selected" => isset($_REQUEST["entity"]) && $_REQUEST["entity"] == "dashboard"
			);
		}
		
		$adminMenu = array_merge($adminMenu, Utilities::GetAdminMenu());
		
		if(in_array($user->GetProperty('Role'), array(INTEGRATOR, ADMINISTRATOR)))
		{
			$adminMenu[] = array(
				"Title" => GetTranslation("admin-menu-user-list"),
				"Link" => "user.php",
				"AdminMenuIcon" => "fa fa-users"
			);
		}
		if(in_array($user->GetProperty('Role'), array(INTEGRATOR)))
		{
			$adminMenu[] = array(
				"Title" => GetTranslation("admin-menu-user-action-list"),
				"Link" => "user_action.php",
				"AdminMenuIcon" => "fa fa-eye"
			);
		}
		
		for ($i = 0; $i < count($adminMenu); $i++)
		{
			if ($header['Navigation'][0]['Link'] == $adminMenu[$i]["Link"])
				$adminMenu[$i]["Selected"] = true;
		}
		$this->headerTmpl->SetLoop("AdminMenu", $adminMenu);
	}
	
	function _InitFooter($header, $pageID)
	{
		parent::_InitFooter($header, $pageID);
		
		$voximplant = new VoxImplant();
		$this->footerTmpl->SetLoop("VoxImplantRedirectUserList", $voximplant->GetVoximplantRedirectUserList());
	}
}

class PopupPage extends LocalPage
{
	var $fMenu;
	var $sMenu;
	var $cMenu;

	function PopupPage($module = null, $isAdmin = true)
	{
		parent::LocalPage($module);

		$this->isAdmin = $isAdmin;

		if ($isAdmin)
		{
			if (!is_null($this->module))
			{
				array_push($this->includePaths, PROJECT_DIR."module/".$this->module."/template/");
			}
			array_push($this->includePaths, PROJECT_DIR."template/");
		}
		else
		{
			array_push($this->includePaths, PROJECT_DIR."template/");
		}
	}

	function Load($file, $header = array(), $pageID = null)
	{
		$tmpl = $this->_CreateTemplate($file, $header, $pageID);
		$tmpl->LoadFromArray($header);
		$tmpl->SetVar("PageID", $pageID);
		return $tmpl;
	}

	function _CreateTemplate($file, $header, $pageID)

	{
		$tmpl = parent::_CreateTemplate($file, $header, $pageID);

		if (!$this->isAdmin)
		{
			$defineCurrent = true;
			if (isset($header["InsideModule"]))
				$defineCurrent = false;

			if (!$this->fMenu)
			{
				$pageList = new PageList();
				$result = $pageList->GetMenuList($pageID, $defineCurrent);
				$this->fMenu = $result["full"];
				$this->sMenu = $result["menu_successor"];
				$this->cMenu = $result["menu_current"];
			}

			if (is_array($this->fMenu) && count($this->fMenu))
			{
				foreach ($this->fMenu as $menu)
				{
					if (isset($menu["Children0"]))
						$tmpl->SetLoop("MENU_".$menu["StaticPath"], $menu["Children0"]);
				}
			}

			if (is_array($this->sMenu) && count($this->sMenu))
			{
				$tmpl->SetLoop("MENU_successor", $this->sMenu);
			}

			if (is_array($this->cMenu) && count($this->cMenu))
			{
				$tmpl->SetLoop("MENU_current", $this->cMenu);
			}
		}

		return $tmpl;
	}

	function Output($contentTmpl)
	{
		$contentTmpl->pparse();
	}

	function Grab($contentTmpl)
	{
		return $contentTmpl->Grab();
	}
}

?>