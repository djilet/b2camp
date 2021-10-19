<?php

es_include("mysqli/connection5.php");
es_include("object/session.php");
es_include("localobject.php");
es_include("localobjectlist.php");
es_include("language.php");

function &GetConnection()
{
	static $instance;
	if (is_null($instance))
	{
		$language =& GetLanguage();
		$instance = new Connection(GetFromConfig("Host", "mysql"), GetFromConfig("Database", "mysql"), GetFromConfig("User", "mysql"), GetFromConfig("Password", "mysql"), $language->GetMySQLEncoding());
	}
	return $instance;
}

function GetStatement()
{
	$instance = GetConnection();
	return $instance->CreateStatement(MYSQLI_ASSOC, E_USER_WARNING);
}

function &GetLanguage()
{
	static $language;
	if (is_null($language))
	{
		$language = new Language();
	}
	return $language;
}

function &GetURLParser()
{
	static $parser;
	if (is_null($parser))
	{
		$parser = new URLParser();
	}
	return $parser;
}

function GetTranslation($key, $module = null, $replacements = array())
{
	$language =& GetLanguage();

	if (is_array($module))
	{
		$replacements = $module;
		$module = null;
	}

	return $language->GetTranslation($key, $module, $replacements);
}

function &GetSession()
{
	static $session;
	if (is_null($session))
	{
		$session = new Session("sm");
	}
	return $session;
}

function GetFromConfig($param, $section = "common")
{
	static $websiteConfig;

	if (is_null($websiteConfig))
	{
		$configFile = dirname(__FILE__)."/../configure.ini";
		if (is_file($configFile))
			$websiteConfig = parse_ini_file($configFile, true);
	}

	if (isset($websiteConfig[$section][$param]))
		return $websiteConfig[$section][$param];
	else
		return null;
}

function LocalDate($format, $timeStamp = null)
{
	$text = array('F', 'M', 'l', 'D');
	$found = array();

	// Find text representations of week & month in date format
	for ($i = 0; $i < count($text); $i++)
	{
		$pos = strpos($format, $text[$i]);
		if ($pos !== false && substr($format, $pos - 1, 1) != "\\")
		{
			$format = str_replace($text[$i], "__\\".$text[$i]."__", $format);
			$found[] = $text[$i];
		}
	}

	if (is_null($timeStamp))
		$result = date($format);
	else
		$result = date($format, $timeStamp);

	// For found text representations replace it by correct language
	for ($i = 0; $i < count($found); $i++)
	{
		if (is_null($timeStamp))
			$textInLang = GetTranslation("date-".date($found[$i]));
		else
			$textInLang = GetTranslation("date-".date($found[$i], $timeStamp));
		$result = str_replace("__".$found[$i]."__", $textInLang, $result);
	}

	return $result;
}

function SmallString($str, $size)
{
	if (mb_strlen($str, "UTF-8") <= $size) return $str;
	return mb_substr($str, 0, $size-3, "UTF-8")."...";
}

