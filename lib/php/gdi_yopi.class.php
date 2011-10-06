<?php
/**
 * Gudang Data Indonesia
 *
 * @author	Yopi <youppie@gmail.com>
 * @since	2010-11-17
 **/

class gdi
{
	private $path_data="data/yopi/";
	function get_data($file_data)
	{
		$handle = fopen($this->path_data.$file_data.'.txt', "r");

		// ambil meta data;
		fgets($handle);
		$meta=Array();
		while($itemmeta = explode(":", fgets($handle)))
		{
			if (isset($itemmeta[1]))
				$meta[$itemmeta[0]] = $itemmeta[1];
			else break;
		}

		// ambil struktur data
		$meta['struktur_tabel'] = explode(";", trim($meta['struktur_tabel']));

		// dua baris berikut ini memanggil fgets karena dalam file data ada 
		// baris, <DATA> dan nama field yang tidak diproses
		/*echo*/ fgets($handle);
		$itembaca = explode(";", fgets($handle));
		while($itembaca = fgetcsv($handle,10000, ";"))
		{
			if ('</DATA>' == $itembaca[0]) break;
			for($i=0, $c=count($itembaca); $i<$c; $i++) {
				$tempdata[$meta['struktur_tabel'][$i]] = $itembaca[$i];
			}
			$data[$itembaca[0]] = $tempdata;
		}
		fclose($handle);
		return $data;
	}
}
