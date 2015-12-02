<?php

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

require_once('TwitterAPIExchange.php');
require_once('TextRazor.php');
include_once 'class.tweet.php';

$noinput = true;

if (isset($_POST['tags'])) {
    // Escape any html characters
    echo "kjdlkjsdhfjlkhfjlksdhfjlksdhfjlkshfjlkshfsldhfjkhsak";
    echo htmlentities($_POST['tags']);
    $tags = htmlentities($_POST['tags']);

    $tags_array = explode(' ', $tags);
}


if(strcmp(trim($tags), "") != 0) $noinput = false;



$settings = array(
    'oauth_access_token' => "124980145-g9l0E6Gc16WqmNtXZKp24EfnwXeMk8SLMGZgGHnb",
    'oauth_access_token_secret' => "Q4AjGuVvSd1nzDOGaDtk2hNQsK3G9KQNKysohNyGNI51W",
    'consumer_key' => "BTeBDWm1cbVvgbc7oEuiiFI7j",
    'consumer_secret' => "RoVflZPzncdwikE2jRND9GJcdBANP0PhufLBOHDMJsbXJ9BK8i"
);

$tweetArray = array();

$api_key= "0ef5823cbd642999a810dac5f663defef6765f2ca9bec392e5d4e9f9";

$textrazor = new TextRazor($api_key);