function SendMailFromAdmin($to, $subject, $text, $attachments = array(), $fromEmail = null, $fromName = null, $unsubscribeURL = false)
{
	es_include("phpmailer/phpmailer.php");

	$language =& GetLanguage();

	$phpmailer = new PHPMailer();

	$mailer = GetFromConfig("Mailer", "phpmailer");
	switch ($mailer)
	{
		case 'smtp':
			$phpmailer->IsSMTP();
			if (GetFromConfig("SMTP_Debug", "phpmailer"))
			{
				$phpmailer->SMTPDebug = true;
			}
			else
			{
				$phpmailer->SMTPDebug = false;
			}
			break;
		case 'mail':
			$phpmailer->IsMail();
			break;
		case 'sendmail':
			$phpmailer->IsSendmail();
			break;
	}

	$login = GetFromConfig("SMTP_Login", "phpmailer");
	$password = GetFromConfig("SMTP_Password", "phpmailer");
	$phpmailer->Host = GetFromConfig("SMTP_Host", "phpmailer");
	$phpmailer->Port = GetFromConfig("SMTP_Port", "phpmailer");

	if ($login && $password)
	{
		$phpmailer->SMTPAuth = true;
		$phpmailer->Username = $login;
		$phpmailer->Password = $password;
	}
	else
	{
		$phpmailer->SMTPAuth = false;
	}

	$phpmailer->ContentType = "text/html";
	$phpmailer->CharSet = $language->GetHTMLCharset();

	if(is_null($fromEmail))
		$fromEmail = GetFromConfig("FromEmail");
	if(is_null($fromName))
		$fromName = GetFromConfig("FromName");

	if($unsubscribeURL)
	{
	    $phpmailer->addCustomHeader("Precedence", "bulk");
	    $phpmailer->addCustomHeader("List-Unsubscribe", "<".$unsubscribeURL.">");
	}	
	$phpmailer->From = $fromEmail;
	$phpmailer->FromName = $fromName;
	$phpmailer->AddReplyTo($phpmailer->From, $phpmailer->FromName);
	$phpmailer->Subject = $subject;
	$phpmailer->Body = $text;
	$phpmailer->AddAddress($to);
	
	$phpmailer->DKIM_domain = "2bcampcrm.ru";
	$phpmailer->DKIM_selector = "1538640796.2bcampcrm";
	$phpmailer->DKIM_private = PROJECT_DIR.'include/phpmailer/dkim_private.key';
	$phpmailer->DKIM_passphrase = '';
	$phpmailer->DKIM_identity = $phpmailer->From;
	
	if (is_array($attachments) && count($attachments) > 0)
	{
		foreach ($attachments as $v)
		{
			$phpmailer->AddAttachment($v);
		}
	}

	$result = true;

	if (!$phpmailer->Send())
	{
		$result = $phpmailer->ErrorInfo;
	}
	$phpmailer->ClearAllRecipients();

	// Log message
	$fp = fopen(PROJECT_DIR."var/mail/".date("Y-m-d-H-i-s").".txt", "a");
	$logMessage = "Time: ".date("d.m.Y H:i:s")."\n";
	$logMessage .= "Status: ".($result === true ? "success" : "failed")."\n";
	$logMessage .= "Browser: ".$_SERVER['HTTP_USER_AGENT']."\n";
	$logMessage .= "From: ".$fromEmail."\n";
	$logMessage .= "From Name: ".$fromName."\n";
	$logMessage .= "To: ".$to."\n";
	$logMessage .= "Subject: ".$subject."\n";
	$logMessage .= "Body: ".$text."\n\n";
	fwrite($fp, $logMessage);	
	fclose($fp);

	return $result;
}

function GetDirPrefix()
{
	return PROJECT_PATH;
}

function GetUrlPrefix()
{
	return GetCurrentProtocol().$_SERVER["HTTP_HOST"].PROJECT_PATH;
}

function GetCurrentProtocol()
{
	if(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443)
		return "https://";
	else 
		return "http://";
}

function Send301($newURL)
{
	$language =& GetLanguage();
	header("Content-Type: text/html; charset=".$language->GetHTMLCharset());
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: ".$newURL);
	echo "<!DOCTYPE HTML PUBLIC \"-//IETF//DTD HTML 2.0//EN\">
<html><head>
<title>301 Moved Permanently</title>
</head><body>
<h1>Moved Permanently</h1>
<p>The document has moved <a href=\"".$newURL."\">here</a>.</p>
<hr>
".$_SERVER['SERVER_SIGNATURE']."</body></html>";
	exit();
}

function Send403()
{
	$language =& GetLanguage();
	header("Content-Type: text/html; charset=".$language->GetHTMLCharset());
	header("HTTP/1.1 403 Forbidden");

	$customFile = GetFromConfig("Error403Document");
	if (strlen($customFile) > 0 && is_file(PROJECT_DIR.$customFile))
	{
		$handle = fopen(PROJECT_DIR.$customFile, "rb");
		$contents = fread($handle, filesize(PROJECT_DIR.$customFile));
		fclose($handle);
		$contents = str_replace("%REQUEST_URI%", htmlspecialchars($_SERVER['REQUEST_URI']), $contents);
		$contents = str_replace("%SERVER_SIGNATURE%", htmlspecialchars($_SERVER['SERVER_SIGNATURE']), $contents);
		echo $contents;
	}
	else
	{
		echo "<!DOCTYPE HTML PUBLIC \"-//IETF//DTD HTML 2.0//EN\">
<html><head>
<title>403 Forbidden</title>
</head><body>
<h1>Forbidden</h1>
<p>You don't have permission to access ".htmlspecialchars($_SERVER['REQUEST_URI'])." on this server.</p>
<hr>
".$_SERVER['SERVER_SIGNATURE']."</body></html>";
	}
	exit();
}

