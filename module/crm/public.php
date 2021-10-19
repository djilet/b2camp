<?php
require_once(dirname(__FILE__)."/../../include/init.php");
require_once(dirname(__FILE__)."/init.php");

$module = "crm";
$request = new LocalObject(array_merge($_GET, $_POST));
$result = "";

switch($request->GetProperty("Action"))
{
	case "Unsubscribe":
		if($request->GetIntProperty("EntityID") > 0 
			&& in_array($request->GetProperty("Entity"), array("child", "school", "legal", "staff")) 
			&& $request->GetProperty("Sign") == md5(CRM_UNSUBSCRIBE_SALT.$request->GetProperty("Entity").$request->GetProperty("EntityID")))
		{
		    $stmt = GetStatement();
		    if($config = getMailingConfig($request))
		    {
		        $query = "UPDATE ".$config["Table"]."
							SET onSending='N'
						WHERE ".ucfirst($request->GetProperty("Entity"))."ID=".$request->GetPropertyForSQL("EntityID");
		    }
		    else
		    {
    			$query = "UPDATE ".$module."_".$request->GetProperty("Entity")." 
    							SET Mailing='N' 
    						WHERE ".ucfirst($request->GetProperty("Entity"))."ID=".$request->GetPropertyForSQL("EntityID");
		    }
			if($stmt->Execute($query))
			{
				$result = GetTranslation("public-unsubscribe-message", $module); 
			}
			else
			{
				$result = GetTranslation("public-error", $module);
			}
		}
		else
		{
			$result = GetTranslation("public-error", $module);
		}
		break;
	default:
		$result = GetTranslation("public-error", $module);
		break;
}

function getMailingConfig($request)
{
    $configList = $GLOBALS['entityConfig'][$request->GetProperty("Entity")]["EditConfig"]["Fields"];
    foreach($configList as $config)
    {
        if(($config["Name"] == "Mailing") && isset($config["Config"]["Table"]))
            return $config["Config"];
    }
    
    return false;
}

header('Content-Type: text/html; charset=utf-8');
echo "<html>
		<head>
			<title>ДОЛ \"Синяя Птица\"</title>
		</head>
		<body>
			<h3 style=\"text-align:center;margin-top:200px;\">".$result."</h3>
		</body>
	</html>";

?>