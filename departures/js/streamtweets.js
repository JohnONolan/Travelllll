(function($){
twitter_params = {
    since_id: tweet_params.since_id,
    lang: 'en',
    rpp: tweet_params.page_tweets,
    show_user: true,
    show_avatar: true,
    allow_reply: true,
    show_tweet_link: true,
    include_entities: true,
	until: tweet_params.until,
    q: tweet_params.hashcode + ((!tweet_params.since_id && tweet_params.since) ? ' since:' + tweet_params.since : '')
};

var stream_params = {
    twitter_timer: 5000,
    reveal_timer: 2000
};

var lasttweet = {
	id: tweet_params.since_id
};

var tweeturl = 'http://search.twitter.com/search.json?callback=?';

var tfetch = null;
var treveal = null;

function setupTweets() {
    treveal = setInterval(showTweets, stream_params.reveal_timer);
}

function startTweets() {
    clearTimeout(tfetch);
    tfetch = setTimeout(getTweets, stream_params.twitter_timer);
}

function getTweets() {
	var media_sizes = ['large', 'medium', 'small', 'thumb'];
    $.getJSON(
        tweeturl,
        twitter_params,
        function (json_data, textStatus) {
            $('.loadingtweets').fadeOut('slow');
            if (json_data['max_id'] > 0) {
                // error handler???
                if ($('.error').html() != null) {
                    $('#tweets').empty();
                }
                twitter_params.since_id = json_data.max_id_str;
				var img;
                for (var j = json_data['results'].length-1; j >= 0; j--) {
					if (lasttweet.id == json_data['results'][j].id) {
						continue;
					}
					if (0 == j) {
						lasttweet.id = json_data['results'][j].id;
					}
                    var nd = new Date();
                    nd.setTime(Date.parse(json_data['results'][j].created_at));
                    var t = timeAgo(nd.getTime() / 1000);
                    var newtweet = $('<li class="hiddentweet"></li>')
                        .css('display','none')
						.html('<div class="photo-wrap"><a href="http://twitter.com/' + json_data['results'][j].from_user + '" title="' + json_data['results'][j].from_user + '"><img src="' + json_data['results'][j].profile_image_url + '" width="40" height="40" /></a></div>'
							  + json_data['results'][j].text
                              + '<span class="date"><a href="http://twitter.com/' + json_data['results'][j].from_user + '/status/' + json_data['results'][j].id + '" target="_blank">' + t + '</a></span>');
                    $('#twitspace').prepend(newtweet);
					if ("undefined" != typeof json_data['results'][j].entities
						&& "undefined" != typeof json_data['results'][j].entities.media
						&& 0 != json_data['results'][j].entities.media.length
						) {
							for (var m = 0; m < json_data['results'][j].entities.media.length; m++) {
								if ("photo" == json_data['results'][j].entities.media[m].type
									&& "undefined" != typeof json_data['results'][j].entities.media[m].media_url
								)

									img = {title: json_data['results'][j].text,
											href: 'http://twitter.com/' + json_data['results'][j]['from_user'] + '/status/' + json_data['results'][j]['id_str']
									};
									for (var i = 0; i < media_sizes.length; i++) {
										if ("undefined" != typeof json_data['results'][j].entities.media[m].sizes[media_sizes[i]]) {
											img[media_sizes[i]] = json_data['results'][j].entities.media[m].media_url + ":" + media_sizes[i];
										} else {
											img[media_sizes[i]] = json_data['results'][j].entities.media[m].media_url;
										}
									}
							if (twitpics_cache.length >= tweet_params.photo_count) {
								twitpics_cache.pop();
							}
							twitpics_cache.unshift(img);
							updateImages();
							}
					}

                }
            }
            clearTimeout(tfetch);
            tfetch = setTimeout(getTweets, stream_params.twitter_timer);
        }
    );
}

function parseTweet(tweet) {
    oldTweet = tweet;
//    tweet = tweet.replace(/(@\w+)/gi, '<a href="/$1" class="mention">$1</a>');
//    tweet = tweet.replace(/(#\w+)/gi, '<a href="/$1" class="hashtag">$1</a>');
//    tweet = tweet.replace(/(https?:\/\/([-\w\.]+)+(:\d+)?(\/([\w~\/_\.]*(\?\S+)?)?)?)/gi, '<a href="$1" class="link" target="_blank">$1</a>');
    return tweet;
}

function showTweets() {
    var hiddentweets = $('.hiddentweet');
	var tweets = $('#twitspace').children(':visible');
    if (hiddentweets.length > 0) {
        if (tweets.length >= twitter_params.rpp || $('#twitspace').height() > $('#departurecontent').height()) {
            var lasttweet = tweets[tweets.length - 1];
            $(lasttweet).remove();
        }
        var lasthiddentweet = hiddentweets[hiddentweets.length - 1];
        $(lasthiddentweet).removeClass('hiddentweet');
		$(lasthiddentweet).css('display','block');
        $(lasthiddentweet).fadeIn('slow');
    }
}

function timeAgo(time) {
    var periods = new Array("second", "minute", "hour", "day", "week", "month", "year", "decade");
    var lengths = new Array("60","60","24","7","4.35","12","10");
    
    var nowdate = new Date();
    var now = (nowdate.getTime() / 1000);
    
    var difference = now - time;
    var tense = "ago";
    
    for(var j = 0; difference >= lengths[j] && j < lengths.length-1; j++) {
        difference /= lengths[j];
    }
    
    difference = Math.round(difference);
    
    if(difference != 1) {
        periods[j] += "s";
    }
    
    return difference + ' ' + periods[j] + ' ago';
}

function isAllSlots(wrapSelector) {
	// check the last slot existing
	return ("undefined" == typeof $(wrapSelector) || $(wrapSelector).children().length < twitpics_slots.length) ? false : true;
}

function addNewSlot(src) {
//	check current images and add new one if absents
	var wrapSelector = ".trip-photo-wrap";
	if ($(wrapSelector).size() <= 0) {
		// create block and first slot
		$('<h2 class="bar"><span>Trip photos</span></h2><div class="' + wrapSelector.substring(1) + '"><a href="' + src["href"] + '" target="_blank"><img src="' + src[twitpics_slots[0].type] + '" class="' + twitpics_slots[0]["class"] + '"></a></div>').insertAfter('#trending');
//		$(wrapSelector).append('<a href="' + src["href"] + '" target="_blank"><img src="' + src[twitpics_slots[0].type] + '" class="' + twitpics_slots[0]["class"] + '"></a>');
	} else if (!isAllSlots(wrapSelector)) {
		// check other slots
		var index = $(wrapSelector).children().length;
		$(wrapSelector).append('<a href="' + src["href"] + '" target="_blank"><img src="' + src[twitpics_slots[index].type] + '" class="' + twitpics_slots[index]["class"] + '"></a>');
	}
}

function updateImages(src, href) {
	// create/update slots according to current twitpics_cache values
	var wrapSelector = '.trip-photo-wrap';
	// added
	if (!isAllSlots(wrapSelector)) {
		addNewSlot(twitpics_cache[twitpics_cache.length - 1]);
	}
	// update src value
	var images = $(wrapSelector).find('img');
	for (var i = 0; i < images.length; i++) {
		$(images[i]).removeProp("src");
		$(images[i]).prop("src", twitpics_cache[i][twitpics_slots[i].type]);
		$(images[i]).removeProp("title");
		$(images[i]).prop("title", twitpics_cache[i]["title"]);
		$(images[i]).removeProp("alt");
		$(images[i]).prop("alt", twitpics_cache[i]["title"]);
		// change link
		$(images[i]).parent().removeProp("href");
		$(images[i]).parent().prop("href", twitpics_cache[i]["href"]);
		$(images[i]).parent().removeProp("title");
		$(images[i]).parent().prop("title", twitpics_cache[i]["title"]);
		$(images[i]).parent().removeProp("alt");
		$(images[i]).parent().prop("alt", twitpics_cache[i]["title"]);
	}
}

$(document).ready(function () {
    setupTweets();
    if (twitter_params.q != null && twitter_params.q != '' && tweet_params.live) {
        getTweets();
        startTweets();
    }  
}); 
})(jQuery);
