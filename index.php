<?php

//ini_set('display_errors',1);
//ini_set('display_startup_errors',1);
//error_reporting(-1);

require_once('TwitterAPIExchange.php');
require_once('TextRazor.php');
include_once 'class.tweet.php';

$noinput = true;

//if (isset($_POST['tags'])) {
//    // Escape any html characters
//   // echo "kjdlkjsdhfjlkhfjlksdhfjlksdhfjlkshfjlkshfsldhfjkhsak";
//   // echo htmlentities($_POST['tags']);
//    $tags = htmlentities($_POST['tags']);
//
//    $tags_array = explode(' ', $tags);
//}

$tags = "";

if (isset($_POST['tags'])) {
    // Escape any html characters
    //echo $_POST['tags'];
    $tags = htmlentities($_POST['tags']);
    $tags = str_replace(',', ' ',$tags);

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

$api_key= "c6537cb196c9154b50bdf6654978976b825dd1f1ac09a0ea6e0d6af2";

$textrazor = new TextRazor($api_key);


function getNews($getfield, $newsSource,$settings,$api_key, $tweetArray,$tags_array){

    //echo "INSIDE FUNCTION";

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

    //echo '<br>';
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
//                    if (strpos($entity['entityId'], 'http') !== false) {
//                        //echo "SDKJLFHSDLKFJHSDLJKFHSDLJ";
//                        $urlcontent = $entity['entityId'];
//                        // echo $urlcontent;
//                    }

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


                        if(count($tweetArray) == 0){

                            $newTweet = new tweet($newsSource, $stringArray2,$entity_data["fbase:/location/location/geolocation>/location/geocode/latitude"][0], $entity_data["fbase:/location/location/geolocation>/location/geocode/longitude"][0], $urlcontent);
                            array_push($tweetArray, $newTweet);
                        }
                        else{
                            $newTweet = new tweet($newsSource, $stringArray2,$entity_data["fbase:/location/location/geolocation>/location/geocode/latitude"][0], $entity_data["fbase:/location/location/geolocation>/location/geocode/longitude"][0], $urlcontent);
//                            for($i = 0; $i <= count($tweetArray); $i++) {
//                                if($tweetArray[$i]->stweet !==  $stringArray2){
//                                    array_push($tweetArray, $newTweet);
//                                }
//
//                            }
                            foreach($tweetArray as $tw){
                                if($tw->stweet !==  $stringArray2){
                                    array_push($tweetArray, $newTweet);
                                    break;
                                }
                            }
                        }





                    }

                    //print(PHP_EOL);

                }
            }
        }
    }
    //echo '<br>';
    //var_dump($tweetArray);
    return $tweetArray;
}


if(!$noinput){


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

    $getfield7 = '?screen_name=nytimes';
    $newsSource7 = 'NewYorkTimes';
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
    $newsSource11 = 'Dawn';
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
    
    $getfield14 = '?screen_name=japantimes';
    $newsSource14 = 'JapanTimes';
    $tweetArray14 = array();
    array_push($tweetArray,getNews($getfield14, $newsSource14,$settings,$api_key, $tweetArray14,$tags_array));
    
    $getfield15 = '?screen_name=Folha_english';
    $newsSource15 = 'Folha';
    $tweetArray15 = array();
    array_push($tweetArray,getNews($getfield15, $newsSource15,$settings,$api_key, $tweetArray15,$tags_array));
    
    $getfield16 = '?screen_name=cctvnewsafrica';
    $newsSource16 = 'CCTVAfrica';
    $tweetArray16 = array();
    array_push($tweetArray,getNews($getfield16, $newsSource16,$settings,$api_key, $tweetArray16,$tags_array));
    
    $getfield17 = '?screen_name=XHNNews';
    $newsSource17 = 'China Xinhua News';
    $tweetArray17 = array();
    array_push($tweetArray,getNews($getfield17, $newsSource17,$settings,$api_key, $tweetArray17,$tags_array));
    
    $getfield18 = '?screen_name=IndianExpress';
    $newsSource18 = 'Indian Express';
    $tweetArray18 = array();
    array_push($tweetArray,getNews($getfield18, $newsSource18,$settings,$api_key, $tweetArray18,$tags_array));
    
    $getfield19 = '?screen_name=allafrica';
    $newsSource19 = 'All Africa News';
    $tweetArray19 = array();
    array_push($tweetArray,getNews($getfield19, $newsSource19,$settings,$api_key, $tweetArray19,$tags_array));
    
    $getfield20 = '?screen_name=CBCNews';
    $newsSource20 = 'CBC News';
    $tweetArray20 = array();
    array_push($tweetArray,getNews($getfield20, $newsSource20,$settings,$api_key, $tweetArray20,$tags_array));
    
//    $getfield21 = '?screen_name=jakpost';
//    $newsSource21 = 'Jakarta Post';
//    $tweetArray21 = array();
//    array_push($tweetArray,getNews($getfield21, $newsSource21,$settings,$api_key, $tweetArray21,$tags_array));
    
    $getfield22 = '?screen_name=australian';
    $newsSource22 = 'The Australian';
    $tweetArray22 = array();
    array_push($tweetArray,getNews($getfield22, $newsSource22,$settings,$api_key, $tweetArray22,$tags_array));
    
    $getfield23 = '?screen_name=TodayRussia';
    $newsSource23 = 'Russia Today';
    $tweetArray23 = array();
    array_push($tweetArray,getNews($getfield23, $newsSource23,$settings,$api_key, $tweetArray23,$tags_array));
    
    $getfield24 = '?screen_name=RT_com';
    $newsSource24 = 'RT';
    $tweetArray24 = array();
    array_push($tweetArray,getNews($getfield24, $newsSource24,$settings,$api_key, $tweetArray24,$tags_array));
    
    

//var_dump($tweetArray);





}


