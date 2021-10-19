<?php
es_include("object/object.php");

class LocalObject extends Object
{

	function LocalObject($data = array(), $statement = null)
	{
		if ($statement === null && !is_array($data) && !is_null($data))
		{
			$statement = GetStatement();
		}
		$this->Object($data, $statement);
	}

	function LoadFromSQL($query, $statement = null, $merge = false)
	{
		if ($statement === null)
		{
			$statement = GetStatement();
		}
		parent::LoadFromSQL($query, $statement, $merge);
	}

	function AppendFromSQL($query, $statement = null)
	{
		if ($statement === null)
		{
			$statement = GetStatement();
		}
		parent::AppendFromSQL($query, $statement);
	}

}
?>