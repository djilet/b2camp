<?php

es_include("localobject.php");
es_include("filesys.php");
es_include("phpmailer/phpmailer.php");

class User extends LocalObject
{
	var $_acceptMimeTypes = array(
			'image/png',
			'image/x-png',
			'image/gif',
			'image/jpeg',
			'image/pjpeg'
	);
	var $params;

	function User($data = array())
	{
		parent::LocalObject($data);
		$this->params = LoadImageConfig('UserImage', "user", GetFromConfig("UserImage"));
	}
	
	function GetQueryPrefix()
	{
		$query = "SELECT UserID, Email, FirstName, MiddleName, LastName, CONCAT(LastName, ' ', FirstName) AS Name, UserImage, UserImageConfig, 
						DOB, Sex, Phone, Social, City, Street, House, Flat, VoxImplantLogin, VoxImplantPassword,
						Role, Created, LastLogin, LastIP, InManagerStat
					FROM `user`";

		return $query;
	}

	function LoadByID($id, $authRole = null)
	{
		if (is_null($authRole))
		{
			$query = $this->GetQueryPrefix()." WHERE UserID=".intval($id);
		}
		else
		{
			$roles = $this->GetAvailableRoles($authRole, false);
			if (is_array($roles) && count($roles) > 0)
			{
				// Do not load users with higher role or another website
				$query = $this->GetQueryPrefix()." WHERE UserID=".intval($id)."
					AND Role IN (".implode(",", Connection::GetSQLArray($roles)).")";
			}
			else
			{
				return false;
			}
		}
		$this->LoadFromSQL($query);

		if ($this->GetIntProperty("UserID"))
		{
			$this->PrepareBeforeShow();
			return true;
		}
		else
		{
			return false;
		}
	}

	function LoadBySession()
	{
		// Clear properties before load
		$this->LoadFromArray(array());

		$session =& GetSession();
		if (is_array($session->GetProperty("LoggedInUser")))
		{
			// Check that logged in user has access to current website
			$user = $session->GetProperty("LoggedInUser");
			
			$this->LoadFromArray($user);
			$session->UpdateExpireDate();
			return true;
		}
		return false;
	}

	function LoadByRequest($request)
	{
		$query = $this->GetQueryPrefix()." WHERE
			Email=".$request->GetPropertyForSQL("Login")." AND
			Passwd=MD5(".$request->GetPropertyForSQL("Password").")";
		$this->LoadFromSQL($query);

		if ($this->GetIntProperty("UserID"))
		{
			$this->PrepareBeforeShow();

			$stmt = GetStatement();
			$query = "UPDATE `user` SET LastLogin=NOW(),
				LastIP=".Connection::GetSQLString(getenv("REMOTE_ADDR"))."
				WHERE UserID=".$this->GetIntProperty("UserID");
			$stmt->Execute($query);

			$session =& GetSession();
			$session->SetProperty("LoggedInUser", $this->GetProperties());
			$session->SaveToDB($request->GetIntProperty("RememberMe"));
			return true;
		}
		else
		{
			$this->AddError("wrong-login-password");
			return false;
		}
	}

	function PrepareBeforeShow()
	{
		if ($this->GetIntProperty("UserID") > 0)
			$this->SetProperty("RoleTitle", GetTranslation("role-".$this->GetProperty("Role")));
		
		if ($this->GetProperty("UserImage"))
		{
			$imageConfig = LoadImageConfigValues("UserImage", $this->GetProperty("UserImageConfig"));
			$this->AppendFromArray($imageConfig);
			
			for ($i = 0; $i < count($this->params); $i++)
			{
				$v = $this->params[$i];

				if($v["Resize"] == 13)
					$this->SetProperty($v["Name"]."Path", InsertCropParams($v["Path"], 
																	$this->GetIntProperty($v["Name"]."X1"), 
																	$this->GetIntProperty($v["Name"]."Y1"), 
																	$this->GetIntProperty($v["Name"]."X2"), 
																	$this->GetIntProperty($v["Name"]."Y2")).$this->GetProperty("UserImage"));
				else
					$this->SetProperty($v["Name"]."Path", $v["Path"].$this->GetProperty("UserImage"));
		
				if ($v["Name"] != 'UserImage')
				{
					$this->SetProperty($v["Name"]."Width", $v["Width"]);
					$this->SetProperty($v["Name"]."Height", $v["Height"]);
				}
			}
		}
	}

