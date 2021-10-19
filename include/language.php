<?php

class Language extends Object
{

	var $_tags;
	var $_values;
	var $_interfaceLanguageCode = null;
	var $_translatePHP = null;
	var $_translateTemplate = null;
	var $_cacheLifeTime = 604800;
	var $_cacheFile;
	var $_mysqlCharsetMap;
	var $_dateTimeFormatMap;
	
	var $_dateFormatForPHP = null;
	var $_timeFormatForPHP = null;
	var $_dateFormatForJS = null;
	var $_timeFormatForJS = null;

	function Language()
	{
		$this->_mysqlCharsetMap = array(
			'big5'			=> 'big5',
			'cp-866'		=> 'cp866',
			'euc-jp'		=> 'ujis',
			'euc-kr'		=> 'euckr',
			'gb2312'		=> 'gb2312',
			'gbk'			=> 'gbk',
			'iso-8859-1'	=> 'latin1',
			'iso-8859-2'	=> 'latin2',
			'iso-8859-7'	=> 'greek',
			'iso-8859-8'	=> 'hebrew',
			'iso-8859-8-i'	=> 'hebrew',
			'iso-8859-9'	=> 'latin5',
			'iso-8859-13'	=> 'latin7',
			'iso-8859-15'	=> 'latin1',
			'koi8-r'		=> 'koi8r',
			'shift_jis'		=> 'sjis',
			'tis-620'		=> 'tis620',
			'utf-8'			=> 'utf8',
			'windows-1250'	=> 'cp1250',
			'windows-1251'	=> 'cp1251',
			'windows-1252'	=> 'latin1',
			'windows-1256'	=> 'cp1256',
			'windows-1257'	=> 'cp1257',
		);

		$this->_dateTimeFormatMap = array(
			'%'		=> '%%', // a literal % character
			'A'		=> '%p', // "PM" or "AM"
			'a'		=> '%P', // "pm" or "am"
			'D'		=> '%a', // abbreviated weekday name
			'l'		=> '%A', // full weekday name
			'M'		=> '%b', // abbreviated month name
			'F'		=> '%B', // full month name
			'd'		=> '%d', // the day of the month ( 01 .. 31 )
			'j'		=> '%e', // the day of the month ( 1 .. 31 )
			'H'		=> '%H', // hour ( 00 .. 23 )
			'h'		=> '%I', // hour ( 01 .. 12 )
			'z'		=> '%j', // day of the year ( 0 .. 365 )
			'G'		=> '%k', // hour ( 0 .. 23 )
			'g'		=> '%l', // hour ( 1 .. 12 )
			'm'		=> '%m', // month ( 01 .. 12 )
			'i'		=> '%M', // minute ( 00 .. 59 )
			's'		=> '%S', // second ( 00 .. 59 )
			'U'		=> '%s', // number of seconds since Epoch (since Jan 01 1970 00:00:00 UTC)
			'W'		=> '%W', // the week number (ISO 8601)
			'w'		=> '%w', // the day of the week ( 0 .. 6, 0 = SUN )
			'y'		=> '%y', // year without the century ( 00 .. 99 )
			'Y'		=> '%Y', // year including the century ( ex. 1979 )
			'\t'	=> '%t', // a tab character
			'\n'	=> '%n', // a new line character
			'\\'	=> '\\\\', // backslash
		);
		
		$this->_interfaceLanguageCode = GetFromConfig("InterfaceLanguageCode");
	}

	function GetHTMLCharset()
	{
		return "utf-8";
	}

	function GetMySQLEncoding()
	{
		return "utf8";
	}

	function _ConvertForPHP($format)
	{
		$format = str_replace('\t', "\t", $format);
		$format = str_replace('\n', "\n", $format);
		$format = str_replace('\\', '\\\\', $format);
		$uncompatibleSymbols = array('B', 'c', 'I', 'L', 'n', 'O', 'r', 'S', 't', 'T', 'Z');
		for ($i = 0; $i < count($uncompatibleSymbols); $i++)
		{
			$format = str_replace($uncompatibleSymbols[$i], '\\'.$uncompatibleSymbols[$i], $format);
		}
		return $format;
	}

	function GetDateFormat()
	{
		if(is_null($this->_dateFormatForPHP))
		{
			$this->_dateFormatForPHP = $this->_ConvertForPHP(GetFromConfig("DateFormat"));
		}
		return $this->_dateFormatForPHP;
	}

