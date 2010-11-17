<?php
/**
 * Gudang Data Indonesia
 *
 * @author	Ivan Lanin <ivan@lanin.org>
 * @author	Agastiya S. Mohammad <agastiya@gmail.com>
 * @since	2010-11-13 23:35
 */

define('LF', "\r\n");

interface output
{
	function out($data);
}

class csv implements output
{
	function out($result)
	{
		$ret = '';
		$rows = count($result);
		foreach ($result[0] as $column => $value)
			$head .= ($head ? ',' : '') . $column;
		$ret .= $head . LF;
		foreach ($result as $rows)
		{
			$row = '';
			foreach ($rows as $column => $value)
				$row .= ($row ? ',' : '') . $value;
			$ret .= $row . LF;
		}
		return $ret;
	}
}

class html implements output
{
	function out($result)
	{
		$ret  = '<table>';
		$ret .= '<tr>';
		$first_row = reset($result);
		foreach ($first_row as $column => $value)
			$ret .= '<th>' . $column . '</th>';
		$ret .= '</tr>';
		foreach ($result as $rows)
		{
			$ret .= '<tr>';
			foreach ($rows as $column => $value)
				$ret .= '<td>' . $value . '</td>';
			$ret .= '</tr>';
		}
		$ret .= '</table>';
		return $ret;
	}
}

class xml implements output
{
	/**
	 * Array to XML
	 */
	private function array_to_xml(&$array)
	{
		$ret = '';
		foreach ($array as $key => $value)
		{
			$keyName = is_numeric($key) ? 'elm' . $key : $key;
			if (!is_array($value))
			{
				$ret .= sprintf('<%1$s>%2$s</%1$s>', $keyName, $value) . LF;
			}
			else
			{
				$ret .= sprintf('<%1$s>', $keyName) . LF;
				$ret .= array_to_xml($value);
				$ret .= sprintf('</%1$s>', $keyName) . LF;
			}
		}
		return($ret);
	}

	/**
	 * output XML
	 */
	function out($apiData)
	{
		$ret  = '<?xml version="1.0"?>' . LF;
		$ret .= '<gdi status="1">' . LF;
		$ret .= $this->array_to_xml($apiData);
		$ret .= '</gdi>' . LF;
		//header('Content-type: text/xml');
		return($ret);
	}
}

class json implements output
{
	/**
	 * output JSON
	 */
	function out($apiData)
	{
		$data = array('gdi'=>$apiData);
		$ret  = json_encode($data);
		//header('Content-type: application/json');
		return($ret);
	}
}

//require_once('gdi.class.php');
require_once('gdi_yopi.class.php');
$gdi = new gdi();
$level = (isset($_GET['q'])) ? $_GET['q'] : 'wilayah';
$result = $gdi->get_data($level);

// Tampilkan hasil
if (class_exists('PEAR') && PEAR::isError($result))
{
	echo $result->getMessage() . "<br />\n" . $result->getDebugInfo();
}
else
{
	$output_class = 'html';
	if (isset($_GET['f']) && class_exists($_GET['f'])) $output_class = $_GET['f'];
	$o = new $output_class;
	echo $o->out($result);
}
