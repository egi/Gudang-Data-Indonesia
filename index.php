<?php
/**
 * Gudang Data Indonesia
 *
 * @author	Ivan Lanin <ivan@lanin.org>
 * @author	Agastiya S. Mohammad <agastiya@gmail.com>
 * @since	2010-11-13 23:35
 */
require_once('MDB2.php');
define('LF', "\r\n");

$gdi = new gdi();
if ($_GET['q'] == 'wilayah')
{
	$result = $gdi->get_data('SELECT * FROM ' . $_GET['q'] . ' LIMIT 0, 50');
}

// Tampilkan hasil
if ($result)
{
	$output_fn = 'output_html';
	if (isset($_GET['f']) && function_exists('output_'.$_GET['f']))
		$output_fn = 'output_' . $_GET['f'];
	echo $output_fn($result);
}

function output_csv($result)
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

function output_html($result)
{
	$ret  = '<table>';
	$ret .= '<tr>';
	foreach ($result[0] as $column => $value)
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

/**
 * Array to XML
 */
function array_to_xml(&$array)
{
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
function output_xml(&$apiData)
{
	$ret .= '<?xml version="1.0"?>' . LF;
	$ret .= '<gdi status="1">' . LF;
	$ret .= array_to_xml($apiData);
	$ret .= '</gdi>' . LF;
	//header('Content-type: text/xml');
	return($ret);
}

/**
 * output JSON
 */
function output_json(&$apiData)
{
	$data = array('gdi'=>$apiData);
	$ret .= json_encode($data);
	//header('Content-type: application/json');
	return($ret);
}

/**
 */
class gdi
{
	var $dsn;
	var $_db;

	/**
	 */
	function __construct()
	{
		$dsn = array(
			'host' => 'localhost',
			'name' => '',
			'user' => '',
			'pass' => '',
		);
		$this->dsn = sprintf('%1$s://%2$s:%3$s@%4$s/%5$s',
			'mysql', $dsn['user'], $dsn['pass'], $dsn['host'], $dsn['name']);
		$this->_db =& MDB2::factory($this->dsn);
		if (PEAR::isError($this->_db)) die($this->_db->getMessage());
		$this->_db->exec("SET NAMES 'utf8'");
		$fetch_mode = MDB2_FETCHMODE_ASSOC;
	}

	/**
	 */
	function get_data($query)
	{
		$rows = $this->_db->queryAll($query, null, $fetch_mode);
		return($rows);
	}
}
