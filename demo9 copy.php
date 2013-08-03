<html>
<head><title>Trending</title></head>
<body>
<?php
  
	//$proxy="10.3.100.209";
//	$port="8080";
  /* Function does a post request given the url */
  function post($url,$data){
	global $proxy;
	global $port;
	$post_string="";
	foreach($data as $key=>$value) { $post_string .= $key.'='.$value.'&'; }
	rtrim($post_string,'&');
  	$ch = curl_init(); 
  	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch,CURLOPT_POST,count($data));
	//Required for proxy
	curl_setopt($ch,CURLOPT_PROXYPORT,$port);
	curl_setopt($ch, CURLOPT_PROXY, $proxy);
	
	curl_setopt($ch,CURLOPT_POSTFIELDS,$post_string); 
  	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
  	$output = curl_exec($ch); 
  	curl_close($ch);
  	return $output;
 }
 
 /* Functon does a get request of the given url */
 function get($url){
	global $proxy;
        global $port;
	$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
  	curl_setopt($ch,CURLOPT_PROXYPORT,$port);
        curl_setopt($ch, CURLOPT_PROXY, $proxy);
	$output = curl_exec($ch); 
  	curl_close($ch);
  	return $output;
 }
 

	
  //The default place is set to bangalore
 $place="Bangalore";
 if(isset($_GET["place"])){
 	$place=$_GET["place"];
 }

 // Get the woeid from the Yahoo! placemaker API

 $placemaker_url="http://wherein.yahooapis.com/v1/document";
 $post_data=array();
 $post_data["app_id"]="pxgFEq3c"; //Required for accessing the api
 $post_data["documentContent"]=$place;
 $post_data["documentType"]="text/plain";

 //Get the information as XML
 $xmlstring=post($placemaker_url,$post_data);
print_r($xmlstring);
 $xml=simplexml_load_string($xmlstring);
 


//Get the woeid
 $woeid=$xml->document->geographicScope->woeId;
 if(!isset($woeid)){
 	echo "Woeid not found for the place";
	exit(1);
 }
 echo "<h1> The trending for the place $place with woeid $woeid </h1>";

	
 //Get the trending information using the Twitter API
 //$twitter_url="http://api.twitter.com/1/trends/$woeid.json";
	$twitter_url="https://api.twitter.com/1.1/trends/place.json?id=".$woeid."&oauth_consumer_key=DwHpXoO7l1NCpaTYEWm2g&oauth_nonce=D802df1a2b541e53d608c02bb47e09f78&oauth_signature_method=DHMAC-SHA1&oauth_timestamp=D1375348557&oauth_token=Dtest123&oauth_version=D1.0";
 echo "The url for retrieval is $twitter_url";
 $output=get($twitter_url);
 
//echo $output;

 // Parse the JSON data returned from the API
 $json_data=json_decode($output,true);
 if(isset($json_data["error"])){
	echo "<h3>No trending available for the place</h3>";
}
else{
 $trends=$json_data[0]["trends"];
 	echo "<ul>";
 	foreach($json_data[0]["trends"] as $trend){
		echo "<li><a href='".$trend["url"]."'>".$trend["name"] ."</a></li>";
 	}
echo "</ul>";
}
?>
</body>
</html>
