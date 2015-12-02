<?php
class tweet{
    
     
    public $newsSource;
    public $stweet;
    public $latitude;
    public $longitude;
    public $url;

    
    
    public function __construct($newsSource,$stweet,$latitude, $longitude, $url) {
        
        $this->newsSource = $newsSource;
        $this->stweet = $stweet;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->url = $url;
        

    }
    
    
}