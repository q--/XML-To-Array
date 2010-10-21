<?php

class XML_To_Array
{
	public $array = array();

	function __construct(&$xml)
	{
		$parser = xml_parser_create();

		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);

		xml_parse_into_struct($parser, $xml, $values);

		xml_parser_free($parser);

		foreach ( $values as $value )
		{
			$tag = $value['tag'];

			if ( $value['type'] == 'open' )
			{
				$currentTag = &$this->array[$tag];

				if ( isset($value['attributes']) )
				{
					$currentTag['_ATTR'] = $value['attributes'];
				}

				$currentTag['_p'] = &$this->array;
				$this->array      = &$currentTag;
			}
			elseif ( $value['type'] == 'complete' )
			{
				$currentTag = &$this->array[$tag];

				if ( isset($value['attributes']) )
				{
					$currentTag['_ATTR'][$k] = $value['attributes'];
				}

				$currentTag['_VALUE'] = isset($value['value']) ? $value['value'] : '';

			}
			elseif ( $value['type'] == 'close' )
			{
				$this->array = &$this->array['_p'];
			}
		}

		$this->removeRecursion($this->array);

		return $this->array;
	}

	function removeRecursion(&$array)
	{
		foreach ( $array as $k => $v )
		{
			if ( $k == '_p' )
			{
				unset($array[$k]);
			}
			elseif ( is_array($array[$k]) )
			{
				$this->removeRecursion($array[$k]);
			}
		}
	}
}