	function GetTimeFormat()
	{
		if(is_null($this->_timeFormatForPHP))
		{
			$this->_timeFormatForPHP = $this->_ConvertForPHP(GetFromConfig("TimeFormat"));
		}
		return $this->_timeFormatForPHP;
	}

	function GetDateFormatForJS()
	{
		if(is_null($this->_dateFormatForJS))
		{
			$this->_dateFormatForJS = GetFromConfig("DateFormat");
			foreach ($this->_dateTimeFormatMap as $phpCode => $jsCode)
			{
				$this->_dateFormatForJS = str_replace($phpCode, $jsCode, $this->_dateFormatForJS);
			}
		}
		return $this->_dateFormatForJS;
	}

	function GetTimeFormatForJS()
	{
		if(is_null($this->_timeFormatForJS))
		{
			$this->_timeFormatForJS = GetFromConfig("TimeFormat");
			foreach ($this->_timeFormatForJS as $phpCode => $jsCode)
			{
				$this->timeFormatForJS = str_replace($phpCode, $jsCode, $this->_timeFormatForJS);
			}
		}
		return $this->_timeFormatForJS;
	}

	function GetTranslation($key, $module = null, $replacements = array())
	{
		$this->_LoadForPHP($module);
		if (isset($this->_translatePHP["module".$module][$key]))
		{
			return PrepareContentBeforeShow(Language::ReplacePairs($this->_translatePHP["module".$module][$key]["Value"], $replacements));
		}
		else
		{
			// Translation is not found
			return $key;
		}
	}

	function ReplacePairs($str = '', $replacements = array(), $open = '%', $close = '%')
	{
		if (strlen($str) > 0 && count($replacements) > 0)
		{
			$resReplace = array();
			foreach ($replacements as $key => $value)
			{
				$resReplace[$open.$key.$close] = $value;
			}
			$str = str_replace(array_keys($resReplace), array_values($resReplace), $str);
		}

		return $str;
	}

	function LoadForTempate($template, $module, $isAdmin)
	{
		$lang = $this->_interfaceLanguageCode;
			
		$template = str_replace("/", "_", $template);
		if (!isset($this->_translateTemplate["module".$module]))
		{
			$data = array();
			$files = array();

			$files[] = PROJECT_DIR."language/".$lang."/_template.xml";
			if (strlen($module) > 0)
			{
				$files[] = PROJECT_DIR."language/".$lang."/".$module."_template.xml";
			}

			if ($this->_CheckCache($files))
			{
				$data = unserialize(fread($fp = fopen($this->_cacheFile, 'r'), filesize($this->_cacheFile)));
			}
			else
			{
				for ($i = 0; $i < count($files); $i++)
				{
					if ($this->_LoadXML($files[$i]))
						$data = array_merge_recursive2($data, $this->_GetTemplateXMLAsArray());
				}
				$this->_CreateCache(serialize($data));
			}

			$this->_translateTemplate["module".$module] = $data;
		}

		// Get commmon translation
		if (isset($this->_translateTemplate["module".$module]['common']))
			$result = $this->_translateTemplate["module".$module]['common'];
		else
			$result = array();

		// Get actual translation for curent template and merge it with commmon translation
		if ($isAdmin && isset($this->_translateTemplate["module".$module]['admin'][$template]) && is_array($this->_translateTemplate["module".$module]['admin'][$template]))
		{
			$result = array_merge($result, $this->_translateTemplate["module".$module]['admin'][$template]);
		}
		else if (isset($this->_translateTemplate["module".$module]['public'][$template]) && is_array($this->_translateTemplate["module".$module]['public'][$template]))
		{
			$result = array_merge($result, $this->_translateTemplate["module".$module]['public'][$template]);
		}

		return $result;
	}

	function LoadForJS($module = null)
	{
		$this->_LoadForPHP($module);
		return $this->_translatePHP["module".$module];
	}

	function _LoadXML($file)
	{
		if (!file_exists($file))
			return false;

		$parser = xml_parser_create("UTF-8");
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
		xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8");
		xml_parse_into_struct($parser, implode("", file($file)), $this->_values, $this->_tags);
		xml_parser_free($parser);

		return true;
	}

