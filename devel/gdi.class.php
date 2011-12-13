<?php
/**
 * Gudang Data Indonesia
 *
 * @author	Ivan Lanin <ivan@lanin.org>
 * @author	Agastiya S. Mohammad <agastiya@gmail.com>
 * @since	2010-11-13 23:35
 */
require_once('MDB2.php');

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
	}

	/**
	 */
	function get_data($query)
	{
		$filter = '1=1 ';
		if ('propinsi' == $query)
			$filter .= 'AND `level`="P"';
		elseif ('kabupaten' == $query)
			$filter .= 'AND `level`="K"';
		$query = 'SELECT * FROM wilayah WHERE '.$filter.' LIMIT 50';
		$fetch_mode = MDB2_FETCHMODE_ASSOC;
		$rows = $this->_db->queryAll($query, null, $fetch_mode);
		return($rows);
	}

	function import_from($filename)
	{
		$this->_db->loadModule('Extended');

		$sql = <<<EOSQL
DROP TABLE IF EXISTS `wilayah`;
CREATE TABLE IF NOT EXISTS `wilayah` (
  `id` char(10) NOT NULL,
  `name` varchar(30) NOT NULL,
  `level` char(1) NOT NULL,
  `provinsi_id` tinyint(3) NOT NULL,
  `kota_id` tinyint(2) NOT NULL,
  `kecamatan_id` tinyint(2) NOT NULL,
  `parent_id` char(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
EOSQL;
		$this->_db->query($sql);

		$last_id = array('P'=>0, 'K'=>0);
		$fields = array('id', 'name', 'level', 'provinsi_id', 'kota_id', 'kecamatan_id');
		$rs = fopen($filename, 'r');
		while ($row = fgetcsv($rs, 0, "\t"))
		{
			$row = array_combine($fields, array_merge($row, sscanf($row[0], '%3s%2s%2s000')));

			// penentuan parent_id semudah ini kalau data sudah diurutkan 
			// berdasarkan code. kalau data tidak urut, sebaiknya diurutkan dulu 
			// di shell
			switch ($row['level'])
			{
			case 'P':
				$last_id['P'] = $row['id'];
				break;
			case 'K':
				$last_id['K'] = $row['id'];
				$row['parent_id'] = $last_id['P'];
				break;
			case 'C':
				$row['parent_id'] = $last_id['K'];
				break;
			}
			$this->_db->autoExecute('wilayah', $row, MDB2_AUTOQUERY_INSERT);
		}
		fclose($rs);
	}
}
