<?php
/**
* @author eliya
* @copyright 2018
*/
class Cache
{
    private $cache_dir ;
    private $cache_time ;

    public function __construct( $dir, $time ) {
        $this->cache_dir  = $dir ;
        $this->cache_time = $time ;
    }

    private function getCacheFilename( $prefix, $key = null )
    {
        $prefix = str_replace( '/', '', str_replace( ':', '', $prefix ) ) ;
        $filename = $this->cache_dir."stat_".$prefix ;
        if ($key)
        {
            $filename .= "_" . (is_array($key) ? implode("_", $key) : $key) ;
        }
        $filename .= ".tmp" ;
        return $filename ;
    }

    public function saveCache( $data, $prefix, $key = null )
    {
        file_put_contents( $this->getCacheFilename($prefix, $key), serialize($data));
    }

    public function loadCache($prefix, $key = null)
    {
        $filename = $this->getCacheFilename( $prefix, $key );
        if ( !file_exists($filename) )
            return null;

        if ( filemtime($filename) + $this->cache_time < time() )
            return null;

        return unserialize( file_get_contents($filename) ) ;
    }

}
?>