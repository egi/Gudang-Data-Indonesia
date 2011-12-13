<?php
if(isset($_GET['q']))
{
	$default_data = 'propinsi';
	$default_output = 'graph';
	$q = ucwords(str_replace("_", " ", $_GET['q']));
	$gdi = new gdi();
	$data = (isset($_GET['q']) && file_exists(DATA_DIR.$_GET['q'].'.txt')) ? $_GET['q'] : $default_data;
	$output = (isset($_GET['o']) && class_exists($_GET['o'])) ? $_GET['o'] : $default_output;
	$columns = explode(";", (isset($_GET['cols']) && file_exists(DATA_DIR.$_GET['q'].'.txt')) ? $_GET['cols'] : "");

	if(sizeof($columns) == 1 and empty($columns[0]))
		$columns = "";

	$result = $gdi->get_data($data, $output);

	$o = new $output;
	if($output == 'graph')
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

		$data_strs = array();
		$data_str = "[";
		foreach($plot_data as $index=>$data)
		{
			$data_strs[] .= "[".implode(",", $data)."]";
		}
		$data_str .= implode(",", $data_strs);
		$data_str .= "]";

		$series_str = "[".implode(",", $series)."]";

		$width = 30 * sizeof($ticks);
		if ($width > 800) $width = 800;
	}

}