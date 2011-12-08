<?php
/**
 * Gudang Data Indonesia
 *
 * @author		Wida Sari <wida.sari@yahoo.com>
 * @since		2011-10-05 10:30
 * @last_update Wida Sari <wida.sari@yahoo.com> 2011-10-05 11:18
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