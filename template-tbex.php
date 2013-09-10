<?php
/*
Template Name: TBEX Live
*/
?>

<?php get_template_part( 'header' ); ?>

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

<?php //FUNCTIONS

//global vars
$eventlogo = get_post_meta($post->ID, 'eventlogo', true);
$eventstartdate = get_post_meta($post->ID, 'eventstartdate', true);
$eventenddate = get_post_meta($post->ID, 'eventenddate', true);

//foursquare vars
$foursquarelocation = get_post_meta($post->ID, 'foursquarelocation', true);

//twitter vars
$twitterhashtag = get_post_meta($post->ID, 'twitterhashtag', true);
$twitterexclusions = get_post_meta($post->ID, 'twitterexclusions', true);

//livestream vars
$ustreamembed = get_post_meta($post->ID, 'ustreamembed', true);
$currentspeaker = get_post_meta($post->ID, 'currentspeaker', true);
$nextspeaker = get_post_meta($post->ID, 'nextspeaker', true);
$ustreamchat = get_post_meta($post->ID, 'ustreamchat', true);

//announcement vars
$organisertwitter = get_post_meta($post->ID, 'organisertwitter', true);
$weatherlocation = get_post_meta($post->ID, 'weatherlocation', true);
$announcementinfo = get_post_meta($post->ID, 'announcementinfo', true);

//instagram vars
$instagramhashtag = get_post_meta($post->ID, 'instagramhashtag', true);

class Instagram {

 	private $client_id;
 	private $access_token;
 	private $endpoint = 'https://api.instagram.com/v1/';

 	public function __construct($cfg){
 		if( array_key_exists('client_id', $cfg) ){
 			$this->client_id = $cfg['client_id'];
 		}
 		
 		if( array_key_exists('access_token', $cfg) ){
 			$this->access_token = $cfg['access_token'];
 		}
 	}

 	private function request($endpoint, $params = array()){
 		$request = $this->buildRequest($endpoint, $params);
 		return $this->sendRequest($request);
 	}

 	public function buildRequest($endpoint = '', $params = ''){
 		$endpoint = $this->endpoint . $endpoint . '?';

 		if($this->client_id) {
 			$endpoint = $endpoint . 'client_id=' . $this->client_id . '&';
 		}

 		if($this->access_token) {
 			$endpoint = $endpoint . 'access_token=' . $this->access_token . '&';
 		}

 		if($params) {
 			$endpoint = $endpoint . http_build_query($params);
 		}

 		return $endpoint;
 	}

 	private function sendRequest($uri){
 		$curl = curl_init($uri);
 		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
 		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
 		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
 		$response = curl_exec($curl);
 		curl_close($curl);
 		return $response;
 	}

 	public function tags_media_recent($tag, $params = array()){
 		return $this->request('tags/' . $tag . '/media/recent', $params);
 	}

 }

if ( ! wp_next_scheduled('tweet_count_hook') ) {
	wp_schedule_event( time(), 'hourly', 'tweet_count_hook' );
}
add_action( 'tweet_count_hook', 'get_tweet_count' );

function get_tweet_count() {
	add_post_meta(get_the_ID(), '_twitter_count', 0, true);
	$twitter_count = get_post_meta($post->ID, '_twitter_count');
	$twitter_count = @unserialize($twitter_count[0]);

	$url = "http://search.twitter.com/search.json?q=" . urlencode("tbex") . "&callback=?&rpp=100";
	$curl = curl_init();
	curl_setopt( $curl, CURLOPT_URL, $url );
	curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
	$result = curl_exec( $curl );
	curl_close( $curl );
	$data = json_decode( $result, true );
	
	if (count($twitter_count) != 0) {
		for ($i = 0; $i < sizeof($data['results']); $i++)
			$twitter_count[] = $data['results'][$i]["id_str"];
		
		$twitter_count = array_unique($twitter_count);
		sort($twitter_count);
		$twitter_count = serialize($twitter_count);
		update_post_meta($post->ID, '_twitter_count', $twitter_count);
	}
}

$twitterCount = get_post_meta($post->ID, '_twitter_count');
$twitterCount = @unserialize($twitterCount[0]);
$twitterCount = count($twitterCount);


if ( ! wp_next_scheduled('photo_count_hook') ) {
	wp_schedule_event( time(), 'hourly', 'photo_count_hook' );
}
add_action( 'photo_count_hook', 'get_photo_count' );

function get_photo_count() { 
	add_post_meta(get_the_ID(), '_instagram_count', 0, true);
	$instagram_count = get_post_meta($post->ID, '_instagram_count');
	$instagram_count = @unserialize($instagram_count[0]);

	 $instagram = new Instagram(array(
		'client_id' => '4dbe36aac4c943b9b260f078f33b4edc',
		'access_token' => '1685551.4dbe36a.1970e3a32fff4545a4e3ceded4e059ee'

	));
	$result = $instagram->tags_media_recent('tbex');
	$data = json_decode( $result, true );
	if (count($instagram_count) != 0) {
		for ($i = 0; $i < sizeof($data['data']); $i++)
			$instagram_count[] = $data['data'][$i]["id"];
		
		$instagram_count = array_unique($instagram_count);
		sort($instagram_count);
		$instagram_count = serialize($instagram_count);
		update_post_meta($post->ID, '_instagram_count', $instagram_count);
	}
}

$instagramCount = get_post_meta($post->ID, '_instagram_count');
$instagramCount = @unserialize($instagramCount[0]);
$instagramCount = count($instagramCount);

?>

<style type="text/css">

	body {
		background: #222;
		color: #fff;
		text-shadow: #111 0 1px 3px;
	}
	#breadcrumbs, #site-navigation {
		display: none;
	}
	form#searchform input {
		width: 180px;
	}
	.col-3, .col-6, .col-12 {
		position:relative; float:left; margin-right:20px;
	}
	#main .wrap, #top .wrap, .col-12 {
		width: 1200px;
	}
	.col-3 {
		width: 230px;
	}
	.col-6 {
		width: 700px;
	}
	#the-post {
		width: 680px;
		padding: 10px;
	}
	.box {
		box-shadow: 0px 1px 15px #000;
		-webkit-box-shadow: 0px 1px 15px #000;
		-moz-box-shadow: 0px 1px 15px #000;
		-o-box-shadow: 0px 1px 15px #000;
		background: #fff;
		margin-bottom: 20px;
		color: #222;
		text-shadow: none;
	}
	
	#eventlogo {
		position: absolute;
		top: -83px;
		left: 520px;
		z-index: 999;
	}
	
	#livevideo {
		padding: 10px;
	}
	#livechat {
		height: 300px;
		overflow: hidden;
	}
	h2 {
		font-size: 40px;
		color: #fff;
		margin:0 0 10px 0;
	}
	.subtext {
		text-transform: uppercase;
		font-weight: bold;
		margin: 0 0 10px 0;
		display: block;
	}
	.green {color: #b9d342;}
	.blue {color: #42a0e1;}
	.yellow {color: #f8bc36;}
	.pink {color: #e85283;}
	
	#foursquare {
		color: #fff;
	}
	#hotspots {
		list-style: none;
		font-weight: bold;
	}
	#hotspots li {
		line-height: 23px;
	}
	#hotspots a {
		font-weight: normal;
		text-decoration: none;
		color: #b9d342;
	}
	#hotspots a:hover {
		border-bottom: 1px dashed #b9d342;
	}
	#twitter {
		
	}
	#twitter a {
		font-weight: normal;
		text-decoration: none;
		color: #42a0e1;
	}
	#twitter a:hover {
		border-bottom: 1px dashed #42a0e1;
	}
	#hottopics {
		margin-bottom: 10px;
	}
	.tweet {
		width: 210px;
		padding: 10px;
		font-size: 12px;
		display: none;
	}
	#twitter .tweet .twitteruser {
		display: block;
		font-size: 13px;
		line-height: 13px;
		font-weight: bold;
		margin-bottom: 3px;
		position: relative;
		top: -2px;
	}
	#twitter .tweet .twitteruser:hover {
		border: none;
	}
	.tweet .tweetcontent {
		width: 150px;
		float: right;
		line-height: 16px;
		display: block;
	}
	.tweet .tweettext {
		display: block;
		margin-bottom: 1px;
	}
	#twitter .tweet .timestamp {
		color: #898989;
		font-size: 11px;
		display: block;
	}
	#twitter .tweet .timestamp:hover {
		border: none;
		color: #42a0e1;
	}
	#announcements a {
		color: #e85283;
		text-decoration: none;
	}
	#announcements a:hover {
		border-bottom: 1px dashed #e85283;
	}
	#announcetweet {
		margin-bottom: 30px;
	}
	#announcetweet a {
		font-size: 12px;
	}
	#weather {
		font-weight: bold;
		margin-bottom: 30px;
	}
	
	#foursquare, #announcements {
		height: 350px;
	}
	
	#map {
		width: 100%;
		height: 400px;
		background:#111;
	}
	#center {
		top: -100px;
	}
	#nowonstage {
		font-family: 'League Gothic', 'Helvetica Neue', Helvetica, Arial, sans-serif;
		font-size: 30px;
		text-transform: uppercase;
		display: block;
		margin-top: 15px;
	}
	#nextonstage {
		display: block;
		margin-top: 5px;
	}
	#instagramfeed article { display: none; }
	#instagramfeed article a {
		line-height: 0;
		margin: 0;
		display: block;
		padding: 5px;
	}

	/*********** Leaflet Start ***********/

	/* required styles */
	.leaflet-map-pane,
	.leaflet-tile,
	.leaflet-marker-icon,
	.leaflet-marker-shadow,
	.leaflet-tile-pane,
	.leaflet-overlay-pane,
	.leaflet-shadow-pane,
	.leaflet-marker-pane,
	.leaflet-popup-pane,
	.leaflet-overlay-pane svg,
	.leaflet-zoom-box,
	.leaflet-image-layer { /* TODO optimize classes */
		position: absolute;
		}
	.leaflet-container {
		overflow: hidden;
		}
	.leaflet-tile-pane, .leaflet-container {
		-webkit-transform: translate3d(0,0,0);
		}
	.leaflet-tile,
	.leaflet-marker-icon,
	.leaflet-marker-shadow {
		-moz-user-select: none;
		-webkit-user-select: none;
		user-select: none;
		}
	.leaflet-marker-icon,
	.leaflet-marker-shadow {
		display: block;
		}
	.leaflet-clickable {
		cursor: pointer;
		}
	.leaflet-container img {
		max-width: none !important;
		}

	.leaflet-tile-pane { z-index: 2; }

	.leaflet-objects-pane { z-index: 3; }
	.leaflet-overlay-pane { z-index: 4; }
	.leaflet-shadow-pane { z-index: 5; }
	.leaflet-marker-pane { z-index: 6; }
	.leaflet-popup-pane { z-index: 7; }

	.leaflet-zoom-box {
		width: 0;
		height: 0;
		}

	.leaflet-tile {
		visibility: hidden;
		}
	.leaflet-tile-loaded {
		visibility: inherit;
		}

	a.leaflet-active {
		outline: 2px solid orange;
		}


	/* Leaflet controls */

	.leaflet-control {
		position: relative;
		z-index: 7;
		}
	.leaflet-top,
	.leaflet-bottom {
		position: absolute;
		}
	.leaflet-top {
		top: 0;
		}
	.leaflet-right {
		right: 0;
		}
	.leaflet-bottom {
		bottom: 0;
		}
	.leaflet-left {
		left: 0;
		}
	.leaflet-control {
		float: left;
		clear: both;
		}
	.leaflet-right .leaflet-control {
		float: right;
		}
	.leaflet-top .leaflet-control {
		margin-top: 10px;
		}
	.leaflet-bottom .leaflet-control {
		margin-bottom: 10px;
		}
	.leaflet-left .leaflet-control {
		margin-left: 10px;
		}
	.leaflet-right .leaflet-control {
		margin-right: 10px;
		}

	.leaflet-control-zoom, .leaflet-control-layers {
		-moz-border-radius: 7px;
		-webkit-border-radius: 7px;
		border-radius: 7px;
		}
	.leaflet-control-zoom {
		padding: 5px;
		background: rgba(0, 0, 0, 0.25);
		}
	.leaflet-control-zoom a {
		background-color: rgba(255, 255, 255, 0.75);
		}
	.leaflet-control-zoom a, .leaflet-control-layers a {
		background-position: 50% 50%;
		background-repeat: no-repeat;
		display: block;
		}
	.leaflet-control-zoom a {
		-moz-border-radius: 4px;
		-webkit-border-radius: 4px;
		border-radius: 4px;
		width: 19px;
		height: 19px;
		}
	.leaflet-control-zoom a:hover {
		background-color: #fff;
		}
	.leaflet-big-buttons .leaflet-control-zoom a {
		width: 27px;
		height: 27px;
		}
	.leaflet-control-zoom-in {
		background-image: url(images/zoom-in.png);
		margin-bottom: 5px;
		}
	.leaflet-control-zoom-out {
		background-image: url(images/zoom-out.png);
		}

	.leaflet-control-layers {
		-moz-box-shadow: 0 0 7px #999;
		-webkit-box-shadow: 0 0 7px #999;
		box-shadow: 0 0 7px #999;

		background: #f8f8f9;
		}
	.leaflet-control-layers a {
		background-image: url(images/layers.png);
		width: 36px;
		height: 36px;
		}
	.leaflet-big-buttons .leaflet-control-layers a {
		width: 44px;
		height: 44px;
		}
	.leaflet-control-layers .leaflet-control-layers-list,
	.leaflet-control-layers-expanded .leaflet-control-layers-toggle {
		display: none;
		}
	.leaflet-control-layers-expanded .leaflet-control-layers-list {
		display: block;
		position: relative;
		}
	.leaflet-control-layers-expanded {
		padding: 6px 10px 6px 6px;
		font: 12px/1.5 "Helvetica Neue", Arial, Helvetica, sans-serif;
		color: #333;
		background: #fff;
		}
	.leaflet-control-layers input {
		margin-top: 2px;
		position: relative;
		top: 1px;
		}
	.leaflet-control-layers label {
		display: block;
		}
	.leaflet-control-layers-separator {
		height: 0;
		border-top: 1px solid #ddd;
		margin: 5px -10px 5px -6px;
		}

	.leaflet-container .leaflet-control-attribution {
		margin: 0;
		padding: 0 5px;

		font: 11px/1.5 "Helvetica Neue", Arial, Helvetica, sans-serif;
		color: #333;

		background-color: rgba(255, 255, 255, 0.7);

		-moz-box-shadow: 0 0 7px #ccc;
		-webkit-box-shadow: 0 0 7px #ccc;
		box-shadow: 0 0 7px #ccc;
		}


	/* Fade animations */

	.leaflet-fade-anim .leaflet-tile {
		opacity: 0;

		-webkit-transition: opacity 0.2s linear;
		-moz-transition: opacity 0.2s linear;
		-o-transition: opacity 0.2s linear;
		transition: opacity 0.2s linear;
		}
	.leaflet-fade-anim .leaflet-tile-loaded {
		opacity: 1;
		}

	.leaflet-fade-anim .leaflet-popup {
		opacity: 0;

		-webkit-transition: opacity 0.2s linear;
		-moz-transition: opacity 0.2s linear;
		-o-transition: opacity 0.2s linear;
		transition: opacity 0.2s linear;
		}
	.leaflet-fade-anim .leaflet-map-pane .leaflet-popup {
		opacity: 1;
		}

	.leaflet-zoom-anim .leaflet-tile {
		-webkit-transition: none;
		-moz-transition: none;
		-o-transition: none;
		transition: none;
		}

	.leaflet-zoom-anim .leaflet-objects-pane {
		visibility: hidden;
		}


	/* Popup layout */

	.leaflet-popup {
		position: absolute;
		text-align: center;
		-webkit-transform: translate3d(0,0,0);
		}
	.leaflet-popup-content-wrapper {
		padding: 1px;
		text-align: left;
		}
	.leaflet-popup-content {
		margin: 19px;
		}
	.leaflet-popup-tip-container {
		margin: 0 auto;
		width: 40px;
		height: 16px;
		position: relative;
		overflow: hidden;
		}
	.leaflet-popup-tip {
		width: 15px;
		height: 15px;
		padding: 1px;

		margin: -8px auto 0;

		-moz-transform: rotate(45deg);
		-webkit-transform: rotate(45deg);
		-ms-transform: rotate(45deg);
		-o-transform: rotate(45deg);
		transform: rotate(45deg);
		}
	.leaflet-popup-close-button {
		position: absolute;
		top: 9px;
		right: 9px;

		width: 10px;
		height: 10px;

		overflow: hidden;
		}
	.leaflet-popup-content p {
		margin: 18px 0;
		}


	/* Visual appearance */

	.leaflet-container {
		background: #ddd;
		}
	.leaflet-container a {
		color: #0078A8;
		}
	.leaflet-zoom-box {
		border: 2px dotted #05f;
		background: white;
		opacity: 0.5;
		}
	.leaflet-popup-content-wrapper, .leaflet-popup-tip {
		background: white;

		box-shadow: 0 1px 10px #888;
		-moz-box-shadow: 0 1px 10px #888;
		 -webkit-box-shadow: 0 1px 14px #999;
		}
	.leaflet-popup-content-wrapper {
		-moz-border-radius: 20px;
		-webkit-border-radius: 20px;
		border-radius: 20px;
		}
	.leaflet-popup-content {
		font: 12px/1.4 "Helvetica Neue", Arial, Helvetica, sans-serif;
		}
	.leaflet-popup-close-button {
		background: white url(images/popup-close.png);
		}

	
	/*@media only screen and (max-width: 1200px) {
		#main .wrap, #top .wrap, .col-12 {
			width: 700px;
		}
		.col-3, form#searchform input {
			display: none;
		}
		.col-6 {
			margin: 0;
		}
	}*/
	
</style>



<img id="eventlogo" src="http://f.cl.ly/items/182j1h1Y0d0K353S1T2E/live.gif" alt="" />

</div>

	<section id="map" style="position:relative;overflow:hidden;">
	</section>
	
<div class="wrap">
		
	<section id="content" class="col-12">
		
		<div id="left" class="col-3">
			
			<section id="foursquare">
				<h2><span class="green"><?php echo $instagramCount; ?></span> Check-ins</h2>
				<span class="subtext">Current Hot Spots:</span>
				<ul id="hotspots"></ul>
			</section>
			
			<section id="twitter">
				<h2><span class="blue"><?php echo $twitterCount; ?></span> Tweets</h2>
				<span class="subtext">Current Hot Topics:</span>
				<div id="hottopics"></div>
				<div id="twitterstream"></div>
			</section>
			
		</div><!--left-->
		
		<div id="center" class="col-6">
		
			<section id="livestream">
			
				<div id="livevideo" class="box">
					<iframe src="http://www.ustream.tv/embed/11189490" width="680" height="409" scrolling="no" frameborder="0" style="border: 0px none transparent;"></iframe>
					<span id="nowonstage"><span class="pink">Now</span>: Christopher Baker / Closing Keynote</span>
					<span id="nextonstage"><span class="blue"><b>Next</b></span>: -End-</span>
				</div>
				
				<div id="livechat" class="box">
					<iframe width="700" scrolling="no" height="300" frameborder="0" style="border: 0px none transparent;" src="http://www.ustream.tv/socialstream/11189490"></iframe>
				</div>
			
			</section>
					
			<article id="the-post" <?php post_class( 'box' ); ?>>
				<section class="entry-content">
					<?php the_content(); ?>
				</section>
			</article>
		
		</div><!--center-->
		
		<div id="right" class="col-3 last">
			
			<section id="announcements">
				<h2><span class="pink">Announcements</span></h2>
				<div id="announcetweet">
					Ok everyone, we’re running about 5 minutes ahead of schedule. Hang tight, find a seat, and get ready for the day ahead! <a href="#">3 minutes ago</a>
				</div>
				<div id="weather">
					The forecast for today is <a href="http://www.weather.com/weather/hourbyhour/graph/80435" class="weatherclass">sun</a>! For now. Might need a raincoat after lunch. 
				</div>
				<div id="eventinfo">
					
				</div>
			</section>
			
			<section id="instagram">
				<h2><span class="yellow"><?php echo $instagramCount; ?></span> Photos</h2>
				<div id="instagramfeed"></div>
			</section>
			
		</div><!--right-->
		
	</section><!-- #content -->
	
<?php endwhile; endif; ?>

<script type="tweetBlock" id="tweetBlock">
	<article class="tweet box">
	  <img src="{{profile_image_url}}" width="48" alt="" />
	  <div class="tweetcontent">
	    <a class="twitteruser" href="{{profile}}" target="_blank">@{{from_user}}</a>
	    <span class="tweettext">{{text}}</span>
	    <a class="timestamp" href="{{permalink}}" target="_blank">{{created_at}}</a>
	  </div>
	</article>
</script>

<script type="photoBlock" id="photoBlock">
	<article class="photo box">
		<a href="{{link}}"><img src="{{low_resolution}}" alt="" width="220" /></a>
	</article>
</script>

<script type="venueBlock" id="venueBlock">
	<li><a href="#">{{name}}</a> {{checkins}}</li>
</script>