$newsSourceArray = array();
$latitudeArray = array();
$longitudeArray = array();
$sTweetArray = array();
$urlArray = array();
for ($x = 0; $x < 24; $x++) {
    for ($i = 0; $i < sizeof($tweetArray[$x]); $i++) {
        array_push($newsSourceArray, $tweetArray[$x][$i]->newsSource);
        array_push($sTweetArray, $tweetArray[$x][$i]->stweet);
        array_push($latitudeArray, $tweetArray[$x][$i]->latitude);
        array_push($longitudeArray, $tweetArray[$x][$i]->longitude);
        array_push($urlArray, $tweetArray[$x][$i]->url);
    }
}


//echo '<pre>';
//var_dump($urlArray);
//echo '</pre>';
//
//echo '<pre>';
//var_dump($newsSourceArray);
//echo '</pre>';



//echo '<pre>';
//var_dump($newsSourceArray);
//echo '</pre>';
//
//
//echo '<pre>';
//var_dump($sTweetArray);
//echo '</pre>';
//
//echo '<pre>';
//var_dump($latitudeArray);
//echo '</pre>';




//echo '<pre>';
//var_dump($tweetArray);
//echo '</pre>';


?>

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="style/normalize.css"/>
    <link rel="stylesheet" href="style/main.css"/>
    
        <link rel="stylesheet" type="text/css" href="./src/jquery.tagsinput.css" />
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script type="text/javascript" src="./src/jquery.tagsinput.js"></script>
    <!-- To test using the original jQuery.autocomplete, uncomment the following -->
    <!--
    <script type='text/javascript' src='http://xoxco.com/x/tagsinput/jquery-autocomplete/jquery.autocomplete.min.js'></script>
    <link rel="stylesheet" type="text/css" href="http://xoxco.com/x/tagsinput/jquery-autocomplete/jquery.autocomplete.css" />
    -->
    <script type='text/javascript' src='https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js'></script>
    <link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/themes/start/jquery-ui.css" />

    <script src="http://maps.googleapis.com/maps/api/js" type="text/javascript"></script>

    <title>Welcome - Movie Database</title>
