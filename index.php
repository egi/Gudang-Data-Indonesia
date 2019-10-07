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

require_once('lib/php/catalog.class.php');
require_once('lib/php/output.class.php');
require_once('lib/php/gdi.class.php');
require_once('lib/gettext/gettext.inc');

if (!file_exists('config.php'))
	die(__('config.php tidak ditemukan. Buat config.php dari config.sample.php'));

require_once('config.php');

// Locale
$locale = 'id';
T_setlocale(LC_MESSAGES, $locale);
T_bindtextdomain(APP_ID, APP_DIR . '/assets/locale');
T_textdomain(APP_ID);

// Process query or get catalog
$TITLE = 'Gudang Data Indonesia';
$ACTION = '';
$query  = isset($_GET['q']) ? $_GET['q'] : null;
$output = isset($_GET['o']) ? $_GET['o'] : null;
if (isset($query))
{
	$gdi = new gdi();
	$query  = file_exists(DATA_DIR . $query .'.txt') ? $query : DEFAULT_DATA;
	$output = class_exists($output) ? $output : DEFAULT_OUTPUT;
	$o = new $output;
	$data = $gdi->get_data($query, $output);
	$CONTENT = $o->out($data);
	if (in_array($output, array('meta', 'html', 'graph')))
	{
		$meta = $gdi->get_meta($query, $output);
		if ($output == 'meta')
			$CONTENT = $o->out($meta);
		$TITLE = $meta['deskripsi'];
		$types = json_decode('{"html":"","meta":"","graph":"","csv":"","json":"","xml":""}', true);
		foreach ($types as $key => $val)
		{
			$ACTION .= sprintf('<a href="./%1$s?q=%2$s&o=%3$s"  class="dropdown-item" >%4$s</a>',
				$val, $query, $key, $key);
		}
		// $ACTION = '<ul class="action">' . $ACTION . '</ul>';
		$ACTION = '
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Output Type
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          ' .$ACTION. '
        </div>
      </li>

		';
	}
	else
		die($CONTENT);
}
else
{
	$catalog = new catalog();
	$catalog->get_catalog(DATA_DIR);
	$CONTENT = $catalog->render_catalog();
}

// Further process if else
$MENU  = '<li class="nav-item"><a class="nav-link" href="./?">Katalog</a></li>';
$MENU .= '<li class="nav-item"><a class="nav-link" href="#">Dataset baru</a></li>';
// $MENU = '<ul class="menu">' . $MENU . '</ul>';

$MENU = '<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand" href="#">GDI</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
    	' . $MENU . '
    	' . $ACTION . '
    </ul>
  </div>
</nav>
';

$HEADER  = sprintf('<h1 class="m-3">%1$s</h1>', $TITLE) . $MENU;
$FOOTER  = sprintf('<footer class="footer">
      <div class="container text-center">&copy; %1$s <a href="http://id-php.org/GDI">GDI</a></div>
    </footer>', date('Y'));
$THEME = 'assets/styles/bootstrap.min.css';

if ($output == 'graph') include_once('graph.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="id" dir="ltr"" xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo($TITLE); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="<?php echo($THEME); ?>" />
<link rel="stylesheet" type="text/css" href="assets/styles/sticky-footer.css" />
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<?php if ($output == 'graph') { ?>
<link rel="stylesheet" type="text/css" href="lib/jqplot/jquery.jqplot.css" />
<link rel="stylesheet" type="text/css" href="lib/jqplot/gdi.jqplot.css" />
<script language="javascript" type="text/javascript" src="lib/jqplot/jquery.min.js"></script>
<script language="javascript" type="text/javascript" src="lib/jqplot/jquery.jqplot.js"></script>
<script language="javascript" type="text/javascript" src="lib/jqplot/plugins/jqplot.logAxisRenderer.min.js"></script>
<script language="javascript" type="text/javascript" src="lib/jqplot/plugins/jqplot.canvasTextRenderer.min.js"></script>
<script language="javascript" type="text/javascript" src="lib/jqplot/plugins/jqplot.canvasAxisLabelRenderer.min.js"></script>
<script language="javascript" type="text/javascript" src="lib/jqplot/plugins/jqplot.canvasAxisTickRenderer.min.js"></script>
<script language="javascript" type="text/javascript" src="lib/jqplot/plugins/jqplot.dateAxisRenderer.min.js"></script>
<script language="javascript" type="text/javascript" src="lib/jqplot/plugins/jqplot.categoryAxisRenderer.min.js"></script>
<script language="javascript" type="text/javascript" src="lib/jqplot/plugins/jqplot.barRenderer.min.js"></script>
<script language="javascript" type="text/javascript" src="lib/jqplot/plugins/jqplot.highlighter.min.js"></script>
<script language="javascript" type="text/javascript" src="lib/jqplot/plugins/jqplot.cursor.min.js"></script>
<script language="javascript" type="text/javascript">
var data_str = <?php echo $data_str ?>;
var series_str = <?php echo $series_str?>;
var ticks_str = <?php echo $ticks_str ?>;
var title_str = '<?php echo $q ?>';
</script>
<script language="javascript" type="text/javascript" src="lib/jqplot/gdi.jqplot.js"></script>
<?php } ?>
</head>
<body>
<?php echo($HEADER); ?>
<?php if ($output == 'graph') { ?>
<div class="jqplot code" id="chart" style="margin-top:20px; margin-left:20px; width:<?php echo $width?>px; height:800px;"></div>
<?php } else { ?>
<?php echo($CONTENT); ?>
<?php } ?>
<div id="footer"><?php echo($FOOTER); ?></div>
</body>
</html>