	function GetImageParams()
	{
		$paramList = array();
		for ($i = 0; $i < count($this->params); $i++)
		{
			$paramList[] = array(
				"Name" => $this->params[$i]['Name'],
				"SourceName" => $this->params[$i]['SourceName'],
				"Width" => $this->params[$i]['Width'],
				"Height" => $this->params[$i]['Height'],
				"Resize" => $this->params[$i]['Resize'],
				"X1" => $this->GetIntProperty("UserImage".$this->params[$i]['SourceName']."X1"),
				"Y1" => $this->GetIntProperty("UserImage".$this->params[$i]['SourceName']."Y1"),
				"X2" => $this->GetIntProperty("UserImage".$this->params[$i]['SourceName']."X2"),
				"Y2" => $this->GetIntProperty("UserImage".$this->params[$i]['SourceName']."Y2")
			);
		}
		return $paramList;
	}

	function Validate($role = null)
	{
		if ($this->GetIntProperty("UserID"))
		{
			if (is_array($role))
			{
				if (in_array($this->GetProperty("Role"), $role))
				{
					return true;
				}
			}
			else if ($this->GetProperty("Role") == $role || is_null($role))
			{
				return true;
			}
		}
		$this->AddError('not-enough-rights');
		return false;
	}

	function ValidateAccess($role = null)
	{
		if ($this->LoadBySession())
		{
			if (is_array($role))
			{
				if (in_array($this->GetProperty("Role"), $role))
				{
					return true;
				}
			}
			else if ($this->GetProperty("Role") == $role || is_null($role))
			{
				return true;
			}

			if (defined('IS_ADMIN'))
				Send403();
			else
				return false;
		}
		else
		{
			// Not logged in users redirect to home page
			if (defined('IS_ADMIN'))
			{
				header("Location: ".ADMIN_PATH."index.php?ReturnPath=".urlencode($_SERVER['REQUEST_URI']));
				exit();
			}
			else
			{
				return false;
			}
		}
	}

	function Logout()
	{
		// Clear properties before logout
		$this->LoadFromArray(array());

		$session =& GetSession();
		$session->RemoveProperty("LoggedInUser");
		$session->SaveToDB();

		$this->AddMessage("logged-out");
	}

	function GetAvailableRoles($authRole, $forTemplate = true)
	{
		$roles = array();
		switch($authRole)
		{
			case INTEGRATOR:
				if ($forTemplate)
				{
					$roles[] = array("Value" => INTEGRATOR, "Title" => GetTranslation("role-".INTEGRATOR));
					$roles[] = array("Value" => ADMINISTRATOR, "Title" => GetTranslation("role-".ADMINISTRATOR));
					$roles[] = array("Value" => MANAGER, "Title" => GetTranslation("role-".MANAGER));
					$roles[] = array("Value" => GUIDE, "Title" => GetTranslation("role-".GUIDE));
				}
				else
				{
					$roles = array(INTEGRATOR, ADMINISTRATOR, MANAGER, GUIDE);
				}
				break;
			case ADMINISTRATOR:
				if ($forTemplate)
				{
					$roles[] = array("Value" => ADMINISTRATOR, "Title" => GetTranslation("role-".ADMINISTRATOR));
					$roles[] = array("Value" => MANAGER, "Title" => GetTranslation("role-".MANAGER));
					$roles[] = array("Value" => GUIDE, "Title" => GetTranslation("role-".GUIDE));
				}
				else
				{
					$roles = array(ADMINISTRATOR, MANAGER, GUIDE);
				}
				break;
		}

		return $roles;
	}

