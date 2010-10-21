<?php

error_reporting(E_ALL);

$xml = '<?xml version="1.0" encoding="UTF-8" standalone="no"?><NABTransactMessage><MessageInfo><messageID>8af793f9af34bea0cf40f5fc011e0c</messageID><messageTimestamp>20102110135831313000+660</messageTimestamp><apiVersion>xml-4.2</apiVersion></MessageInfo><RequestType>Payment</RequestType><MerchantInfo><merchantID>ABC0001</merchantID></MerchantInfo><Status><statusCode>000</statusCode><statusDescription>Normal</statusDescription></Status><Payment><TxnList count="1"><Txn ID="1"><txnType>0</txnType><txnSource>23</txnSource><amount>200</amount><currency>AUD</currency><purchaseOrderNo>test</purchaseOrderNo><approved>Yes</approved><responseCode>00</responseCode><responseText>Approved</responseText><settlementDate>20101021</settlementDate><txnID>526404</txnID><authID/><CreditCardInfo><pan>444433...111</pan><expiryDate>08/12</expiryDate><cardType>6</cardType><cardDescription>Visa</cardDescription></CreditCardInfo></Txn></TxnList></Payment></NABTransactMessage>';
//$xml = '<foo id="0"><bar id="1">one</bar><bar id="2">two</bar></foo>';

$XMLToArray = new XML_To_Array($xml);

echo '<pre>';
print_r($XMLToArray->array);
echo '</pre>';

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
