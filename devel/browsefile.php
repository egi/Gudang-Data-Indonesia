<?php

$path_data="../data/";
$file_data="propinsi.txt";

$data=file($path_data.$file_data);
foreach($data as $key=>$val){
echo "$key = > $val <br />";
}

?>