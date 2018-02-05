<?php
/**
 * Basic function for this projects.
 * May be used in another applications.
 */
function getRemoteContent($url) {
/*
    if( !filter_var( $url, FILTER_VALIDATE_URL ) ){
        die("CURL Error: Domain '$url' does not exist");
    }
*/
    $ch = curl_init();

    if( $ch === false ) die( 'CURL Error : ' . curl_error($ch) ) ;

    $options =
    [
        CURLOPT_URL => $url,
        CURLOPT_HEADER => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FAILONERROR => true,
        CURLOPT_USERAGENT => "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36"
    ] ;

    curl_setopt_array($ch, $options) ;
    $result = curl_exec($ch);

    if( $result === false ) die( 'CURL Error : ' . curl_error($ch) ) ;

    curl_close($ch);

    return $result ;
}

function isDomainAvailible($domain) {
    // Validation of URL value
    if(!filter_var($domain, FILTER_VALIDATE_URL)){
           return false;
    }

    $ch = curl_init($domain);
    $options =
    [
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_HEADER => true,
        CURLOPT_NOBODY => true,
        CURLOPT_RETURNTRANSFER => true
    ] ;
    curl_setopt_array($ch, $options);

    $response = curl_exec($curlInit);

    curl_close($curlInit);

    if ($response) return true;

    return false;
}


function user_var_dump ($array){
    if (!isset($array)) {
        echo "VALUE NOT SET<br />" ;
        return false;
    }
    if (!is_array($array)) {
        echo $array."&nbsp;<font color='blue'>(<i>".gettype($array)."</i>)</font><br />" ;
        return false;
    }
	foreach ( $array as $key => $val ) {
        if ( is_array($val) ) {
            echo $key." => [<br />" ;
            user_var_dump($val) ;
            echo "]<br />" ;
        } else {
    		$val = (is_null($val)) ? "NULL" : $val ;
    		echo "$key => <font color='blue'>$val</font>&nbsp;(<i>".gettype($val)."</i>)<br />" ;
        }
	}
}

function user_parse_string ($string, $separator = ""){
    $res = str_replace( $separator, "<br />", $string ) ;
    echo $res ;
}

?>