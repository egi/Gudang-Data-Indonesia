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
	function list_catalog($dir)
	{
		$data_sets = array();
		if ($handle = opendir($dir)) {

			/* This is the correct way to loop over the directory. */
			while (false !== ($file = readdir($handle))) {
				if(is_file("$dir/$file"))
				{
					$info = pathinfo($file);
					// only load .txt file
					if ($info['extension'] == 'txt')
						$data_sets[] = basename($file,'.'.$info['extension']);
				}
			}

			closedir($handle);

		}
		else
		{
			echo "Tidak bisa list katalog data";
		}
		return $data_sets;
	}
}
?>