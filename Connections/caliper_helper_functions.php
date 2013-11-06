<?php
// Print arrays to JS console immediately inline
// Different from console_log which collects logs (see below)
function console_json($array, $title="json array") {
	echo "<script type='text/javascript'>";
	if ($title !== 0) {
		echo "console.log('$title');";
	}
	echo	"console.log(".json_encode($array).");
	</script>";
}

// Log differently for different types
function jslog($obj, $title="logged var") {

	echo "<script type='text/javascript'>";

  switch (gettype($obj)) {
    case "object":
    	echo "console.log('$title : %O', ".json_encode($obj)." );";  
      break;
    case "array":
/*     	echo "console.groupCollapsed('$title');"; */
    	$obj_array = array();
      foreach ( $obj as $key => $val ) {
        $obj_array[ $key ] = $val;
/*         echo "console.log(".json_encode($val).");"; */
/*         echo "console.log({".$key.": ".json_encode($val)."});"; */
      }
      echo "console.log('$title', ".json_encode($obj_array).");";
/*     	echo "console.groupEnd();"; */
  
      break;
    case "integer":
    case "float":
    case "string":
    		echo "console.log('$title');console.log($obj);";
      break;
    case "boolean":
      $bool = ($obj) ? 'true' : 'false';
  		echo "console.assert($bool,'$title');";
      break;
    default:
      $output .= "console.log('ERROR: tried to log bad type [".gettype($obj)."]')";
      break;
       
  }
	
  echo "
	</script>";
  
}


/**
 * Different from console_json:
 * This function collects logs throughout the PHP process
 * And prints them as inline JS in the footer
 * if the LOG_TO_CONSOLE var == TRUE
*/
function console_log($array, $title="logged var") {
  global $logs;
  
  if ( count($logs) == 0 ) {
    $logs = array(
      "<script type='text/javascript'>",
      "</script>"
    );
  }
  
  $backtrace = debug_backtrace();
  
  $fullArray = array(
      "\"$title\"" => $array
    , "From"     => $backtrace[0]['file']
  );
  
  array_splice($logs, count($logs) - 1, 0,
    "console.log(".json_encode($fullArray).");"
  );
  
}


/**
 * This function sits in the footer and
 * prints the logs collected to the JS console
 */
function print_logs() {
  global $logs;
  
  if (LOG_TO_CONSOLE && $logs !== null )
    echo implode("\n\t",$logs) . "\n";
}

/**
 * Used for PHP console logging
 *
 * If configured properly can be used with
 * Log Viewer WP plugin : http://wordpress.org/plugins/log-viewer/
 */
function _log( $data , $label = null , $backtrace = false ) {
  $output = '';
  
  switch (gettype($data)) {
    case "array":
    case "object":
      $output .= print_r($data, TRUE);
      
      if ( $label !== null )
        $output .= "\n\n---^^^^^^^ END $label ^^^^^^^---";    
      
      break;
    case "integer":
    case "float":
    case "string":
      $output .= $data;
      break;
    case "boolean":
      $converted_data = ($data) ? 'true' : 'false';
      $output .= $converted_data;
      break;
    default:
      $output .= "ERROR: tried to log bad type [".gettype($data)."]";
      break;
       
  }
  
  if ( $backtrace )      
    _backtrace( $label );
  
  if ( !empty( $label ) ) {
    $output = "\t--- [ $label ] ---\n\n" . $output . "\n";
  }
  
  error_log($output);
  
}

function _backtrace( $label = '' ) {
  ob_start();
  if ( phpversion() >= 5.4 ) {
    debug_print_backtrace( 0 , 5); 
  } else {
    debug_print_backtrace();   
  }
  $trace = ob_get_contents(); 
  ob_end_clean();   
    
  _log( $trace, 'backtrace '. $label );
  _log("\t---^^^^^^^ END backtrace ^^^^^^^---");
}

?>