	function Save($authRole, $authUserID)
	{
		$stmt = GetStatement();

		$roles = $this->GetAvailableRoles($authRole, false);

		if ($this->GetIntProperty("UserID") > 0)
		{
			$query = "SELECT Role FROM `user` WHERE UserID=".$this->GetIntProperty("UserID");
			$currentRole = $stmt->FetchField($query);
			if (!(is_array($roles) && count($roles) > 0 && in_array($currentRole, $roles)))
			{
				// Do not allow to edit user with higher role
				$this->RemoveProperty("UserID");
			}
		}

		if (!$this->ValidateEmail("Email"))
			$this->AddError("incorrect-email-format");

		if (!$this->ValidateNotEmpty("LastName"))
			$this->AddError("lastname-required");
		if (!$this->ValidateNotEmpty("FirstName"))
			$this->AddError("firstname-required");
			
		if (!in_array($this->GetProperty("Sex"), array('M', 'F')))
			$this->AddError("sex-required");
			
		if(!$this->ValidateDate("DOB", "yyyy-mm-dd"))
			$this->AddError("dob-required");
			
		if(!$this->ValidatePhone("Phone"))
			$this->AddError("phone-incorrect");
			
		if ($authUserID == $this->GetProperty("UserID"))
		{
			$this->SetProperty("Role", $authRole);
			
			if ($this->GetProperty("Password1"))
			{
				$query = "SELECT COUNT(UserID) FROM `user` WHERE
					UserID=".$this->GetIntProperty("UserID")." AND
					Passwd=MD5(".$this->GetPropertyForSQL("OldPassword").")";
				if (!$stmt->FetchField($query))
				{
					$this->AddError("wrong-old-password");
				}
			}
		}
		else
		{
			if (!(count($roles) > 0 && in_array($this->GetProperty("Role"), $roles)))
			{
				$this->AddError("role-undefined");
			}
		}
		
		if ($this->GetIntProperty("UserID") == 0 && !$this->GetProperty("Password1"))
			$this->AddError("password-empty");

		if ($this->GetProperty("Password1") && $this->GetProperty("Password1") != $this->GetProperty("Password2"))
			$this->AddError("password-not-equal");

		$this->SaveUserImage($this->GetProperty("SavedUserImage"));
		
		if ($this->HasErrors())
		{
			return false;
		}
		else
		{
			$query = "SELECT COUNT(*) FROM `user` WHERE
				Email=".$this->GetPropertyForSQL("Email")."
				AND UserID<>".$this->GetIntProperty("UserID");
			
			if ($stmt->FetchField($query))
			{
				$this->AddError("email-is-not-unique");
				return false;
			}

			$inManagerStat = $this->GetProperty('InManagerStat');

			if ($inManagerStat == 'on') {
				$inManagerStat = 1;
			} else {
				$inManagerStat = 0;
			}

			if ($this->GetIntProperty("UserID") > 0)
			{
				$query = "UPDATE `user` SET
						Email=".$this->GetPropertyForSQL("Email").",
						".($this->GetProperty("Password1") ? "Passwd=MD5(".$this->GetPropertyForSQL("Password1").")," : "")."
						FirstName=".$this->GetPropertyForSQL("FirstName").",
						MiddleName=".$this->GetPropertyForSQL("MiddleName").",
						LastName=".$this->GetPropertyForSQL("LastName").",
						UserImage=".$this->GetPropertyForSQL("UserImage").",
						UserImageConfig=".Connection::GetSQLString(json_encode($this->GetProperty("UserImageConfig"))).",
						DOB=".$this->GetPropertyForSQL("DOB").",
						Sex=".$this->GetPropertyForSQL("Sex").",
						Phone=".$this->GetPropertyForSQL("Phone").",
						Social=".$this->GetPropertyForSQL("Social").",
						City=".$this->GetPropertyForSQL("City").",
						Street=".$this->GetPropertyForSQL("Street").",
						House=".$this->GetPropertyForSQL("House").",
						Flat=".$this->GetPropertyForSQL("Flat").",
						VoxImplantLogin=".$this->GetPropertyForSQL("VoxImplantLogin").",
						VoxImplantPassword=".$this->GetPropertyForSQL("VoxImplantPassword").",
						Role=".$this->GetPropertyForSQL("Role").",
						InManagerStat=".$inManagerStat."
					WHERE UserID=".$this->GetIntProperty("UserID");
			}
			else
			{
				$query = "INSERT INTO `user` (Email, Passwd, FirstName, MiddleName, LastName, UserImage, UserImageConfig, 
					DOB, Sex, Phone, Social, City, Street, House, Flat, VoxImplantLogin, VoxImplantPassword, Role, InManagerStat, Created) VALUES (
						".$this->GetPropertyForSQL("Email").",
						MD5(".$this->GetPropertyForSQL("Password1")."),
						".$this->GetPropertyForSQL("FirstName").",
						".$this->GetPropertyForSQL("MiddleName").",
						".$this->GetPropertyForSQL("LastName").",
						".$this->GetPropertyForSQL("UserImage").",
						".Connection::GetSQLString(json_encode($this->GetProperty("UserImageConfig"))).",
						".$this->GetPropertyForSQL("DOB").",
						".$this->GetPropertyForSQL("Sex").",
						".$this->GetPropertyForSQL("Phone").",
						".$this->GetPropertyForSQL("Social").",
						".$this->GetPropertyForSQL("City").",
						".$this->GetPropertyForSQL("Street").",
						".$this->GetPropertyForSQL("House").",
						".$this->GetPropertyForSQL("Flat").",
						".$this->GetPropertyForSQL("VoxImplantLogin").",
						".$this->GetPropertyForSQL("VoxImplantPassword").",
						".$this->GetPropertyForSQL("Role").",
						".$inManagerStat.",
						NOW())";
			}

			if ($stmt->Execute($query))
			{
				if ($this->GetIntProperty("UserID") == 0)
				{
					$this->SetProperty("UserID", $stmt->GetLastInsertID());
				}

				// Update current user info in session
				if ($authUserID == $this->GetProperty("UserID"))
				{
					// We have to reload data by UserID to save actual info
					$this->LoadByID($this->GetProperty("UserID"));
					$session =& GetSession();
					$session->SetProperty("LoggedInUser", $this->GetProperties());
					$session->SaveToDB();
				}

				$this->AddMessage("user-is-updated");
				return true;
			}
			else
			{
				$this->AddError("sql-error");
				return false;
			}
		}
	}

	function UpdateRegistrationData($authUserID)
	{
		if ($authUserID != $this->GetProperty("UserID"))
		{
			$this->AddError("user-edit-access-denied");
			return false;
		}

		$stmt = GetStatement();

		$session =& GetSession();

		if (!$authUserID && strtoupper($this->GetProperty('CaptchaCode')) != $session->GetProperty('CaptchaCode'))
			$this->AddError('incorrect-captcha');

		if (!$this->ValidateEmail("Email"))
			$this->AddError("incorrect-email-format");

		if (!$this->ValidateNotEmpty("FirstName"))
			$this->AddError("firstname-required");
			
		if (!$this->ValidateNotEmpty("LastName"))
			$this->AddError("lastname-required");

		if ($this->GetIntProperty("UserID") == 0 && !$this->GetProperty("Password1"))
			$this->AddError("password-empty");

		if ($this->GetProperty("Password1") && $this->GetProperty("Password1") != $this->GetProperty("Password2"))
			$this->AddError("password-not-equal");

		if ($this->HasErrors())
		{
			return false;
		}
		else
		{
			$query = "SELECT COUNT(*) FROM `user` WHERE Email=".$this->GetPropertyForSQL("Email");

			if ($stmt->FetchField($query))
			{
				$this->AddError("email-is-not-unique");
				return false;
			}

			if ($this->GetIntProperty("UserID") > 0)
			{
				$query = "UPDATE `user` SET
						Email=".$this->GetPropertyForSQL("Email").",
						".($this->GetProperty("Password1") ? "Passwd=MD5(".$this->GetPropertyForSQL("Password1").")," : "")."
						Name=".$this->GetPropertyForSQL("Name").",
						Phone=".$this->GetPropertyForSQL("Phone")."
					WHERE UserID=".$this->GetIntProperty("UserID");
			}
			else
			{
				$query = "INSERT INTO `user` (Email, Passwd, Name,
					Phone, Role, Created) 
					VALUES (
						".$this->GetPropertyForSQL("Email").",
						MD5(".$this->GetPropertyForSQL("Password1")."),
						".$this->GetPropertyForSQL("Name").",
						".$this->GetPropertyForSQL("Phone").",
						'user',
						".intval(WEBSITE_ID).",
						NOW())";
			}

			if ($stmt->Execute($query))
			{
				if ($this->GetIntProperty("UserID") > 0)
				{
					$this->AddMessage("public-user-is-updated");
				}
				else
				{
					$this->AddMessage("public-user-is-registered");
					$this->SetProperty("UserID", $stmt->GetLastInsertID());
				}

				// Update current user info in session
				// We have to reload data by UserID to save actual info
				$this->LoadByID($this->GetProperty("UserID"));
				$session->SetProperty("LoggedInUser", $this->GetProperties());
				$session->SaveToDB();

				return true;
			}
			else
			{
				$this->AddError("sql-error");
				return false;
			}
		}
	}
	
	function SaveUserImage($savedImage = "")
	{
		$fileSys = new FileSys();
	
		$newUserImage = $fileSys->Upload("UserImage", USER_IMAGE_DIR, false, $this->_acceptMimeTypes);
		if ($newUserImage)
		{
			$this->SetProperty("UserImage", $newUserImage["FileName"]);
	
			// Remove old image if it has different name
			if ($savedImage && $savedImage != $newUserImage["FileName"])
				@unlink(USER_IMAGE_DIR.$savedImage);
		}
		else
		{
			if ($savedImage)
				$this->SetProperty("UserImage", $savedImage);
			else
				$this->SetProperty("UserImage", null);
		}

		$this->SetProperty("UserImageConfig", array());
		
		if ($this->GetProperty('UserImage'))
		{
			if ($info = @getimagesize(USER_IMAGE_DIR.$this->GetProperty('UserImage')))
			{
				$this->_properties["UserImageConfig"]["Width"] = $info[0];
				$this->_properties["UserImageConfig"]["Height"] = $info[1];
			}
		}
		
		$this->AppendErrorsFromObject($fileSys);
	
		return !$fileSys->HasErrors();
	}
	
	function RemoveUserImage($userID, $savedImage)
	{
		if ($savedImage)
		{
			@unlink(USER_IMAGE_DIR.$savedImage);
		}
	
		$userID = intval($userID);
		if ($userID > 0)
		{
			$stmt = GetStatement();
			$imageFile = $stmt->FetchField("SELECT UserImage FROM `user` WHERE UserID=".$userID);
	
			if ($imageFile)
				@unlink(USER_IMAGE_DIR.$imageFile);
	
			$stmt->Execute("UPDATE `user` SET UserImage=NULL WHERE UserID=".$userID);
		}
	}

	function SendPasswordToEmail()
	{
		if ($this->ValidateEmail('Email'))
		{
			$stmt = GetStatement();
			$password = $this->GeneratePassword();
			$stmt->Execute("UPDATE `user` SET
				Passwd=md5(".Connection::GetSQLString($password).")
				WHERE Email=".$this->GetPropertyForSQL('Email'));
			if ($stmt->GetAffectedRows())
			{
				$emailTemplate = new PopupPage();
				$tmpl = $emailTemplate->Load("forgot_password_email.html");
				$tmpl->SetVar("Password", $password);
				SendMailFromAdmin($this->GetProperty('Email'), GetTranslation("new-password"), $emailTemplate->Grab($tmpl));

				$this->AddMessage("password-is-changed-and-sent");
				return true;
			}
			else
			{
				$this->AddError("incorrect-email-address");
			}
		}
		else
		{
			$this->AddError("incorrect-email-format");
		}
		return false;
	}

	function GeneratePassword()
	{
		$arr = array('a','b','c','d','e','f',
			'g','h','j','k',
			'm','n','p','r','s',
			't','u','v','x','y','z',
			'A','B','C','D','E','F',
			'G','H','J','K',
			'M','N','P','R','S',
			'T','U','V','X','Y','Z',
			'2','3','4','5','6',
			'7','8','9');

		$number = mt_rand(6, 10);

		$pass = "";

		for ($i = 0; $i < $number; $i++)
		{
			$index = mt_rand(0, count($arr) - 1);
			$pass .= $arr[$index];
		}

		return $pass;
	}
}

?>