function MultiSort($array)
{
	for ($i = 1; $i < func_num_args(); $i += 3)
	{
		$key = func_get_arg($i);
  		if (is_string($key)) $key = '"'.$key.'"';

		$order = true;
		if ($i + 1 < func_num_args())
			 $order = func_get_arg($i + 1);

		$type = 0;
		if ($i + 2 < func_num_args())
			 $type = func_get_arg($i + 2);
		switch($type)
		{
			 case 1: // Case insensitive natural.
				  $t = 'strcasecmp($a[' . $key . '], $b[' . $key . '])';
				  break;
			 case 2: // Numeric.
				  $t = '($a[' . $key . '] == $b[' . $key . ']) ? 0:(($a[' . $key . '] < $b[' . $key . ']) ? -1 : 1)';
				  break;
			 case 3: // Case sensitive string.
				  $t = 'strcmp($a[' . $key . '], $b[' . $key . '])';
				  break;
			 case 4: // Case insensitive string.
				  $t = 'strcasecmp($a[' . $key . '], $b[' . $key . '])';
				  break;
			 default: // Case sensitive natural.
				  $t = 'strnatcmp($a[' . $key . '], $b[' . $key . '])';
				  break;
		}
		usort($array, create_function('$a, $b', '; return ' . ($order ? '' : '-') . '(' . $t . ');'));
	}
	return $array;
}

function GetImageFields($prefix = '', $num)
{
	$result = array();
	for ($i = 1; $i < $num + 1; $i++)
	{
		$result[] = $prefix.$i;
		$result[] = $prefix.$i."Config";
	}
	if (count($result) > 0)
		return implode(", ", $result).", ";
	else
		return "";
}

function PrepareContentBeforeSave($content)
{
	// Replace PROJECT_PATH by <P_T_R> (no need to update content when you move site from one folder to another)
	if (strlen($content) > 0)
	{
		$content = str_replace("href=\"".PROJECT_PATH, "href=\"<P_T_R>", $content);
		$content = str_replace("href='".PROJECT_PATH, "href='<P_T_R>", $content);
		$content = str_replace("href=".PROJECT_PATH, "href=<P_T_R>", $content);

		$content = str_replace("src=\"".PROJECT_PATH, "src=\"<P_T_R>", $content);
		$content = str_replace("src='".PROJECT_PATH, "src='<P_T_R>", $content);
		$content = str_replace("src=".PROJECT_PATH, "src=<P_T_R>", $content);

		$content = str_replace("background=\"".PROJECT_PATH, "background=\"<P_T_R>", $content);
		$content = str_replace("background='".PROJECT_PATH, "background='<P_T_R>", $content);
		$content = str_replace("background=".PROJECT_PATH, "background=<P_T_R>", $content);
	}
	return $content;
}

function PrepareContentBeforeShow($content)
{
	// Replace <P_T_R> by PROJECT_PATH
	if (strlen($content) > 0)
	{
		$content = str_replace("<P_T_R>", PROJECT_PATH, $content);
	}
	return $content;
}

function PrepareContentBeforeSend($content)
{
	// Replace PROJECT_PATH by full url prefix
	if (strlen($content) > 0)
	{
		$content = str_replace("href=\"".PROJECT_PATH, "href=\"".GetUrlPrefix(), $content);
		$content = str_replace("href='".PROJECT_PATH, "href='".GetUrlPrefix(), $content);
		$content = str_replace("href=".PROJECT_PATH, "href=".GetUrlPrefix(), $content);

		$content = str_replace("src=\"".PROJECT_PATH, "src=\"".GetUrlPrefix(), $content);
		$content = str_replace("src='".PROJECT_PATH, "src='".GetUrlPrefix(), $content);
		$content = str_replace("src=".PROJECT_PATH, "src=".GetUrlPrefix(), $content);

		$content = str_replace("background=\"".PROJECT_PATH, "background=\"".GetUrlPrefix(), $content);
		$content = str_replace("background='".PROJECT_PATH, "background='".GetUrlPrefix(), $content);
		$content = str_replace("background=".PROJECT_PATH, "background=".GetUrlPrefix(), $content);
	}
	return $content;
}

