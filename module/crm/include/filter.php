<?php 

class BaseFilter
{
	var $name;
	var $config;
	
	function BaseFilter($name, $config)
	{
		$this->name = $name;
		$this->config = $config;
	}
	
	function AppendSQLCondition($request, &$join, &$where, &$having){}
	
	function LoadFilterData($request, &$content)
	{
		if(!is_array($request->GetProperty($this->config["Name"])) && $request->ValidateNotEmpty($this->config["Name"]))
			$content->SetVar($this->config["Name"], $request->GetProperty($this->config["Name"]));
			
		if(isset($this->config["Autocomplete"]) && $this->config["Autocomplete"])
		{
			$script = '<script type="text/javascript">$(document).ready(function(){';
			
			$script .= "var ".$this->name."values = new Bloodhound({
			                datumTokenizer: Bloodhound.tokenizers.obj.whitespace('".$this->config["AutocompleteField"]."'),
			                queryTokenizer: Bloodhound.tokenizers.whitespace,
			                limit: 50,
			                remote: {
			                    url: '".PROJECT_PATH."module/crm/ajax.php?Action=GetAutocompleteData&Table=".$this->config["AutocompleteTable"]."&Field=".$this->config["AutocompleteField"]."&Query=%QUERY',
			                }
			            });

			            ".$this->name."values.initialize();

			            $('input[name=".$this->name."]').typeahead(null, {
			                name: '".$this->name."values',
			                displayKey: '".$this->config["AutocompleteField"]."',
			                source: ".$this->name."values.ttAdapter()
			            });";
			$script .= "});</script>";
			$content->SetVar($this->name."AutocompleteHTML", $script);
		}
	}
	
	function GetFilterFieldNames()
	{
		return array($this->name);
	}
}

?>