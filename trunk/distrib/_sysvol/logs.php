<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 29.12.2014
 * Time: 19:37
 */

class logs {
    public static function WriteLogs($where,$content)
    {
        if($handle = fopen('logZ/'.$where.'['.@date("d_m_Y", time()).'].log', 'a+'))
        {
            if (fwrite($handle, "[".@date("H:i:s", time())."] IP [".getenv("REMOTE_ADDR")."] \r\n Message: ". htmlspecialchars($content)." \r\n query: '".$_SERVER['QUERY_STRING']."' \r\n refer: '".getenv('HTTP_REFERER')."' \r\n user Agent: '".$_SERVER['HTTP_USER_AGENT']."' \r\n\n") === FALSE)
                fclose($handle);
        }
    }
}