function PrepareFilePathBeforeSave($path)
{
	return str_replace(PROJECT_DIR, "<P_D>", $path);
}

function PrepareFilePathBeforeShow($path)
{
	return str_replace("<P_D>", PROJECT_DIR, $path);
}

function ProjectDirToURLPrefix($path)
{
	return str_replace("<P_D>", GetUrlPrefix(), $path);
}

function LoadImageConfig($name, $folder, $configString)
{
	$params = null;
	$imageConfig = explode(',', $configString);
	if (is_array($imageConfig) && count($imageConfig) > 0)
	{
		for ($i = 0; $i < count($imageConfig); $i++)
		{
			$data = explode('|', $imageConfig[$i]);
			if (is_array($data) && count($data) > 0)
			{
				if (isset($data[2]) && strlen($data[2]) > 0)
				{
					$params[$i] = array('Width' => 0, 'Height' => 0,
						'Resize' => 8, 'Name' => $name.$data[2], 'SourceName' => $data[2], 'Path' => '');

					$s = explode("x", $data[0]);
					if (count($s) == 2)
					{
						$params[$i]['Width'] = abs(intval($s[0]));
						$params[$i]['Height'] = abs(intval($s[1]));
					}

					// Resize way
					$params[$i]['Resize'] = abs(intval($data[1]));

					if($params[$i]['Resize'] == 13)
						$cropPart = "_#X1#_#Y1#_#X2#_#Y2#";
					else 
						$cropPart = "";
						
					$params[$i]['Path'] = PROJECT_PATH."images/".$folder."-".$params[$i]['Width']."x".$params[$i]['Height'].$cropPart."_".$params[$i]['Resize']."/";
				}
			}
		}
	}
	return $params;
}

function InsertCropParams($path, $x1, $y1, $x2, $y2)
{
	$path = str_replace("#X1#", $x1, $path);
	$path = str_replace("#Y1#", $y1, $path);
	$path = str_replace("#X2#", $x2, $path);
	$path = str_replace("#Y2#", $y2, $path);
	return $path;
}

function LoadImageConfigValues($imageName, $value)
{
	$result = array();
	if(strlen($value) > 0)
	{
		$value = json_decode($value, true);
		if(!is_null($value) && is_array($value))
		{
			foreach ($value as $k => $v)
			{
				if(is_array($v))
				{
					foreach ($v as $k2 => $v2)
					{
						$result[$imageName.$k.$k2] = $v2;
					}
				}
				else
				{
					$result[$imageName.$k] = $v;
				}
			}
		}
	}
	return $result;
}

function GetRealImageSize($resize, $origW, $origH, $dstW, $dstH)
{
	if (!($origW > 0 && $origH > 0 && $dstW > 0 && $dstH > 0))
		return array($dstW, $dstH);

	switch ($resize)
	{
		case RESIZE_PROPORTIONAL:
			if ($origW/$dstW > $origH/$dstH)
			{
				$k = $dstW/$origW;
				$dstH = round($origH*$k);
			}
			else
			{
				$k = $dstH/$origH;
				$dstW = round($origW*$k);
			}
			break;
		case RESIZE_PROPORTIONAL_FIXED_WIDTH:
			$k = $dstW/$origW;
			$dstH = round($origH*$k);
			break;
		case RESIZE_PROPORTIONAL_FIXED_HEIGHT:
			$k = $dstH/$origH;
			$dstW = round($origW*$k);
			break;
	}

	return array($dstW, $dstH);
}

function GetPageData($what)
{
	$default = array('ColorA' => '#000000', 'ColorI' => '#bcbcbc');

	$data = array(
		'page' => array('ColorA' => '#000000', 'ColorI' => '#bcbcbc'),
		'link' => array('ColorA' => '#0055ff', 'ColorI' => '#bcbcbc')
	);

	if (isset($data[$what])) return $data[$what];

	es_include('module.php');
	$module = new Module();
	$mList = $module->GetModuleList('', false, true);
	for ($i = 0; $i < count($mList); $i++)
	{
		if ($mList[$i]['Folder'] == $what)
			return $mList[$i];
	}

	return $default;
}