</head>
<body>
<header>
    <h2>News Pulse</h2>
    <div id="headwrap">

        <p>Add tags to search for news around the world!</p>


        <form action="index.php" method="post" id="addmovieform">


            <div id="new_tags">

                <input id="tags_1" type="text" class="tags" name="tags" value="<?php
                if (isset($_POST['tags'])) {
                    // Escape any html characters
                    echo $_POST['tags'];
                }

                ?>
                "/></p>

            </div>

            <div class="input_movie">
            </div>

            <div id="searchsubmit">
                <input id="btn1" type="submit" name="search" value="Search"/>
            </div>

        </form>

    </div>

    <div id="legend">
        <div class="legend_row">
            <div class="legend_item">
                <div class="legend_color">
                    <img src="http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|5cd622">
                </div>
                <div class="legend_text">
                    <p>U.S.A</p>
                </div>
            </div>
            <div class="legend_item">
                <div class="legend_color">
                    <img src="http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|1fabf0">
                </div>
                <div class="legend_text">
                    <p>U.K</p>
                </div>
            </div>
        </div>
        <div class="legend_row">
            <div class="legend_item">
                <div class="legend_color">
                    <img src="http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|CC3232">
                </div>
                <div class="legend_text">
                    <p>Turkey</p>
                </div>
            </div>
            <div class="legend_item">
                <div class="legend_color">
                    <img src="http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|FFDB58">
                </div>
                <div class="legend_text">
                    <p>China</p>
                </div>
            </div>
        </div>
        <div class="legend_row">
            <div class="legend_item">
                <div class="legend_color">
                    <img src="http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|D1D0C6">
                </div>
                <div class="legend_text">
                    <p>Japan</p>
                </div>
            </div>
            <div class="legend_item">
                <div class="legend_color">
                    <img src="http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|F99D31">
                </div>
                <div class="legend_text">
                    <p>India</p>
                </div>
            </div>
        </div>
        <div class="legend_row">
            <div class="legend_item">
                <div class="legend_color">
                    <img src="http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|734A12">
                </div>
                <div class="legend_text">
                    <p>South Africa</p>
                </div>
            </div>
            <div class="legend_item">
                <div class="legend_color">
                    <img src="http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|7EB6FF">
                </div>
                <div class="legend_text">
                    <p>Russia</p>
                </div>
            </div>
        </div>
        <div class="legend_row">
            <div class="legend_item">
                <div class="legend_color">
                    <img src="http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|9B30FF">
                </div>
                <div class="legend_text">
                    <p>Australia</p>
                </div>
            </div>
            <div class="legend_item">
                <div class="legend_color">
                    <img src="http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|E5F2FF">
                </div>
                <div class="legend_text">
                    <p>Canada</p>
                </div>
            </div>
        </div>
        <div class="legend_row">
            <div class="legend_item">
                <div class="legend_color">
                    <img src="http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|3EA055">
                </div>
                <div class="legend_text">
                    <p>Pakistan</p>
                </div>
            </div>
            <div class="legend_item">
                <div class="legend_color">
                    <img src="http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|D1E231">
                </div>
                <div class="legend_text">
                    <p>Brazil</p>
                </div>
            </div>
        </div>

    </div>

    <img id="loading" src="https://promotions.coca-cola.com/etc/designs/promotions/img/loading.gif">

</header>
<div id="first_section" style="height: 100%;">



</div>



</body>

<script type="text/javascript">


    console.log("dasdasdasdads");

    var newsSource = [
        <?php
           foreach($newsSourceArray as $news)
           {
               echo '"'.$news.'",';
           }
        ?>
    ];

//    for(var l=0; l < newsSource.length; l++){
//        console.log(newsSource[l]);
//    }


    var latitudeArray = [
        <?php
           foreach($latitudeArray as $latitude)
           {
               echo '"'.$latitude.'",';
           }
        ?>
    ];

//    for(var l=0; l < latitudeArray.length; l++){
//        console.log(latitudeArray[l]);
//    }


    var longitudeArray = [
        <?php
           foreach($longitudeArray as $longitude)
           {
               echo '"'.$longitude.'",';
           }
        ?>
    ];


    for(var l=0; l < longitudeArray.length; l++){
        console.log(longitudeArray[l]);
    }

    var sTweetArray = [
        <?php
           foreach($sTweetArray as $twe)
           {
                echo '"'.urlencode($twe).'",';


           }
        ?>
    ];

    for(var l=0; l < sTweetArray.length; l++){
        console.log(sTweetArray[l]);
    }


    var urlArray = [
        <?php
           foreach($urlArray as $ur)
           {
               echo '"'.$ur[0].'",';
           }
        ?>
    ];


    for(var l=0; l < urlArray.length; l++){
        console.log(urlArray[l]);
    }


   
        
        
        var map = new google.maps.Map(document.getElementById('first_section'), {
            zoom: 2,
            center: new google.maps.LatLng(0,0),
            mapTypeId: google.maps.MapTypeId.ROADMAP
        });
    
    
