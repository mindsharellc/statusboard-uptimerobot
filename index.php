<?php
// Load config file
require dirname(__FILE__) . '/config.php';

// Set history
$with_history = (isset($_GET['with_history'])) ? true : false;

// Do something ...
if( isset($_GET['save']) ) {
  save_data();
} elseif( isset($_GET['update']) ) {
	update_data();
} else {
  show_data();
}

/**
 * Save data (for Uptime Robot webhook)
 */
function save_data() {
  $newdata = array(
	  'monitorFriendlyName'   => urldecode($_GET['monitorFriendlyName']),
	  'alertType'             => urldecode($_GET['alertType']),
	  'monitorURL'            => urldecode($_GET['monitorURL']),
	  'alertDetails'          => urldecode($_GET['alertDetails']),
	  'time'                  => time()
	);

	$data = json_decode( file_get_contents("uptimerobot_data.json"), true );
	$data[ $_GET['monitorID'] ] = $newdata;
	file_put_contents("uptimerobot_data.json", json_encode($data), LOCK_EX);
}

/**
 * Update data (for AJAX)
 */
function update_data() {
  global $with_history;
  $data = json_decode( file_get_contents("uptimerobot_data.json"), true );
  $update = array();
  $up = array();
  $down = array();

  if(!empty($data)) {
    foreach($data as $monitor => $info) {

      if($info['alertType'] == 1 or $with_history) {
        $url = parse_url($info['monitorURL']);
        $domain = preg_replace('/^www\./', '', $url['host']);
        $codedata = explode(' ', $info['alertDetails']);
      }

      if($info['alertType'] == 1) {
        $down[$info['time']] = array ('name'=> $domain, 'time' => time_elapsed_string('@'.$info['time']), 'code' => $codedata[1], 'type' => $info['alertType'] );
      } else if($info['alertType'] == 2 && $with_history) {
        $up[$info['time']] = array ('name'=> $domain, 'time' => time_elapsed_string('@'.$info['time']), 'code' => $codedata[1], 'type' => $info['alertType'] );
      }
    }
    krsort($down);
    krsort($up);
    $update = array_merge($down, $up);
  }
  $status = (empty($update)) ? 0 : 1;
  $output = array( 'status' => $status, 'content' => $update, 'count' => count($update) );
  echo json_encode( $output );
  die();
}

/**
 * Show data for Status Board
 */
function show_data() {
  global $with_history, $config; ?>
<!DOCTYPE>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
	  <title>Uptime Robot</title>
	  <meta application-name="Uptime Robot" data-allows-resizing="YES" data-default-size="4,4" data-min-size="4,2" data-max-size="4,4" data-allows-scrolling="YES" />
		<meta http-equiv="Content-Type" content="text/html; charset=utf8" />
		<meta http-equiv="Cache-control" content="no-cache" />
    <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script>
    $(document).ready(function($) {

      update_content();
      setInterval(update_content,<?php echo $config['refresh_rate']; ?>);

      if (document.location.href.indexOf('desktop') > -1) {
				$("#data").css('background-color', 'black');
			}

      function update_content() {
        $.ajax({
            type: 'GET',
            dataType: 'json',
            url: "index.php?update<?php echo ($with_history) ? '&with_history' : ''; ?>",
            data: {},
            success: function(data){
              var output = '', down = '', up = '', head = '', count_down = 0, count_up = 0;
              if(data.status == "1") {
                $.each( data.content, function( key, value ) {
                  if(value.type == "1") {
                    down = down + '<div>' + value['name'] + ' <span>' + value['time'] + ' (' + value['code'] + ')</span> </div>';
                    count_down++;
                  } else {
                    up = up + '<div>' + value['name'] + ' <span>' + value['time'] + '</span> </div>';
                    count_up++;
                  }
                });

                if(count_up > 0 && count_down == 0) {
                  head = '<h2 class="up"><i class="fa <?php echo $config['icon_up']; ?>"></i></h2>';
                } else {
                  head = '<h2 class="down">' + count_down + ' <i class="fa <?php echo $config['icon_down']; ?>"></i></h2>';
                }

                if(count_up > 0 && count_down == 0) {
                  output = head + '<div class="up">' + up + '</div>';
                } else if(count_up > 0) {
                  output = head + '<div class="down">' + down + '</div><div class="up border">' + up + '</div>';
                } else {
                  output = head + '<div class="down">' + down + '</div>';
                }
              } else {
                output = '<h2 class="all up"><i class="fa <?php echo $config['icon_up']; ?> fa-3x"></i></h2>';
              }
              $("#data").html(output);
            }
        });
      }

    });
    </script>
    <style>

			body, div, h2 {
				margin: 0;
				padding: 0;
				-webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
			}

      #data {
        font-family: "Roadgeek 2005 Series D";
        padding: 10px;
        color: <?php echo $config['color_text']; ?>;
        position: relative;
        height: 100%;
        width: 100%;
      }

      #data div {
        font-size: 20px;
        line-height: 30px;
      }

      #data div span {
        float: right;
        font-family: "Roadgeek 2005 Series C";
        line-height: 30px;
      }

      #data div.border {
        border-top: 1px solid <?php echo $config['color_up']; ?>;
      }

      h2 {
        text-align: center;
        font-size: 40px;
        line-height: 40px;
      }

      h2.down i, h2.down, #data div.down span {
        color: <?php echo $config['color_down']; ?>;
      }

      h2.up i, h2.up, #data div.up span {
        color: <?php echo $config['color_up']; ?>;
      }

      h2.all {
        position: relative;
        top: 50%;
        -webkit-transform: translateY(-50%);
        -moz-transform: translateY(-50%);
        -ms-transform: translateY(-50%);
        -o-transform: translateY(-50%);
        transform: translateY(-50%);
        font-size: 1.5em;
        line-height: 1.5em;
      }

    </style>
	</head>
	<body>
    <div id="data"></div>
	</body>
</html>
<?php }

/**
 * Helper function for time formating
 */
function time_elapsed_string($datetime, $full = false) {
  global $config;
  $string = $config['time_shortcuts'];

  $now = new DateTime;
  $ago = new DateTime($datetime);
  $diff = $now->diff($ago);

  $diff->w = floor($diff->d / 7);
  $diff->d -= $diff->w * 7;

  foreach ($string as $k => &$v) {
      if ($diff->$k) {
          $v = $diff->$k . ' ' . $v ;
      } else {
          unset($string[$k]);
      }
  }

  if (!$full) $string = array_slice($string, 0, 1);
  return $string ? implode(', ', $string) : $config['now'];
}

?>