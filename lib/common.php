<?php
/**
* Common settings and variables
* @author eliya
*/
// Only for the testing purposes
$br = "<br />" ;
// Settings for local and production servers
define( 'DOCUMENT_ROOT', dirname(__DIR__), true ) ;
define( 'SERVER_NAME', 'http://'.$_SERVER['SERVER_NAME'], true ) ;
define( 'DOCUMENT_PATH', pathinfo($_SERVER["PHP_SELF"], PATHINFO_DIRNAME ), true ) ;
// For cache module: TTL in seconds
define ( 'CACHE_TIME', 300 ) ;

// Define is this server is local or not
$is_local = ( $_SERVER['SERVER_ADDR'] == "192.168.50.100" ) ;
$host = $_SERVER['SERVER_ADDR'] ;

// Database connection parameters
if ( $is_local ) {
    $dbParms = [
        'host' => $host,
        'user' => 'eliya',
        'pass' => '',
        'dbase' => 'colnect',
        'chars' => 'utf8'
    ] ;
} else {
    $dbParms = [
        'host' => $host,
        'user' => 'eu1cj_colnect',
        'pass' => 'FoYMRusI',
        'dbase' => 'eu1cj_colnect',
        'chars' => 'utf8'
    ] ;
}
// Preparing include_dir for files
$IncludeDir = DOCUMENT_ROOT."/" ;

$dir_class = $IncludeDir.'class/' ;
$dir_cache = $IncludeDir.'cached/' ;
$dir_lib   = $IncludeDir.'lib/' ;
$src_js  = SERVER_NAME.DOCUMENT_PATH.'/js/' ;
$src_css = SERVER_NAME.DOCUMENT_PATH.'/css/' ;

require_once "functions.php" ;
require_once $dir_class."Simplehtml.class.php" ;
require_once $dir_class."Colnect.class.php" ;
require_once $dir_class."Cache.class.php" ;

$cache = new Cache( $dir_cache, CACHE_TIME ) ;
?>