function GetPriority($level)
{
	switch($level)
	{
		case 1:
			$priority = 1;
			break;
		case 2:
			$priority = 0.8;
			break;
		case 3:
			$priority = 0.6;
			break;
		case 4:
			$priority = 0.4;
			break;
		default:
			$priority = 0.2;
			break;
	}
	return $priority;
}

function GetUploadMaxFileSize()
{
	$val = ini_get("upload_max_filesize");
	$val = strtolower(trim($val));
	$val = str_replace("m", " Mb", $val);
	$val = str_replace("g", " Gb", $val);
	$val = str_replace("k", " Kb", $val);

	return $val;
}


function ConvertURL2Value()
{
	$stmt = GetStatement();
	$page = new LocalObjectList();
	$page->LoadFromSQL("SELECT PageID, Config, Description FROM `page`");
	$pages = $page->GetItems();
	for ($i = 0; $i < count($pages); $i++)
	{
		$query = "UPDATE `page` SET Config=".Connection::GetSQLString(value_encode(urldecode($pages[$i]['Config'])))."
			,Description=".Connection::GetSQLString("Description=".value_encode(substr(urldecode($pages[$i]['Description']),12)))." 
			WHERE PageID=".$pages[$i]['PageID'];
		$stmt->Execute($query);		
	}
	
	$catalogItem = new LocalObjectList();
	$catalogItem->LoadFromSQL("SELECT ItemID, Description FROM `catalog_item`");
	$catalogItems = $catalogItem->GetItems();
	for ($i = 0; $i < count($catalogItems); $i++)
	{
	 	$query = "UPDATE `catalog_item` SET Description=".Connection::GetSQLString("Description=".value_encode(substr(urldecode($catalogItems[$i]['Description']),12)))." 
			WHERE ItemID=".$catalogItems[$i]['ItemID'];
		$stmt->Execute($query);		

	}

}

/**
* array_merge_recursive2()
*
* Similar to array_merge_recursive but keyed-valued are always overwritten.
* Priority goes to the 2nd array.
*
* @static yes
* @public yes
* @param $paArray1 array
* @param $paArray2 array
* @return array
*/
function array_merge_recursive2($paArray1, $paArray2)
{
   if (!is_array($paArray1) or !is_array($paArray2)) { return $paArray2; }
   foreach ($paArray2 AS $sKey2 => $sValue2)
   {
       $paArray1[$sKey2] = array_merge_recursive2(@$paArray1[$sKey2], $sValue2);
   }
   return $paArray1;
}

function value_encode($str)
{
	$str = str_replace("=", "%3D", $str);
	$str = str_replace("&", "%26", $str);
	return $str;
}

function value_decode($str)
{
	$str = str_replace("%3D", "=", $str);
	$str = str_replace("%26", "&", $str);
	return $str;
}

function GetValidStaticPath($staticPath, $table)
{
	$stmt = GetStatement();	
	$i = 1;
	$validStaticPath = $staticPath;
	$query = "SELECT COUNT(*) FROM `" . $table . "` WHERE StaticPath=".Connection::GetSQLString($staticPath);
	while(($result = $stmt->FetchField($query)) > 0)
	{
		if($result === false || $result === null)
			break;
		$i++;
		$validStaticPath = $staticPath . "-" . $i;
		$query = "SELECT COUNT(*) FROM `" . $table . "` WHERE StaticPath=".Connection::GetSQLString($validStaticPath);
	}
	return $validStaticPath;	
}

function ToSQLDate($date)
{
	return date("Y-m-d", strtotime($date));
}