<script type="text/javascript">
/*
 Copyright (c) 2010-2011, CloudMade, Vladimir Agafonkin
 Leaflet is a modern open-source JavaScript library for interactive maps.
 http://leaflet.cloudmade.com
*/
(function(a){a.L={VERSION:"0.3",ROOT_URL:a.L_ROOT_URL||function(){var a=document.getElementsByTagName("script"),b=/\/?leaflet[\-\._]?([\w\-\._]*)\.js\??/,c,d,e,f;for(c=0,d=a.length;c<d;c++){e=a[c].src,f=e.match(b);if(f)return f[1]==="include"?"../../dist/":e.split(b)[0]+"/"}return""}(),noConflict:function(){return a.L=this._originalL,this},_originalL:a.L}})(this),L.Util={extend:function(a){var b=Array.prototype.slice.call(arguments,1);for(var c=0,d=b.length,e;c<d;c++){e=b[c]||{};for(var f in e)e.hasOwnProperty(f)&&(a[f]=e[f])}return a},bind:function(a,b){return function(){return a.apply(b,arguments)}},stamp:function(){var a=0,b="_leaflet_id";return function(c){return c[b]=c[b]||++a,c[b]}}(),requestAnimFrame:function(){function a(a){window.setTimeout(a,1e3/60)}var b=window.requestAnimationFrame||window.webkitRequestAnimationFrame||window.mozRequestAnimationFrame||window.oRequestAnimationFrame||window.msRequestAnimationFrame||a;return function(c,d,e,f){c=d?L.Util.bind(c,d):c,e&&b===a?c():b(c,f)}}(),limitExecByInterval:function(a,b,c){function g(){d=!1,e&&(f.callee.apply(c,f),e=!1)}var d,e,f;return function(){f=arguments,d?e=!0:(d=!0,setTimeout(g,b),a.apply(c,f))}},falseFn:function(){return!1},formatNum:function(a,b){var c=Math.pow(10,b||5);return Math.round(a*c)/c},setOptions:function(a,b){a.options=L.Util.extend({},a.options,b)},getParamString:function(a){var b=[];for(var c in a)a.hasOwnProperty(c)&&b.push(c+"="+a[c]);return"?"+b.join("&")},template:function(a,b){return a.replace(/\{ *([\w_]+) *\}/g,function(a,c){var d=b[c];if(!b.hasOwnProperty(c))throw Error("No value provided for variable "+a);return d})}},L.Class=function(){},L.Class.extend=function(a){var b=function(){this.initialize&&this.initialize.apply(this,arguments)},c=function(){};c.prototype=this.prototype;var d=new c;d.constructor=b,b.prototype=d,b.superclass=this.prototype;for(var e in this)this.hasOwnProperty(e)&&e!=="prototype"&&e!=="superclass"&&(b[e]=this[e]);return a.statics&&(L.Util.extend(b,a.statics),delete a.statics),a.includes&&(L.Util.extend.apply(null,[d].concat(a.includes)),delete a.includes),a.options&&d.options&&(a.options=L.Util.extend({},d.options,a.options)),L.Util.extend(d,a),b.extend=L.Class.extend,b.include=function(a){L.Util.extend(this.prototype,a)},b},L.Mixin={},L.Mixin.Events={addEventListener:function(a,b,c){var d=this._leaflet_events=this._leaflet_events||{};return d[a]=d[a]||[],d[a].push({action:b,context:c||this}),this},hasEventListeners:function(a){var b="_leaflet_events";return b in this&&a in this[b]&&this[b][a].length>0},removeEventListener:function(a,b,c){if(!this.hasEventListeners(a))return this;for(var d=0,e=this._leaflet_events,f=e[a].length;d<f;d++)if(e[a][d].action===b&&(!c||e[a][d].context===c))return e[a].splice(d,1),this;return this},fireEvent:function(a,b){if(!this.hasEventListeners(a))return this;var c=L.Util.extend({type:a,target:this},b),d=this._leaflet_events[a].slice();for(var e=0,f=d.length;e<f;e++)d[e].action.call(d[e].context||this,c);return this}},L.Mixin.Events.on=L.Mixin.Events.addEventListener,L.Mixin.Events.off=L.Mixin.Events.removeEventListener,L.Mixin.Events.fire=L.Mixin.Events.fireEvent,function(){var a=navigator.userAgent.toLowerCase(),b=!!window.ActiveXObject,c=a.indexOf("webkit")!==-1,d=typeof orientation!="undefined"?!0:!1,e=a.indexOf("android")!==-1,f=window.opera;L.Browser={ie:b,ie6:b&&!window.XMLHttpRequest,webkit:c,webkit3d:c&&"WebKitCSSMatrix"in window&&"m11"in new window.WebKitCSSMatrix,gecko:a.indexOf("gecko")!==-1,opera:f,android:e,mobileWebkit:d&&c,mobileOpera:d&&f,mobile:d,touch:function(){var a=!1,b="ontouchstart";if(b in document.documentElement)return!0;var c=document.createElement("div");return!c.setAttribute||!c.removeAttribute?!1:(c.setAttribute(b,"return;"),typeof c[b]=="function"&&(a=!0),c.removeAttribute(b),c=null,a)}()}}(),L.Point=function(a,b,c){this.x=c?Math.round(a):a,this.y=c?Math.round(b):b},L.Point.prototype={add:function(a){return this.clone()._add(a)},_add:function(a){return this.x+=a.x,this.y+=a.y,this},subtract:function(a){return this.clone()._subtract(a)},_subtract:function(a){return this.x-=a.x,this.y-=a.y,this},divideBy:function(a,b){return new L.Point(this.x/a,this.y/a,b)},multiplyBy:function(a){return new L.Point(this.x*a,this.y*a)},distanceTo:function(a){var b=a.x-this.x,c=a.y-this.y;return Math.sqrt(b*b+c*c)},round:function(){return this.clone()._round()},_round:function(){return this.x=Math.round(this.x),this.y=Math.round(this.y),this},clone:function(){return new L.Point(this.x,this.y)},toString:function(){return"Point("+L.Util.formatNum(this.x)+", "+L.Util.formatNum(this.y)+")"}},L.Bounds=L.Class.extend({initialize:function(a,b){if(!a)return;var c=a instanceof Array?a:[a,b];for(var d=0,e=c.length;d<e;d++)this.extend(c[d])},extend:function(a){!this.min&&!this.max?(this.min=new L.Point(a.x,a.y),this.max=new L.Point(a.x,a.y)):(this.min.x=Math.min(a.x,this.min.x),this.max.x=Math.max(a.x,this.max.x),this.min.y=Math.min(a.y,this.min.y),this.max.y=Math.max(a.y,this.max.y))},getCenter:function(a){return new L.Point((this.min.x+this.max.x)/2,(this.min.y+this.max.y)/2,a)},contains:function(a){var b,c;return a instanceof L.Bounds?(b=a.min,c=a.max):b=c=a,b.x>=this.min.x&&c.x<=this.max.x&&b.y>=this.min.y&&c.y<=this.max.y},intersects:function(a){var b=this.min,c=this.max,d=a.min,e=a.max,f=e.x>=b.x&&d.x<=c.x,g=e.y>=b.y&&d.y<=c.y;return f&&g}}),L.Transformation=L.Class.extend({initialize:function(a,b,c,d){this._a=a,this._b=b,this._c=c,this._d=d},transform:function(a,b){return this._transform(a.clone(),b)},_transform:function(a,b){return b=b||1,a.x=b*(this._a*a.x+this._b),a.y=b*(this._c*a.y+this._d),a},untransform:function(a,b){return b=b||1,new L.Point((a.x/b-this._b)/this._a,(a.y/b-this._d)/this._c)}}),L.DomUtil={get:function(a){return typeof a=="string"?document.getElementById(a):a},getStyle:function(a,b){var c=a.style[b];!c&&a.currentStyle&&(c=a.currentStyle[b]);if(!c||c==="auto"){var d=document.defaultView.getComputedStyle(a,null);c=d?d[b]:null}return c==="auto"?null:c},getViewportOffset:function(a){var b=0,c=0,d=a,e=document.body;do{b+=d.offsetTop||0,c+=d.offsetLeft||0;if(d.offsetParent===e&&L.DomUtil.getStyle(d,"position")==="absolute")break;d=d.offsetParent}while(d);d=a;do{if(d===e)break;b-=d.scrollTop||0,c-=d.scrollLeft||0,d=d.parentNode}while(d);return new L.Point(c,b)},create:function(a,b,c){var d=document.createElement(a);return d.className=b,c&&c.appendChild(d),d},disableTextSelection:function(){document.selection&&document.selection.empty&&document.selection.empty(),this._onselectstart||(this._onselectstart=document.onselectstart,document.onselectstart=L.Util.falseFn)},enableTextSelection:function(){document.onselectstart=this._onselectstart,this._onselectstart=null},hasClass:function(a,b){return a.className.length>0&&RegExp("(^|\\s)"+b+"(\\s|$)").test(a.className)},addClass:function(a,b){L.DomUtil.hasClass(a,b)||(a.className+=(a.className?" ":"")+b)},removeClass:function(a,b){a.className=a.className.replace(/(\S+)\s*/g,function(a,c){return c===b?"":a}).replace(/^\s+/,"")},setOpacity:function(a,b){L.Browser.ie?a.style.filter="alpha(opacity="+Math.round(b*100)+")":a.style.opacity=b},testProp:function(a){var b=document.documentElement.style;for(var c=0;c<a.length;c++)if(a[c]in b)return a[c];return!1},getTranslateString:function(a){return L.DomUtil.TRANSLATE_OPEN+a.x+"px,"+a.y+"px"+L.DomUtil.TRANSLATE_CLOSE},getScaleString:function(a,b){var c=L.DomUtil.getTranslateString(b),d=" scale("+a+") ",e=L.DomUtil.getTranslateString(b.multiplyBy(-1));return c+d+e},setPosition:function(a,b){a._leaflet_pos=b,L.Browser.webkit3d?(a.style[L.DomUtil.TRANSFORM]=L.DomUtil.getTranslateString(b),L.Browser.android&&(a.style["-webkit-perspective"]="1000",a.style["-webkit-backface-visibility"]="hidden")):(a.style.left=b.x+"px",a.style.top=b.y+"px")},getPosition:function(a){return a._leaflet_pos}},L.Util.extend(L.DomUtil,{TRANSITION:L.DomUtil.testProp(["transition","webkitTransition","OTransition","MozTransition","msTransition"]),TRANSFORM:L.DomUtil.testProp(["transformProperty","WebkitTransform","OTransform","MozTransform","msTransform"]),TRANSLATE_OPEN:"translate"+(L.Browser.webkit3d?"3d(":"("),TRANSLATE_CLOSE:L.Browser.webkit3d?",0)":")"}),L.LatLng=function(a,b,c){var d=parseFloat(a),e=parseFloat(b);if(isNaN(d)||isNaN(e))throw Error("Invalid LatLng object: ("+a+", "+b+")");c!==!0&&(d=Math.max(Math.min(d,90),-90),e=(e+180)%360+(e<-180||e===180?180:-180)),this.lat=d,this.lng=e},L.Util.extend(L.LatLng,{DEG_TO_RAD:Math.PI/180,RAD_TO_DEG:180/Math.PI,MAX_MARGIN:1e-9}),L.LatLng.prototype={equals:function(a){if(a instanceof L.LatLng){var b=Math.max(Math.abs(this.lat-a.lat),Math.abs(this.lng-a.lng));return b<=L.LatLng.MAX_MARGIN}return!1},toString:function(){return"LatLng("+L.Util.formatNum(this.lat)+", "+L.Util.formatNum(this.lng)+")"},distanceTo:function(a){var b=6378137,c=L.LatLng.DEG_TO_RAD,d=(a.lat-this.lat)*c,e=(a.lng-this.lng)*c,f=this.lat*c,g=a.lat*c,h=Math.sin(d/2),i=Math.sin(e/2),j=h*h+i*i*Math.cos(f)*Math.cos(g);return b*2*Math.atan2(Math.sqrt(j),Math.sqrt(1-j))}},L.LatLngBounds=L.Class.extend({initialize:function(a,b){if(!a)return;var c=a instanceof Array?a:[a,b];for(var d=0,e=c.length;d<e;d++)this.extend(c[d])},extend:function(a){!this._southWest&&!this._northEast?(this._southWest=new L.LatLng(a.lat,a.lng,!0),this._northEast=new L.LatLng(a.lat,a.lng,!0)):(this._southWest.lat=Math.min(a.lat,this._southWest.lat),this._southWest.lng=Math.min(a.lng,this._southWest.lng),this._northEast.lat=Math.max(a.lat,this._northEast.lat),this._northEast.lng=Math.max(a.lng,this._northEast.lng))},getCenter:function(){return new L.LatLng((this._southWest.lat+this._northEast.lat)/2,(this._southWest.lng+this._northEast.lng)/2)},getSouthWest:function(){return this._southWest},getNorthEast:function(){return this._northEast},getNorthWest:function(){return new L.LatLng(this._northEast.lat,this._southWest.lng,!0)},getSouthEast:function(){return new L.LatLng(this._southWest.lat,this._northEast.lng,!0)},contains:function(a){var b=this._southWest,c=this._northEast,d,e;return a instanceof L.LatLngBounds?(d=a.getSouthWest(),e=a.getNorthEast()):d=e=a,d.lat>=b.lat&&e.lat<=c.lat&&d.lng>=b.lng&&e.lng<=c.lng},intersects:function(a){var b=this._southWest,c=this._northEast,d=a.getSouthWest(),e=a.getNorthEast(),f=e.lat>=b.lat&&d.lat<=c.lat,g=e.lng>=b.lng&&d.lng<=c.lng;return f&&g},toBBoxString:function(){var a=this._southWest,b=this._northEast;return[a.lng,a.lat,b.lng,b.lat].join(",")}}),L.Projection={},L.Projection.SphericalMercator={MAX_LATITUDE:85.0511287798,project:function(a){var b=L.LatLng.DEG_TO_RAD,c=this.MAX_LATITUDE,d=Math.max(Math.min(c,a.lat),-c),e=a.lng*b,f=d*b;return f=Math.log(Math.tan(Math.PI/4+f/2)),new L.Point(e,f)},unproject:function(a,b){var c=L.LatLng.RAD_TO_DEG,d=a.x*c,e=(2*Math.atan(Math.exp(a.y))-Math.PI/2)*c;return new L.LatLng(e,d,b)}},L.Projection.LonLat={project:function(a){return new L.Point(a.lng,a.lat)},unproject:function(a,b){return new L.LatLng(a.y,a.x,b)}},L.CRS={latLngToPoint:function(a,b){var c=this.projection.project(a);return this.transformation._transform(c,b)},pointToLatLng:function(a,b,c){var d=this.transformation.untransform(a,b);return this.projection.unproject(d,c)},project:function(a){return this.projection.project(a)}},L.CRS.EPSG3857=L.Util.extend({},L.CRS,{code:"EPSG:3857",projection:L.Projection.SphericalMercator,transformation:new L.Transformation(.5/Math.PI,.5,-0.5/Math.PI,.5),project:function(a){var b=this.projection.project(a),c=6378137;return b.multiplyBy(c)}}),L.CRS.EPSG900913=L.Util.extend({},L.CRS.EPSG3857,{code:"EPSG:900913"}),L.CRS.EPSG4326=L.Util.extend({},L.CRS,{code:"EPSG:4326",projection:L.Projection.LonLat,transformation:new L.Transformation(1/360,.5,-1/360,.5)}),L.Map=L.Class.extend({includes:L.Mixin.Events,options:{crs:L.CRS.EPSG3857||L.CRS.EPSG4326,scale:function(a){return 256*Math.pow(2,a)},center:null,zoom:null,layers:[],dragging:!0,touchZoom:L.Browser.touch&&!L.Browser.android,scrollWheelZoom:!L.Browser.touch,doubleClickZoom:!0,boxZoom:!0,zoomControl:!0,attributionControl:!0,fadeAnimation:L.DomUtil.TRANSITION&&!L.Browser.android,zoomAnimation:L.DomUtil.TRANSITION&&!L.Browser.android&&!L.Browser.mobileOpera,trackResize:!0,closePopupOnClick:!0,worldCopyJump:!0},initialize:function(a,b){L.Util.setOptions(this,b),this._container=L.DomUtil.get(a);if(this._container._leaflet)throw Error("Map container is already initialized.");this._container._leaflet=!0,this._initLayout(),L.DomEvent&&(this._initEvents(),L.Handler&&this._initInteraction(),L.Control&&this._initControls()),this.options.maxBounds&&this.setMaxBounds(this.options.maxBounds);var c=this.options.center,d=this.options.zoom;c!==null&&d!==null&&this.setView(c,d,!0);var e=this.options.layers;e=e instanceof Array?e:[e],this._tileLayersNum=0,this._initLayers(e)},setView:function(a,b){return this._resetView(a,this._limitZoom(b)),this},setZoom:function(a){return this.setView(this.getCenter(),a)},zoomIn:function(){return this.setZoom(this._zoom+1)},zoomOut:function(){return this.setZoom(this._zoom-1)},fitBounds:function(a){var b=this.getBoundsZoom(a);return this.setView(a.getCenter(),b)},fitWorld:function(){var a=new L.LatLng(-60,-170),b=new L.LatLng(85,179);return this.fitBounds(new L.LatLngBounds(a,b))},panTo:function(a){return this.setView(a,this._zoom)},panBy:function(a){return this.fire("movestart"),this._rawPanBy(a),this.fire("move"),this.fire("moveend"),this},setMaxBounds:function(a){this.options.maxBounds=a;if(!a)return this._boundsMinZoom=null,this;var b=this.getBoundsZoom(a,!0);return this._boundsMinZoom=b,this._loaded&&(this._zoom<b?this.setView(a.getCenter(),b):this.panInsideBounds(a)),this},panInsideBounds:function(a){var b=this.getBounds(),c=this.project(b.getSouthWest()),d=this.project(b.getNorthEast()),e=this.project(a.getSouthWest()),f=this.project(a.getNorthEast()),g=0,h=0;return d.y<f.y&&(h=f.y-d.y),d.x>f.x&&(g=f.x-d.x),c.y>e.y&&(h=e.y-c.y),c.x<e.x&&(g=e.x-c.x),this.panBy(new L.Point(g,h,!0))},addLayer:function(a,b){var c=L.Util.stamp(a);if(this._layers[c])return this;this._layers[c]=a,a.options&&!isNaN(a.options.maxZoom)&&(this._layersMaxZoom=Math.max(this._layersMaxZoom||0,a.options.maxZoom)),a.options&&!isNaN(a.options.minZoom)&&(this._layersMinZoom=Math.min(this._layersMinZoom||Infinity,a.options.minZoom)),this.options.zoomAnimation&&L.TileLayer&&a instanceof L.TileLayer&&(this._tileLayersNum++,a.on("load",this._onTileLayerLoad,this)),this.attributionControl&&a.getAttribution&&this.attributionControl.addAttribution(a.getAttribution());var d=function(){a.onAdd(this,b),this.fire("layeradd",{layer:a})};return this._loaded?d.call(this):this.on("load",d,this),this},removeLayer:function(a){var b=L.Util.stamp(a);return this._layers[b]&&(a.onRemove(this),delete this._layers[b],this.options.zoomAnimation&&L.TileLayer&&a instanceof L.TileLayer&&(this._tileLayersNum--,a.off("load",this._onTileLayerLoad,this)),this.attributionControl&&a.getAttribution&&this.attributionControl.removeAttribution(a.getAttribution()),this.fire("layerremove",{layer:a})),this},hasLayer:function(a){var b=L.Util.stamp(a);return this._layers.hasOwnProperty(b)},invalidateSize:function(){var a=this.getSize();return this._sizeChanged=!0,this.options.maxBounds&&this.setMaxBounds(this.options.maxBounds),this._loaded?(this._rawPanBy(a.subtract(this.getSize()).divideBy(2,!0)),this.fire("move"),clearTimeout(this._sizeTimer),this._sizeTimer=setTimeout(L.Util.bind(function(){this.fire("moveend")},this),200),this):this},getCenter:function(a){var b=this.getSize().divideBy(2),c=this._getTopLeftPoint().add(b);return this.unproject(c,this._zoom,a)},getZoom:function(){return this._zoom},getBounds:function(){var a=this.getPixelBounds(),b=this.unproject(new L.Point(a.min.x,a.max.y),this._zoom,!0),c=this.unproject(new L.Point(a.max.x,a.min.y),this._zoom,!0);return new L.LatLngBounds(b,c)},getMinZoom:function(){var a=this.options.minZoom||0,b=this._layersMinZoom||0,c=this._boundsMinZoom||0;return Math.max(a,b,c)},getMaxZoom:function(){var a=isNaN(this.options.maxZoom)?Infinity:this.options.maxZoom,b=this._layersMaxZoom||Infinity;return Math.min(a,b)},getBoundsZoom:function(a,b){var c=this.getSize(),d=this.options.minZoom||0,e=this.getMaxZoom(),f=a.getNorthEast(),g=a.getSouthWest(),h,i,j,k=!0;b&&d--;do d++,i=this.project(f,d),j=this.project(g,d),h=new L.Point(i.x-j.x,j.y-i.y),b?k=h.x<c.x||h.y<c.y:k=h.x<=c.x&&h.y<=c.y;while(k&&d<=e);return k&&b?null:b?d:d-1},getSize:function(){if(!this._size||this._sizeChanged)this._size=new L.Point(this._container.clientWidth,this._container.clientHeight),this._sizeChanged=!1;return this._size},getPixelBounds:function(){var a=this._getTopLeftPoint(),b=this.getSize();return new L.Bounds(a,a.add(b))},getPixelOrigin:function(){return this._initialTopLeftPoint},getPanes:function(){return this._panes},mouseEventToContainerPoint:function(a){return L.DomEvent.getMousePosition(a,this._container)},mouseEventToLayerPoint:function(a){return this.containerPointToLayerPoint(this.mouseEventToContainerPoint(a))},mouseEventToLatLng:function(a){return this.layerPointToLatLng(this.mouseEventToLayerPoint(a))},containerPointToLayerPoint:function(a){return a.subtract(L.DomUtil.getPosition(this._mapPane))},layerPointToContainerPoint:function(a){return a.add(L.DomUtil.getPosition(this._mapPane))},layerPointToLatLng:function(a){return this.unproject(a.add(this._initialTopLeftPoint))},latLngToLayerPoint:function(a){return this.project(a)._round()._subtract(this._initialTopLeftPoint)},project:function(a,b){return b=typeof b=="undefined"?this._zoom:b,this.options.crs.latLngToPoint(a,this.options.scale(b))},unproject:function(a,b,c){return b=typeof b=="undefined"?this._zoom:b,this.options.crs.pointToLatLng(a,this.options.scale(b),c)},_initLayout:function(){var a=this._container;a.innerHTML="",a.className+=" leaflet-container",this.options.fadeAnimation&&(a.className+=" leaflet-fade-anim");var b=L.DomUtil.getStyle(a,"position");b!=="absolute"&&b!=="relative"&&(a.style.position="relative"),this._initPanes(),this._initControlPos&&this._initControlPos()},_initPanes:function(){var a=this._panes={};this._mapPane=a.mapPane=this._createPane("leaflet-map-pane",this._container),this._tilePane=a.tilePane=this._createPane("leaflet-tile-pane",this._mapPane),this._objectsPane=a.objectsPane=this._createPane("leaflet-objects-pane",this._mapPane),a.shadowPane=this._createPane("leaflet-shadow-pane"),a.overlayPane=this._createPane("leaflet-overlay-pane"),a.markerPane=this._createPane("leaflet-marker-pane"),a.popupPane=this._createPane("leaflet-popup-pane")},_createPane:function(a,b){return L.DomUtil.create("div",a,b||this._objectsPane)},_resetView:function(a,b,c,d){var e=this._zoom!==b;d||(this.fire("movestart"),e&&this.fire("zoomstart")),this._zoom=b,this._initialTopLeftPoint=this._getNewTopLeftPoint(a);if(!c)L.DomUtil.setPosition(this._mapPane,new L.Point(0,0));else{var f=L.DomUtil.getPosition(this._mapPane);this._initialTopLeftPoint._add(f)}this._tileLayersToLoad=this._tileLayersNum,this.fire("viewreset",{hard:!c}),this.fire("move"),(e||d)&&this.fire("zoomend"),this.fire("moveend"),this._loaded||(this._loaded=!0,this.fire("load"))},_initLayers:function(a){this._layers={};var b,c;for(b=0,c=a.length;b<c;b++)this.addLayer(a[b])},_initControls:function(){this.options.zoomControl&&this.addControl(new L.Control.Zoom),this.options.attributionControl&&(this.attributionControl=new L.Control.Attribution,this.addControl(this.attributionControl))},_rawPanBy:function(a){var b=L.DomUtil.getPosition(this._mapPane);L.DomUtil.setPosition(this._mapPane,b.subtract(a))},_initEvents:function(){L.DomEvent.addListener(this._container,"click",this._onMouseClick,this);var a=["dblclick","mousedown","mouseenter","mouseleave","mousemove","contextmenu"],b,c;for(b=0,c=a.length;b<c;b++)L.DomEvent.addListener(this._container,a[b],this._fireMouseEvent,this);this.options.trackResize&&L.DomEvent.addListener(window,"resize",this._onResize,this)},_onResize:function(){L.Util.requestAnimFrame(this.invalidateSize,this,!1,this._container)},_onMouseClick:function(a){if(!this._loaded||this.dragging&&this.dragging.moved())return;this.fire("pre"+a.type),this._fireMouseEvent(a)},_fireMouseEvent:function(a){if(!this._loaded)return;var b=a.type;b=b==="mouseenter"?"mouseover":b==="mouseleave"?"mouseout":b;if(!this.hasEventListeners(b))return;b==="contextmenu"&&L.DomEvent.preventDefault(a),this.fire(b,{latlng:this.mouseEventToLatLng(a),layerPoint:this.mouseEventToLayerPoint(a)})},_initInteraction:function(){var a={dragging:L.Map.Drag,touchZoom:L.Map.TouchZoom,doubleClickZoom:L.Map.DoubleClickZoom,scrollWheelZoom:L.Map.ScrollWheelZoom,boxZoom:L.Map.BoxZoom},b;for(b in a)a.hasOwnProperty(b)&&a[b]&&(this[b]=new a[b](this),this.options[b]&&this[b].enable())},_onTileLayerLoad:function(){this._tileLayersToLoad--,this._tileLayersNum&&!this._tileLayersToLoad&&this._tileBg&&(clearTimeout(this._clearTileBgTimer),this._clearTileBgTimer=setTimeout(L.Util.bind(this._clearTileBg,this),500))},_getTopLeftPoint:function(){if(!this._loaded)throw Error("Set map center and zoom first.");var a=L.DomUtil.getPosition(this._mapPane);return this._initialTopLeftPoint.subtract(a)},_getNewTopLeftPoint:function(a){var b=this.getSize().divideBy(2);return this.project(a).subtract(b).round()},_limitZoom:function(a){var b=this.getMinZoom(),c=this.getMaxZoom();return Math.max(b,Math.min(c,a))}}),L.Projection.Mercator={MAX_LATITUDE:85.0840591556,R_MINOR:6356752.3142,R_MAJOR:6378137,project:function(a){var b=L.LatLng.DEG_TO_RAD,c=this.MAX_LATITUDE,d=Math.max(Math.min(c,a.lat),-c),e=this.R_MAJOR,f=this.R_MINOR,g=a.lng*b*e,h=d*b,i=f/e,j=Math.sqrt(1-i*i),k=j*Math.sin(h);k=Math.pow((1-k)/(1+k),j*.5);var l=Math.tan(.5*(Math.PI*.5-h))/k;return h=-f*Math.log(l),new L.Point(g,h)},unproject:function(a,b){var c=L.LatLng.RAD_TO_DEG,d=this.R_MAJOR,e=this.R_MINOR,f=a.x*c/d,g=e/d,h=Math.sqrt(1-g*g),i=Math.exp(-a.y/e),j=Math.PI/2-2*Math.atan(i),k=15,l=1e-7,m=k,n=.1,o;while(Math.abs(n)>l&&--m>0)o=h*Math.sin(j),n=Math.PI/2-2*Math.atan(i*Math.pow((1-o)/(1+o),.5*h))-j,j+=n;return new L.LatLng(j*c,f,b)}},L.CRS.EPSG3395=L.Util.extend({},L.CRS,{code:"EPSG:3395",projection:L.Projection.Mercator,transformation:function(){var a=L.Projection.Mercator,b=a.R_MAJOR,c=a.R_MINOR;return new L.Transformation(.5/(Math.PI*b),.5,-0.5/(Math.PI*c),.5)}()}),L.TileLayer=L.Class.extend({includes:L.Mixin.Events,options:{minZoom:0,maxZoom:18,tileSize:256,subdomains:"abc",errorTileUrl:"",attribution:"",opacity:1,scheme:"xyz",continuousWorld:!1,noWrap:!1,zoomOffset:0,zoomReverse:!1,unloadInvisibleTiles:L.Browser.mobile,updateWhenIdle:L.Browser.mobile,reuseTiles:!1},initialize:function(a,b,c){L.Util.setOptions(this,b),this._url=a,this._urlParams=c,typeof this.options.subdomains=="string"&&(this.options.subdomains=this.options.subdomains.split(""))},onAdd:function(a,b){this._map=a,this._insertAtTheBottom=b,this._initContainer(),this._createTileProto(),a.on("viewreset",this._resetCallback,this),this.options.updateWhenIdle?a.on("moveend",this._update,this):(this._limitedUpdate=L.Util.limitExecByInterval(this._update,150,this),a.on("move",this._limitedUpdate,this)),this._reset(),this._update()},onRemove:function(a){this._map.getPanes().tilePane.removeChild(this._container),this._container=null,this._map.off("viewreset",this._resetCallback,this),this.options.updateWhenIdle?this._map.off("moveend",this._update,this):this._map.off("move",this._limitedUpdate,this)},getAttribution:function(){return this.options.attribution},setOpacity:function(a){this.options.opacity=a,this._setOpacity(a);if(L.Browser.webkit)for(var b in this._tiles)this._tiles.hasOwnProperty(b)&&(this._tiles[b].style.webkitTransform+=" translate(0,0)")},_setOpacity:function(a){a<1&&L.DomUtil.setOpacity(this._container,a)},_initContainer:function(){var a=this._map.getPanes().tilePane,b=a.firstChild;if(!this._container||a.empty)this._container=L.DomUtil.create("div","leaflet-layer"),this._insertAtTheBottom&&b?a.insertBefore(this._container,b):a.appendChild(this._container),this._setOpacity(this.options.opacity)},_resetCallback:function(a){this._reset(a.hard)},_reset:function(a){var b;for(b in this._tiles)this._tiles.hasOwnProperty(b)&&this.fire("tileunload",{tile:this._tiles[b]});this._tiles={},this.options.reuseTiles&&(this._unusedTiles=[]),a&&this._container&&(this._container.innerHTML=""),this._initContainer()},_update:function(){var a=this._map.getPixelBounds(),b=this._map.getZoom(),c=this.options.tileSize;if(b>this.options.maxZoom||b<this.options.minZoom)return;var d=new L.Point(Math.floor(a.min.x/c),Math.floor(a.min.y/c)),e=new L.Point(Math.floor(a.max.x/c),Math.floor(a.max.y/c)),f=new L.Bounds(d,e);this._addTilesFromCenterOut(f),(this.options.unloadInvisibleTiles||this.options.reuseTiles)&&this._removeOtherTiles(f)},_addTilesFromCenterOut:function(a){var b=[],c=a.getCenter();for(var d=a.min.y;d<=a.max.y;d++)for(var e=a.min.x;e<=a.max.x;e++){if(e+":"+d in this._tiles)continue;b.push(new L.Point(e,d))}b.sort(function(a,b){return a.distanceTo(c)-b.distanceTo(c)});var f=document.createDocumentFragment();this._tilesToLoad=b.length;for(var g=0,h=this._tilesToLoad;g<h;g++)this._addTile(b[g],f);this._container.appendChild(f)},_removeOtherTiles:function(a){var b,c,d,e,f;for(e in this._tiles)if(this._tiles.hasOwnProperty(e)){b=e.split(":"),c=parseInt(b[0],10),d=parseInt(b[1],10);if(c<a.min.x||c>a.max.x||d<a.min.y||d>a.max.y)f=this._tiles[e],this.fire("tileunload",{tile:f,url:f.src}),f.parentNode===this._container&&this._container.removeChild(f),this.options.reuseTiles&&this._unusedTiles.push(this._tiles[e]),f.src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=",delete this._tiles[e]}},_addTile:function(a,b){var c=this._getTilePos(a),d=this._map.getZoom(),e=a.x+":"+a.y,f=Math.pow(2,this._getOffsetZoom(d));if(!this.options.continuousWorld){if(!this.options.noWrap)a.x=(a.x%f+f)%f;else if(a.x<0||a.x>=f){this._tilesToLoad--;return}if(a.y<0||a.y>=f){this._tilesToLoad--;return}}var g=this._getTile();L.DomUtil.setPosition(g,c),this._tiles[e]=g,this.options.scheme==="tms"&&(a.y=f-a.y-1),this._loadTile(g,a,d),b.appendChild(g)},_getOffsetZoom:function(a){return a=this.options.zoomReverse?this.options.maxZoom-a:a,a+this.options.zoomOffset},_getTilePos:function(a){var b=this._map.getPixelOrigin(),c=this.options.tileSize;return a.multiplyBy(c).subtract(b)},getTileUrl:function(a,b){var c=this.options.subdomains,d=this.options.subdomains[(a.x+a.y)%c.length];return L.Util.template(this._url,L.Util.extend({s:d,z:this._getOffsetZoom(b),x:a.x,y:a.y},this._urlParams))},_createTileProto:function(){this._tileImg=L.DomUtil.create("img","leaflet-tile"),this._tileImg.galleryimg="no";var a=this.options.tileSize;this._tileImg.style.width=a+"px",this._tileImg.style.height=a+"px"},_getTile:function(){if(this.options.reuseTiles&&this._unusedTiles.length>0){var a=this._unusedTiles.pop();return this._resetTile(a),a}return this._createTile()},_resetTile:function(a){},_createTile:function(){var a=this._tileImg.cloneNode(!1);return a.onselectstart=a.onmousemove=L.Util.falseFn,a},_loadTile:function(a,b,c){a._layer=this,a.onload=this._tileOnLoad,a.onerror=this._tileOnError,a.src=this.getTileUrl(b,c)},_tileOnLoad:function(a){var b=this._layer;this.className+=" leaflet-tile-loaded",b.fire("tileload",{tile:this,url:this.src}),b._tilesToLoad--,b._tilesToLoad||b.fire("load")},_tileOnError:function(a){var b=this._layer;b.fire("tileerror",{tile:this,url:this.src});var c=b.options.errorTileUrl;c&&(this.src=c)}}),L.TileLayer.WMS=L.TileLayer.extend({defaultWmsParams:{service:"WMS",request:"GetMap",version:"1.1.1",layers:"",styles:"",format:"image/jpeg",transparent:!1},initialize:function(a,b){this._url=a,this.wmsParams=L.Util.extend({},this.defaultWmsParams),this.wmsParams.width=this.wmsParams.height=this.options.tileSize;for(var c in b)this.options.hasOwnProperty(c)||(this.wmsParams[c]=b[c]);L.Util.setOptions(this,b)},onAdd:function(a){var b=parseFloat(this.wmsParams.version)<1.3?"srs":"crs";this.wmsParams[b]=a.options.crs.code,L.TileLayer.prototype.onAdd.call(this,a)},getTileUrl:function(a,b){var c=this.options.tileSize,d=a.multiplyBy(c),e=d.add(new L.Point(c,c)),f=this._map.unproject(d,this._zoom,!0),g=this._map.unproject(e,this._zoom,!0),h=this._map.options.crs.project(f),i=this._map.options.crs.project(g),j=[h.x,i.y,i.x,h.y].join(",");return this._url+L.Util.getParamString(this.wmsParams)+"&bbox="+j}}),L.TileLayer.Canvas=L.TileLayer.extend({options:{async:!1},initialize:function(a){L.Util.setOptions(this,a)},redraw:function(){for(var a in this._tiles){var b=this._tiles[a];this._redrawTile(b)}},_redrawTile:function(a){this.drawTile(a,a._tilePoint,a._zoom)},_createTileProto:function(){this._canvasProto=L.DomUtil.create("canvas","leaflet-tile");var a=this.options.tileSize;this._canvasProto.width=a,this._canvasProto.height=a},_createTile:function(){var a=this._canvasProto.cloneNode(!1);return a.onselectstart=a.onmousemove=L.Util.falseFn,a},_loadTile:function(a,b,c){a._layer=this,a._tilePoint=b,a._zoom=c,this.drawTile(a,b,c),this.options.async||this.tileDrawn(a)},drawTile:function(a,b,c){},tileDrawn:function(a){this._tileOnLoad.call(a)}}),L.ImageOverlay=L.Class.extend({includes:L.Mixin.Events,initialize:function(a,b){this._url=a,this._bounds=b},onAdd:function(a){this._map=a,this._image||this._initImage(),a.getPanes().overlayPane.appendChild(this._image),a.on("viewreset",this._reset,this),this._reset()},onRemove:function(a){a.getPanes().overlayPane.removeChild(this._image),a.off("viewreset",this._reset,this)},_initImage:function(){this._image=L.DomUtil.create("img","leaflet-image-layer"),this._image.style.visibility="hidden",L.Util.extend(this._image,{galleryimg:"no",onselectstart:L.Util.falseFn,onmousemove:L.Util.falseFn,onload:L.Util.bind(this._onImageLoad,this),src:this._url})},_reset:function(){var a=this._map.latLngToLayerPoint(this._bounds.getNorthWest()),b=this._map.latLngToLayerPoint(this._bounds.getSouthEast()),c=b.subtract(a);L.DomUtil.setPosition(this._image,a),this._image.style.width=c.x+"px",this._image.style.height=c.y+"px"},_onImageLoad:function(){this._image.style.visibility="",this.fire("load")}}),L.Icon=L.Class.extend({iconUrl:L.ROOT_URL+"images/marker.png",shadowUrl:L.ROOT_URL+"images/marker-shadow.png",iconSize:new L.Point(25,41),shadowSize:new L.Point(41,41),iconAnchor:new L.Point(13,41),popupAnchor:new L.Point(0,-33),initialize:function(a){a&&(this.iconUrl=a)},createIcon:function(){return this._createIcon("icon")},createShadow:function(){return this._createIcon("shadow")},_createIcon:function(a){var b=this[a+"Size"],c=this[a+"Url"];if(!c&&a==="shadow")return null;var d;return c?d=this._createImg(c):d=this._createDiv(),d.className="leaflet-marker-"+a,d.style.marginLeft=-this.iconAnchor.x+"px",d.style.marginTop=-this.iconAnchor.y+"px",b&&(d.style.width=b.x+"px",d.style.height=b.y+"px"),d},_createImg:function(a){var b;return L.Browser.ie6?(b=document.createElement("div"),b.style.filter='progid:DXImageTransform.Microsoft.AlphaImageLoader(src="'+a+'")'):(b=document.createElement("img"),b.src=a),b},_createDiv:function(){return document.createElement("div")}}),L.Marker=L.Class.extend({includes:L.Mixin.Events,options:{icon:new L.Icon,title:"",clickable:!0,draggable:!1,zIndexOffset:0},initialize:function(a,b){L.Util.setOptions(this,b),this._latlng=a},onAdd:function(a){this._map=a,this._initIcon(),a.on("viewreset",this._reset,this),this._reset()},onRemove:function(a){this._removeIcon(),this.closePopup&&this.closePopup(),this._map=null,a.off("viewreset",this._reset,this)},getLatLng:function(){return this._latlng},setLatLng:function(a){this._latlng=a,this._icon&&(this._reset(),this._popup&&this._popup.setLatLng(this._latlng))},setZIndexOffset:function(a){this.options.zIndexOffset=a,this._icon&&this._reset()},setIcon:function(a){this._map&&this._removeIcon(),this.options.icon=a,this._map&&(this._initIcon(),this._reset())},_initIcon:function(){this._icon||(this._icon=this.options.icon.createIcon(),this.options.title&&(this._icon.title=this.options.title),this._initInteraction()),this._shadow||(this._shadow=this.options.icon.createShadow()),this._map._panes.markerPane.appendChild(this._icon),this._shadow&&this._map._panes.shadowPane.appendChild(this._shadow)},_removeIcon:function(){this._map._panes.markerPane.removeChild(this._icon),this._shadow&&this._map._panes.shadowPane.removeChild(this._shadow),this._icon=this._shadow=null},_reset:function(){var a=this._map.latLngToLayerPoint(this._latlng).round();L.DomUtil.setPosition(this._icon,a),this._shadow&&L.DomUtil.setPosition(this._shadow,a),this._icon.style.zIndex=a.y+this.options.zIndexOffset},_initInteraction:function(){if(this.options.clickable){this._icon.className+=" leaflet-clickable",L.DomEvent.addListener(this._icon,"click",this._onMouseClick,this);var a=["dblclick","mousedown","mouseover","mouseout"];for(var b=0;b<a.length;b++)L.DomEvent.addListener(this._icon,a[b],this._fireMouseEvent,this)}L.Handler.MarkerDrag&&(this.dragging=new L.Handler.MarkerDrag(this),this.options.draggable&&this.dragging.enable())},_onMouseClick:function(a){L.DomEvent.stopPropagation(a);if(this.dragging&&this.dragging.moved())return;this.fire(a.type)},_fireMouseEvent:function(a){this.fire(a.type),L.DomEvent.stopPropagation(a)}}),L.Popup=L.Class.extend({includes:L.Mixin.Events,options:{minWidth:50,maxWidth:300,autoPan:!0,closeButton:!0,offset:new L.Point(0,2),autoPanPadding:new L.Point(5,5),className:""},initialize:function(a,b){L.Util.setOptions(this,a),this._source=b},onAdd:function(a){this._map=a,this._container||this._initLayout(),this._updateContent(),this._container.style.opacity="0",this._map._panes.popupPane.appendChild(this._container),this._map.on("viewreset",this._updatePosition,this),this._map.options.closePopupOnClick&&this._map.on("preclick",this._close,this),this._update(),this._container.style.opacity="1",this._opened=!0},onRemove:function(a){a._panes.popupPane.removeChild(this._container),L.Util.falseFn(this._container.offsetWidth),a.off("viewreset",this._updatePosition,this),a.off("click",this._close,this),this._container.style.opacity="0",this._opened=!1},setLatLng:function(a){return this._latlng=a,this._opened&&this._update(),this},setContent:function(a){return this._content=a,this._opened&&this._update(),this},_close:function(){this._opened&&this._map.closePopup()},_initLayout:function(){this._container=L.DomUtil.create("div","leaflet-popup "+this.options.className),this.options.closeButton&&(this._closeButton=L.DomUtil.create("a","leaflet-popup-close-button",this._container),this._closeButton.href="#close",L.DomEvent.addListener(this._closeButton,"click",this._onCloseButtonClick,this)),this._wrapper=L.DomUtil.create("div","leaflet-popup-content-wrapper",this._container),L.DomEvent.disableClickPropagation(this._wrapper),this._contentNode=L.DomUtil.create("div","leaflet-popup-content",this._wrapper),this._tipContainer=L.DomUtil.create("div","leaflet-popup-tip-container",this._container),this._tip=L.DomUtil.create("div","leaflet-popup-tip",this._tipContainer)},_update:function(){this._container.style.visibility="hidden",this._updateContent(),this._updateLayout(),this._updatePosition(),this._container.style.visibility="",this._adjustPan()},_updateContent:function(){if(!this._content)return;typeof this._content=="string"?this._contentNode.innerHTML=this._content:(this._contentNode.innerHTML="",this._contentNode.appendChild(this._content))},_updateLayout:function(){this._container.style.width="",this._container.style.whiteSpace="nowrap";var a=this._container.offsetWidth;this._container.style.width=(a>this.options.maxWidth?this.options.maxWidth:a<this.options.minWidth?this.options.minWidth:a)+"px",this._container.style.whiteSpace="",this._containerWidth=this._container.offsetWidth},_updatePosition:function(){var a=this._map.latLngToLayerPoint(this._latlng);this._containerBottom=-a.y-this.options.offset.y,this._containerLeft=a.x-Math.round(this._containerWidth/2)+this.options.offset.x,this._container.style.bottom=this._containerBottom+"px",this._container.style.left=this._containerLeft+"px"},_adjustPan:function(){if(!this.options.autoPan)return;var a=this._container.offsetHeight,b=new L.Point(this._containerLeft,-a-this._containerBottom),c=this._map.layerPointToContainerPoint(b),d=new L.Point(0,0),e=this.options.autoPanPadding,f=this._map.getSize();c.x<0&&(d.x=c.x-e.x),c.x+this._containerWidth>f.x&&(d.x=c.x+this._containerWidth-f.x+e.x),c.y<0&&(d.y=c.y-e.y),c.y+a>f.y&&(d.y=c.y+a-f.y+e.y),(d.x||d.y)&&this._map.panBy(d)},_onCloseButtonClick:function(a){this._close(),L.DomEvent.stop(a)}}),L.Marker.include({openPopup:function(){return this._popup.setLatLng(this._latlng),this._map&&this._map.openPopup(this._popup),this},closePopup:function(){return this._popup&&this._popup._close(),this},bindPopup:function(a,b){return b=L.Util.extend({offset:this.options.icon.popupAnchor},b),this._popup||this.on("click",this.openPopup,this),this._popup=new L.Popup(b,this),this._popup.setContent(a),this},unbindPopup:function(){return this._popup&&(this._popup=null,this.off("click",this.openPopup)),this}}),L.Map.include({openPopup:function(a){return this.closePopup(),this._popup=a,this.addLayer(a),this.fire("popupopen",{popup:this._popup}),this},closePopup:function(){return this._popup&&(this.removeLayer(this._popup),this.fire("popupclose",{popup:this._popup}),this._popup=null),this}}),L.LayerGroup=L.Class.extend({initialize:function(a){this._layers={};if(a)for(var b=0,c=a.length;b<c;b++)this.addLayer(a[b])},addLayer:function(a){var b=L.Util.stamp(a);return this._layers[b]=a,this._map&&this._map.addLayer(a),this},removeLayer:function(a){var b=L.Util.stamp(a);return delete this._layers[b],this._map&&this._map.removeLayer(a),this},clearLayers:function(){return this._iterateLayers(this.removeLayer,this),this},invoke:function(a){var b=Array.prototype.slice.call(arguments,1),c,d;for(c in this._layers)this._layers.hasOwnProperty(c)&&(d=this._layers[c],d[a]&&d[a].apply(d,b));return this},onAdd:function(a){this._map=a,this._iterateLayers(a.addLayer,a)},onRemove:function(a){this._iterateLayers(a.removeLayer,a),delete this._map},_iterateLayers:function(a,b){for(var c in this._layers)this._layers.hasOwnProperty(c)&&a.call(b,this._layers[c])}}),L.FeatureGroup=L.LayerGroup.extend({includes:L.Mixin.Events,addLayer:function(a){this._initEvents(a),L.LayerGroup.prototype.addLayer.call(this,a),this._popupContent&&a.bindPopup&&a.bindPopup(this._popupContent)},bindPopup:function(a){return this._popupContent=a,this.invoke("bindPopup",a)},setStyle:function(a){return this.invoke("setStyle",a)},_events:["click","dblclick","mouseover","mouseout"],_initEvents:function(a){for(var b=0,c=this._events.length;b<c;b++)a.on(this._events[b],this._propagateEvent,this)},_propagateEvent:function(a){a.layer=a.target,a.target=this,this.fire(a.type,a)}}),L.Path=L.Class.extend({includes:[L.Mixin.Events],statics:{CLIP_PADDING:.5},options:{stroke:!0,color:"#0033ff",weight:5,opacity:.5,fill:!1,fillColor:null,fillOpacity:.2,clickable:!0,updateOnMoveEnd:!0},initialize:function(a){L.Util.setOptions(this,a)},onAdd:function(a){this._map=a,this._initElements(),this._initEvents(),this.projectLatlngs(),this._updatePath(),a.on("viewreset",this.projectLatlngs,this),this._updateTrigger=this.options.updateOnMoveEnd?"moveend":"viewreset",a.on(this._updateTrigger,this._updatePath,this)},onRemove:function(a){this._map=null,a._pathRoot.removeChild(this._container),a.off("viewreset",this.projectLatlngs,this),a.off(this._updateTrigger,this._updatePath,this)},projectLatlngs:function(){},setStyle:function(a){return L.Util.setOptions(this,a),this._container&&this._updateStyle(),this},_redraw:function(){this._map&&(this.projectLatlngs(),this._updatePath())}}),L.Map.include({_updatePathViewport:function(){var a=L.Path.CLIP_PADDING,b=this.getSize(),c=L.DomUtil.getPosition(this._mapPane),d=c.multiplyBy(-1).subtract(b.multiplyBy(a)),e=d.add(b.multiplyBy(1+a*2));this._pathViewport=new L.Bounds(d,e)}}),L.Path.SVG_NS="http://www.w3.org/2000/svg",L.Browser.svg=!!document.createElementNS&&!!document.createElementNS(L.Path.SVG_NS,"svg").createSVGRect,L.Path=L.Path.extend({statics:{SVG:L.Browser.svg,_createElement:function(a){return document.createElementNS(L.Path.SVG_NS,a)}},getPathString:function(){},_initElements:function(){this._map._initPathRoot(),this._initPath(),this._initStyle()},_initPath:function(){this._container=L.Path._createElement("g"),this._path=L.Path._createElement("path"),this._container.appendChild(this._path),this._map._pathRoot.appendChild(this._container)},_initStyle:function(){this.options.stroke&&(this._path.setAttribute("stroke-linejoin","round"),this._path.setAttribute("stroke-linecap","round")),this.options.fill?this._path.setAttribute("fill-rule","evenodd"):this._path.setAttribute("fill","none"),this._updateStyle()},_updateStyle:function(){this.options.stroke&&(this._path.setAttribute("stroke",this.options.color),this._path.setAttribute("stroke-opacity",this.options.opacity),this._path.setAttribute("stroke-width",this.options.weight)),this.options.fill&&(this._path.setAttribute("fill",this.options.fillColor||this.options.color),this._path.setAttribute("fill-opacity",this.options.fillOpacity))},_updatePath:function(){var a=this.getPathString();a||(a="M0 0"),this._path.setAttribute("d",a)},_initEvents:function(){if(this.options.clickable){L.Browser.vml||this._path.setAttribute("class","leaflet-clickable"),L.DomEvent.addListener(this._container,"click",this._onMouseClick,this);var a=["dblclick","mousedown","mouseover","mouseout","mousemove"];for(var b=0;b<a.length;b++)L.DomEvent.addListener(this._container,a[b],this._fireMouseEvent,this)}},_onMouseClick:function(a){if(this._map.dragging&&this._map.dragging.moved())return;this._fireMouseEvent(a)},_fireMouseEvent:function(a){if(!this.hasEventListeners(a.type))return;this.fire(a.type,{latlng:this._map.mouseEventToLatLng(a),layerPoint:this._map.mouseEventToLayerPoint(a)}),L.DomEvent.stopPropagation(a)}}),L.Map.include({_initPathRoot:function(){this._pathRoot||(this._pathRoot=L.Path._createElement("svg"),this._panes.overlayPane.appendChild(this._pathRoot),this.on("moveend",this._updateSvgViewport),this._updateSvgViewport())},_updateSvgViewport:function(){this._updatePathViewport();var a=this._pathViewport,b=a.min,c=a.max,d=c.x-b.x,e=c.y-b.y,f=this._pathRoot,g=this._panes.overlayPane;L.Browser.webkit&&g.removeChild(f),L.DomUtil.setPosition(f,b),f.setAttribute("width",d),f.setAttribute("height",e),f.setAttribute("viewBox",[b.x,b.y,d,e].join(" ")),L.Browser.webkit&&g.appendChild(f)}}),L.Path.include({bindPopup:function(a,b){if(!this._popup||this._popup.options!==b)this._popup=new L.Popup(b,this);return this._popup.setContent(a),this._openPopupAdded||(this.on("click",this._openPopup,this),this._openPopupAdded=!0),this},_openPopup:function(a){this._popup.setLatLng(a.latlng),this._map.openPopup(this._popup)}}),L.Browser.vml=function(){var a=document.createElement("div"),b;return a.innerHTML='<v:shape adj="1"/>',b=a.firstChild,b.style.behavior="url(#default#VML)",b&&typeof b.adj=="object"}(),L.Path=L.Browser.svg||!L.Browser.vml?L.Path:L.Path.extend({statics:{VML:!0,CLIP_PADDING:.02,_createElement:function(){try{return document.namespaces.add("lvml","urn:schemas-microsoft-com:vml"),function(a){return document.createElement("<lvml:"+a+' class="lvml">')}}catch(a){return function(a){return document.createElement("<"+a+' xmlns="urn:schemas-microsoft.com:vml" class="lvml">')}}}()},_initPath:function(){this._container=L.Path._createElement("shape"),this._container.className+=" leaflet-vml-shape"+(this.options.clickable?" leaflet-clickable":""),this._container.coordsize="1 1",this._path=L.Path._createElement("path"),this._container.appendChild(this._path),this._map._pathRoot.appendChild(this._container)},_initStyle:function(){this.options.stroke?(this._stroke=L.Path._createElement("stroke"),this._stroke.endcap="round",this._container.appendChild(this._stroke)):this._container.stroked=!1,this.options.fill?(this._container.filled=!0,this._fill=L.Path._createElement("fill"),this._container.appendChild(this._fill)):this._container.filled=!1,this._updateStyle()},_updateStyle:function(){this.options.stroke&&(this._stroke.weight=this.options.weight+"px",this._stroke.color=this.options.color,this._stroke.opacity=this.options.opacity),this.options.fill&&(this._fill.color=this.options.fillColor||this.options.color,this._fill.opacity=this.options.fillOpacity)},_updatePath:function(){this._container.style.display="none",this._path.v=this.getPathString()+" ",this._container.style.display=""}}),L.Map.include(L.Browser.svg||!L.Browser.vml?{}:{_initPathRoot:function(){this._pathRoot||(this._pathRoot=document.createElement("div"),this._pathRoot.className="leaflet-vml-container",this._panes.overlayPane.appendChild(this._pathRoot),this.on("moveend",this._updatePathViewport),this._updatePathViewport())}}),L.Browser.canvas=function(){return!!document.createElement("canvas").getContext}(),L.Path=L.Path.SVG&&!window.L_PREFER_CANVAS||!L.Browser.canvas?L.Path:L.Path.extend({statics:{CANVAS:!0,SVG:!1},options:{updateOnMoveEnd:!0},_initElements:function(){this._map._initPathRoot(),this._ctx=this._map._canvasCtx},_updateStyle:function(){this.options.stroke&&(this._ctx.lineWidth=this.options.weight,this._ctx.strokeStyle=this.options.color),this.options.fill&&(this._ctx.fillStyle=this.options.fillColor||this.options.color)},_drawPath:function(){var a,b,c,d,e,f;this._ctx.beginPath();for(a=0,c=this._parts.length;a<c;a++){for(b=0,d=this._parts[a].length;b<d;b++)e=this._parts[a][b],f=(b===0?"move":"line")+"To",this._ctx[f](e.x,e.y);this instanceof L.Polygon&&this._ctx.closePath()}},_checkIfEmpty:function(){return!this._parts.length},_updatePath:function(){if(this._checkIfEmpty())return;this._drawPath(),this._ctx.save(),this._updateStyle();var a=this.options.opacity,b=this.options.fillOpacity;this.options.fill&&(b<1&&(this._ctx.globalAlpha=b),this._ctx.fill()),this.options.stroke&&(a<1&&(this._ctx.globalAlpha=a),this._ctx.stroke()),this._ctx.restore()},_initEvents:function(){this.options.clickable&&this._map.on("click",this._onClick,this)},_onClick:function(a){this._containsPoint(a.layerPoint)&&this.fire("click",a)},onRemove:function(a){a.off("viewreset",this._projectLatlngs,this),a.off(this._updateTrigger,this._updatePath,this),a.fire(this._updateTrigger)}}),L.Map.include(L.Path.SVG&&!window.L_PREFER_CANVAS||!L.Browser.canvas?{}:{_initPathRoot:function(){var a=this._pathRoot,b;a||(a=this._pathRoot=document.createElement("canvas"),a.style.position="absolute",b=this._canvasCtx=a.getContext("2d"),b.lineCap="round",b.lineJoin="round",this._panes.overlayPane.appendChild(a),this.on("moveend",this._updateCanvasViewport),this._updateCanvasViewport())},_updateCanvasViewport:function(){this._updatePathViewport();var a=this._pathViewport,b=a.min,c=a.max.subtract(b),d=this._pathRoot;L.DomUtil.setPosition(d,b),d.width=c.x,d.height=c.y,d.getContext("2d").translate(-b.x,-b.y)}}),L.LineUtil={simplify:function(a,b){if(!b||!a.length)return a.slice();var c=b*b;return a=this._reducePoints(a,c),a=this._simplifyDP(a,c),a},pointToSegmentDistance:function(a,b,c){return Math.sqrt(this._sqClosestPointOnSegment(a,b,c,!0))},closestPointOnSegment:function(a,b,c){return this._sqClosestPointOnSegment(a,b,c)},_simplifyDP:function(a,b){var c=a.length,d=typeof Uint8Array!="undefined"?Uint8Array:Array,e=new d(c);e[0]=e[c-1]=1,this._simplifyDPStep(a,e,b,0,c-1);var f,g=[];for(f=0;f<c;f++)e[f]&&g.push(a[f]);return g},_simplifyDPStep:function(a,b,c,d,e){var f=0,g,h,i;for(h=d+1;h<=e-1;h++)i=this._sqClosestPointOnSegment(a[h],a[d],a[e],!0),i>f&&(g=h,f=i);f>c&&(b[g]=1,this._simplifyDPStep(a,b,c,d,g),this._simplifyDPStep(a,b,c,g,e))},_reducePoints:function(a,b){var c=[a[0]];for(var d=1,e=0,f=a.length;d<f;d++)this._sqDist(a[d],a[e])>b&&(c.push(a[d]),e=d);return e<f-1&&c.push(a[f-1]),c},clipSegment:function(a,b,c,d){var e=c.min,f=c.max,g=d?this._lastCode:this._getBitCode(a,c),h=this._getBitCode(b,c);this._lastCode=h;for(;;){if(!(g|h))return[a,b];if(g&h)return!1;var i=g||h,j=this._getEdgeIntersection(a,b,i,c),k=this._getBitCode(j,c);i===g?(a=j,g=k):(b=j,h=k)}},_getEdgeIntersection:function(a,b,c,d){var e=b.x-a.x,f=b.y-a.y,g=d.min,h=d.max;if(c&8)return new L.Point(a.x+e*(h.y-a.y)/f,h.y);if(c&4)return new L.Point(a.x+e*(g.y-a.y)/f,g.y);if(c&2)return new L.Point(h.x,a.y+f*(h.x-a.x)/e);if(c&1)return new L.Point(g.x,a.y+f*(g.x-a.x)/e)},_getBitCode:function(a,b){var c=0;return a.x<b.min.x?c|=1:a.x>b.max.x&&(c|=2),a.y<b.min.y?c|=4:a.y>b.max.y&&(c|=8),c},_sqDist:function(a,b){var c=b.x-a.x,d=b.y-a.y;return c*c+d*d},_sqClosestPointOnSegment:function(a,b,c,d){var e=b.x,f=b.y,g=c.x-e,h=c.y-f,i=g*g+h*h,j;return i>0&&(j=((a.x-e)*g+(a.y-f)*h)/i,j>1?(e=c.x,f=c.y):j>0&&(e+=g*j,f+=h*j)),g=a.x-e,h=a.y-f,d?g*g+h*h:new L.Point(e,f)}},L.Polyline=L.Path.extend({initialize:function(a,b){L.Path.prototype.initialize.call(this,b),this._latlngs=a},options:{smoothFactor:1,noClip:!1,updateOnMoveEnd:!0},projectLatlngs:function(){this._originalPoints=[];for(var a=0,b=this._latlngs.length;a<b;a++)this._originalPoints[a]=this._map.latLngToLayerPoint(this._latlngs[a])},getPathString:function(){for(var a=0,b=this._parts.length,c="";a<b;a++)c+=this._getPathPartStr(this._parts[a]);return c},getLatLngs:function(){return this._latlngs},setLatLngs:function(a){return this._latlngs=a,this._redraw(),this},addLatLng:function(a){return this._latlngs.push(a),this._redraw(),this},spliceLatLngs:function(a,b){var c=[].splice.apply(this._latlngs,arguments);return this._redraw(),c},closestLayerPoint:function(a){var b=Infinity,c=this._parts,d,e,f=null;for(var g=0,h=c.length;g<h;g++){var i=c[g];for(var j=1,k=i.length;j<k;j++){d=i[j-1],e=i[j];var l=L.LineUtil._sqClosestPointOnSegment(a,d,e);l._sqDist<b&&(b=l._sqDist,f=l)}}return f&&(f.distance=Math.sqrt(b)),f},getBounds:function(){var a=new L.LatLngBounds,b=this.getLatLngs();for(var c=0,d=b.length;c<d;c++)a.extend(b[c]);return a},_getPathPartStr:function(a){var b=L.Path.VML;for(var c=0,d=a.length,e="",f;c<d;c++)f=a[c],b&&f._round(),e+=(c?"L":"M")+f.x+" "+f.y;return e},_clipPoints:function(){var a=this._originalPoints,b=a.length,c,d,e;if(this.options.noClip){this._parts=[a];return}this._parts=[];var f=this._parts,g=this._map._pathViewport,h=L.LineUtil;for(c=0,d=0;c<b-1;c++){e=h.clipSegment(a[c],a[c+1],g,c);if(!e)continue;f[d]=f[d]||[],f[d].push(e[0]);if(e[1]!==a[c+1]||c===b-2)f[d].push(e[1]),d++}},_simplifyPoints:function(){var a=this._parts,b=L.LineUtil;for(var c=0,d=a.length;c<d;c++)a[c]=b.simplify(a[c],this.options.smoothFactor)},_updatePath:function(){this._clipPoints(),this._simplifyPoints(),L.Path.prototype._updatePath.call(this)}}),L.PolyUtil={},L.PolyUtil.clipPolygon=function(a,b){var c=b.min,d=b.max,e,f=[1,4,2,8],g,h,i,j,k,l,m,n,o=L.LineUtil;for(g=0,l=a.length;g<l;g++)a[g]._code=o._getBitCode(a[g],b);for(i=0;i<4;i++){m=f[i],e=[];for(g=0,l=a.length,h=l-1;g<l;h=g++)j=a[g],k=a[h],j._code&m?k._code&m||(n=o._getEdgeIntersection(k,j,m,b),n._code=o._getBitCode(n,b),e.push(n)):(k._code&m&&(n=o._getEdgeIntersection(k,j,m,b),n._code=o._getBitCode(n,b),e.push(n)),e.push(j));a=e}return a},L.Polygon=L.Polyline.extend({options:{fill:!0},initialize:function(a,b){L.Polyline.prototype.initialize.call(this,a,b),a&&a[0]instanceof Array&&(this._latlngs=a[0],this._holes=a.slice(1))},projectLatlngs:function(){L.Polyline.prototype.projectLatlngs.call(this),this._holePoints=[];if(!this._holes)return;for(var a=0,b=this._holes.length,c;a<b;a++){this._holePoints[a]=[];for(var d=0,e=this._holes[a].length;d<e;d++)this._holePoints[a][d]=this._map.latLngToLayerPoint(this._holes[a][d])}},_clipPoints:function(){var a=this._originalPoints,b=[];this._parts=[a].concat(this._holePoints);if(this.options.noClip)return;for(var c=0,d=this._parts.length;c<d;c++){var e=L.PolyUtil.clipPolygon(this._parts[c],this._map._pathViewport);if(!e.length)continue;b.push(e)}this._parts=b},_getPathPartStr:function(a){var b=L.Polyline.prototype._getPathPartStr.call(this,a);return b+(L.Browser.svg?"z":"x")}}),function(){function a(a){return L.FeatureGroup.extend({initialize:function(a,b){this._layers={},this._options=b,this.setLatLngs(a)},setLatLngs:function(b){var c=0,d=b.length;this._iterateLayers(function(a){c<d?a.setLatLngs(b[c++]):this.removeLayer(a)},this);while(c<d)this.addLayer(new a(b[c++],this._options))}})}L.MultiPolyline=a(L.Polyline),L.MultiPolygon=a(L.Polygon)}(),L.Circle=L.Path.extend({initialize:function(a,b,c){L.Path.prototype.initialize.call(this,c),this._latlng=a,this._mRadius=b},options:{fill:!0},setLatLng:function(a){return this._latlng=a,this._redraw(),this},setRadius:function(a){return this._mRadius=a,this._redraw(),this},projectLatlngs:function(){var a=40075017,b=a*Math.cos(L.LatLng.DEG_TO_RAD*this._latlng.lat),c=this._mRadius/b*360,d=new L.LatLng(this._latlng.lat,this._latlng.lng-c,!0),e=this._map.latLngToLayerPoint(d);this._point=this._map.latLngToLayerPoint(this._latlng),this._radius=Math.round(this._point.x-e.x)},getPathString:function(){var a=this._point,b=this._radius;return this._checkIfEmpty()?"":L.Browser.svg?"M"+a.x+","+(a.y-b)+"A"+b+","+b+",0,1,1,"+(a.x-.1)+","+(a.y-b)+" z":(a._round(),b=Math.round(b),"AL "+a.x+","+a.y+" "+b+","+b+" 0,"+23592600)},_checkIfEmpty:function(){var a=this._map._pathViewport,b=this._radius,c=this._point;return c.x-b>a.max.x||c.y-b>a.max.y||c.x+b<a.min.x||c.y+b<a.min.y}}),L.CircleMarker=L.Circle.extend({options:{radius:10,weight:2},initialize:function(a,b){L.Circle.prototype.initialize.call(this,a,null,b),this._radius=this.options.radius},projectLatlngs:function(){this._point=this._map.latLngToLayerPoint(this._latlng)},setRadius:function(a){return this._radius=a,this._redraw(),this}}),L.Polyline.include(L.Path.CANVAS?{_containsPoint:function(a,b){var c,d,e,f,g,h,i,j=this.options.weight/2;L.Browser.touch&&(j+=10);for(c=0,f=this._parts.length;c<f;c++){i=this._parts[c];for(d=0,g=i.length,e=g-1;d<g;e=d++){if(!b&&d===0)continue;h=L.LineUtil.pointToSegmentDistance(a,i[e],i[d]);if(h<=j)return!0}}return!1}}:{}),L.Polygon.include(L.Path.CANVAS?{_containsPoint:function(a){var b=!1,c,d,e,f,g,h,i,j;if(L.Polyline.prototype._containsPoint.call(this,a,!0))return!0;for(f=0,i=this._parts.length;f<i;f++){c=this._parts[f];for(g=0,j=c.length,h=j-1;g<j;h=g++)d=c[g],e=c[h],d.y>a.y!=e.y>a.y&&a.x<(e.x-d.x)*(a.y-d.y)/(e.y-d.y)+d.x&&(b=!b)}return b}}:{}),L.Circle.include(L.Path.CANVAS?{_drawPath:function(){var a=this._point;this._ctx.beginPath(),this._ctx.arc(a.x,a.y,this._radius,0,Math.PI*2)},_containsPoint:function(a){var b=this._point,c=this.options.stroke?this.options.weight/2:0;return a.distanceTo(b)<=this._radius+c}}:{}),L.GeoJSON=L.FeatureGroup.extend({initialize:function(a,b){L.Util.setOptions(this,b),this._geojson=a,this._layers={},a&&this.addGeoJSON(a)},addGeoJSON:function(a){if(a.features){for(var b=0,c=a.features.length;b<c;b++)this.addGeoJSON(a.features[b]);return}var d=a.type==="Feature",e=d?a.geometry:a,f=L.GeoJSON.geometryToLayer(e,this.options.pointToLayer);this.fire("featureparse",{layer:f,properties:a.properties,geometryType:e.type,bbox:a.bbox,id:a.id}),this.addLayer(f)}}),L.Util.extend(L.GeoJSON,{geometryToLayer:function(a,b){var c=a.coordinates,d,e,f,g,h,i=[];switch(a.type){case"Point":return d=this.coordsToLatLng(c),b?b(d):new L.Marker(d);case"MultiPoint":for(f=0,g=c.length;f<g;f++)d=this.coordsToLatLng(c[f]),h=b?b(d):new L.Marker(d),i.push(h);return new L.FeatureGroup(i);case"LineString":return e=this.coordsToLatLngs(c),new L.Polyline(e);case"Polygon":return e=this.coordsToLatLngs(c,1),new L.Polygon(e);case"MultiLineString":return e=this.coordsToLatLngs(c,1),new L.MultiPolyline(e);case"MultiPolygon":return e=this.coordsToLatLngs(c,2),new L.MultiPolygon(e);case"GeometryCollection":for(f=0,g=a.geometries.length;f<g;f++)h=this.geometryToLayer(a.geometries[f],b),i.push(h);return new L.FeatureGroup(i);default:throw Error("Invalid GeoJSON object.")}},coordsToLatLng:function(a,b){var c=parseFloat(a[b?0:1]),d=parseFloat(a[b?1:0]);return new L.LatLng(c,d,!0)},coordsToLatLngs:function(a,b,c){var d,e=[],f,g=a.length;for(f=0;f<g;f++)d=b?this.coordsToLatLngs(a[f],b-1,c):this.coordsToLatLng(a[f],c),e.push(d);return e}}),L.DomEvent={addListener:function(a,b,c,d){var e=L.Util.stamp(c),f="_leaflet_"+b+e;if(a[f])return;var g=function(b){return c.call(d||a,b||L.DomEvent._getEvent())};if(L.Browser.touch&&b==="dblclick"&&this.addDoubleTapListener)this.addDoubleTapListener(a,g,e);else if("addEventListener"in a)if(b==="mousewheel")a.addEventListener("DOMMouseScroll",g,!1),a.addEventListener(b,g,!1);else if(b==="mouseenter"||b==="mouseleave"){var h=g,i=b==="mouseenter"?"mouseover":"mouseout";g=function(b){if(!L.DomEvent._checkMouse(a,b))return;return h(b)},a.addEventListener(i,g,!1)}else a.addEventListener(b,g,!1);else"attachEvent"in a&&a.attachEvent("on"+b,g);a[f]=g},removeListener:function(a,b,c){var d=L.Util.stamp(c),e="_leaflet_"+b+d,f=a[e];if(!f)return;L.Browser.touch&&b==="dblclick"&&this.removeDoubleTapListener?this.removeDoubleTapListener(a,d):"removeEventListener"in a?b==="mousewheel"?(a.removeEventListener("DOMMouseScroll",f,!1),a.removeEventListener(b,f,!1)):b==="mouseenter"||b==="mouseleave"?a.removeEventListener(b==="mouseenter"?"mouseover":"mouseout",f,!1):a.removeEventListener(b,f,!1):"detachEvent"in a&&a.detachEvent("on"+b,f),a[e]=null},_checkMouse:function(a,b){var c=b.relatedTarget;if(!c)return!0;try{while(c&&c!==a)c=c.parentNode}catch(d){return!1}return c!==a},_getEvent:function(){var a=window.event;if(!a){var b=arguments.callee.caller;while(b){a=b.arguments[0];if(a&&window.Event===a.constructor)break;b=b.caller}}return a},stopPropagation:function(a){a.stopPropagation?a.stopPropagation():a.cancelBubble=!0},disableClickPropagation:function(a){L.DomEvent.addListener(a,L.Draggable.START,L.DomEvent.stopPropagation),L.DomEvent.addListener(a,"click",L.DomEvent.stopPropagation),L.DomEvent.addListener(a,"dblclick",L.DomEvent.stopPropagation)},preventDefault:function(a){a.preventDefault?a.preventDefault():a.returnValue=!1},stop:function(a){L.DomEvent.preventDefault(a),L.DomEvent.stopPropagation(a)},getMousePosition:function(a,b){var c=a.pageX?a.pageX:a.clientX+document.body.scrollLeft+document.documentElement.scrollLeft,d=a.pageY?a.pageY:a.clientY+document.body.scrollTop+document.documentElement.scrollTop,e=new L.Point(c,d);return b?e.subtract(L.DomUtil.getViewportOffset(b)):e},getWheelDelta:function(a){var b=0;return a.wheelDelta&&(b=a.wheelDelta/120),a.detail&&(b=-a.detail/3),b}},L.Draggable=L.Class.extend({includes:L.Mixin.Events,statics:{START:L.Browser.touch?"touchstart":"mousedown",END:L.Browser.touch?"touchend":"mouseup",MOVE:L.Browser.touch?"touchmove":"mousemove",TAP_TOLERANCE:15},initialize:function(a,b){this._element=a,this._dragStartTarget=b||a},enable:function(){if(this._enabled)return;L.DomEvent.addListener(this._dragStartTarget,L.Draggable.START,this._onDown,this),this._enabled=!0},disable:function(){if(!this._enabled)return;L.DomEvent.removeListener(this._dragStartTarget,L.Draggable.START,this._onDown),this._enabled=!1},_onDown:function(a){if(!L.Browser.touch&&a.shiftKey||a.which!==1&&a.button!==1&&!a.touches)return;if(a.touches&&a.touches.length>1)return;var b=a.touches&&a.touches.length===1?a.touches[0]:a,c=b.target;L.DomEvent.preventDefault(a),L.Browser.touch&&c.tagName.toLowerCase()==="a"&&(c.className+=" leaflet-active"),this._moved=!1;if(this._moving)return;L.Browser.touch||(L.DomUtil.disableTextSelection(),this._setMovingCursor()),this._startPos=this._newPos=L.DomUtil.getPosition(this._element),this._startPoint=new L.Point(b.clientX,b.clientY),L.DomEvent.addListener(document,L.Draggable.MOVE,this._onMove,this),L.DomEvent.addListener(document,L.Draggable.END,this._onUp,this)},_onMove:function(a){if(a.touches&&a.touches.length>1)return;L.DomEvent.preventDefault(a);var b=a.touches&&a.touches.length===1?a.touches[0]:a;this._moved||(this.fire("dragstart"),this._moved=!0),this._moving=!0;var c=new L.Point(b.clientX,b.clientY);this._newPos=this._startPos.add(c).subtract(this._startPoint),L.Util.requestAnimFrame(this._updatePosition,this,!0,this._dragStartTarget)},_updatePosition:function(){this.fire("predrag"),L.DomUtil.setPosition(this._element,this._newPos),this.fire("drag")},_onUp:function(a){if(a.changedTouches){var b=a.changedTouches[0],c=b.target,d=this._newPos&&this._newPos.distanceTo(this._startPos)||0;c.tagName.toLowerCase()==="a"&&(c.className=c.className.replace(" leaflet-active","")),d<L.Draggable.TAP_TOLERANCE&&this._simulateEvent("click",b)}L.Browser.touch||(L.DomUtil.enableTextSelection(),this._restoreCursor()),L.DomEvent.removeListener(document,L.Draggable.MOVE,this._onMove),L.DomEvent.removeListener(document,L.Draggable.END,this._onUp),this._moved&&this.fire("dragend"),this._moving=!1},_setMovingCursor:function(){this._bodyCursor=document.body.style.cursor,document.body.style.cursor="move"},_restoreCursor:function(){document.body.style.cursor=this._bodyCursor},_simulateEvent:function(a,b){var c=document.createEvent("MouseEvents");c.initMouseEvent(a,!0,!0,window,1,b.screenX,b.screenY,b.clientX,b.clientY,!1,!1,!1,!1,0,null),b.target.dispatchEvent(c)}}),L.Handler=L.Class.extend({initialize:function(a){this._map=a},enable:function(){if(this._enabled)return;this._enabled=!0,this.addHooks()},disable:function(){if(!this._enabled)return;this._enabled=!1,this.removeHooks()},enabled:function(){return!!this._enabled}}),L.Map.Drag=L.Handler.extend({addHooks:function(){if(!this._draggable){this._draggable=new L.Draggable(this._map._mapPane,this._map._container),this._draggable.on("dragstart",this._onDragStart,this).on("drag",this._onDrag,this).on("dragend",this._onDragEnd,this);var a=this._map.options;a.worldCopyJump&&!a.continuousWorld&&(this._draggable.on("predrag",this._onPreDrag,this),this._map.on("viewreset",this._onViewReset,this))}this._draggable.enable()},removeHooks:function(){this._draggable.disable()},moved:function(){return this._draggable&&this._draggable._moved},_onDragStart:function(){this._map.fire("movestart").fire("dragstart")},_onDrag:function(){this._map.fire("move").fire("drag")},_onViewReset:function(){var a=this._map.getSize().divideBy(2),b=this._map.latLngToLayerPoint(new L.LatLng(0,0));this._initialWorldOffset=b.subtract(a)},_onPreDrag:function(){var a=this._map,b=a.options.scale(a.getZoom()),c=Math.round(b/2),d=this._initialWorldOffset.x,e=this._draggable._newPos.x,f=(e-c+d)%b+c-d,g=(e+c+d)%b-c-d,h=Math.abs(f+d)<Math.abs(g+d)?f:g;this._draggable._newPos.x=h},_onDragEnd:function(){var a=this._map;a.fire("moveend").fire("dragend"),a.options.maxBounds&&L.Util.requestAnimFrame(this._panInsideMaxBounds,a,!0,a._container)},_panInsideMaxBounds:function(){this.panInsideBounds(this.options.maxBounds)}}),L.Map.DoubleClickZoom=L.Handler.extend({addHooks:function(){this._map.on("dblclick",this._onDoubleClick)},removeHooks:function(){this._map.off("dblclick",this._onDoubleClick)},_onDoubleClick:function(a){this.setView(a.latlng,this._zoom+1)}}),L.Map.ScrollWheelZoom=L.Handler.extend({addHooks:function(){L.DomEvent.addListener(this._map._container,"mousewheel",this._onWheelScroll,this),this._delta=0},removeHooks:function(){L.DomEvent.removeListener(this._map._container,"mousewheel",this._onWheelScroll)},_onWheelScroll:function(a){var b=L.DomEvent.getWheelDelta(a);this._delta+=b,this._lastMousePos=this._map.mouseEventToContainerPoint(a),clearTimeout(this._timer),this._timer=setTimeout(L.Util.bind(this._performZoom,this),50),L.DomEvent.preventDefault(a)},_performZoom:function(){var a=this._map,b=Math.round(this._delta),c=a.getZoom();b=Math.max(Math.min(b,4),-4),b=a._limitZoom(c+b)-c,this._delta=0;if(!b)return;var d=this._getCenterForScrollWheelZoom(this._lastMousePos,b),e=c+b;a.setView(d,e)},_getCenterForScrollWheelZoom:function(a,b){var c=this._map,d=c.getPixelBounds().getCenter(),e=c.getSize().divideBy(2),f=a.subtract(e).multiplyBy(1-Math.pow(2,-b)),g=d.add(f);return c.unproject(g,c._zoom,!0)}}),L.Util.extend(L.DomEvent,{addDoubleTapListener:function(a,b,c){function k(a){if(a.touches.length!==1)return;var b=Date.now(),c=b-(d||b);g=a.touches[0],e=c>0&&c<=f,d=b}function l(a){e&&(g.type="dblclick",b(g),d=null)}var d,e=!1,f=250,g,h="_leaflet_",i="touchstart",j="touchend";a[h+i+c]=k,a[h+j+c]=l,a.addEventListener(i,k,!1),a.addEventListener(j,l,!1)},removeDoubleTapListener:function(a,b){var c="_leaflet_";a.removeEventListener(a,a[c+"touchstart"+b],!1),a.removeEventListener(a,a[c+"touchend"+b],!1)}}),L.Map.TouchZoom=L.Handler.extend({addHooks:function(){L.DomEvent.addListener(this._map._container,"touchstart",this._onTouchStart,this)},removeHooks:function(){L.DomEvent.removeListener(this._map._container,"touchstart",this._onTouchStart,this)},_onTouchStart:function(a){if(!a.touches||a.touches.length!==2||this._map._animatingZoom)return;var b=this._map.mouseEventToLayerPoint(a.touches[0]),c=this._map.mouseEventToLayerPoint(a.touches[1]),d=this._map.containerPointToLayerPoint(this._map.getSize().divideBy(2));this._startCenter=b.add(c).divideBy(2,!0),this._startDist=b.distanceTo(c),this._moved=!1,this._zooming=!0,this._centerOffset=d.subtract(this._startCenter),L.DomEvent.addListener(document,"touchmove",this._onTouchMove,this),L.DomEvent.addListener(document,"touchend",this._onTouchEnd,this),L.DomEvent.preventDefault(a)},_onTouchMove:function(a){if(!a.touches||a.touches.length!==2)return;this._moved||(this._map._mapPane.className+=" leaflet-zoom-anim",this._map.fire("zoomstart").fire("movestart")._prepareTileBg(),this._moved=!0);var b=this._map.mouseEventToLayerPoint(a.touches[0]),c=this._map.mouseEventToLayerPoint(a.touches[1]);this._scale=b.distanceTo(c)/this._startDist,this._delta=b.add(c).divideBy(2,!0).subtract(this._startCenter),this._map._tileBg.style.webkitTransform=[L.DomUtil.getTranslateString(this._delta),L.DomUtil.getScaleString(this._scale,this._startCenter)].join(" "),L.DomEvent.preventDefault(a)},_onTouchEnd:function(a){if(!this._moved||!this._zooming)return;this._zooming=!1;var b=this._map.getZoom(),c=Math.log(this._scale)/Math.LN2,d=c>0?Math.ceil(c):Math.floor(c),e=this._map._limitZoom(b+d),f=e-b,g=this._centerOffset.subtract(this._delta).divideBy(this._scale),h=this._map.getPixelOrigin().add(this._startCenter).add(g),i=this._map.unproject(h);L.DomEvent.removeListener(document,"touchmove",this._onTouchMove),L.DomEvent.removeListener(document,"touchend",this._onTouchEnd);var j=Math.pow(2,f);this._map._runAnimation(i,e,j/this._scale,this._startCenter.add(g))}}),L.Map.BoxZoom=L.Handler.extend({initialize:function(a){this._map=a,this._container=a._container,this._pane=a._panes.overlayPane},addHooks:function(){L.DomEvent.addListener(this._container,"mousedown",this._onMouseDown,this)},removeHooks:function(){L.DomEvent.removeListener(this._container,"mousedown",this._onMouseDown)},_onMouseDown:function(a){if(!a.shiftKey||a.which!==1&&a.button!==1)return!1;L.DomUtil.disableTextSelection(),this._startLayerPoint=this._map.mouseEventToLayerPoint(a),this._box=L.DomUtil.create("div","leaflet-zoom-box",this._pane),L.DomUtil.setPosition(this._box,this._startLayerPoint),this._container.style.cursor="crosshair",L.DomEvent.addListener(document,"mousemove",this._onMouseMove,this),L.DomEvent.addListener(document,"mouseup",this._onMouseUp,this),L.DomEvent.preventDefault(a)},_onMouseMove:function(a){var b=this._map.mouseEventToLayerPoint(a),c=b.x-this._startLayerPoint.x,d=b.y-this._startLayerPoint.y,e=Math.min(b.x,this._startLayerPoint.x),f=Math.min(b.y,this._startLayerPoint.y),g=new L.Point(e,f);L.DomUtil.setPosition(this._box,g),this._box.style.width=Math.abs(c)-4+"px",this._box.style.height=Math.abs(d)-4+"px"},_onMouseUp:function(a){this._pane.removeChild(this._box),this._container.style.cursor="",L.DomUtil.enableTextSelection(),L.DomEvent.removeListener(document,"mousemove",this._onMouseMove),L.DomEvent.removeListener(document,"mouseup",this._onMouseUp);var b=this._map.mouseEventToLayerPoint(a),c=new L.LatLngBounds(this._map.layerPointToLatLng(this._startLayerPoint),this._map.layerPointToLatLng(b));this._map.fitBounds(c)}}),L.Handler.MarkerDrag=L.Handler.extend({initialize:function(a){this._marker=a},addHooks:function(){var a=this._marker._icon;this._draggable||(this._draggable=new L.Draggable(a,a),this._draggable.on("dragstart",this._onDragStart,this).on("drag",this._onDrag,this).on("dragend",this._onDragEnd,this)),this._draggable.enable()},removeHooks:function(){this._draggable.disable()},moved:function(){return this._draggable&&this._draggable._moved},_onDragStart:function(a){this._marker.closePopup().fire("movestart").fire("dragstart")},_onDrag:function(a){var b=L.DomUtil.getPosition(this._marker._icon);this._marker._shadow&&L.DomUtil.setPosition(this._marker._shadow,b),this._marker._latlng=this._marker._map.layerPointToLatLng(b),this._marker.fire("move").fire("drag")},_onDragEnd:function(){this._marker.fire("moveend").fire("dragend")}}),L.Control={},L.Control.Position={TOP_LEFT:"topLeft",TOP_RIGHT:"topRight",BOTTOM_LEFT:"bottomLeft",BOTTOM_RIGHT:"bottomRight"},L.Map.include({addControl:function(a){a.onAdd(this);var b=a.getPosition(),c=this._controlCorners[b],d=a.getContainer();return L.DomUtil.addClass(d,"leaflet-control"),b.indexOf("bottom")!==-1?c.insertBefore(d,c.firstChild):c.appendChild(d),this},removeControl:function(a){var b=a.getPosition(),c=this._controlCorners[b],d=a.getContainer();return c.removeChild(d),a.onRemove&&a.onRemove(this),this},_initControlPos:function(){var a=this._controlCorners={},b="leaflet-",c=b+"top",d=b+"bottom",e=b+"left",f=b+"right",g=L.DomUtil.create("div",b+"control-container",this._container);L.Browser.touch&&(g.className+=" "+b+"big-buttons"),a.topLeft=L.DomUtil.create("div",c+" "+e,g),a.topRight=L.DomUtil.create("div",c+" "+f,g),a.bottomLeft=L.DomUtil.create("div",d+" "+e,g),a.bottomRight=L.DomUtil.create("div",d+" "+f,g)}}),L.Control.Zoom=L.Class.extend({onAdd:function(a){this._map=a,this._container=L.DomUtil.create("div","leaflet-control-zoom"),this._zoomInButton=this._createButton("Zoom in","leaflet-control-zoom-in",this._map.zoomIn,this._map),this._zoomOutButton=this._createButton("Zoom out","leaflet-control-zoom-out",this._map.zoomOut,this._map),this._container.appendChild(this._zoomInButton),this._container.appendChild(this._zoomOutButton)},getContainer:function(){return this._container},getPosition:function(){return L.Control.Position.TOP_LEFT},_createButton:function(a,b,c,d){var e=document.createElement("a");return e.href="#",e.title=a,e.className=b,L.Browser.touch||L.DomEvent.disableClickPropagation(e),L.DomEvent.addListener(e,"click",L.DomEvent.preventDefault),L.DomEvent.addListener(e,"click",c,d),e}}),L.Control.Attribution=L.Class.extend({initialize:function(a){this._prefix=a||'Powered by <a href="http://leaflet.cloudmade.com">Leaflet</a>',this._attributions={}},onAdd:function(a){this._container=L.DomUtil.create("div","leaflet-control-attribution"),L.DomEvent.disableClickPropagation(this._container),this._map=a,this._update()},getPosition:function(){return L.Control.Position.BOTTOM_RIGHT},getContainer:function(){return this._container},setPrefix:function(a){this._prefix=a,this._update()},addAttribution:function(a){if(!a)return;this._attributions[a]||(this._attributions[a]=0),this._attributions[a]++,this._update()},removeAttribution:function(a){if(!a)return;this._attributions[a]--,this._update()},_update:function(){if(!this._map)return;var a=[];for(var b in this._attributions)this._attributions.hasOwnProperty(b)&&a.push(b);var c=[];this._prefix&&c.push(this._prefix),a.length&&c.push(a.join(", ")),this._container.innerHTML=c.join(" &mdash; ")}}),L.Control.Layers=L.Class.extend({options:{collapsed:!0},initialize:function(a,b,c){L.Util.setOptions(this,c),this._layers={};for(var d in a)a.hasOwnProperty(d)&&this._addLayer(a[d],d);for(d in b)b.hasOwnProperty(d)&&this._addLayer(b[d],d,!0)},onAdd:function(a){this._map=a,this._initLayout(),this._update()},getContainer:function(){return this._container},getPosition:function(){return L.Control.Position.TOP_RIGHT},addBaseLayer:function(a,b){return this._addLayer(a,b),this._update(),this},addOverlay:function(a,b){return this._addLayer(a,b,!0),this._update(),this},removeLayer:function(a){var b=L.Util.stamp(a);return delete this._layers[b],this._update(),this},_initLayout:function(){this._container=L.DomUtil.create("div","leaflet-control-layers"),L.Browser.touch||L.DomEvent.disableClickPropagation(this._container),this._form=L.DomUtil.create("form","leaflet-control-layers-list");if(this.options.collapsed){L.DomEvent.addListener(this._container,"mouseover",this._expand,this),L.DomEvent.addListener(this._container,"mouseout",this._collapse,this);var a=this._layersLink=L.DomUtil.create("a","leaflet-control-layers-toggle");a.href="#",a.title="Layers",L.Browser.touch?L.DomEvent.addListener(a,"click",this._expand,this):L.DomEvent.addListener(a,"focus",this._expand,this),this._map.on("movestart",this._collapse,this),this._container.appendChild(a)}else this._expand();this._baseLayersList=L.DomUtil.create("div","leaflet-control-layers-base",this._form),this._separator=L.DomUtil.create("div","leaflet-control-layers-separator",this._form),this._overlaysList=L.DomUtil.create("div","leaflet-control-layers-overlays",this._form),this._container.appendChild(this._form)},_addLayer:function(a,b,c){var d=L.Util.stamp(a);this._layers[d]={layer:a,name:b,overlay:c}},_update:function(){if(!this._container)return;this._baseLayersList.innerHTML="",this._overlaysList.innerHTML="";var a=!1,b=!1;for(var c in this._layers)if(this._layers.hasOwnProperty(c)){var d=this._layers[c];this._addItem(d),b=b||d.overlay,a=a||!d.overlay}this._separator.style.display=b&&a?"":"none"},_addItem:function(a,b){var c=document.createElement("label"),d=document.createElement("input");a.overlay||(d.name="leaflet-base-layers"),d.type=a.overlay?"checkbox":"radio",d.checked=this._map.hasLayer(a.layer),d.layerId=L.Util.stamp(a.layer),L.DomEvent.addListener(d,"click",this._onInputClick,this);var e=document.createTextNode(" "+a.name);c.appendChild(d),c.appendChild(e);var f=a.overlay?this._overlaysList:this._baseLayersList;f.appendChild(c)},_onInputClick:function(){var a,b,c,d=this._form.getElementsByTagName("input"),e=d.length;for(a=0;a<e;a++)b=d[a],c=this._layers[b.layerId],b.checked?this._map.addLayer(c.layer,!c.overlay):this._map.removeLayer(c.layer)},_expand:function(){L.DomUtil.addClass(this._container,"leaflet-control-layers-expanded")},_collapse:function(){this._container.className=this._container.className.replace(" leaflet-control-layers-expanded","")}}),L.Transition=L.Class.extend({includes:L.Mixin.Events,statics:{CUSTOM_PROPS_SETTERS:{position:L.DomUtil.setPosition},implemented:function(){return L.Transition.NATIVE||L.Transition.TIMER}},options:{easing:"ease",duration:.5},_setProperty:function(a,b){var c=L.Transition.CUSTOM_PROPS_SETTERS;a in c?c[a](this._el,b):this._el.style[a]=b}}),L.Transition=L.Transition.extend({statics:function(){var a=L.DomUtil.TRANSITION,b=a==="webkitTransition"||a==="OTransition"?a+"End":"transitionend";return{NATIVE:!!a,TRANSITION:a,PROPERTY:a+"Property",DURATION:a+"Duration",EASING:a+"TimingFunction",END:b,CUSTOM_PROPS_PROPERTIES:{position:L.Browser.webkit?L.DomUtil.TRANSFORM:"top, left"}}}(),options:{fakeStepInterval:100},initialize:function(a,b){this._el=a,L.Util.setOptions(this,b),L.DomEvent.addListener(a,L.Transition.END,this._onTransitionEnd,this),this._onFakeStep=L.Util.bind(this._onFakeStep,this)},run:function(a){var b,c=[],d=L.Transition.CUSTOM_PROPS_PROPERTIES;for(b in a)a.hasOwnProperty(b)&&(b=d[b]?d[b]:b,b=this._dasherize(b),c.push(b));this._el.style[L.Transition.DURATION]=this.options.duration+"s",this._el.style[L.Transition.EASING]=this.options.easing,this._el.style[L.Transition.PROPERTY]=c.join(", ");for(b in a)a.hasOwnProperty(b)&&this._setProperty(b,a[b]);this._inProgress=!0,this.fire("start"),L.Transition.NATIVE?(clearInterval(this._timer),this._timer=setInterval(this._onFakeStep,this.options.fakeStepInterval)):this._onTransitionEnd()},_dasherize:function(){function b(a){return"-"+a.toLowerCase()}var a=/([A-Z])/g;return function(c){return c.replace(a,b)}}(),_onFakeStep:function(){this.fire("step")},_onTransitionEnd:function(){this._inProgress&&(this._inProgress=!1,clearInterval(this._timer),this._el.style[L.Transition.PROPERTY]="none",this.fire("step"),this.fire("end"))}}),L.Transition=L.Transition.NATIVE?L.Transition:L.Transition.extend({statics:{getTime:Date.now||function(){return+(new Date)},TIMER:!0,EASINGS:{ease:[.25,.1,.25,1],linear:[0,0,1,1],"ease-in":[.42,0,1,1],"ease-out":[0,0,.58,1],"ease-in-out":[.42,0,.58,1]},CUSTOM_PROPS_GETTERS:{position:L.DomUtil.getPosition},UNIT_RE:/^[\d\.]+(\D*)$/},options:{fps:50},initialize:function(a,b){this._el=a,L.Util.extend(this.options,b);var c=L.Transition.EASINGS[this.options.easing]||L.Transition.EASINGS.ease;this._p1=new L.Point(0,0),this._p2=new L.Point(c[0],c[1]),this._p3=new L.Point(c[2],c[3]),this._p4=new L.Point(1,1),this._step=L.Util.bind(this._step,this),this._interval=Math.round(1e3/this.options.fps)},run:function(a){this._props={};var b=L.Transition.CUSTOM_PROPS_GETTERS,c=L.Transition.UNIT_RE;this.fire("start");for(var d in a)if(a.hasOwnProperty(d)){var e={};if(d in b)e.from=b[d](this._el);else{var f=this._el.style[d].match(c);e.from=parseFloat(f[0]),e.unit=f[1]}e.to=a[d],this._props[d]=e}clearInterval(this._timer),this._timer=setInterval(this._step,this._interval),this._startTime=L.Transition.getTime()},_step:function(){var a=L.Transition.getTime(),b=a-this._startTime,c=this.options.duration*1e3;b<c?this._runFrame(this._cubicBezier(b/c)):(this._runFrame(1),this._complete())},_runFrame:function(a){var b=L.Transition.CUSTOM_PROPS_SETTERS,c,d,e;for(c in this._props)this._props.hasOwnProperty(c)&&(d=this._props[c],c in b?(e=d.to.subtract(d.from).multiplyBy(a).add(d.from),b[c](this._el,e)):this._el.style[c]=(d.to-d.from)*a+d.from+d.unit);this.fire("step")},_complete:function(){clearInterval(this._timer),this.fire("end")},_cubicBezier:function(a){var b=Math.pow(1-a,3),c=3*Math.pow(1-a,2)*a,d=3*(1-a)*Math.pow(a,2),e=Math.pow(a,3),f=this._p1.multiplyBy(b),g=this._p2.multiplyBy(c),h=this._p3.multiplyBy(d),i=this._p4.multiplyBy(e);return f.add(g).add(h).add(i).y}}),L.Map.include(!L.Transition||!L.Transition.implemented()?{}:{setView:function(a,b,c){b=this._limitZoom(b);var d=this._zoom!==b;if(this._loaded&&!c&&this._layers){var e=this._getNewTopLeftPoint(a).subtract(this._getTopLeftPoint());a=new L.LatLng(a.lat,a.lng);var f=d?!!this._zoomToIfCenterInView&&this._zoomToIfCenterInView(a,b,e):this._panByIfClose(e);if(f)return this}return this._resetView(a,b),this},panBy:function(a){return!a.x&&!a.y?this:(this._panTransition||(this._panTransition=new L.Transition(this._mapPane,{duration:.3}),this._panTransition.on("step",this._onPanTransitionStep,this),this._panTransition.on("end",this._onPanTransitionEnd,this)),this.fire("movestart"),this._panTransition.run({position:L.DomUtil.getPosition(this._mapPane).subtract(a)}),this)},_onPanTransitionStep:function(){this.fire("move")},_onPanTransitionEnd:function(){this.fire("moveend")},_panByIfClose:function(a){return this._offsetIsWithinView(a)?(this.panBy(a),!0):!1},_offsetIsWithinView:function(a,b){var c=b||1,d=this.getSize();return Math.abs(a.x)<=d.x*c&&Math.abs(a.y)<=d.y*c}}),L.Map.include(L.DomUtil.TRANSITION?{_zoomToIfCenterInView:function(a,b,c){if(this._animatingZoom)return!0;if(!this.options.zoomAnimation)return!1;var d=b-this._zoom,e=Math.pow(2,d),f=c.divideBy(1-1/e);if(!this._offsetIsWithinView(f,1))return!1;this._mapPane.className+=" leaflet-zoom-anim",this.fire("movestart").fire("zoomstart");var g=this.containerPointToLayerPoint(this.getSize().divideBy(2)),h=g.add(f);return this._prepareTileBg(),this._runAnimation(a,b,e,h),!0},_runAnimation:function(a,b,c,d){this._animatingZoom=!0,this._animateToCenter=a,this._animateToZoom=b;var e=L.DomUtil.TRANSFORM;clearTimeout(this._clearTileBgTimer);if(L.Browser.gecko||window.opera)this._tileBg.style[e]+=" translate(0,0)";var f;L.Browser.android?(this._tileBg.style[e+"Origin"]=d.x+"px "+d.y+"px",f="scale("+c+")"):f=L.DomUtil.getScaleString(c,d),L.Util.falseFn(this._tileBg.offsetWidth);var g={};g[e]=this._tileBg.style[e]+" "+f,this._tileBg.transition.run(g)},_prepareTileBg:function(){this._tileBg||(this._tileBg=this._createPane("leaflet-tile-pane",this._mapPane),this._tileBg.style.zIndex=1);var a=this._tilePane,b=this._tileBg;b.style[L.DomUtil.TRANSFORM]="",b.style.visibility="hidden",b.empty=!0,a.empty=!1,this._tilePane=this._panes.tilePane=b,this._tileBg=a,this._tileBg.transition||(this._tileBg.transition=new L.Transition(this._tileBg,{duration:.3,easing:"cubic-bezier(0.25,0.1,0.25,0.75)"}),this._tileBg.transition.on("end",this._onZoomTransitionEnd,this)),this._stopLoadingBgTiles()},_stopLoadingBgTiles:function(){var a=[].slice.call(this._tileBg.getElementsByTagName("img"));for(var b=0,c=a.length;b<c;b++)a[b].complete||(a[b].onload=L.Util.falseFn,a[b].onerror=L.Util.falseFn,a[b].src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=",a[b].parentNode.removeChild(a[b]),a[b]=null)},_onZoomTransitionEnd:function(){this._restoreTileFront(),L.Util.falseFn(this._tileBg.offsetWidth),this._resetView(this._animateToCenter,this._animateToZoom,!0,!0),this._mapPane.className=this._mapPane.className.replace(" leaflet-zoom-anim",""),this._animatingZoom=!1},_restoreTileFront:function(){this._tilePane.innerHTML="",this._tilePane.style.visibility="",this._tilePane.style.zIndex=2,this._tileBg.style.zIndex=1},_clearTileBg:function(){!this._animatingZoom&&!this.touchZoom._zooming&&(this._tileBg.innerHTML="")}}:{}),L.Map.include({locate:function(a){this._locationOptions=a=L.Util.extend({watch:!1,setView:!1,maxZoom:Infinity,timeout:1e4,maximumAge:0,enableHighAccuracy:!1},a);if(!navigator.geolocation)return this.fire("locationerror",{code:0,message:"Geolocation not supported."});var b=L.Util.bind(this._handleGeolocationResponse,this),c=L.Util.bind(this._handleGeolocationError,this);return a.watch?this._locationWatchId=navigator.geolocation.watchPosition(b,c,a):navigator.geolocation.getCurrentPosition(b,c,a),this},stopLocate:function(){navigator.geolocation&&navigator.geolocation.clearWatch(this._locationWatchId)},locateAndSetView:function(a,b){return b=L.Util.extend({maxZoom:a||Infinity,setView:!0},b),this.locate(b)},_handleGeolocationError:function(a){var b=a.code,c=b===1?"permission denied":b===2?"position unavailable":"timeout";this._locationOptions.setView&&!this._loaded&&this.fitWorld(),this.fire("locationerror",{code:b,message:"Geolocation error: "+c+"."})},_handleGeolocationResponse:function(a){var b=180*a.coords.accuracy/4e7,c=b*2,d=a.coords.latitude,e=a.coords.longitude,f=new L.LatLng(d,e),g=new L.LatLng(d-b,e-c),h=new L.LatLng(d+b,e+c),i=new L.LatLngBounds(g,h);if(this._locationOptions.setView){var j=Math.min(this.getBoundsZoom(i),this._locationOptions.maxZoom);this.setView(f,j)}this.fire("locationfound",{latlng:f,bounds:i,accuracy:a.coords.accuracy})}});

var tweetTemplate = jQuery('#tweetBlock').html(),
	photoTemplate = jQuery('#photoBlock').html(),
	venueTemplate = jQuery('#venueBlock').html(),
	twitterStream = jQuery('#twitterstream'),
	instagramFeed = jQuery('#instagramfeed'),
	hotspots = jQuery('#hotspots'),
	announceTweet = jQuery('#announcetweet'),
	twTrickle = false,
	inTrickle = false,
	twitterIndex = new Array(),
	instagramIndex = new Array(),
	fCount = 0,

	checkIns = new Array(),
	topics = new Array(),
	hot_topics = new Array(),

	/* These two arrays should be filled out! */
	banned_words = ["cunt", "bastard", "asshole"],
	ignored_keywords = ["the", "at", "rt", "a", "and", "if", "of", "for", "you", "from", "with", "don't", "about", "check", "your", "have", "this", "says", "not", "you're", "who", "what", "when", "where", "why"];

;(function() {
    tData("tbex", 0); // where "wwdc" is the tag!
    iData("tbex", 0); // where "snow" is the tag!
    fData(39.607937, -105.958371, 0);
    initializeMap(39.607937, -105.958371);
    tAnnouncement("tbexevents");
})();

/**
 * Initialzes the map using MapBox (Wax & Leaflet)
**/
function initializeMap(latitude, longitude) {
    var map = new L.Map('map', { zoomControl: false });
    var cloudmade = new L.TileLayer('http://{s}.tile.cloudmade.com/ac0ea41b1d2b4cee9b49b2ea29a91a4c/997/256/{z}/{x}/{y}.png', {
	    maxZoom: 18
	});

    var MyIcon = L.Icon.extend({
	    iconUrl: '<?php echo get_bloginfo("template_directory"); ?>/library/images/marker.png',
	    shadowUrl: '<?php echo get_bloginfo("template_directory"); ?>/library/images/marker-shadow.png',
	});

	var icon = new MyIcon();

    var markerLocation = new L.LatLng(latitude, longitude);
    var marker = new L.Marker(markerLocation, {icon: icon});
    map.addLayer(marker);

    var center = new L.LatLng(latitude, longitude); // geographical point (longitude and latitude)
	map.setView(center, 15).addLayer(cloudmade);
}

/**
 * Gets all the latest Instagram photos by tag. Refreshes after 1 minute
**/
function iData(tag, delay) {
	setTimeout(function() {
		jQuery.ajax({
			url: "https://api.instagram.com/v1/tags/" + tag + "/media/recent",
			data: "callback=?&access_token=1685551.4dbe36a.1970e3a32fff4545a4e3ceded4e059ee",
			dataType: "json",
			success: function(data) {
				var photos = new Array();
				for (var i = 0; i < data.data.length; i++) {
					var photoEntry = {
						id: data.data[i].id,
						link: data.data[i].link,
						low_resolution: data.data[i].images.low_resolution.url
					}
					if (jQuery.inArray(photoEntry.id, instagramIndex) === -1) {
	                	photos.push(photoEntry);
	                	instagramIndex.push(photoEntry.id);
						if (inTrickle === false)
	    					inTrickle = trickle(instagramFeed.attr("id"), inTrickle, 2, 0);
	    			}
				}
				formatPhotos(photos);
				get_hot_topics();
			},
			error: function(data) {
		        console.log('Error: Instagram feed');
		    },
		});
		iData(tag, 60000)
    }, delay);
}

/**
 * Gets a mix of latest and most popular tweets, and puts the most relevant info into an array.
 * formatTweets is then called. Function called repeatedly every 12 seconds.
 * TODO: Filter tweets that contain banned words
**/
function tData(tag, delay) {
    setTimeout(function() {
    	jQuery.ajax({
	        url: "http://search.twitter.com/search.json",
	        data: "q=" + escape(tag) + "&callback=?&rpp=50",
	        dataType: "json",
	        success: function(data) {
	            var tweets = new Array();
	            for (var i = 0; i < data['results'].length; i++) {
	                var tweetEntry = {
	                	id: data['results'][i].id_str,
	                    profile_image_url: data['results'][i].profile_image_url,
	                    from_user: data['results'][i].from_user,
	                    text: data['results'][i].text,
	                    created_at: data['results'][i].created_at,
	                    permalink: 'http://twitter.com/#!/'+ data['results'][i].from_user +'/status/'+ data['results'][i].id_str,
	                    profile: 'http://twitter.com/#!/'+ data['results'][i].from_user,
	                };


	                if (jQuery.inArray(tweetEntry.id, twitterIndex) === -1) {
	                	tweets.push(tweetEntry);
	                	twitterIndex.push(tweetEntry.id);
	                	if (twTrickle === false)
    						twTrickle = trickle(twitterStream.attr("id"), twTrickle, 4, 0);
    				}
	            }
	            formatTweets(tweets);
	        },
	        error: function() {
	            console.log('Error: Twitter feed');
	        },
	    });
		tData(tag, 12000)
    }, delay);
}

function tAnnouncement(username) {
	jQuery.ajax({
		url: "http://api.twitter.com/1/statuses/user_timeline.json",
		data: "screen_name=" + username + "&exclude_replies=true&count=1&callback=?&oauth_token=14943957-K7Yfpq3XI4vKAqRh4O8zHeKP2MkMTYY5DwQEcRkvQ",
		dataType: "json",
		success: function(data) {
			var announceText = twttr.txt.autoLink(data[0].text);
			var permalink = 'http://twitter.com/#!/'+ data[0].from_user +'/status/'+ data[0].id_str;
			var createdAt = timeAgo(data[0].created_at);
			announceTweet.empty();
			announceTweet.html(announceText);
			announceTweet.append(" <a href=" + permalink + " target=\"_blank\">" + createdAt + "</a>")
		},
		error: function(data) {
			console.log('Error: Announcement tweet');
		}
	})
}


function fData(latitude, longitude, delay) {
	setTimeout(function() {
		jQuery.ajax({
	        url: "https://api.foursquare.com/v2/venues/search",
	        data: "ll=" + latitude + "," + longitude + "&radius=8000&client_id=XNQXGYR5PVCJIH4RYSHGBO5EBY4MOV5L5MCKRCKNPOPIWSMJ&client_secret=FPEOXXV30ZMSLSNJR3XLC3HW2L2L5MKSXGTYJNBMTTTM00FC&v=20120614",
	        dataType: "json",
	        success: function(data) {
	            var venues = new Array();
	            fCount = 0;
				for (var i = 0; i < data.response.venues.length; i++) {
					var venueEntry = {
						id: data.response.venues[i].id,
						name: data.response.venues[i].name,
						checkins: data.response.venues[i].stats.checkinsCount
					}
					fCount += venueEntry.checkins;
	                venues.push(venueEntry);
				}
				formatVenues(venues);
	        },
	        error: function() {
	            console.log('Error: Foursquare feed');
	        },
	    });
    	fData(latitude, longitude, 60000)
    }, delay);
}

/**
 * Objects from tData get formatted into the JS 'tweetBlock' template.
 * Skips a batch if too many tweets are in the animation queue.
**/
function formatTweets(tweets) {
    if (jQuery('#twitterstream').children('article').filter(':hidden').length > 3) {
    	//console.log('no formatting!');
    }
    else {
    	
	    for (var i = tweets.length -1; i > -1; i--) {

	    	if (jQuery.inArray(tweets[i].text, banned_words) !== -1)
	    			console.log("banned!");
	    	else {
		        twitterStream.prepend(tweetTemplate
		            .replace(/{{profile_image_url}}/, tweets[i].profile_image_url)
		            .replace(/{{from_user}}/, tweets[i].from_user)
		            .replace(/{{text}}/, twttr.txt.autoLink(tweets[i].text, tweets[i].entities))
		            .replace(/{{created_at}}/, timeAgo(tweets[i].created_at))
		            .replace(/{{permalink}}/, tweets[i].permalink)
		        	.replace(/{{profile}}/, tweets[i].profile));
				populate_topics(tweets[i].text.split(" "));
			}
	    }
	}
}

function formatPhotos(photos) {
	if (jQuery('#instagramfeed').children('article').filter(':hidden').length > 3) {
    	//console.log('no formatting!');
    }
    else {
	    for (var i = photos.length -1; i > -1; i--) {
	        instagramFeed.prepend(photoTemplate
	            .replace(/{{low_resolution}}/, photos[i].low_resolution)
	            .replace(/{{link}}/, photos[i].link));
	    }
	}
}

function formatVenues(venues) {
	var fCountDiv = jQuery('#foursquare span.green').eq(0).html(addCommas(fCount));

	checkIns = venues.sort(function(a,b){
	    var result = b.checkins - a.checkins;
	    return result;
	});
	hotspots.empty();
	for (var i = 0; i < 10; i++) {
	    hotspots.append(venueTemplate
	        .replace(/{{name}}/, checkIns[i].name)
	        .replace(/{{checkins}}/, addCommas(checkIns[i].checkins)));
	}
	
}

/**
 * Performs trickle-style animation. Old tweets get removed, new tweets slide in.
**/
function trickle(node, isCalled, count, delay) {
	
    setTimeout(function() {
    	isCalled = true;
    	var stream = jQuery('#' + node).children('article');
    	var visible = stream.filter(':visible');
    	var hidden = stream.filter(':hidden');
    	if (visible.length > count && hidden.length !== undefined) {
    		visible.eq(count).fadeOut(300, function() {
    			jQuery(this).remove();
    		});
    	}
    	if (hidden.length !== undefined) {
        	var entry = hidden.eq(hidden.length - 1);
        	entry.slideDown(600);
        }
        trickle(node, isCalled, count, 5000);
    }, delay);     
}

/**
 * Updates the list of all legitimate keywords (ignored words are filtered out).
**/
function populate_topics(arr) {
	for (var a in arr) {
		var value = arr[a].toLowerCase(),
			found = false;
		if (jQuery.inArray(value, ignored_keywords) !== -1 || value.length < 3)
			continue;
		for (var i = 0; i < topics.length; i++) {
			if (topics[i].keyword === value) {
				topics[i].count++;
				found = true;
			}
		}
		if (found === false) {
			var obj = {
				keyword: value,
				count: 0
			};
			topics.push(obj);
		}

	}
}

/**
 * Sorts the list of legitmate keywords by occurrence. This updates on each Twitter search call. 
**/
function get_hot_topics() {
	hot_topics = topics.sort(function(a,b){
	    var result = b.count - a.count;
	    return result;
	});
	var arr = [];
	for (var i = 0; i < 8; i++) {
		arr.push("<a href=\"https://twitter.com/#!/search?q=" + escape(hot_topics[i].keyword) + "\">" + hot_topics[i].keyword + "</a>");
	}
	var str = arr.join(", ");
	var node = jQuery('#hottopics').html(str);
}

function addCommas(nStr)
{
	nStr += '';
	x = nStr.split('.');
	x1 = x[0];
	x2 = x.length > 1 ? '.' + x[1] : '';
	var rgx = /(\d+)(\d{3})/;
	while (rgx.test(x1)) {
		x1 = x1.replace(rgx, '$1' + ',' + '$2');
	}
	return x1 + x2;
}


function timeAgo(dateString){var rightNow=new Date();var then=new Date(dateString);var diff=rightNow-then;var second=1000,minute=second*60,hour=minute*60,day=hour*24,week=day*7;if(isNaN(diff)||diff<0)
return"";if(diff<second*2)
return"right now";if(diff<minute)
return Math.floor(diff/second)+" seconds ago";if(diff<minute*2)
return"about 1 minute ago";if(diff<hour)
return Math.floor(diff/minute)+" minutes ago";if(diff<hour*2)
return"about 1 hour ago";if(diff<day)
return Math.floor(diff/hour)+" hours ago";if(diff>day&&diff<day*2)
return"yesterday";if(diff<day*365)
return Math.floor(diff/day)+" days ago";else
return"over a year ago";};
if (typeof window === "undefined" || window === null) {
  window = { twttr: {} };
}
if (window.twttr == null) {
  window.twttr = {};
}
if (typeof twttr === "undefined" || twttr === null) {
  twttr = {};
}

(function() {
  twttr.txt = {};
  twttr.txt.regexen = {};

  var HTML_ENTITIES = {
    '&': '&amp;',
    '>': '&gt;',
    '<': '&lt;',
    '"': '&quot;',
    "'": '&#39;'
  };

  // HTML escaping
  twttr.txt.htmlEscape = function(text) {
    return text && text.replace(/[&"'><]/g, function(character) {
      return HTML_ENTITIES[character];
    });
  };

  // Builds a RegExp
  function regexSupplant(regex, flags) {
    flags = flags || "";
    if (typeof regex !== "string") {
      if (regex.global && flags.indexOf("g") < 0) {
        flags += "g";
      }
      if (regex.ignoreCase && flags.indexOf("i") < 0) {
        flags += "i";
      }
      if (regex.multiline && flags.indexOf("m") < 0) {
        flags += "m";
      }

      regex = regex.source;
    }

    return new RegExp(regex.replace(/#\{(\w+)\}/g, function(match, name) {
      var newRegex = twttr.txt.regexen[name] || "";
      if (typeof newRegex !== "string") {
        newRegex = newRegex.source;
      }
      return newRegex;
    }), flags);
  }

  twttr.txt.regexSupplant = regexSupplant;

  // simple string interpolation
  function stringSupplant(str, values) {
    return str.replace(/#\{(\w+)\}/g, function(match, name) {
      return values[name] || "";
    });
  }

  twttr.txt.stringSupplant = stringSupplant;

  function addCharsToCharClass(charClass, start, end) {
    var s = String.fromCharCode(start);
    if (end !== start) {
      s += "-" + String.fromCharCode(end);
    }
    charClass.push(s);
    return charClass;
  }

  twttr.txt.addCharsToCharClass = addCharsToCharClass;

  // Space is more than %20, U+3000 for example is the full-width space used with Kanji. Provide a short-hand
  // to access both the list of characters and a pattern suitible for use with String#split
  // Taken from: ActiveSupport::Multibyte::Handlers::UTF8Handler::UNICODE_WHITESPACE
  var fromCode = String.fromCharCode;
  var UNICODE_SPACES = [
    fromCode(0x0020), // White_Space # Zs       SPACE
    fromCode(0x0085), // White_Space # Cc       <control-0085>
    fromCode(0x00A0), // White_Space # Zs       NO-BREAK SPACE
    fromCode(0x1680), // White_Space # Zs       OGHAM SPACE MARK
    fromCode(0x180E), // White_Space # Zs       MONGOLIAN VOWEL SEPARATOR
    fromCode(0x2028), // White_Space # Zl       LINE SEPARATOR
    fromCode(0x2029), // White_Space # Zp       PARAGRAPH SEPARATOR
    fromCode(0x202F), // White_Space # Zs       NARROW NO-BREAK SPACE
    fromCode(0x205F), // White_Space # Zs       MEDIUM MATHEMATICAL SPACE
    fromCode(0x3000)  // White_Space # Zs       IDEOGRAPHIC SPACE
  ];
  addCharsToCharClass(UNICODE_SPACES, 0x009, 0x00D); // White_Space # Cc   [5] <control-0009>..<control-000D>
  addCharsToCharClass(UNICODE_SPACES, 0x2000, 0x200A); // White_Space # Zs  [11] EN QUAD..HAIR SPACE

  var INVALID_CHARS = [
    fromCode(0xFFFE),
    fromCode(0xFEFF), // BOM
    fromCode(0xFFFF) // Special
  ];
  addCharsToCharClass(INVALID_CHARS, 0x202A, 0x202E); // Directional change

  twttr.txt.regexen.spaces_group = regexSupplant(UNICODE_SPACES.join(""));
  twttr.txt.regexen.spaces = regexSupplant("[" + UNICODE_SPACES.join("") + "]");
  twttr.txt.regexen.invalid_chars_group = regexSupplant(INVALID_CHARS.join(""));
  twttr.txt.regexen.punct = /\!'#%&'\(\)*\+,\\\-\.\/:;<=>\?@\[\]\^_{|}~\$/;

  var nonLatinHashtagChars = [];
  // Cyrillic
  addCharsToCharClass(nonLatinHashtagChars, 0x0400, 0x04ff); // Cyrillic
  addCharsToCharClass(nonLatinHashtagChars, 0x0500, 0x0527); // Cyrillic Supplement
  addCharsToCharClass(nonLatinHashtagChars, 0x2de0, 0x2dff); // Cyrillic Extended A
  addCharsToCharClass(nonLatinHashtagChars, 0xa640, 0xa69f); // Cyrillic Extended B
  // Hebrew
  addCharsToCharClass(nonLatinHashtagChars, 0x0591, 0x05bf); // Hebrew
  addCharsToCharClass(nonLatinHashtagChars, 0x05c1, 0x05c2);
  addCharsToCharClass(nonLatinHashtagChars, 0x05c4, 0x05c5);
  addCharsToCharClass(nonLatinHashtagChars, 0x05c7, 0x05c7);
  addCharsToCharClass(nonLatinHashtagChars, 0x05d0, 0x05ea);
  addCharsToCharClass(nonLatinHashtagChars, 0x05f0, 0x05f4);
  addCharsToCharClass(nonLatinHashtagChars, 0xfb12, 0xfb28); // Hebrew Presentation Forms
  addCharsToCharClass(nonLatinHashtagChars, 0xfb2a, 0xfb36);
  addCharsToCharClass(nonLatinHashtagChars, 0xfb38, 0xfb3c);
  addCharsToCharClass(nonLatinHashtagChars, 0xfb3e, 0xfb3e);
  addCharsToCharClass(nonLatinHashtagChars, 0xfb40, 0xfb41);
  addCharsToCharClass(nonLatinHashtagChars, 0xfb43, 0xfb44);
  addCharsToCharClass(nonLatinHashtagChars, 0xfb46, 0xfb4f);
  // Arabic
  addCharsToCharClass(nonLatinHashtagChars, 0x0610, 0x061a); // Arabic
  addCharsToCharClass(nonLatinHashtagChars, 0x0620, 0x065f);
  addCharsToCharClass(nonLatinHashtagChars, 0x066e, 0x06d3);
  addCharsToCharClass(nonLatinHashtagChars, 0x06d5, 0x06dc);
  addCharsToCharClass(nonLatinHashtagChars, 0x06de, 0x06e8);
  addCharsToCharClass(nonLatinHashtagChars, 0x06ea, 0x06ef);
  addCharsToCharClass(nonLatinHashtagChars, 0x06fa, 0x06fc);
  addCharsToCharClass(nonLatinHashtagChars, 0x06ff, 0x06ff);
  addCharsToCharClass(nonLatinHashtagChars, 0x0750, 0x077f); // Arabic Supplement
  addCharsToCharClass(nonLatinHashtagChars, 0x08a0, 0x08a0); // Arabic Extended A
  addCharsToCharClass(nonLatinHashtagChars, 0x08a2, 0x08ac);
  addCharsToCharClass(nonLatinHashtagChars, 0x08e4, 0x08fe);
  addCharsToCharClass(nonLatinHashtagChars, 0xfb50, 0xfbb1); // Arabic Pres. Forms A
  addCharsToCharClass(nonLatinHashtagChars, 0xfbd3, 0xfd3d);
  addCharsToCharClass(nonLatinHashtagChars, 0xfd50, 0xfd8f);
  addCharsToCharClass(nonLatinHashtagChars, 0xfd92, 0xfdc7);
  addCharsToCharClass(nonLatinHashtagChars, 0xfdf0, 0xfdfb);
  addCharsToCharClass(nonLatinHashtagChars, 0xfe70, 0xfe74); // Arabic Pres. Forms B
  addCharsToCharClass(nonLatinHashtagChars, 0xfe76, 0xfefc);
  addCharsToCharClass(nonLatinHashtagChars, 0x200c, 0x200c); // Zero-Width Non-Joiner
  // Thai
  addCharsToCharClass(nonLatinHashtagChars, 0x0e01, 0x0e3a);
  addCharsToCharClass(nonLatinHashtagChars, 0x0e40, 0x0e4e);
  // Hangul (Korean)
  addCharsToCharClass(nonLatinHashtagChars, 0x1100, 0x11ff); // Hangul Jamo
  addCharsToCharClass(nonLatinHashtagChars, 0x3130, 0x3185); // Hangul Compatibility Jamo
  addCharsToCharClass(nonLatinHashtagChars, 0xA960, 0xA97F); // Hangul Jamo Extended-A
  addCharsToCharClass(nonLatinHashtagChars, 0xAC00, 0xD7AF); // Hangul Syllables
  addCharsToCharClass(nonLatinHashtagChars, 0xD7B0, 0xD7FF); // Hangul Jamo Extended-B
  addCharsToCharClass(nonLatinHashtagChars, 0xFFA1, 0xFFDC); // half-width Hangul
  // Japanese and Chinese
  addCharsToCharClass(nonLatinHashtagChars, 0x30A1, 0x30FA); // Katakana (full-width)
  addCharsToCharClass(nonLatinHashtagChars, 0x30FC, 0x30FE); // Katakana Chouon and iteration marks (full-width)
  addCharsToCharClass(nonLatinHashtagChars, 0xFF66, 0xFF9F); // Katakana (half-width)
  addCharsToCharClass(nonLatinHashtagChars, 0xFF70, 0xFF70); // Katakana Chouon (half-width)
  addCharsToCharClass(nonLatinHashtagChars, 0xFF10, 0xFF19); // \
  addCharsToCharClass(nonLatinHashtagChars, 0xFF21, 0xFF3A); //  - Latin (full-width)
  addCharsToCharClass(nonLatinHashtagChars, 0xFF41, 0xFF5A); // /
  addCharsToCharClass(nonLatinHashtagChars, 0x3041, 0x3096); // Hiragana
  addCharsToCharClass(nonLatinHashtagChars, 0x3099, 0x309E); // Hiragana voicing and iteration mark
  addCharsToCharClass(nonLatinHashtagChars, 0x3400, 0x4DBF); // Kanji (CJK Extension A)
  addCharsToCharClass(nonLatinHashtagChars, 0x4E00, 0x9FFF); // Kanji (Unified)
  // -- Disabled as it breaks the Regex.
  //addCharsToCharClass(nonLatinHashtagChars, 0x20000, 0x2A6DF); // Kanji (CJK Extension B)
  addCharsToCharClass(nonLatinHashtagChars, 0x2A700, 0x2B73F); // Kanji (CJK Extension C)
  addCharsToCharClass(nonLatinHashtagChars, 0x2B740, 0x2B81F); // Kanji (CJK Extension D)
  addCharsToCharClass(nonLatinHashtagChars, 0x2F800, 0x2FA1F); // Kanji (CJK supplement)
  addCharsToCharClass(nonLatinHashtagChars, 0x3003, 0x3003); // Kanji iteration mark
  addCharsToCharClass(nonLatinHashtagChars, 0x3005, 0x3005); // Kanji iteration mark
  addCharsToCharClass(nonLatinHashtagChars, 0x303B, 0x303B); // Han iteration mark

  twttr.txt.regexen.nonLatinHashtagChars = regexSupplant(nonLatinHashtagChars.join(""));

  var latinAccentChars = [];
  // Latin accented characters (subtracted 0xD7 from the range, it's a confusable multiplication sign. Looks like "x")
  addCharsToCharClass(latinAccentChars, 0x00c0, 0x00d6);
  addCharsToCharClass(latinAccentChars, 0x00d8, 0x00f6);
  addCharsToCharClass(latinAccentChars, 0x00f8, 0x00ff);
  // Latin Extended A and B
  addCharsToCharClass(latinAccentChars, 0x0100, 0x024f);
  // assorted IPA Extensions
  addCharsToCharClass(latinAccentChars, 0x0253, 0x0254);
  addCharsToCharClass(latinAccentChars, 0x0256, 0x0257);
  addCharsToCharClass(latinAccentChars, 0x0259, 0x0259);
  addCharsToCharClass(latinAccentChars, 0x025b, 0x025b);
  addCharsToCharClass(latinAccentChars, 0x0263, 0x0263);
  addCharsToCharClass(latinAccentChars, 0x0268, 0x0268);
  addCharsToCharClass(latinAccentChars, 0x026f, 0x026f);
  addCharsToCharClass(latinAccentChars, 0x0272, 0x0272);
  addCharsToCharClass(latinAccentChars, 0x0289, 0x0289);
  addCharsToCharClass(latinAccentChars, 0x028b, 0x028b);
  // Okina for Hawaiian (it *is* a letter character)
  addCharsToCharClass(latinAccentChars, 0x02bb, 0x02bb);
  // Combining diacritics
  addCharsToCharClass(latinAccentChars, 0x0300, 0x036f);
  // Latin Extended Additional
  addCharsToCharClass(latinAccentChars, 0x1e00, 0x1eff);
  twttr.txt.regexen.latinAccentChars = regexSupplant(latinAccentChars.join(""));

  // A hashtag must contain characters, numbers and underscores, but not all numbers.
  twttr.txt.regexen.hashSigns = /[#＃]/;
  twttr.txt.regexen.hashtagAlpha = regexSupplant(/[a-z_#{latinAccentChars}#{nonLatinHashtagChars}]/i);
  twttr.txt.regexen.hashtagAlphaNumeric = regexSupplant(/[a-z0-9_#{latinAccentChars}#{nonLatinHashtagChars}]/i);
  twttr.txt.regexen.endHashtagMatch = regexSupplant(/^(?:#{hashSigns}|:\/\/)/);
  twttr.txt.regexen.hashtagBoundary = regexSupplant(/(?:^|$|[^&a-z0-9_#{latinAccentChars}#{nonLatinHashtagChars}])/);
  twttr.txt.regexen.validHashtag = regexSupplant(/(#{hashtagBoundary})(#{hashSigns})(#{hashtagAlphaNumeric}*#{hashtagAlpha}#{hashtagAlphaNumeric}*)/gi);

  // Mention related regex collection
  twttr.txt.regexen.validMentionPrecedingChars = /(?:^|[^a-zA-Z0-9_!#$%&*@＠]|RT:?)/;
  twttr.txt.regexen.atSigns = /[@＠]/;
  twttr.txt.regexen.validMentionOrList = regexSupplant(
    '(#{validMentionPrecedingChars})' +  // $1: Preceding character
    '(#{atSigns})' +                     // $2: At mark
    '([a-zA-Z0-9_]{1,20})' +             // $3: Screen name
    '(\/[a-zA-Z][a-zA-Z0-9_\-]{0,24})?'  // $4: List (optional)
  , 'g');
  twttr.txt.regexen.validReply = regexSupplant(/^(?:#{spaces})*#{atSigns}([a-zA-Z0-9_]{1,20})/);
  twttr.txt.regexen.endMentionMatch = regexSupplant(/^(?:#{atSigns}|[#{latinAccentChars}]|:\/\/)/);

  // URL related regex collection
  twttr.txt.regexen.validUrlPrecedingChars = regexSupplant(/(?:[^A-Za-z0-9@＠$#＃#{invalid_chars_group}]|^)/);
  twttr.txt.regexen.invalidUrlWithoutProtocolPrecedingChars = /[-_.\/]$/;
  twttr.txt.regexen.invalidDomainChars = stringSupplant("#{punct}#{spaces_group}#{invalid_chars_group}", twttr.txt.regexen);
  twttr.txt.regexen.validDomainChars = regexSupplant(/[^#{invalidDomainChars}]/);
  twttr.txt.regexen.validSubdomain = regexSupplant(/(?:(?:#{validDomainChars}(?:[_-]|#{validDomainChars})*)?#{validDomainChars}\.)/);
  twttr.txt.regexen.validDomainName = regexSupplant(/(?:(?:#{validDomainChars}(?:-|#{validDomainChars})*)?#{validDomainChars}\.)/);
  twttr.txt.regexen.validGTLD = regexSupplant(/(?:(?:aero|asia|biz|cat|com|coop|edu|gov|info|int|jobs|mil|mobi|museum|name|net|org|pro|tel|travel|xxx)(?=[^0-9a-zA-Z]|$))/);
  twttr.txt.regexen.validCCTLD = regexSupplant(/(?:(?:ac|ad|ae|af|ag|ai|al|am|an|ao|aq|ar|as|at|au|aw|ax|az|ba|bb|bd|be|bf|bg|bh|bi|bj|bm|bn|bo|br|bs|bt|bv|bw|by|bz|ca|cc|cd|cf|cg|ch|ci|ck|cl|cm|cn|co|cr|cs|cu|cv|cx|cy|cz|dd|de|dj|dk|dm|do|dz|ec|ee|eg|eh|er|es|et|eu|fi|fj|fk|fm|fo|fr|ga|gb|gd|ge|gf|gg|gh|gi|gl|gm|gn|gp|gq|gr|gs|gt|gu|gw|gy|hk|hm|hn|hr|ht|hu|id|ie|il|im|in|io|iq|ir|is|it|je|jm|jo|jp|ke|kg|kh|ki|km|kn|kp|kr|kw|ky|kz|la|lb|lc|li|lk|lr|ls|lt|lu|lv|ly|ma|mc|md|me|mg|mh|mk|ml|mm|mn|mo|mp|mq|mr|ms|mt|mu|mv|mw|mx|my|mz|na|nc|ne|nf|ng|ni|nl|no|np|nr|nu|nz|om|pa|pe|pf|pg|ph|pk|pl|pm|pn|pr|ps|pt|pw|py|qa|re|ro|rs|ru|rw|sa|sb|sc|sd|se|sg|sh|si|sj|sk|sl|sm|sn|so|sr|ss|st|su|sv|sy|sz|tc|td|tf|tg|th|tj|tk|tl|tm|tn|to|tp|tr|tt|tv|tw|tz|ua|ug|uk|us|uy|uz|va|vc|ve|vg|vi|vn|vu|wf|ws|ye|yt|za|zm|zw)(?=[^0-9a-zA-Z]|$))/);
  twttr.txt.regexen.validPunycode = regexSupplant(/(?:xn--[0-9a-z]+)/);
  twttr.txt.regexen.validDomain = regexSupplant(/(?:#{validSubdomain}*#{validDomainName}(?:#{validGTLD}|#{validCCTLD}|#{validPunycode}))/);
  twttr.txt.regexen.validAsciiDomain = regexSupplant(/(?:(?:[a-z0-9#{latinAccentChars}]+)\.)+(?:#{validGTLD}|#{validCCTLD}|#{validPunycode})/gi);
  twttr.txt.regexen.invalidShortDomain = regexSupplant(/^#{validDomainName}#{validCCTLD}$/);

  twttr.txt.regexen.validPortNumber = regexSupplant(/[0-9]+/);

  twttr.txt.regexen.validGeneralUrlPathChars = regexSupplant(/[a-z0-9!\*';:=\+,\.\$\/%#\[\]\-_~|&#{latinAccentChars}]/i);
  // Allow URL paths to contain balanced parens
  //  1. Used in Wikipedia URLs like /Primer_(film)
  //  2. Used in IIS sessions like /S(dfd346)/
  twttr.txt.regexen.validUrlBalancedParens = regexSupplant(/\(#{validGeneralUrlPathChars}+\)/i);
  // Valid end-of-path chracters (so /foo. does not gobble the period).
  // 1. Allow =&# for empty URL parameters and other URL-join artifacts
  twttr.txt.regexen.validUrlPathEndingChars = regexSupplant(/[\+\-a-z0-9=_#\/#{latinAccentChars}]|(?:#{validUrlBalancedParens})/i);
  // Allow @ in a url, but only in the middle. Catch things like http://example.com/@user/
  twttr.txt.regexen.validUrlPath = regexSupplant('(?:' +
    '(?:' +
      '#{validGeneralUrlPathChars}*' +
        '(?:#{validUrlBalancedParens}#{validGeneralUrlPathChars}*)*' +
        '#{validUrlPathEndingChars}'+
      ')|(?:@#{validGeneralUrlPathChars}+\/)'+
    ')', 'i');

  twttr.txt.regexen.validUrlQueryChars = /[a-z0-9!?\*'\(\);:&=\+\$\/%#\[\]\-_\.,~|]/i;
  twttr.txt.regexen.validUrlQueryEndingChars = /[a-z0-9_&=#\/]/i;
  twttr.txt.regexen.extractUrl = regexSupplant(
    '('                                                            + // $1 total match
      '(#{validUrlPrecedingChars})'                                + // $2 Preceeding chracter
      '('                                                          + // $3 URL
        '(https?:\\/\\/)?'                                         + // $4 Protocol (optional)
        '(#{validDomain})'                                         + // $5 Domain(s)
        '(?::(#{validPortNumber}))?'                               + // $6 Port number (optional)
        '(\\/#{validUrlPath}*)?'                                   + // $7 URL Path
        '(\\?#{validUrlQueryChars}*#{validUrlQueryEndingChars})?'  + // $8 Query String
      ')'                                                          +
    ')'
  , 'gi');

  twttr.txt.regexen.validTcoUrl = /^https?:\/\/t\.co\/[a-z0-9]+/i;

  // cashtag related regex
  twttr.txt.regexen.cashtag = /[a-z]{1,6}(?:[._][a-z]{1,2})?/i;
  twttr.txt.regexen.validCashtag = regexSupplant('(?:^|#{spaces})\\$(#{cashtag})(?=$|\\s|[#{punct}])', 'gi');

  // These URL validation pattern strings are based on the ABNF from RFC 3986
  twttr.txt.regexen.validateUrlUnreserved = /[a-z0-9\-._~]/i;
  twttr.txt.regexen.validateUrlPctEncoded = /(?:%[0-9a-f]{2})/i;
  twttr.txt.regexen.validateUrlSubDelims = /[!$&'()*+,;=]/i;
  twttr.txt.regexen.validateUrlPchar = regexSupplant('(?:' +
    '#{validateUrlUnreserved}|' +
    '#{validateUrlPctEncoded}|' +
    '#{validateUrlSubDelims}|' +
    '[:|@]' +
  ')', 'i');

  twttr.txt.regexen.validateUrlScheme = /(?:[a-z][a-z0-9+\-.]*)/i;
  twttr.txt.regexen.validateUrlUserinfo = regexSupplant('(?:' +
    '#{validateUrlUnreserved}|' +
    '#{validateUrlPctEncoded}|' +
    '#{validateUrlSubDelims}|' +
    ':' +
  ')*', 'i');

  twttr.txt.regexen.validateUrlDecOctet = /(?:[0-9]|(?:[1-9][0-9])|(?:1[0-9]{2})|(?:2[0-4][0-9])|(?:25[0-5]))/i;
  twttr.txt.regexen.validateUrlIpv4 = regexSupplant(/(?:#{validateUrlDecOctet}(?:\.#{validateUrlDecOctet}){3})/i);

  // Punting on real IPv6 validation for now
  twttr.txt.regexen.validateUrlIpv6 = /(?:\[[a-f0-9:\.]+\])/i;

  // Also punting on IPvFuture for now
  twttr.txt.regexen.validateUrlIp = regexSupplant('(?:' +
    '#{validateUrlIpv4}|' +
    '#{validateUrlIpv6}' +
  ')', 'i');

  // This is more strict than the rfc specifies
  twttr.txt.regexen.validateUrlSubDomainSegment = /(?:[a-z0-9](?:[a-z0-9_\-]*[a-z0-9])?)/i;
  twttr.txt.regexen.validateUrlDomainSegment = /(?:[a-z0-9](?:[a-z0-9\-]*[a-z0-9])?)/i;
  twttr.txt.regexen.validateUrlDomainTld = /(?:[a-z](?:[a-z0-9\-]*[a-z0-9])?)/i;
  twttr.txt.regexen.validateUrlDomain = regexSupplant(/(?:(?:#{validateUrlSubDomainSegment]}\.)*(?:#{validateUrlDomainSegment]}\.)#{validateUrlDomainTld})/i);

  twttr.txt.regexen.validateUrlHost = regexSupplant('(?:' +
    '#{validateUrlIp}|' +
    '#{validateUrlDomain}' +
  ')', 'i');

  // Unencoded internationalized domains - this doesn't check for invalid UTF-8 sequences
  twttr.txt.regexen.validateUrlUnicodeSubDomainSegment = /(?:(?:[a-z0-9]|[^\u0000-\u007f])(?:(?:[a-z0-9_\-]|[^\u0000-\u007f])*(?:[a-z0-9]|[^\u0000-\u007f]))?)/i;
  twttr.txt.regexen.validateUrlUnicodeDomainSegment = /(?:(?:[a-z0-9]|[^\u0000-\u007f])(?:(?:[a-z0-9\-]|[^\u0000-\u007f])*(?:[a-z0-9]|[^\u0000-\u007f]))?)/i;
  twttr.txt.regexen.validateUrlUnicodeDomainTld = /(?:(?:[a-z]|[^\u0000-\u007f])(?:(?:[a-z0-9\-]|[^\u0000-\u007f])*(?:[a-z0-9]|[^\u0000-\u007f]))?)/i;
  twttr.txt.regexen.validateUrlUnicodeDomain = regexSupplant(/(?:(?:#{validateUrlUnicodeSubDomainSegment}\.)*(?:#{validateUrlUnicodeDomainSegment}\.)#{validateUrlUnicodeDomainTld})/i);

  twttr.txt.regexen.validateUrlUnicodeHost = regexSupplant('(?:' +
    '#{validateUrlIp}|' +
    '#{validateUrlUnicodeDomain}' +
  ')', 'i');

  twttr.txt.regexen.validateUrlPort = /[0-9]{1,5}/;

  twttr.txt.regexen.validateUrlUnicodeAuthority = regexSupplant(
    '(?:(#{validateUrlUserinfo})@)?'  + // $1 userinfo
    '(#{validateUrlUnicodeHost})'     + // $2 host
    '(?::(#{validateUrlPort}))?'        //$3 port
  , "i");

  twttr.txt.regexen.validateUrlAuthority = regexSupplant(
    '(?:(#{validateUrlUserinfo})@)?' + // $1 userinfo
    '(#{validateUrlHost})'           + // $2 host
    '(?::(#{validateUrlPort}))?'       // $3 port
  , "i");

  twttr.txt.regexen.validateUrlPath = regexSupplant(/(\/#{validateUrlPchar}*)*/i);
  twttr.txt.regexen.validateUrlQuery = regexSupplant(/(#{validateUrlPchar}|\/|\?)*/i);
  twttr.txt.regexen.validateUrlFragment = regexSupplant(/(#{validateUrlPchar}|\/|\?)*/i);

  // Modified version of RFC 3986 Appendix B
  twttr.txt.regexen.validateUrlUnencoded = regexSupplant(
    '^'                               + // Full URL
    '(?:'                             +
      '([^:/?#]+):\\/\\/'             + // $1 Scheme
    ')?'                              +
    '([^/?#]*)'                       + // $2 Authority
    '([^?#]*)'                        + // $3 Path
    '(?:'                             +
      '\\?([^#]*)'                    + // $4 Query
    ')?'                              +
    '(?:'                             +
      '#(.*)'                         + // $5 Fragment
    ')?$'
  , "i");


  // Default CSS class for auto-linked URLs
  var DEFAULT_URL_CLASS = "tweet-url";
  // Default CSS class for auto-linked lists (along with the url class)
  var DEFAULT_LIST_CLASS = "list-slug";
  // Default CSS class for auto-linked usernames (along with the url class)
  var DEFAULT_USERNAME_CLASS = "username";
  // Default CSS class for auto-linked hashtags (along with the url class)
  var DEFAULT_HASHTAG_CLASS = "hashtag";
  // Default CSS class for auto-linked cashtags (along with the url class)
  var DEFAULT_CASHTAG_CLASS = "cashtag";
  // HTML attribute for robot nofollow behavior (default)
  var HTML_ATTR_NO_FOLLOW = " rel=\"nofollow\"";
  // Options which should not be passed as HTML attributes
  var OPTIONS_NOT_ATTRIBUTES = {'urlClass':true, 'listClass':true, 'usernameClass':true, 'hashtagClass':true, 'cashtagClass':true,
                            'usernameUrlBase':true, 'listUrlBase':true, 'hashtagUrlBase':true, 'cashtagUrlBase':true,
                            'usernameUrlBlock':true, 'listUrlBlock':true, 'hashtagUrlBlock':true, 'linkUrlBlock':true,
                            'usernameIncludeSymbol':true, 'suppressLists':true, 'suppressNoFollow':true,
                            'suppressDataScreenName':true, 'urlEntities':true, 'before':true
                            };
  var BOOLEAN_ATTRIBUTES = {'disabled':true, 'readonly':true, 'multiple':true, 'checked':true};

  // Simple object cloning function for simple objects
  function clone(o) {
    var r = {};
    for (var k in o) {
      if (o.hasOwnProperty(k)) {
        r[k] = o[k];
      }
    }

    return r;
  }

  twttr.txt.linkToHashtag = function(entity, text, options) {
    var d = {
        hash: text.substring(entity.indices[0], entity.indices[0] + 1),
        preText: "",
        text: twttr.txt.htmlEscape(entity.hashtag),
        postText: "",
        extraHtml: options.suppressNoFollow ? "" : HTML_ATTR_NO_FOLLOW
      };
      for (var k in options) {
        if (options.hasOwnProperty(k)) {
          d[k] = options[k];
        }
      }

      return stringSupplant("#{before}<a href=\"#{hashtagUrlBase}#{text}\" title=\"##{text}\" class=\"#{urlClass} #{hashtagClass}\"#{extraHtml}>#{hash}#{preText}#{text}#{postText}</a>", d);
  };

  twttr.txt.linkToCashtag = function(entity, text, options) {
    var d = {
        preText: "",
        text: twttr.txt.htmlEscape(entity.cashtag),
        postText: "",
        extraHtml: options.suppressNoFollow ? "" : HTML_ATTR_NO_FOLLOW
      };
      for (var k in options) {
        if (options.hasOwnProperty(k)) {
          d[k] = options[k];
        }
      }

      return stringSupplant("#{before}<a href=\"#{cashtagUrlBase}#{text}\" title=\"$#{text}\" class=\"#{urlClass} #{cashtagClass}\"#{extraHtml}>$#{preText}#{text}#{postText}</a>", d);
  };

  twttr.txt.linkToMentionAndList = function(entity, text, options) {
    var at = text.substring(entity.indices[0], entity.indices[0] + 1);
    var d = {
      at: options.usernameIncludeSymbol ? "" : at,
      at_before_user: options.usernameIncludeSymbol ? at : "",
      user: twttr.txt.htmlEscape(entity.screenName),
      slashListname: twttr.txt.htmlEscape(entity.listSlug),
      extraHtml: options.suppressNoFollow ? "" : HTML_ATTR_NO_FOLLOW,
      preChunk: "",
      postChunk: ""
    };
    for (var k in options) {
      if (options.hasOwnProperty(k)) {
        d[k] = options[k];
      }
    }

    if (entity.listSlug && !options.suppressLists) {
      // the link is a list
      var list = d.chunk = stringSupplant("#{user}#{slashListname}", d);
      d.list = twttr.txt.htmlEscape(list.toLowerCase());
      return stringSupplant("#{before}#{at}<a class=\"#{urlClass} #{listClass}\" href=\"#{listUrlBase}#{list}\"#{extraHtml}>#{preChunk}#{at_before_user}#{chunk}#{postChunk}</a>", d);
    } else {
      // this is a screen name
      d.chunk = d.user;
      d.dataScreenName = !options.suppressDataScreenName ? stringSupplant("data-screen-name=\"#{chunk}\" ", d) : "";
      return stringSupplant("#{before}#{at}<a class=\"#{urlClass} #{usernameClass}\" #{dataScreenName}href=\"#{usernameUrlBase}#{chunk}\"#{extraHtml}>#{preChunk}#{at_before_user}#{chunk}#{postChunk}</a>", d);
    }
  };

  twttr.txt.linkToUrl = function(entity, text, options) {
    var url = entity.url;
    var displayUrl = url;
    var linkText = twttr.txt.htmlEscape(displayUrl);

    // If the caller passed a urlEntities object (provided by a Twitter API
    // response with include_entities=true), we use that to render the display_url
    // for each URL instead of it's underlying t.co URL.
    var urlEntity = (options.urlEntities && options.urlEntities[url]) || entity;
    if (urlEntity.display_url) {
      if (!options.title) {
        options.htmlAttrs = (options.htmlAttrs || "") + " title=\"" + urlEntity.expanded_url + "\"";
      }
      linkText = twttr.txt.linkTextWithEntity(urlEntity, options);
    }

    var d = {
      htmlAttrs: options.htmlAttrs,
      url: twttr.txt.htmlEscape(url),
      linkText: linkText
    };

    return stringSupplant("<a href=\"#{url}\"#{htmlAttrs}>#{linkText}</a>", d);
  };

  twttr.txt.linkTextWithEntity = function (entity, options) {
    var displayUrl = entity.display_url;
    var expandedUrl = entity.expanded_url;

    // Goal: If a user copies and pastes a tweet containing t.co'ed link, the resulting paste
    // should contain the full original URL (expanded_url), not the display URL.
    //
    // Method: Whenever possible, we actually emit HTML that contains expanded_url, and use
    // font-size:0 to hide those parts that should not be displayed (because they are not part of display_url).
    // Elements with font-size:0 get copied even though they are not visible.
    // Note that display:none doesn't work here. Elements with display:none don't get copied.
    //
    // Additionally, we want to *display* ellipses, but we don't want them copied.  To make this happen we
    // wrap the ellipses in a tco-ellipsis class and provide an onCopy handler that sets display:none on
    // everything with the tco-ellipsis class.
    //
    // Exception: pic.twitter.com images, for which expandedUrl = "https://twitter.com/#!/username/status/1234/photo/1
    // For those URLs, display_url is not a substring of expanded_url, so we don't do anything special to render the elided parts.
    // For a pic.twitter.com URL, the only elided part will be the "https://", so this is fine.

    var displayUrlSansEllipses = displayUrl.replace(/…/g, ""); // We have to disregard ellipses for matching
    // Note: we currently only support eliding parts of the URL at the beginning or the end.
    // Eventually we may want to elide parts of the URL in the *middle*.  If so, this code will
    // become more complicated.  We will probably want to create a regexp out of display URL,
    // replacing every ellipsis with a ".*".
    if (expandedUrl.indexOf(displayUrlSansEllipses) != -1) {
      var displayUrlIndex = expandedUrl.indexOf(displayUrlSansEllipses);
      var v = {
        displayUrlSansEllipses: displayUrlSansEllipses,
        // Portion of expandedUrl that precedes the displayUrl substring
        beforeDisplayUrl: expandedUrl.substr(0, displayUrlIndex),
        // Portion of expandedUrl that comes after displayUrl
        afterDisplayUrl: expandedUrl.substr(displayUrlIndex + displayUrlSansEllipses.length),
        precedingEllipsis: displayUrl.match(/^…/) ? "…" : "",
        followingEllipsis: displayUrl.match(/…$/) ? "…" : ""
      };
      for (var k in v) {
        if (v.hasOwnProperty(k)) {
          v[k] = twttr.txt.htmlEscape(v[k]);
        }
      }
      // As an example: The user tweets "hi http://longdomainname.com/foo"
      // This gets shortened to "hi http://t.co/xyzabc", with display_url = "…nname.com/foo"
      // This will get rendered as:
      // <span class='tco-ellipsis'> <!-- This stuff should get displayed but not copied -->
      //   …
      //   <!-- There's a chance the onCopy event handler might not fire. In case that happens,
      //        we include an &nbsp; here so that the … doesn't bump up against the URL and ruin it.
      //        The &nbsp; is inside the tco-ellipsis span so that when the onCopy handler *does*
      //        fire, it doesn't get copied.  Otherwise the copied text would have two spaces in a row,
      //        e.g. "hi  http://longdomainname.com/foo".
      //   <span style='font-size:0'>&nbsp;</span>
      // </span>
      // <span style='font-size:0'>  <!-- This stuff should get copied but not displayed -->
      //   http://longdomai
      // </span>
      // <span class='js-display-url'> <!-- This stuff should get displayed *and* copied -->
      //   nname.com/foo
      // </span>
      // <span class='tco-ellipsis'> <!-- This stuff should get displayed but not copied -->
      //   <span style='font-size:0'>&nbsp;</span>
      //   …
      // </span>
      v['invisible'] = options.invisibleTagAttrs;
      return stringSupplant("<span class='tco-ellipsis'>#{precedingEllipsis}<span #{invisible}>&nbsp;</span></span><span #{invisible}>#{beforeDisplayUrl}</span><span class='js-display-url'>#{displayUrlSansEllipses}</span><span #{invisible}>#{afterDisplayUrl}</span><span class='tco-ellipsis'><span #{invisible}>&nbsp;</span>#{followingEllipsis}</span>", v);
    }
    return displayUrl;
  };

  twttr.txt.autoLinkEntities = function(text, entities, options) {
    options = clone(options || {});

    if (!options.suppressNoFollow) {
      options.rel = "nofollow";
    }
    if (options.urlClass) {
      options["class"] = options.urlClass;
    }
    options.urlClass = options.urlClass || DEFAULT_URL_CLASS;
    options.hashtagClass = options.hashtagClass || DEFAULT_HASHTAG_CLASS;
    options.hashtagUrlBase = options.hashtagUrlBase || "https://twitter.com/#!/search?q=%23";
    options.cashtagClass = options.cashtagClass || DEFAULT_CASHTAG_CLASS;
    options.cashtagUrlBase = options.cashtagUrlBase || "https://twitter.com/#!/search?q=%24";
    options.listClass = options.listClass || DEFAULT_LIST_CLASS;
    options.usernameClass = options.usernameClass || DEFAULT_USERNAME_CLASS;
    options.usernameUrlBase = options.usernameUrlBase || "https://twitter.com/";
    options.listUrlBase = options.listUrlBase || "https://twitter.com/";
    options.before = options.before || "";
    options.htmlAttrs = twttr.txt.extractHtmlAttrsFromOptions(options);
    options.invisibleTagAttrs = options.invisibleTagAttrs || "style='position:absolute;left:-9999px;'";

    // remap url entities to hash
    var urlEntities, i, len;
    if(options.urlEntities) {
      urlEntities = {};
      for(i = 0, len = options.urlEntities.length; i < len; i++) {
        urlEntities[options.urlEntities[i].url] = options.urlEntities[i];
      }
      options.urlEntities = urlEntities;
    }

    var result = "";
    var beginIndex = 0;

    // sort entities by start index
    entities.sort(function(a,b){ return a.indices[0] - b.indices[0]; });

    for (var i = 0; i < entities.length; i++) {
      var entity = entities[i];
      result += text.substring(beginIndex, entity.indices[0]);

      if (entity.url) {
        result += twttr.txt.linkToUrl(entity, text, options);
      } else if (entity.hashtag) {
        result += twttr.txt.linkToHashtag(entity, text, options);
      } else if (entity.screenName) {
        result += twttr.txt.linkToMentionAndList(entity, text, options);
      } else if (entity.cashtag) {
        result += twttr.txt.linkToCashtag(entity, text, options);
      }
      beginIndex = entity.indices[1];
    }
    result += text.substring(beginIndex, text.length);
    return result;
  };

  twttr.txt.autoLinkWithJSON = function(text, json, options) {
    // concatenate all entities
    var entities = [];
    for (var key in json) {
      entities = entities.concat(json[key]);
    }
    // map JSON entity to twitter-text entity
    for (var i = 0; i < entities.length; i++) {
      entity = entities[i];
      if (entity.screen_name) {
        // this is @mention
        entity.screenName = entity.screen_name;
      } else if (entity.text) {
        // this is #hashtag
        entity.hashtag = entity.text;
      }
    }
    // modify indices to UTF-16
    twttr.txt.modifyIndicesFromUnicodeToUTF16(text, entities);

    return twttr.txt.autoLinkEntities(text, entities, options);
  };

  twttr.txt.extractHtmlAttrsFromOptions = function(options) {
    var htmlAttrs = "";
    for (var k in options) {
      var v = options[k];
      if (OPTIONS_NOT_ATTRIBUTES[k]) continue;
      if (BOOLEAN_ATTRIBUTES[k]) {
        v = v ? k : null;
      }
      if (v == null) continue;
      htmlAttrs += stringSupplant(" #{k}=\"#{v}\" ", {k: twttr.txt.htmlEscape(k), v: twttr.txt.htmlEscape(v.toString())});
    }
    return htmlAttrs;
  };

  twttr.txt.autoLink = function(text, options) {
    var entities = twttr.txt.extractEntitiesWithIndices(text, {extractUrlWithoutProtocol: false});
    return twttr.txt.autoLinkEntities(text, entities, options);
  };

  twttr.txt.autoLinkUsernamesOrLists = function(text, options) {
    var entities = twttr.txt.extractMentionsOrListsWithIndices(text);
    return twttr.txt.autoLinkEntities(text, entities, options);
  };

  twttr.txt.autoLinkHashtags = function(text, options) {
    var entities = twttr.txt.extractHashtagsWithIndices(text);
    return twttr.txt.autoLinkEntities(text, entities, options);
  };

  twttr.txt.autoLinkCashtags = function(text, options) {
    var entities = twttr.txt.extractCashtagsWithIndices(text);
    return twttr.txt.autoLinkEntities(text, entities, options);
  };

  twttr.txt.autoLinkUrlsCustom = function(text, options) {
    var entities = twttr.txt.extractUrlsWithIndices(text, {extractUrlWithoutProtocol: false});
    return twttr.txt.autoLinkEntities(text, entities, options);
  };

  twttr.txt.removeOverlappingEntities = function(entities) {
    entities.sort(function(a,b){ return a.indices[0] - b.indices[0]; });

    var prev = entities[0];
    for (var i = 1; i < entities.length; i++) {
      if (prev.indices[1] > entities[i].indices[0]) {
        entities.splice(i, 1);
        i--;
      } else {
        prev = entities[i];
      }
    }
  };

  twttr.txt.extractEntitiesWithIndices = function(text, options) {
    var entities = twttr.txt.extractUrlsWithIndices(text, options)
                    .concat(twttr.txt.extractMentionsOrListsWithIndices(text))
                    .concat(twttr.txt.extractHashtagsWithIndices(text, {checkUrlOverlap: false}))
                    .concat(twttr.txt.extractCashtagsWithIndices(text));

    if (entities.length == 0) {
      return [];
    }

    twttr.txt.removeOverlappingEntities(entities);
    return entities;
  };

  twttr.txt.extractMentions = function(text) {
    var screenNamesOnly = [],
        screenNamesWithIndices = twttr.txt.extractMentionsWithIndices(text);

    for (var i = 0; i < screenNamesWithIndices.length; i++) {
      var screenName = screenNamesWithIndices[i].screenName;
      screenNamesOnly.push(screenName);
    }

    return screenNamesOnly;
  };

  twttr.txt.extractMentionsWithIndices = function(text) {
    var mentions = [];
    var mentionsOrLists = twttr.txt.extractMentionsOrListsWithIndices(text);

    for (var i = 0 ; i < mentionsOrLists.length; i++) {
      mentionOrList = mentionsOrLists[i];
      if (mentionOrList.listSlug == '') {
        mentions.push({
          screenName: mentionOrList.screenName,
          indices: mentionOrList.indices
        });
      }
    }

    return mentions;
  };

  /**
   * Extract list or user mentions.
   * (Presence of listSlug indicates a list)
   */
  twttr.txt.extractMentionsOrListsWithIndices = function(text) {
    if (!text || !text.match(twttr.txt.regexen.atSigns)) {
      return [];
    }

    var possibleNames = [],
        position = 0;

    text.replace(twttr.txt.regexen.validMentionOrList, function(match, before, atSign, screenName, slashListname, offset, chunk) {
      var after = chunk.slice(offset + match.length);
      if (!after.match(twttr.txt.regexen.endMentionMatch)) {
        slashListname = slashListname || '';
        var startPosition = text.indexOf(atSign + screenName + slashListname, position);
        position = startPosition + screenName.length + slashListname.length + 1;
        possibleNames.push({
          screenName: screenName,
          listSlug: slashListname,
          indices: [startPosition, position]
        });
      }
    });

    return possibleNames;
  };


  twttr.txt.extractReplies = function(text) {
    if (!text) {
      return null;
    }

    var possibleScreenName = text.match(twttr.txt.regexen.validReply);
    if (!possibleScreenName ||
        RegExp.rightContext.match(twttr.txt.regexen.endMentionMatch)) {
      return null;
    }

    return possibleScreenName[1];
  };

  twttr.txt.extractUrls = function(text, options) {
    var urlsOnly = [],
        urlsWithIndices = twttr.txt.extractUrlsWithIndices(text, options);

    for (var i = 0; i < urlsWithIndices.length; i++) {
      urlsOnly.push(urlsWithIndices[i].url);
    }

    return urlsOnly;
  };

  twttr.txt.extractUrlsWithIndices = function(text, options) {
    if (!options) {
      options = {extractUrlsWithoutProtocol: true};
    }

    if (!text || (options.extractUrlsWithoutProtocol ? !text.match(/\./) : !text.match(/:/))) {
      return [];
    }

    var urls = [];

    while (twttr.txt.regexen.extractUrl.exec(text)) {
      var before = RegExp.$2, url = RegExp.$3, protocol = RegExp.$4, domain = RegExp.$5, path = RegExp.$7;
      var endPosition = twttr.txt.regexen.extractUrl.lastIndex,
          startPosition = endPosition - url.length;

      // if protocol is missing and domain contains non-ASCII characters,
      // extract ASCII-only domains.
      if (!protocol) {
        if (!options.extractUrlsWithoutProtocol
            || before.match(twttr.txt.regexen.invalidUrlWithoutProtocolPrecedingChars)) {
          continue;
        }
        var lastUrl = null,
            lastUrlInvalidMatch = false,
            asciiEndPosition = 0;
        domain.replace(twttr.txt.regexen.validAsciiDomain, function(asciiDomain) {
          var asciiStartPosition = domain.indexOf(asciiDomain, asciiEndPosition);
          asciiEndPosition = asciiStartPosition + asciiDomain.length;
          lastUrl = {
            url: asciiDomain,
            indices: [startPosition + asciiStartPosition, startPosition + asciiEndPosition]
          };
          lastUrlInvalidMatch = asciiDomain.match(twttr.txt.regexen.invalidShortDomain);
          if (!lastUrlInvalidMatch) {
            urls.push(lastUrl);
          }
        });

        // no ASCII-only domain found. Skip the entire URL.
        if (lastUrl == null) {
          continue;
        }

        // lastUrl only contains domain. Need to add path and query if they exist.
        if (path) {
          if (lastUrlInvalidMatch) {
            urls.push(lastUrl);
          }
          lastUrl.url = url.replace(domain, lastUrl.url);
          lastUrl.indices[1] = endPosition;
        }
      } else {
        // In the case of t.co URLs, don't allow additional path characters.
        if (url.match(twttr.txt.regexen.validTcoUrl)) {
          url = RegExp.lastMatch;
          endPosition = startPosition + url.length;
        }
        urls.push({
          url: url,
          indices: [startPosition, endPosition]
        });
      }
    }

    return urls;
  };

  twttr.txt.extractHashtags = function(text) {
    var hashtagsOnly = [],
        hashtagsWithIndices = twttr.txt.extractHashtagsWithIndices(text);

    for (var i = 0; i < hashtagsWithIndices.length; i++) {
      hashtagsOnly.push(hashtagsWithIndices[i].hashtag);
    }

    return hashtagsOnly;
  };

  twttr.txt.extractHashtagsWithIndices = function(text, options) {
    if (!options) {
      options = {checkUrlOverlap: true};
    }

    if (!text || !text.match(twttr.txt.regexen.hashSigns)) {
      return [];
    }

    var tags = [],
        position = 0;

    text.replace(twttr.txt.regexen.validHashtag, function(match, before, hash, hashText, offset, chunk) {
      var after = chunk.slice(offset + match.length);
      if (after.match(twttr.txt.regexen.endHashtagMatch))
        return;
      var startPosition = text.indexOf(hash + hashText, position);
      position = startPosition + hashText.length + 1;
      tags.push({
        hashtag: hashText,
        indices: [startPosition, position]
      });
    });

    if (options.checkUrlOverlap) {
      // also extract URL entities
      var urls = twttr.txt.extractUrlsWithIndices(text);
      if (urls.length > 0) {
        var entities = tags.concat(urls);
        // remove overlap
        twttr.txt.removeOverlappingEntities(entities);
        // only push back hashtags
        tags = [];
        for (var i = 0; i < entities.length; i++) {
          if (entities[i].hashtag) {
            tags.push(entities[i]);
          }
        }
      }
    }

    return tags;
  };

  twttr.txt.extractCashtags = function(text) {
    var cashtagsOnly = [],
        cashtagsWithIndices = twttr.txt.extractCashtagsWithIndices(text);

    for (var i = 0; i < cashtagsWithIndices.length; i++) {
      cashtagsOnly.push(cashtagsWithIndices[i].cashtag);
    }

    return cashtagsOnly;
  };

  twttr.txt.extractCashtagsWithIndices = function(text) {
    if (!text || text.indexOf("$") == -1) {
      return [];
    }

    var tags = [],
        position = 0;

    text.replace(twttr.txt.regexen.validCashtag, function(match, cashtag, offset, chunk) {
      // cashtag doesn't contain $ sign, so need to decrement index by 1.
      var startPosition = text.indexOf(cashtag, position) - 1;
      position = startPosition + cashtag.length + 1;
      tags.push({
        cashtag: cashtag,
        indices: [startPosition, position]
      });
    });

    return tags;
  };

  twttr.txt.modifyIndicesFromUnicodeToUTF16 = function(text, entities) {
    twttr.txt.convertUnicodeIndices(text, entities, false);
  };

  twttr.txt.modifyIndicesFromUTF16ToUnicode = function(text, entities) {
    twttr.txt.convertUnicodeIndices(text, entities, true);
  };

  twttr.txt.convertUnicodeIndices = function(text, entities, indicesInUTF16) {
    if (entities.length == 0) {
      return;
    }

    var charIndex = 0;
    var codePointIndex = 0;

    // sort entities by start index
    entities.sort(function(a,b){ return a.indices[0] - b.indices[0]; });
    var entityIndex = 0;
    var entity = entities[0];

    while (charIndex < text.length) {
      if (entity.indices[0] == (indicesInUTF16 ? charIndex : codePointIndex)) {
        var len = entity.indices[1] - entity.indices[0];
        entity.indices[0] = indicesInUTF16 ? codePointIndex : charIndex;
        entity.indices[1] = entity.indices[0] + len;

        entityIndex++;
        if (entityIndex == entities.length) {
          // no more entity
          break;
        }
        entity = entities[entityIndex];
      }

      var c = text.charCodeAt(charIndex);
      if (0xD800 <= c && c <= 0xDBFF && charIndex < text.length - 1) {
        // Found high surrogate char
        c = text.charCodeAt(charIndex + 1);
        if (0xDC00 <= c && c <= 0xDFFF) {
          // Found surrogate pair
          charIndex++;
        }
      }
      codePointIndex++;
      charIndex++;
    }
  };

  // this essentially does text.split(/<|>/)
  // except that won't work in IE, where empty strings are ommitted
  // so "<>".split(/<|>/) => [] in IE, but is ["", "", ""] in all others
  // but "<<".split("<") => ["", "", ""]
  twttr.txt.splitTags = function(text) {
    var firstSplits = text.split("<"),
        secondSplits,
        allSplits = [],
        split;

    for (var i = 0; i < firstSplits.length; i += 1) {
      split = firstSplits[i];
      if (!split) {
        allSplits.push("");
      } else {
        secondSplits = split.split(">");
        for (var j = 0; j < secondSplits.length; j += 1) {
          allSplits.push(secondSplits[j]);
        }
      }
    }

    return allSplits;
  };

  twttr.txt.hitHighlight = function(text, hits, options) {
    var defaultHighlightTag = "em";

    hits = hits || [];
    options = options || {};

    if (hits.length === 0) {
      return text;
    }

    var tagName = options.tag || defaultHighlightTag,
        tags = ["<" + tagName + ">", "</" + tagName + ">"],
        chunks = twttr.txt.splitTags(text),
        i,
        j,
        result = "",
        chunkIndex = 0,
        chunk = chunks[0],
        prevChunksLen = 0,
        chunkCursor = 0,
        startInChunk = false,
        chunkChars = chunk,
        flatHits = [],
        index,
        hit,
        tag,
        placed,
        hitSpot;

    for (i = 0; i < hits.length; i += 1) {
      for (j = 0; j < hits[i].length; j += 1) {
        flatHits.push(hits[i][j]);
      }
    }

    for (index = 0; index < flatHits.length; index += 1) {
      hit = flatHits[index];
      tag = tags[index % 2];
      placed = false;

      while (chunk != null && hit >= prevChunksLen + chunk.length) {
        result += chunkChars.slice(chunkCursor);
        if (startInChunk && hit === prevChunksLen + chunkChars.length) {
          result += tag;
          placed = true;
        }

        if (chunks[chunkIndex + 1]) {
          result += "<" + chunks[chunkIndex + 1] + ">";
        }

        prevChunksLen += chunkChars.length;
        chunkCursor = 0;
        chunkIndex += 2;
        chunk = chunks[chunkIndex];
        chunkChars = chunk;
        startInChunk = false;
      }

      if (!placed && chunk != null) {
        hitSpot = hit - prevChunksLen;
        result += chunkChars.slice(chunkCursor, hitSpot) + tag;
        chunkCursor = hitSpot;
        if (index % 2 === 0) {
          startInChunk = true;
        } else {
          startInChunk = false;
        }
      } else if(!placed) {
        placed = true;
        result += tag;
      }
    }

    if (chunk != null) {
      if (chunkCursor < chunkChars.length) {
        result += chunkChars.slice(chunkCursor);
      }
      for (index = chunkIndex + 1; index < chunks.length; index += 1) {
        result += (index % 2 === 0 ? chunks[index] : "<" + chunks[index] + ">");
      }
    }

    return result;
  };

  var MAX_LENGTH = 140;

  // Characters not allowed in Tweets
  var INVALID_CHARACTERS = [
    // BOM
    fromCode(0xFFFE),
    fromCode(0xFEFF),

    // Special
    fromCode(0xFFFF),

    // Directional Change
    fromCode(0x202A),
    fromCode(0x202B),
    fromCode(0x202C),
    fromCode(0x202D),
    fromCode(0x202E)
  ];

  // Returns the length of Tweet text with consideration to t.co URL replacement
  twttr.txt.getTweetLength = function(text, options) {
    if (!options) {
      options = {
          short_url_length: 20,
          short_url_length_https: 21
      };
    }
    var textLength = text.length;
    var urlsWithIndices = twttr.txt.extractUrlsWithIndices(text);

    for (var i = 0; i < urlsWithIndices.length; i++) {
    	// Subtract the length of the original URL
      textLength += urlsWithIndices[i].indices[0] - urlsWithIndices[i].indices[1];

      // Add 21 characters for URL starting with https://
      // Otherwise add 20 characters
      if (urlsWithIndices[i].url.toLowerCase().match(/^https:\/\//)) {
         textLength += options.short_url_length_https;
      } else {
        textLength += options.short_url_length;
      }
    }

    return textLength;
  };

  // Check the text for any reason that it may not be valid as a Tweet. This is meant as a pre-validation
  // before posting to api.twitter.com. There are several server-side reasons for Tweets to fail but this pre-validation
  // will allow quicker feedback.
  //
  // Returns false if this text is valid. Otherwise one of the following strings will be returned:
  //
  //   "too_long": if the text is too long
  //   "empty": if the text is nil or empty
  //   "invalid_characters": if the text contains non-Unicode or any of the disallowed Unicode characters
  twttr.txt.isInvalidTweet = function(text) {
    if (!text) {
      return "empty";
    }

    // Determine max length independent of URL length
    if (twttr.txt.getTweetLength(text) > MAX_LENGTH) {
      return "too_long";
    }

    for (var i = 0; i < INVALID_CHARACTERS.length; i++) {
      if (text.indexOf(INVALID_CHARACTERS[i]) >= 0) {
        return "invalid_characters";
      }
    }

    return false;
  };

  twttr.txt.isValidTweetText = function(text) {
    return !twttr.txt.isInvalidTweet(text);
  };

  twttr.txt.isValidUsername = function(username) {
    if (!username) {
      return false;
    }

    var extracted = twttr.txt.extractMentions(username);

    // Should extract the username minus the @ sign, hence the .slice(1)
    return extracted.length === 1 && extracted[0] === username.slice(1);
  };

  var VALID_LIST_RE = regexSupplant(/^#{validMentionOrList}$/);

  twttr.txt.isValidList = function(usernameList) {
    var match = usernameList.match(VALID_LIST_RE);

    // Must have matched and had nothing before or after
    return !!(match && match[1] == "" && match[4]);
  };

  twttr.txt.isValidHashtag = function(hashtag) {
    if (!hashtag) {
      return false;
    }

    var extracted = twttr.txt.extractHashtags(hashtag);

    // Should extract the hashtag minus the # sign, hence the .slice(1)
    return extracted.length === 1 && extracted[0] === hashtag.slice(1);
  };

  twttr.txt.isValidUrl = function(url, unicodeDomains, requireProtocol) {
    if (unicodeDomains == null) {
      unicodeDomains = true;
    }

    if (requireProtocol == null) {
      requireProtocol = true;
    }

    if (!url) {
      return false;
    }

    var urlParts = url.match(twttr.txt.regexen.validateUrlUnencoded);

    if (!urlParts || urlParts[0] !== url) {
      return false;
    }

    var scheme = urlParts[1],
        authority = urlParts[2],
        path = urlParts[3],
        query = urlParts[4],
        fragment = urlParts[5];

    if (!(
      (!requireProtocol || (isValidMatch(scheme, twttr.txt.regexen.validateUrlScheme) && scheme.match(/^https?$/i))) &&
      isValidMatch(path, twttr.txt.regexen.validateUrlPath) &&
      isValidMatch(query, twttr.txt.regexen.validateUrlQuery, true) &&
      isValidMatch(fragment, twttr.txt.regexen.validateUrlFragment, true)
    )) {
      return false;
    }

    return (unicodeDomains && isValidMatch(authority, twttr.txt.regexen.validateUrlUnicodeAuthority)) ||
           (!unicodeDomains && isValidMatch(authority, twttr.txt.regexen.validateUrlAuthority));
  };

  function isValidMatch(string, regex, optional) {
    if (!optional) {
      // RegExp["$&"] is the text of the last match
      // blank strings are ok, but are falsy, so we check stringiness instead of truthiness
      return ((typeof string === "string") && string.match(regex) && RegExp["$&"] === string);
    }

    // RegExp["$&"] is the text of the last match
    return (!string || (string.match(regex) && RegExp["$&"] === string));
  }

  if (typeof module != 'undefined' && module.exports) {
    module.exports = twttr.txt;
  }

}());
</script>

<?php get_template_part( 'footer' ); ?>