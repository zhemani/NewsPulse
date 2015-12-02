<?php

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

require_once('TwitterAPIExchange.php');
echo "asdasdasda";

$settings = array(
    'oauth_access_token' => "124980145-g9l0E6Gc16WqmNtXZKp24EfnwXeMk8SLMGZgGHnb",
    'oauth_access_token_secret' => "Q4AjGuVvSd1nzDOGaDtk2hNQsK3G9KQNKysohNyGNI51W",
    'consumer_key' => "BTeBDWm1cbVvgbc7oEuiiFI7j",
    'consumer_secret' => "RoVflZPzncdwikE2jRND9GJcdBANP0PhufLBOHDMJsbXJ9BK8i"
);

$url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
$getfield = '?screen_name=conflicts';
$requestMethod = 'GET';

$twitter = new TwitterAPIExchange($settings);
$response = $twitter->setGetfield($getfield)
    ->buildOauth($url, $requestMethod)
    ->performRequest();

var_dump(json_decode($response);
?>