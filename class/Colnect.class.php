<?php
/**
 * @author eliya
 * @copyright 2018
 */
class Colnect
{
    private $dbParms   = false ;
    private $dbSource  = false ;
    private $dbStmt    = false ;
    private $stat_parm = [] ;
    private $stat = [] ;
/**
 * Connect to database on constract event using PDO
 */
    public function __construct( $parms ) {
        $this->dbParms = $parms ;
        $this->connect() ;
    }

    private function connect() {

        $parm = $this->dbParms ;
        $dns = "mysql:host=$parm[host]; dbname=$parm[dbase]; charset=$parm[chars]" ;
        $opt = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ] ;

        $this->dbSource = new PDO( $dns, $parm["user"], $parm["pass"], $opt ) ;
    }
/**
 * Get statistic for domain
 */
    public function getStatistic($domain, $tag) {
    // The number of URLs of particular domain have been checked till now.
        $sql = "SELECT COUNT(`url_id`) AS url_count FROM `url` WHERE `url_name` LIKE :dom" ;
        $res = $this->prepareStatistisSQL( $domain, false, $sql, true ) ;
    // Collect statistics
        $this->stat["url_count"]  = $res["url_count"] ;
    // Average page fetch time from particular domain during the last 24 hours.
        $sql = "SELECT ROUND(AVG(r.`duration`)) AS avg_duration FROM `request` r
                LEFT JOIN `domain` d ON r.`domain_id` = d.`domain_id`
                WHERE TIMESTAMPDIFF(SECOND, r.`last_visit`, CURRENT_TIMESTAMP) < 86400
                AND d.`domain_name` = :dom" ;
        $res = $this->prepareStatistisSQL( $domain, false, $sql ) ;
    // Collect statistics
        $this->stat["avg_duration"] = $res["avg_duration"] ;
    // Total count of this element from particular domain
        $sql = "SELECT SUM(r.`count`) AS tag_count FROM `request` r
                LEFT JOIN `domain` d ON r.`domain_id` = d.`domain_id`
                LEFT JOIN `element` e ON r.`element_id` = e.`element_id`
                WHERE e.`element_name` = :tag
                AND d.`domain_name` = :dom" ;
        $res = $this->prepareStatistisSQL( $domain, $tag, $sql ) ;
    // Collect statistics
        $this->stat["tag_count"]  = $res["tag_count"] ;
    // Total count of this element from ALL requests ever made
        $sql = "SELECT SUM(r.`count`) AS total_tag_count FROM `request` r
                LEFT JOIN `element` e ON r.`element_id` = e.`element_id`
                WHERE e.`element_name` = :tag" ;
        $res = $this->prepareStatistisSQL( false, $tag, $sql ) ;
    // Collect statistics
        $this->stat["total_tag_count"] = $res["total_tag_count"] ;

        return $this->stat ;
    }
/**
 * Prepare SQL Statistics string and get data
 */
    private function prepareStatistisSQL( $domain = false, $tag = false, $sql, $like = false ) {

        $res = [] ;

        $this->dbStmt = $this->dbSource->prepare($sql) ;

        if ($domain) {
            $this->dbStmt->bindParam( ":dom", $dom ) ;
            $dom = ($like) ? "%$domain%" : $domain ;
        }

        if ($tag) {
            $this->dbStmt->bindParam( ":tag", $el ) ;
            $el = ($like) ? "%$tag%" : $tag ;
        }

        try {
            $this->dbStmt->execute() ;
            $res = $this->dbStmt->fetch() ;
            $this->dbStmt->closeCursor() ;
        }
        catch (PDOException $e) {
            $this->pdoErrorMessage($e, "prepareMasterTableSQL(): $sql") ;
        }

        return $res ;
    }
/**
 * Update statistics
 */
    public function updateStatistic($parms) {

        $sql = "INSERT INTO `request` SET " ;

        foreach ( $parms["master_tbl"] AS $key => $val ) {
            $res = $this->updateMasterTable( $key, $val ) ;
            if (!$res)
                return false ;
            else
                $sql .= $res ;
        }

        $sql .= "`duration` = :duration, `count` = :count" ;
    // Prepare SQL string and bind vars in loop
        $this->stat_parm["duration"] =  $parms["duration"] ;
        $this->stat_parm["count"] = $parms["count"] ;
        $this->dbStmt = $this->dbSource->prepare($sql) ;

        foreach ( $this->stat_parm AS $column => $value ) {
            $this->dbStmt->bindParam( ":{$column}", ${$column} ) ;
            ${$column} = $value ;
        }

        try {
            $this->dbStmt->execute() ;
            $this->dbStmt->closeCursor() ;
        }
        catch (PDOException $e) {
            $this->pdoErrorMessage($e, "prepareMasterTableSQL(): $sql") ;
        }
    }
/**
 * Update master tables such as "domain", "element", "url"
 */
    private function updateMasterTable( $table = "", $value = "" ) {

        if ( !$table OR !$value ) return false ;

        $id = $this->ifKeyExists( $table, $value ) ;

        if ( !$id ) {
    // If record not exists - Insert new parameter into master table
            $sql = "INSERT INTO `$table` SET `{$table}_name` = :{$table}" ;
            $this->prepareMasterTableSQL( $table, $value, $sql ) ;
    // Obtain last insert id to pass it into updateStatistic function
            $id = $this->dbSource->lastInsertId() ;
        }

        $this->dbStmt->closeCursor();
    // Collect columns and values for updating
        $this->stat_parm["{$table}_id"] = $id ;
        $sql_set = "`{$table}_id` = :{$table}_id, " ;
        return $sql_set ;
    }
/**
 * Check value names on duplicate in master tables
 */
    private function ifKeyExists( $table, $value ) {

        $sql = "SELECT `{$table}_id` FROM `$table` WHERE `{$table}_name` = :{$table}" ;
        $this->prepareMasterTableSQL( $table, $value, $sql ) ;
        $res = $this->dbStmt->fetch() ;

        return $res["{$table}_id"] ;
    }
/**
 * Common function for prepairing SQL"s
 */
    private function prepareMasterTableSQL( $table, $value, $sql ) {

        try {
            $this->dbStmt = $this->dbSource->prepare($sql) ;
            $this->dbStmt->bindParam( ":{$table}", ${$table} ) ;
            ${$table} = $value ;
            $this->dbStmt->execute() ;
        }
        catch (PDOException $e) {
            $this->pdoErrorMessage($e, "prepareMasterTableSQL(): $sql") ;
        }
    }
/**
* Handle MySQL errors
*/
    private function pdoErrorMessage( $exeption, $function = false )
    {
        $br = "<br />" ;
        $extra = ($function) ? $br."Function ".$function : "" ;
        die( $br."Error: ".$exeption->getMessage().$extra ) ;
    }
/**
 * Destroy this instance
 */
    public function __destruct() {
        $this->dbStmt = null ;
        $this->dbSource = null ;
    }

}
?>