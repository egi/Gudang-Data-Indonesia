<?php
/**
 * Gudang Data Indonesia
 *
 * @author		Ivan Lanin <ivan@lanin.org>
 * @author		Agastiya S. Mohammad <agastiya@gmail.com>
 * @author		Wida Sari <wida.sari@yahoo.com>
 * @since		2010-11-13 23:35
 * @last_update 2011-12-09 07:48 - IL
*/
if (!file_exists('config.php'))
	die(__('config.php tidak ditemukan. Buat config.php dari config.sample.php'));

require_once('config.php');
require_once('lib/php/catalog.class.php');
require_once('lib/php/output.class.php');
require_once('lib/php/gdi.class.php');
require_once('lib/gettext/gettext.inc');

// Locale
$locale = 'id';
T_setlocale(LC_MESSAGES, $locale);
T_bindtextdomain(APP_ID, APP_DIR . '/assets/locale');
T_textdomain(APP_ID);

// Process query or get catalog
$TITLE = 'Gudang Data Indonesia';
$query = $_GET['q'];
$output = $_GET['o'];
if (isset($query))
{
	$gdi = new gdi();
	$query  = file_exists(DATA_DIR . $query .'.txt') ? $query : DEFAULT_DATA;
	$output = class_exists($output) ? $output : DEFAULT_OUTPUT;
	$data = $gdi->get_data($query, $output);
	$o = new $output;
	$CONTENT = $o->out($data);
	if ($output == DEFAULT_OUTPUT)
	{
		$meta = $gdi->get_meta($query, $output);
		$TITLE = $meta['deskripsi'];
		$types = json_decode('{"csv":"","json":"","xml":"","graph":"graph.php"}', true);
		foreach ($types as $key => $val)
		{
			$ACTION .= sprintf('<li><a href="./%1$s?q=%2$s&o=%3$s">%4$s</a></li>',
				$val, $query, $key, $key);
		}
		$ACTION = '<ul class="action">' . $ACTION . '</ul>';
	}
}
else
{
	$catalog = new catalog();
	$catalog->get_catalog(DATA_DIR);
	$CONTENT = $catalog->render_catalog();
}

// Die if query and output is not HTML
if (isset($query) && $output != DEFAULT_OUTPUT) die($CONTENT);

// Further process if else
$MENU .= '<li><a href="./?">Katalog</a></li>';
$MENU .= '<li><a href="#">Dataset baru</a></li>';
$MENU = '<ul class="menu">' . $MENU . '</ul>';
$HEADER  = sprintf('<h2>%1$s</h2>', $TITLE) . $MENU;
$CONTENT = $ACTION . $CONTENT;
$FOOTER  = sprintf('&copy; %1$s <a href="http://id-php.org/GDI">GDI</a>', date('Y'));
$THEME = 'assets/themes/default.css';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="id" dir="ltr"" xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo($TITLE); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="<?php echo($THEME); ?>" />
</head>
<body>
<?php echo($HEADER); ?>
<?php echo($CONTENT); ?>
<?php echo($FOOTER); ?>
</body>
</html>
