<?php
/**
 * Gudang Data Indonesia
 *
 * @author	Yopi <youppie@gmail.com>
 * @since	2010-11-17
 * @last_update Wida Sari <wida.sari@yahoo.com> 2011-10-05 11:18
 **/

class gdi
{
	function get_data($file_data, $output='html')
	{
		$handle=fopen(DATA_DIR.$file_data.'.txt',"r");

		//ambil meta data;
		fgets($handle);
		$bacafile=1;
		$meta=Array();
		while($bacafile){
			$itemmeta=explode(":",fgets($handle));
			if (isset($itemmeta[1])) $meta[$itemmeta[0]]=$itemmeta[1];
			else {$bacafile=0;}
		}

		/**/
		//ambil struktur data
		$meta['struktur_tabel']=explode(";",trim($meta['struktur_tabel']));
		/**
		foreach($meta[struktur_tabel] as $val){
			$data[$val]="";
		}
		//print_r($meta[struktur_data]);
		**/

		// dua baris berikut ini memanggil fgets karena dalam file data ada
		// baris ,<DATA> dan nama field yang tidak diproses
		/*echo*/ fgets($handle);
		$itembaca=explode(";",fgets($handle));

		while($itembaca=fgetcsv($handle,10000,";")){

			if ('</DATA>' == $itembaca[0]) break;
			for($i=0;$i<count($itembaca);$i++){
				$tempdata[$meta['struktur_tabel'][$i]]=$itembaca[$i];
			}
			$data[$itembaca[0]]=$tempdata;

		}
		fclose($handle);
		return $data;
	}

	function get_meta($file_data, $output)
	{
		$handle=fopen(DATA_DIR.$file_data.'.txt',"r");

		//ambil meta data;
		fgets($handle);
		$bacafile=1;
		$meta=Array();
		while($bacafile){
			$itemmeta=explode(":",fgets($handle));
			if (isset($itemmeta[1])) $meta[$itemmeta[0]]=$itemmeta[1];
			else {$bacafile=0;}
		}

		/**/
		//ambil struktur data
		$meta['struktur_tabel']=explode(";",trim($meta['struktur_tabel']));
		fclose($handle);
		return $meta;

	}
}