	function _LoadForPHP($module = null)
	{
		if (isset($this->_translatePHP["module".$module])) return;

		$lang = $this->_interfaceLanguageCode;
		
		$data = array();
		$files = array();

		$files[] = PROJECT_DIR."language/".$lang."/_php.xml";
		if (strlen($module) > 0)
		{
			$files[] = PROJECT_DIR."language/".$lang."/".$module."_php.xml";
		}
		if ($this->_CheckCache($files))
		{
			$data = unserialize(fread($fp = fopen($this->_cacheFile, 'r'), filesize($this->_cacheFile)));
		}
		else
		{
			for ($i = 0; $i < count($files); $i++)
			{
				if ($this->_LoadXML($files[$i]))
				{
					for ($j = $this->_tags["Root"][0] + 1; $j < $this->_tags["Root"][1]; $j++)
					{
						if (isset($this->_values[$j]["tag"]))
						{
							$attributeList = array();
							if(isset($this->_values[$j]["attributes"]))
							{
								foreach ($this->_values[$j]["attributes"] as $k => $v)
									$attributeList[] = array("Title" => $k, "Value" => $v);
							}
							$data[$this->_values[$j]["tag"]] = array("Value" => isset($this->_values[$j]["value"]) ? PrepareContentBeforeShow($this->_values[$j]["value"]) : "", 
																				"AttributeList" => $attributeList);
						}
					}
				}
			}
			$this->_CreateCache(serialize($data));
		}

		$this->_translatePHP["module".$module] = $data;
	}

	function _GetTemplateXMLAsArray()
	{
		$templates = array();
		foreach ($this->_values as $id => $value)
		{
			if ($value["level"] == 2)
			{
				$templates[$value["tag"]][$value["type"]] = $id;
			}
		}

		$tmplArray = array();

		foreach ($templates as $name => $template)
		{
			if (isset($template["open"]) && isset($template["close"]))
			{
				for ($i = $template["open"] + 1; $i < $template["close"]; $i++)
				{
					if ($this->_values[$i]["level"] == 3 && $this->_values[$i]["type"] == "complete")
					{
						$attributeList = array();
						if(isset($this->_values[$i]["attributes"]))
						{
							foreach ($this->_values[$i]["attributes"] as $k => $v)
								$attributeList[] = array("Title" => $k, "Value" => $v);
						}
						$tmplArray[$name][$this->_values[$i]["tag"]] = array("Value" => isset($this->_values[$i]["value"]) ? PrepareContentBeforeShow($this->_values[$i]["value"]) : "", 
																			"AttributeList" => $attributeList);
					}

					if ($this->_values[$i]["level"] == 3 && $this->_values[$i]["type"] == "open")
					{
						$file = $this->_values[$i]["tag"];
					}

					if ($this->_values[$i]["level"] == 4 && $this->_values[$i]["type"] == "complete")
					{
						$attributeList = array();
						if(isset($this->_values[$i]["attributes"]))
						{
							foreach ($this->_values[$i]["attributes"] as $k => $v)
								$attributeList[] = array("Title" => $k, "Value" => $v);
						}
						$tmplArray[$name][$file][$this->_values[$i]["tag"]] = array("Value" => isset($this->_values[$i]["value"]) ? PrepareContentBeforeShow($this->_values[$i]["value"]) : "", 
																			"AttributeList" => $attributeList);
					}
				}
			}
			else
			{
				$tmplArray[$name] = array();
			}
		}
		
		return $tmplArray;
	}

	function _CheckCache($xmlFiles)
	{
		if (is_array($xmlFiles) && count($xmlFiles) > 0)
		{
			$this->_cacheFile = $this->_GetFilename(implode("-", $xmlFiles));
			$maxFileTime = 0;
			for ($i = 0; $i < count($xmlFiles); $i++)
			{
				if (!is_file($xmlFiles[$i])) continue;
				$t = filemtime($xmlFiles[$i]);
				if ($t > $maxFileTime) $maxFileTime = $t;
			}

			if (file_exists($this->_cacheFile))
			{
				if (!((filemtime($this->_cacheFile) + $this->_cacheLifeTime) < date('U') ||
					filemtime($this->_cacheFile) < $maxFileTime))
				{
					return true;
				}
			}
		}

		return false;
	}

	function _GetFilename($xmlFile)
	{
		return XML_CACHE_DIR.md5('XMLCachestaR'.$xmlFile).'.xtc';
	}

	function _CreateCache($data)
	{
		if ($fp = fopen($this->_cacheFile, "w"))
		{
			flock($fp, 2); // set an exclusive lock
			fputs($fp, $data); // write the serialized array
			flock($fp, 3); // unlock file
			fclose($fp);
			touch($this->_cacheFile);
			@chmod($this->_cacheFile, 0666);
			return true;
		}
		else
		{
			return false;
		}
	}
}

?>