//    var pinColor = "909090";
//    var pinImage = new google.maps.MarkerImage("http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|" + pinColor);
    var infowindow = new google.maps.InfoWindow();

            for (var i = 0; i < latitudeArray.length; i++) {
                
                    var pinColor = "909090";
                if (newsSource[i] == 'CNN' || newsSource[i] == 'Reuters' || newsSource[i] == 'NewYorkTimes' || newsSource[i] == 'BreakingNews') {
                    pinColor = '5cd622';
                }
                else if (newsSource[i] == 'Independent' || newsSource[i] == 'BBCWorld' || newsSource[i] == 'TheEconomist' || newsSource[i] == 'Conflicts') {
                    pinColor = '1fabf0';
                }
                else if (newsSource[i] == 'HDNER') {
                    pinColor = 'CC3232';
                }
                else if (newsSource[i] == 'SkyNewsAsia' || newsSource[i] == 'China Xinhua News') {
                    pinColor = 'FFDB58';
                }
                else if (newsSource[i] == 'JapanTimes') {
                    pinColor = 'D1D0C6';
                }
                else if (newsSource[i] == 'Indian Express') {
                    pinColor = 'F99D31';
                }
                else if (newsSource[i] == 'All Africa News' || newsSource[i] == 'CCTVAfrica') {
                    pinColor = '734A12';
                }
                else if (newsSource[i] == 'Russia Today') {
                    pinColor = '7EB6FF';
                }
                else if (newsSource[i] == 'The Australian') {
                    pinColor = '9B30FF';
                }
                else if (newsSource[i] == 'CBC News') {
                    pinColor = 'E5F2FF';
                }
                else if (newsSource[i] == 'Dawn') {
                    pinColor = '3EA055';
                }
                else if (newsSource[i] == 'Folha') {
                    pinColor = 'D1E231';
                }


                var pinImage = new google.maps.MarkerImage("http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|" + pinColor);
             
                
                var latlng = new google.maps.LatLng(latitudeArray[i], longitudeArray[i]);
                var stweetx = decodeURIComponent(sTweetArray[i]);
             
                stweetx = stweetx.split('+').join(' ');
                var content ='<b>' + newsSource[i] + '</b><br>' + stweetx + '<br><a href="' + urlArray[i] + '"      target="_blank" style="color:#CC0000">' + urlArray[i] + '</a><br>';
                
                
                marker = new google.maps.Marker({
                    position: latlng,
                    map: map,
                    title: content,
                    icon: pinImage,
                    animation: google.maps.Animation.DROP
                    
                });
                
                
                
   

google.maps.event.addListener(marker, 'click', function() {
    
  infowindow.setContent(this.title);
          infowindow.open(map,this);
});

        }

                                                                
                                                                



       





</script>

<script type="text/javascript">
    function onAddTag(tag) {
        alert("Added a tag: " + tag);
    }
    function onRemoveTag(tag) {
        alert("Removed a tag: " + tag);
    }
    function onChangeTag(input,tag) {
        alert("Changed a tag: " + tag);
    }
    $(function() {
        $('#tags_1').tagsInput({width:'auto'});
        $('#tags_2').tagsInput({
            width: 'auto',
            onChange: function(elem, elem_tags)
            {
                var languages = ['php','ruby','javascript'];
                $('.tag', elem_tags).each(function()
                {
                    if($(this).text().search(new RegExp('\\b(' + languages.join('|') + ')\\b')) >= 0)
                        $(this).css('background-color', 'yellow');
                });
            }
        });
        $('#tags_3').tagsInput({
            width: 'auto',
            //autocomplete_url:'test/fake_plaintext_endpoint.html' //jquery.autocomplete (not jquery ui)
            autocomplete_url:'test/fake_json_endpoint.html' // jquery ui autocomplete requires a json endpoint
        });
// Uncomment this line to see the callback functions in action
//			$('input.tags').tagsInput({onAddTag:onAddTag,onRemoveTag:onRemoveTag,onChange: onChangeTag});
// Uncomment this line to see an input with no interface for adding new tags.
//			$('input.tags').tagsInput({interactive:false});
    });
</script>
<script type="text/javascript">
    $( document ).ready(function() {
        $('#btn1').on('click',function(){

            $('#loading').css({"visibility": "visible"});
            $('#legend').css({"position": "fixed", "visibility": "hidden"});

        });
    });

</script>


</html>