function NumToStr($num, $firstCharToUpper = false) 
{
    $nul='ноль';
    $ten=array(
        array('','один','два','три','четыре','пять','шесть','семь', 'восемь','девять'),
        array('','одна','две','три','четыре','пять','шесть','семь', 'восемь','девять'),
    );
    $a20=array('десять','одиннадцать','двенадцать','тринадцать','четырнадцать' ,'пятнадцать','шестнадцать','семнадцать','восемнадцать','девятнадцать');
    $tens=array(2=>'двадцать','тридцать','сорок','пятьдесят','шестьдесят','семьдесят' ,'восемьдесят','девяносто');
    $hundred=array('','сто','двести','триста','четыреста','пятьсот','шестьсот', 'семьсот','восемьсот','девятьсот');
    $unit=array( // Units
        array('копейка' ,'копейки' ,'копеек',	 1),
        array('рубль'   ,'рубля'   ,'рублей'    ,0),
        array('тысяча'  ,'тысячи'  ,'тысяч'     ,1),
        array('миллион' ,'миллиона','миллионов' ,0),
        array('миллиард','милиарда','миллиардов',0),
    );
    //
    list($rub,$kop) = explode('.',sprintf("%015.2f", floatval($num)));
    $out = array();
    if (intval($rub)>0) {
        foreach(str_split($rub,3) as $uk=>$v) { // by 3 symbols
            if (!intval($v)) continue;
            $uk = sizeof($unit)-$uk-1; // unit key
            $gender = $unit[$uk][3];
            list($i1,$i2,$i3) = array_map('intval',str_split($v,1));
            // mega-logic
            $out[] = $hundred[$i1]; # 1xx-9xx
            if ($i2>1) $out[]= $tens[$i2].' '.$ten[$gender][$i3]; # 20-99
            else $out[]= $i2>0 ? $a20[$i3] : $ten[$gender][$i3]; # 10-19 | 1-9
            // units without rub & kop
            if ($uk>1) $out[]= Morph($v,$unit[$uk][0],$unit[$uk][1],$unit[$uk][2]);
        } //foreach
    }
    else $out[] = $nul;
    $out[] = Morph(intval($rub), $unit[1][0],$unit[1][1],$unit[1][2]); // rub
    $out[] = $kop.' '.Morph($kop,$unit[0][0],$unit[0][1],$unit[0][2]); // kop
    $out = trim(preg_replace('/ {2,}/', ' ', join(' ',$out)));
    if($firstCharToUpper == true){
    	$first = mb_substr($out, 0, 1, "utf-8");
    	$first = mb_convert_case($first, MB_CASE_UPPER, "utf-8");
    	$out = $first . mb_substr($out, 1, mb_strlen($out, "utf-8")-1, "utf-8");
    }
    return $out;
}

function Morph($n, $f1, $f2, $f5) 
{
    $n = abs(intval($n)) % 100;
    if ($n>10 && $n<20) return $f5;
    $n = $n % 10;
    if ($n>1 && $n<5) return $f2;
    if ($n==1) return $f1;
    return $f5;
}

function translit($input){
	$gost = array(
		"Є"=>"YE","І"=>"I","Ѓ"=>"G","і"=>"i","№"=>"-","є"=>"ye","ѓ"=>"g",
		"А"=>"A","Б"=>"B","В"=>"V","Г"=>"G","Д"=>"D",
		"Е"=>"E","Ё"=>"YO","Ж"=>"ZH",
		"З"=>"Z","И"=>"I","Й"=>"J","К"=>"K","Л"=>"L",
		"М"=>"M","Н"=>"N","О"=>"O","П"=>"P","Р"=>"R",
		"С"=>"S","Т"=>"T","У"=>"U","Ф"=>"F","Х"=>"X",
		"Ц"=>"C","Ч"=>"CH","Ш"=>"SH","Щ"=>"SHH","Ъ"=>"'",
		"Ы"=>"Y","Ь"=>"","Э"=>"E","Ю"=>"YU","Я"=>"YA",
		"а"=>"a","б"=>"b","в"=>"v","г"=>"g","д"=>"d",
		"е"=>"e","ё"=>"yo","ж"=>"zh",
		"з"=>"z","и"=>"i","й"=>"j","к"=>"k","л"=>"l",
		"м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
		"с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"x",
		"ц"=>"c","ч"=>"ch","ш"=>"sh","щ"=>"shh","ъ"=>"",
		"ы"=>"y","ь"=>"","э"=>"e","ю"=>"yu","я"=>"ya",
		" "=>"_","—"=>"_",","=>"_","!"=>"_","@"=>"_",
		"#"=>"-","$"=>"","%"=>"","^"=>"","&"=>"","*"=>"",
		"("=>"",")"=>"","+"=>"","="=>"",";"=>"",":"=>"",
		"'"=>"","\""=>"","~"=>"","`"=>"","?"=>"","/"=>"",
		"\\"=>"","["=>"","]"=>"","{"=>"","}"=>"","|"=>""
	);

	return strtr($input, $gost);
}

?>
