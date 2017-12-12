<?php
function getAccess($url) {
    $handle = curl_init($url);

    //We don't want any HTTPS / SSL errors.
    curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($handle, CURLOPT_VERBOSE, 1);
    curl_setopt($handle, CURLOPT_FRESH_CONNECT, true);
    /* Get the HTML or whatever is linked in $url. */
    $response = curl_exec($handle);

    /* Check for 404 (file not found). */
    $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
    curl_close($handle);

    /* If the document has loaded successfully without any redirection or error */
    if ($httpCode >= 200 && $httpCode < 300) {
        return true;
    } else {
        return false;
    }
}
function curlResult($email,$password,$ip,$port)
{
    $detect=0;
    define('USER_AGENT',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.113 Safari/537.36');
    $linke = 'http://mw.stb001.com/api/login/' . $email . '/' . md5($password) . '/' . md5('stb') . '/web?response=json?callback=receiver';

    //Get list channel of last connection
    $listChSavedArray = json_decode(file_get_contents('listcha.json', true));
    if(!empty($listChSavedArray) && (!property_exists($listChSavedArray,'error_code') && !isset($listChSavedArray->error_code)))
    {
        if(getAccess($listChSavedArray->channels[0]->channel->stream_m3u8_url) == false) {
            return $listChSavedArray;
        }
    }
    //Check if empty execute another connexion
    //--Get list information--//
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $linke);
    curl_setopt($curl, CURLOPT_USERAGENT, USER_AGENT);

    //Define proxy
    if (!empty($ip) && !empty($port)) {

        curl_setopt($curl, CURLOPT_PROXY, $ip);
        curl_setopt($curl, CURLOPT_PROXYPORT, $port);
    }

    //We don't want any HTTPS / SSL errors.
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_VERBOSE, 1);
    curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);

    $link = curl_exec($curl);

    //Decode list from JSON to Array
    $listcharray = json_decode($link);
    curl_close($curl);

    //--Get list channel--//
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $listcharray->Client->Api->list_channels);
    curl_setopt($curl, CURLOPT_USERAGENT, USER_AGENT);

    //Define proxy
    curl_setopt($curl, CURLOPT_PROXY, $ip);
    curl_setopt($curl, CURLOPT_PROXYPORT, $port);

    //We don't want any HTTPS / SSL errors.
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_VERBOSE, 1);
    curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);


//Get information of channel.
    $listChJSON = curl_exec($curl);
    $listChArraye = json_decode($listChJSON);

    $fp = fopen('./listcha.json', 'w+');
    fwrite($fp, $listChJSON);
    fclose($fp);


    return $listChArraye;
}
function curlStream($linke,$ip,$port)
{
    define('USER_AGENT',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.113 Safari/537.36');

    //--Get list information--//
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $linke);
    curl_setopt($curl, CURLOPT_USERAGENT, USER_AGENT);

    //Define proxy
    if(!empty($ip) && !empty($port)) {

        curl_setopt($curl, CURLOPT_PROXY, $ip);
        curl_setopt($curl, CURLOPT_PROXYPORT, $port);
    }

    //We don't want any HTTPS / SSL errors.
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_VERBOSE, 1);

    $link = curl_exec($curl);
    curl_close($curl);

    $verti=substr($linke,0,strpos($linke, '/m3u8/'));

    return str_replace('..',siteURL().'/zaaptv.php?ts='.$verti,$link);
}
function curlTS($content,$ip,$port)
{
    define('USER_AGENT',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.113 Safari/537.36');

    //--Get list information--//
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $content);
    curl_setopt($curl, CURLOPT_USERAGENT, USER_AGENT);

    //Define proxy
    if(!empty($ip) && !empty($port)) {

        curl_setopt($curl, CURLOPT_PROXY, $ip);
        curl_setopt($curl, CURLOPT_PROXYPORT, $port);
    }

    //We don't want any HTTPS / SSL errors.
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_VERBOSE, 1);


    $link = curl_exec($curl);
    curl_close($curl);
    return $link;
}
function getURL($listChSavedArray,$name)
{
    for ($i = 0; $i < count($listChSavedArray->channels); $i++)
    {
        if ($listChSavedArray->channels[$i]->channel->title == urldecode($name))
        {
            return $listChSavedArray->channels[$i]->channel->stream_m3u8_url;
        }
    }
    return '';
}

function getLogo($listChSavedArray,$name)
{
    for ($i = 0; $i < count($listChSavedArray->channels); $i++)
    {
        if ($listChSavedArray->channels[$i]->channel->title == urldecode($name))
        {
            return $listChSavedArray->channels[$i]->channel->logo;
        }
    }
    return '';
}
function siteURL() {
    $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ||
        $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $domainName = $_SERVER['HTTP_HOST'];
    return $protocol.$domainName;
}

function getList($listChSavedArray)
{
    $tab=null;
    for ($i = 0; $i < count($listChSavedArray->channels); $i++)
    {
        $tab[]=($listChSavedArray->channels[$i]->channel->genre.' - '.$listChSavedArray->channels[$i]->channel->title.' - ('.siteURL().'/zaaptv.php?name='.urlencode($listChSavedArray->channels[$i]->channel->title).')');

    }
    sort($tab);
    echo('**********************************<br>
            *&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Mogulis(NoBra!n)	 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;*<br>
          **********************************<br><br>');
    for ($i = 0; $i < count($tab); $i++)
    {
        echo($tab[$i].'<br>');
    }
}
function getM3U8($listChSavedArray)
{
    $tab=array();
    echo('#EXTM3U');

    for ($i = 0; $i < count($listChSavedArray->channels); $i++)
    {

        if ($listChSavedArray->channels[$i]->channel->genre <> 'TURKISH LIVE TV' && $listChSavedArray->channels[$i]->channel->genre <> 'CHRISTIAN' && $listChSavedArray->channels[$i]->channel->genre <> 'AFGHAN' && $listChSavedArray->channels[$i]->channel->genre <> 'FARSI' && $listChSavedArray->channels[$i]->channel->genre <> 'PLUS 12' && $listChSavedArray->channels[$i]->channel->genre <> 'PLUS 7') {
            $tab[] = array(
                $listChSavedArray->channels[$i]->channel->genre,
                $listChSavedArray->channels[$i]->channel->title
            );
        }
    }

    sort($tab);

    $te=array();
    $tes='';
    for ($i = 0; $i < count($tab); $i++)
    {
        if(!in_array($tab[$i][1],$te)) {
            $te[]=$tab[$i][1];
            $tes=$tes.("\n#EXTINF:-1, " . strtoupper($tab[$i][1]) . "\n" .siteURL().'/zaaptv.php?name='.str_replace(" ", "%20", $tab[$i][1]));
        }

    }

    header("Content-Disposition: attachement; filename=nobrain".time().".m3u");
    print $tes;
}
function explodeIp($proxyList)
{
    $proxy = explode(":", $proxyList);
    return $proxy[0];
}
function explodePort($proxyList)
{
    $proxy = explode(":", $proxyList);
    return $proxy[1];
}