function getNews($getfield, $newsSource,$settings,$api_key, $tweetArray,$tags_array){

    echo "INSIDE FUNCTION";

    $url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';

    $requestMethod = 'GET';

    $twitter = new TwitterAPIExchange($settings);
    $response = $twitter->setGetfield($getfield)
        ->buildOauth($url, $requestMethod)
        ->performRequest();

    $obj = json_decode($response, true);

    //echo $obj[0]['text'];
    $stringArray = array();

    for ($x =0; $x < 20; $x++) {
        $text1 = $obj[$x]['text'];
        $text2 = str_replace('#', '', $text1);

        array_push($stringArray, $text2);
    }

    echo '<br>';
    for ($i = 0; $i < 20; $i++) {
    $check = false;
        if(isset($tags_array)){
            foreach($tags_array as $tag){
                if(strpos($stringArray[$i], $tag)){
                    $check = true;
                    break;
                }
            }
        }
        else {
            continue;
        }


        if ($check) {
            $text = $stringArray[$i];
            $textrazor = new TextRazor($api_key);
            $textrazor->addExtractor('entities');
            $textrazor->addExtractor('words');
            $textrazor->addEnrichmentQuery("fbase:/location/location/geolocation>/location/geocode/latitude");
            $textrazor->addEnrichmentQuery("fbase:/location/location/geolocation>/location/geocode/longitude");
            $response = $textrazor->analyze($text);
            if (isset($response['response']['entities'])) {
                foreach ($response['response']['entities'] as $entity) {

                    //echo '<h1>';
                    //print("Entity ID: " . $entity['entityId']);
                    $urlcontent = '';
                    if (strpos($entity['entityId'], 'http') !== false) {
                        //echo "SDKJLFHSDLKFJHSDLJKFHSDLJ";
                        $urlcontent = $entity['entityId'];
                        // echo $urlcontent;
                    }

                    $pattern = '#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#' ;
                    $subject = $stringArray[$i];

                    preg_match($pattern, $subject, $matches, PREG_OFFSET_CAPTURE, 3);
                    $urlcontent = $matches[0];

                    $stringArray2 = preg_replace($pattern, '' , $stringArray[$i]);


                    // echo '</h1>';
                    $entity_data = $entity['data'];
                    if (!is_null($entity_data)) {
                        //print(PHP_EOL);
                        //print(PHP_EOL);




                        //echo $entity['entityId'];



                        $newTweet = new tweet($newsSource, $stringArray2,$entity_data["fbase:/location/location/geolocation>/location/geocode/latitude"][0], $entity_data["fbase:/location/location/geolocation>/location/geocode/longitude"][0], $urlcontent);
                        array_push($tweetArray, $newTweet);
                    }

                    //print(PHP_EOL);

                }
            }
        }
    }
    return $tweetArray;
}





    $getfield1 = '?screen_name=conflicts';
    $newsSource1 = 'Conflicts';
    $tweetArray1 = array();
    array_push($tweetArray, getNews($getfield1, $newsSource1 ,$settings,$api_key, $tweetArray1,$tags_array));

    $getfield2 = '?screen_name=CNN';
    $newsSource2 = 'CNN';
    $tweetArray2 = array();
    array_push($tweetArray, getNews($getfield2, $newsSource2,$settings,$api_key, $tweetArray2,$tags_array));

    $getfield3 = '?screen_name=Independent';
    $newsSource3 = 'Independent';
    $tweetArray3 = array();
    array_push($tweetArray, getNews($getfield3, $newsSource3,$settings,$api_key, $tweetArray3,$tags_array));

    $getfield4 = '?screen_name=BBCWorld';
    $newsSource4 = 'BBCWorld';
    $tweetArray4 = array();
    array_push($tweetArray, getNews($getfield4, $newsSource4,$settings,$api_key, $tweetArray4,$tags_array));


    $getfield5 = '?screen_name=AJEnglish';
    $newsSource5 = 'AJEnglish';
    $tweetArray5 = array();
    array_push($tweetArray, getNews($getfield5, $newsSource5,$settings,$api_key, $tweetArray5,$tags_array));


    $getfield6 = '?screen_name=Reuters';
    $newsSource6 = 'Reuters';
    $tweetArray6 = array();
    array_push($tweetArray, getNews($getfield6, $newsSource6,$settings,$api_key, $tweetArray6,$tags_array));

    $getfield7 = '?screen_name=nytimesworld';
    $newsSource7 = 'nytimesworld';
    $tweetArray7 = array();
    array_push($tweetArray,getNews($getfield7, $newsSource7,$settings,$api_key, $tweetArray7,$tags_array));

    $getfield8 = '?screen_name=BreakingNews';
    $newsSource8 = 'BreakingNews';
    $tweetArray8 = array();
    array_push($tweetArray,getNews($getfield8, $newsSource8,$settings,$api_key, $tweetArray8,$tags_array));

    $getfield9 = '?screen_name=TheEconomist';
    $newsSource9 = 'TheEconomist';
    $tweetArray9 = array();
    array_push($tweetArray,getNews($getfield9, $newsSource9,$settings,$api_key, $tweetArray9,$tags_array));

    $getfield10 = '?screen_name=FoxNews';
    $newsSource10 = 'FoxNews';
    $tweetArray10 = array();
    array_push($tweetArray,getNews($getfield10, $newsSource10,$settings,$api_key, $tweetArray10,$tags_array));

    $getfield11 = '?screen_name=dawn_com';
    $newsSource11 = 'dawn_com';
    $tweetArray11 = array();
    array_push($tweetArray,getNews($getfield11, $newsSource11,$settings,$api_key, $tweetArray11,$tags_array));

    $getfield12 = '?screen_name=HDNER';
    $newsSource12 = 'HDNER';
    $tweetArray12 = array();
    array_push($tweetArray,getNews($getfield12, $newsSource12,$settings,$api_key, $tweetArray12,$tags_array));

    $getfield13 = '?screen_name=SkyNewsAsia';
    $newsSource13 = 'SkyNewsAsia';
    $tweetArray13 = array();
    array_push($tweetArray,getNews($getfield13, $newsSource13,$settings,$api_key, $tweetArray13,$tags_array));

$newsSourceArray = array();
$latitudeArray = array();
$longitudeArray = array();
$sTweetArray = array();
$urlArray = array();
for ($x = 0; $x < 13; $x++) {
    for ($i = 0; $i < sizeof($tweetArray[$x]); $i++) {
        echo $tweetArray[$x][$i]->newsSource;
        array_push($newsSourceArray, $tweetArray[$x][$i]->newsSource);
        array_push($latitudeArray, $tweetArray[$x][$i]->latitude);
        array_push($logitudeArray, $tweetArray[$x][$i]->longitude);
        array_push($sTweetArray, $tweetArray[$x][$i]->sTweet);
        array_push($urlArray, $tweetArray[$x][$i]->url);
    }
}

    
var_dump($newsSourceArray);
    
?>