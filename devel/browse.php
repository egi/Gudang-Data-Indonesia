<?php

$path_data="../data/";
ini_set('display_errors',0); 
$file_data="propinsi.txt";
//$file_data="wilayah.txt";
$handle=fopen($path_data.$file_data,"r");

//ambil meta data;
fgets($handle);
$bacafile=1;
$meta=Array();
while($bacafile){
	$itemmeta=explode(":",fgets($handle));
	
	$meta[$itemmeta[0]]=$itemmeta[1];
	if (!isset($itemmeta[1]))
	{
		$bacafile=0;
	}
}
echo "<pre>";
print_r($meta);
echo "</pre>";

/**/
//ambil struktur data
$meta["struktur_tabel"]=explode(";",$meta["struktur_tabel"]);
/**
foreach($meta[struktur_tabel] as $val){
	$data[$val]="";
}
//print_r($meta[struktur_data]);
**/

// dua baris berikut ini memanggil fgets karena dalam file data ada baris ,<DATA> dan nama field yang tidak diproses
echo fgets($handle);
$itembaca=explode(";",fgets($handle));


while($itembaca=fgetcsv($handle,10000,";")){

	for($i=0;$i<count($itembaca);$i++){
		$tempdata[$meta["struktur_tabel"][$i]]=$itembaca[$i];
	}
	$data[$itembaca[0]]=$tempdata;

}
fclose($handle);

// // // // //TES TAMPIL DATA CSV
// // // // //print_r($data);
// // // // foreach($data as $key=>$val){
// // // // echo "$key => ";
// // // // foreach($val as $k=>$v){
// // // // echo "$k:$v;";
// // // // }
// // // // echo "<br />";
// // // // }



//// TES POTONGAN KODE IVAN R. LANIN
define(LF, "\r\n");
// Tampilkan hasil
if ($data)
{
$rows = count($data);
switch ($_GET['f'])
{
case 'xml':
$ret .= output_xml($data);
break;
case 'json':
$ret .= output_json($data);
break;
case 'csv':
foreach ($data[0] as $column => $value)
$head .= ($head ? ',' : '') . $column;
$ret .= $head . LF;
foreach ($data as $rows)
{
$row = '';
foreach ($rows as $column => $value)
$row .= ($row ? ',' : '') . $value;
$ret .= $row . LF;
}
break;
default:
$ret .= '<table>';
$ret .= '<tr>';
foreach ($data[0] as $column => $value)
$ret .= '<th>' . $column . '</th>';
$ret .= '</tr>';
foreach ($data as $rows)
{
$ret .= '<tr>';
foreach ($rows as $column => $value)
$ret .= '<td>' . $value . '</td>';
$ret .= '</tr>';
}
$ret .= '</table>';
break;
}
echo($ret);
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
$ret .= array_to_xml(&$value);
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
$ret .= array_to_xml(&$apiData);
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

?>