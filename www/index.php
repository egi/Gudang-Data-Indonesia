<?php
/**
 * Gudang Data Indonesia
 *
 * @author	Ivan Lanin <ivan@lanin.org>
 * @author	Agastiya S. Mohammad <agastiya@gmail.com>
 * @since	2010-11-13 23:35
 * @last_update Wida Sari <wida.sari@yahoo.com> 2011-10-05 11:18
*/

require_once('config.php');
require_once('../lib/php/catalog.class.php');
require_once('../lib/php/output.class.php');
require_once('../lib/php/gdi.class.php');

if(isset($_GET['q']))
{
	$default_data = 'propinsi';
	$default_output = 'html';

	$gdi = new gdi();
	$data = (isset($_GET['q']) && file_exists(DATA_DIR.$_GET['q'].'.txt')) ? $_GET['q'] : $default_data;
	$output = (isset($_GET['o']) && class_exists($_GET['o'])) ? $_GET['o'] : $default_output;
	$result = $gdi->get_data($data, $output);

	// Tampilkan hasil
	if (class_exists('PEAR') && PEAR::isError($result))
	{
		echo $result->getMessage() . "<br />\n" . $result->getDebugInfo();
	}
	else
	{
		$o = new $output;
		if($output != 'html')
		{
			echo $o->out($result);exit;
		}
		else
		{
			$data_html = $o->out($result);
		}
	}
}
else
{
	$catalog = new catalog();
	$data_sets = $catalog->list_catalog(DATA_DIR);
}
?>
<H2>Gudang Data Indonesia</H2>
<hr>
<?php
if(isset($data_html))
{
	$meta = $gdi->get_meta($data, $output);
	echo '<a href="?">Katalog</a><br>';
	echo '<a href="?q='.$data.'&o=csv" target="_blank">[csv]</a><a href="graph.php?q='.$data.'">[graph]</a><a href="?q='.$data.'&o=json">[json]</a><a href="?q='.$data.'&o=xml">[xml]</a>';
	echo "<pre>";
	print_r($meta);
	echo "</pre>";
	echo $data_html;
}
else if(isset($data_sets))
{
	echo "Katalog GDI<br>";
	echo "Data Set:<br>";
	echo '<a href="upload">Upload Data Set Baru</a><br>';
	foreach($data_sets as $data)
	{
		echo '<a href="?q='.$data.'">'.ucfirst(str_replace('_', ' ', $data)) . '</a><br>';
	}
}
?>
<hr>

&copy; <?php echo date('Y'); ?> id-php@yahoogroups.com
