<?php
/**
 * Gudang Data Indonesia
 *
 * @author		Wida Sari <wida.sari@yahoo.com>
 * @author		Ivan Lanin <ivan@lanin.org>
 * @since		2011-10-05 10:30
 * @last_update 2011-12-09 06:55 - IL
 */

class catalog
{
	var $data_sets; // d

	/**
	 * Populate and return catalog
	 */
	function get_catalog($dir)
	{
		$this->data_sets = array();
		if ($handle = opendir($dir)) {

			/* This is the correct way to loop over the directory. */
			while (false !== ($file = readdir($handle))) {
				if(is_file("$dir/$file"))
				{
					$info = pathinfo($file);
					// only load .txt file
					if ($info['extension'] == 'txt')
						$this->data_sets[] = basename($file,'.'.$info['extension']);
				}
			}
			closedir($handle);
		}
		else
		{
			echo "Tidak bisa list katalog data";
		}
		return($this->data_sets);
	}

	/**
	 * Render catalog
	 */
	function render_catalog()
	{
		$ret  = '<ul>';
		foreach($this->data_sets as $data)
		{
			$ret .= sprintf(
				'<li><a href="./?q=%1$s">%2$s</a></li>',
				$data,
				ucfirst(str_replace('_', ' ', $data))
			);
		}
		$ret .= '</ul>';
		return($ret);
	}
}
?>
