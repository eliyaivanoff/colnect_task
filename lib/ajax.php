<?php
/**
 * @author eliya
 * @copyright 2018
 */
require_once "common.php" ;

$parms = $result = [] ;

if ( isset($_REQUEST['action']) )
{
// Create an instance of basic class
    $colnect = new Colnect( $dbParms ) ;

    if ( 'check_site' == $_REQUEST['action'] )
    {
    // Validate URL and element entered
        if ( !isset($_REQUEST['url']) OR empty($_REQUEST['url']) ) die('Field "Site" cannot be empty!') ;
        if ( !isset($_REQUEST['tag']) OR empty($_REQUEST['tag']) ) die('Field "Element" field cannot be empty!') ;
        if ( !preg_match( ' /^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/', $_REQUEST['url'] ) )
            die("URL is entered in an incorrect format.") ;
        if ( !preg_match( "/^[a-zA-Z1-6]{1,10}$/", $_REQUEST['tag'] ) )
            die("Element '$_REQUEST[tag]' does not exist.") ;

        $url = $_REQUEST['url'] ;
        $tag = $_REQUEST['tag'] ;
        $cnt = 0 ;
    // If the same request was made less than 5 minutes ago - Cache functions are using
    // Else - Get parameters from remoute site and update our database
        $start = microtime(true) ;
        $domain = parse_url( $url, PHP_URL_HOST ) ;
        $fmask = str_replace( '/', '', $url ) ;

        if ( !($result = $cache->loadCache( $fmask, $tag ) ) ) {
            $content = getRemoteContent($url);
            $html = str_get_html($content) ;

            if( $html->innertext AND count( $html->find($tag)) ) {
                $obj = $html->find($tag) ;
                $cnt = count($obj) ;
            }

            $end  = microtime(true) ;
            $time = round( ($end - $start), 3 ) * 1000 ;
        // Update database with statistic
            $parms['master_tbl']['domain'] = $domain ;
            $parms['master_tbl']['element'] = $tag ;
            $parms['master_tbl']['url'] = $url ;
            $parms['count'] = $cnt ;
            $parms['duration'] = $time ;
            $colnect->updateStatistic($parms) ;

            $result = $colnect->getStatistic($domain, $tag) ;
            $result = array_merge( $result, ['count' => $cnt]) ;
            $cache->saveCache( $result, $fmask, $tag );
        }

        $end  = microtime(true) ;
        $time = round( ($end - $start), 3 ) * 1000 ;

        $tag  = "&lt".$tag."&gt" ;
        echo "Element <strong>$tag</strong> appeared <strong>$result[count]</strong> times in page.<br />" ;
        echo "URL <strong>$url</strong> Fetched on ".date( 'd/m/Y H:i:s', time() ).", took <strong>$time</strong> msec.<br /><br />" ;
        echo "<strong>General Statistics:</strong><br />" ;
        echo "<strong>$result[url_count]</strong> different URLs from <strong>$domain</strong> have been fetched<br />" ;
        echo "Average fetch time from <strong>$domain</strong> during the last 24 hours hours is <strong>$result[avg_duration]</strong> msec.<br />" ;
        echo "There was a total of <strong>$result[tag_count]</strong> <strong>$tag</strong> elements from <strong>$domain</strong>.<br />" ;
        echo "Total of <strong>$result[total_tag_count]</strong> <strong>$tag</strong> elements counted in all requests ever made.<br />" ;
    }
    else
    {
        return false ;
    }
}

?>