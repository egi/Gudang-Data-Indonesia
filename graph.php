<?php
//define('DATA_DIR', '/home/fasilkom/staf/w/wida12/public_html/gdi/data/');
require_once('config.php');
require_once('lib/php/output.class.php');
require_once('lib/php/gdi.class.php');

if(isset($_GET['q']))
{

	$default_data = 'propinsi';
	$default_output = 'jqplot_data';
	$q = ucwords(str_replace("_", " ", $_GET['q']));
	$gdi = new gdi();
	$data = (isset($_GET['q']) && file_exists(DATA_DIR.$_GET['q'].'.txt')) ? $_GET['q'] : $default_data;
	$output = (isset($_GET['o']) && class_exists($_GET['o'])) ? $_GET['o'] : $default_output;
	$columns = explode(";", (isset($_GET['cols']) && file_exists(DATA_DIR.$_GET['q'].'.txt')) ? $_GET['cols'] : "");

	if(sizeof($columns) == 1 and empty($columns[0]))
		$columns = "";

	$result = $gdi->get_data($data, $output);

	$o = new $output;
	if($output == 'jqplot_data')
	{
		$apiData = $o->out($result);

		$first_key = "";
		foreach($apiData as $index=>$data)
		{
			$counter = 0;
			foreach($data as $i=>$d)
			{
				if($counter == sizeof($data))
					$counter = 0;

				if($first_key == "") $first_key = $i;
				if($i == $first_key)
					$ticks[] = $d;
				else
				{
					if(is_array($columns))
					{
						if(in_array($counter, $columns))
						{
							$plot_data[$i][$data[$first_key]] = $d;
							$series[$i] = "{label:'".$i."'}";
						}
					}
					else
					{
						$plot_data[$i][$data[$first_key]] = $d;
						$series[$i] = "{label:'".$i."'}";
					}
				}
				$counter++;
			}
		}

		$ticks_str = "['".(implode("','", $ticks))."']";

		$data_str = "[";
		foreach($plot_data as $index=>$data)
		{
			$data_strs[] .= "[".implode(",", $data)."]";
		}
		$data_str .= implode(",", $data_strs);
		$data_str .= "]";

		$series_str = "[".implode(",", $series)."]";

		$width = 30 * sizeof($ticks);
	}

}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">

<html lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>Graph <?php echo $q; ?></title>
  <!--[if IE]><script language="javascript" type="text/javascript" src="../excanvas.js"></script><![endif]-->

  <link rel="stylesheet" type="text/css" href="lib/jqplot/jquery.jqplot.css" />
  <link rel="stylesheet" type="text/css" href="lib/jqplot/examples/examples.css" />

  <!-- BEGIN: load jquery -->
  <script language="javascript" type="text/javascript" src="lib/jqplot/jquery.min.js"></script>
  <!-- END: load jquery -->

  <!-- BEGIN: load jqplot -->
    <script language="javascript" type="text/javascript" src="lib/jqplot/jquery.jqplot.js"></script>
    <script language="javascript" type="text/javascript" src="lib/jqplot/plugins/jqplot.logAxisRenderer.min.js"></script>
    <script language="javascript" type="text/javascript" src="lib/jqplot/plugins/jqplot.canvasTextRenderer.min.js"></script>
    <script language="javascript" type="text/javascript" src="lib/jqplot/plugins/jqplot.canvasAxisLabelRenderer.min.js"></script>
    <script language="javascript" type="text/javascript" src="lib/jqplot/plugins/jqplot.canvasAxisTickRenderer.min.js"></script>
    <script language="javascript" type="text/javascript" src="lib/jqplot/plugins/jqplot.dateAxisRenderer.min.js"></script>
    <script language="javascript" type="text/javascript" src="lib/jqplot/plugins/jqplot.categoryAxisRenderer.min.js"></script>
    <script language="javascript" type="text/javascript" src="lib/jqplot/plugins/jqplot.barRenderer.min.js"></script>
    <script language="javascript" type="text/javascript" src="lib/jqplot/plugins/jqplot.highlighter.mod.js"></script>
    <script language="javascript" type="text/javascript" src="lib/jqplot/plugins/jqplot.cursor.min.js"></script>

  <!-- END: load jqplot -->
  <style type="text/css">
    .jqplot-axis { font-size: 0.85em; }
    .jqplot-legend { font-size: 0.75em; }
    .jqplot-point-label {white-space: nowrap;}
	.jqplot-yaxis-label {font-size: 0.85em;}
    .jqplot-yaxis-tick {font-size: 0.85em;}
    .jqplot { margin: 70px;}
    .jqplot-target { margin-bottom: 2em; }

    pre {
        background: #D8F4DC;
        border: 1px solid rgb(200, 200, 200);
        padding-top: 1em;
        padding-left: 3em;
        padding-bottom: 1em;
        margin-top: 1em;
        margin-bottom: 4em;
    }

    p { margin: 2em 0; }

    .note { font-size: 0.8em; }

    .jqplot-breakTick { }

  </style>

  <script type="text/javascript" language="javascript">
	$.jqplot.config.enablePlugins = true;

$(document).ready(function(){

	plot1 = $.jqplot('chart', eval(<?php echo $data_str ?>), {
 	  title: '<?php echo $q ?>',
	  legend: {show:true, location: 'nw', yoffset: 6},
	  series: <?php echo $series_str?>,
	  axes:{
		xaxis:{
		  renderer:$.jqplot.CategoryAxisRenderer,
		  tickRenderer: $.jqplot.CanvasAxisTickRenderer,
          tickOptions: { angle: 30 },
		  ticks:<?php echo $ticks_str ?>
		},
		yaxis: {
          autoscale: false,
		  tickOptions: {formatString:'%d', formatter: $.jqplot.euroFormatter}
        },
		cursor: { show: false },
		highlighter: {
		}
	  }
	});
});


(function($) {
    $.jqplot.euroFormatter = function (format, val) {
        if (!format) {
            format = '%.1f';
        }
        return numberWithCommas($.jqplot.sprintf(format, val));
    };

    function numberWithCommas(x) {
        return x.toString().replace(/\B(?=(?:\d{3})+(?!\d))/g, ".");
    }
})(jQuery);

  </script>
  <script type="text/javascript" src="lib/jqplot/examples/example.js"></script>
  </head>
  <body>
   <div class="example-content">
   <div class="jqplot code" id="chart" style="margin-top:20px; margin-left:20px; width:<?php echo $width?>px; height:800px;"></div>
   </div>
  </body>
</html>