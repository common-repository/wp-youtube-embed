	/*
    Copyright (c) 2017 Ian Kennerley, http://cheshirewebsolutions.com

    - https://developers.google.com/youtube/v3/docs/playlists/list
    */

    var CWS = CWS || {};


    /* ------------------------------------------------------------------------------
    Called automatically when JavaScript client library is loaded.
    ------------------------------------------------------------------------------ */

    function onClientLoad() {
        //console.log('onClientLoad...');
        gapi.client.load('youtube', 'v3', onYouTubeApiLoad); // this seems to be causing errors on test live site!!!! // try moving init-channel.js before cws-youtube-pro-public.js
    }


(function( $ ) {
	'use strict';

    // create deferred object
    var YTdeferred = $.Deferred();
    window.onYouTubeIframeAPIReady = function() {
        //console.log('API ready');
        // resolve when youtube callback is called
        // passing YT as a parameter
        YTdeferred.resolve(window.YT);
    };


/*------------------------------------------------------------------------

    CWS YouTube Class

------------------------------------------------------------------------*/
    CWS.YouTube = function(config, callback) {

        'use strict';

        var self = this;
        var debug = false;

        var uploadsID;
        var nextPageToken, prevPageToken;
        var player;
        var videoId; // id of video playing, used for next / prev video functionality
        var videoControls = $('#cws-ytp-video-controls');
        var closeVideo = $('#cws-ytp-video-stop-button');
        var playlistId = config.playlistID;
        var channelId = config.channelID;
        var username = config.username;
        var strChannelTitle;
        var strChannelDescription;
        var channelThumb;
        var strView = php_vars.view;

        self.init = function() { 
/*
            self.element = $(config.youtube);

            // Does the youtube area exist?
            if(self.element.length) {
                console.log('youtube area found');
                // self.element.append(config.channelHeader);
            } else {
                console.log('youtube area NOT found');
            }
*/
            self.handleLoadingMechanism();          // handle listeners for next / previous
            self.getHeaderData( config.caller );    // get data for use in header
        };



    /**------------------------------------------------------------------------
     * Get Videos by ID.
     *
     * Get Videos by ID (single for lite and multiple for Pro)
     * This works pretty well, need to remove the next page icons form markup and forece on 1 id if not Pro!
     *
     * [cws_ytp_video vid=fxjOKVwgtHU,-2y2aS9JC-4,guPudp1Uhjs]
     *
     * @since 1.0.0
     *
     * @link https://developers.google.com/youtube/v3/docs/videos/list
     *
     * @param string vid Comma Separated String of Video Ids e.g. fxjOKVwgtHU,-2y2aS9JC-4,guPudp1Uhjs
     * @return array Response Array is passed to function creatVideoObjects.
     ------------------------------------------------------------------------*/
        self.getVideos = function( vid ) {

            var request = gapi.client.youtube.videos.list({
                part: 'snippet,contentDetails,statistics',
                id: vid
            });

            // send request to API server
            request.execute( function( response ) {
                if( response.result ) {
                    self.createVideoObjects( response.items, config.caller );
/*
                    // Only show pagination buttons if there is a pagination token for the
                    // next or previous page of results.
                    self.nextPageToken = response.result.nextPageToken;
                    var nextVis = self.nextPageToken ? 'visible' : 'hidden';
                    $( '#next-button' ).css( 'visibility', nextVis );

                    self.prevPageToken = response.result.prevPageToken
                    var prevVis = self.prevPageToken ? 'visible' : 'hidden';
                    $( '#prev-button' ).css( 'visibility', prevVis );
*/
                } else {
                    alert( 'ERROR: getVideos' );
                }

                if( debug == true ) { 
                    console.log( response ); 
                    for ( var i = 0; i < response.items.length; i++ ) {
                        console.log( response.items[i].snippet.title + " published at " + response.items[i].snippet.publishedAt )                
                    }
                }
            });
        }


    /**------------------------------------------------------------------------
     * Get Data for Header - thumb, channel ID.
     *
     * Assemble data required to present the header 
     *
     * @since 1.0.0
     *
     * @link https://developers.google.com/youtube/v3/docs/channels/list
     *
     * @param string caller defines where the request has originated from e.g. channel, playlistitemslist
     * @param int id channel id or playlistid (NOTE: How are we to differentiate between channelID and username?)
     * @return passes data to function displayHeader.
     ------------------------------------------------------------------------*/
        self.getHeaderData = function( caller, id ) {

            if( caller == 'channel' ) {

                if( channelId ) {
                    var requestOptions = {
                        part: 'snippet,contentDetails',
                        id: config.channelID, 
                        maxResults: config.maxResults
                    }
                } else if( username ) {
                    var requestOptions = {
                        part: 'snippet,contentDetails',
                        forUsername: username,
                        maxResults: config.maxResults
                    }
                }

                var request = gapi.client.youtube.channels.list( requestOptions );
/*
var str='';
str = JSON.stringify(requestOptions);
str = JSON.stringify(requestOptions, null, 4); // (Optional) beautiful indented output.
//console.log(str); // Logs output to dev tools console.
alert(str); // Displays output using window.alert()
*/

                // Send request to API server
                request.execute(function(response) {
                    if( debug == true ) { console.log(response); }

                    // Assemble data
                    self.channelId = config.channelID;
                    self.strChannelTitle = response.items[0].snippet.localized.title;
                    self.strChannelDescription = response.items[0].snippet.localized.description;
                    self.channelThumb = response.items[0].snippet.thumbnails.default.url;
                    
                    // Display the header
                    self.displayHeader(self.channelId, self.strChannelTitle , self.strChannelDescription, self.channelThumb);
                });
            }

            if( caller == 'playlistitems' ) {

                self.playlistId = config.playlistID;

                var requestOptions = {
                    part: 'snippet,contentDetails',
                    playlistId: self.playlistId
                };

                var request = gapi.client.youtube.playlistItems.list( requestOptions );

                // Send request to API server
                // Get the channel id from the playlistid required to get description and thumb etc for channel
                request.execute( function( response ) {    
                    if( debug == true ) { console.log( response ); }

                    self.channelId = response.items[0].snippet.channelId;

                    var request = gapi.client.youtube.channels.list({
                        part: 'snippet,contentDetails',
                        id: self.channelId
                    });

                    // Use the channel id now to get other items needed for header display
                    request.execute( function( response ) {
                        if( debug == true ) { console.log( response ); }

                        // Assemble data
                        self.strChannelTitle = response.items[0].snippet.localized.title;
                        self.strChannelDescription = response.items[0].snippet.localized.description;
                        self.channelThumb = response.items[0].snippet.thumbnails.default.url;
                        
                        // Display the header
                        self.displayHeader( self.channelId, self.strChannelTitle , self.strChannelDescription, self.channelThumb );
                    });
                });
            } 
            
            // Does not make much sense to include header when calling videos by id as they could be from different channels
            // maybe add channle id option to shortcode incase user is adding multi videos from same channel?
            // OR could pull in header from first video and offer the ability to hide header as an option in the shortcode???
            if( caller == 'video' ){

                if( channelId ) {
                    var requestOptions = {
                        part: 'snippet,contentDetails',
                        id: config.channelID, 
                        maxResults: config.maxResults
                    }
                } else if( username ) {
                    var requestOptions = {
                        part: 'snippet,contentDetails',
                        forUsername: username,
                        maxResults: config.maxResults
                    }
                }

                var request = gapi.client.youtube.channels.list( requestOptions );

                // Send request to API server
                request.execute(function(response) {
                    if( debug == true ) { console.log(response); }

                    // Assemble data
                    self.channelId = config.channelID;
                    self.strChannelTitle = response.items[0].snippet.localized.title;
                    self.strChannelDescription = response.items[0].snippet.localized.description;
                    self.channelThumb = response.items[0].snippet.thumbnails.default.url;
                    
                    // Display the header
                    self.displayHeader(self.channelId, self.strChannelTitle , self.strChannelDescription, self.channelThumb);
                });

            }                        

        }


    /**------------------------------------------------------------------------
     * Get Channel Details- thumb, channel ID.
     *
     * Get the channel id, needed to get the 'playlist videos' i.e. the videos uploaded, not in a particular playlist
     * is there a better way to make the distinction?
     *
     * NOTE: Should I be passing the channelId straight into here? rather than using config.channelId?!?!?!
     *
     * @since 1.0.0
     *
     * @link https://developers.google.com/youtube/v3/docs/channels/list
     *
     * @return passes data to function getPlaylistVideos.
     ------------------------------------------------------------------------*/
        self.getChannelDetails = function() {

            if( channelId ) {

                var request = gapi.client.youtube.channels.list({
                    part: 'snippet,contentDetails',
                    id: channelId, // need to uncomment this if using channelID as opposed to username
                    maxResults: config.maxResults
                });
            } else if( username ) {

                var request = gapi.client.youtube.channels.list({
                    part: 'snippet,contentDetails',
                    forUsername: username,
                    maxResults: config.maxResults
                });
            }


            // send request to API server
            request.execute( function( response ) {            

                //if( response.result ) {
                if( response.result && response.items.length > 0 ) {

                    // Get the related plalists uploads id, needed when searching for 'uploads playlist'
                    self.uploadsID = response.items[0].contentDetails.relatedPlaylists.uploads;

                    // Pass the channel id over to get the playlist videos                
                    self.getPlaylistVideos( self.uploadsID );

                } else {
                    alert( 'ERROR: getChannelDetails' );
                }

                if( debug == true ) { 
                    console.log(response);

                    for (var i = 0; i < response.items.length; i++) {
                        console.log(response.items[i].snippet.title + " published at " + response.items[i].snippet.publishedAt)                
                    }
                }

            });

        }; 



    /**------------------------------------------------------------------------
     * Display Header
     *
     * Assemble HTML and apend to elements in markup, also contains Description toggle
     *
     * @since 1.0.0
     * 
     * @param string channelID 
     * @param string channelIName
     * @param string channelDescription
     * @param string channelThumb
     * 
     * @return void
     ------------------------------------------------------------------------*/
        self.displayHeader = function( channelID, channelName, channelDescription, channelThumb ) {

            var channelName = channelName;
            var channelDescription = channelDescription;
            var channelThumb = channelThumb;
            var channelID = channelID;

            var channelLink = "//www.youtube.com/channel/" + channelID;
            var channelThumbHTML = "<div class='cws-ytp-channel-thumb'><a href='" + channelLink + "' target='_blank'><img src='" + channelThumb + "' /></a></div>";
            var channelDetailsHTML = "<div class='cws-ytp-channel-details'><div class='cws-ytp-channel-name'><a href='" + channelLink + "' target='_blank'>" + channelName + "</a></div><div class='cws-ytp-subscribe'><div class='g-ytsubscribe' data-channelid='" + channelID + "' data-layout='default' data-count='default'></div></div></div>";
            var header = $('.cws-ytp #cws-ytp-header');
            var channelThumbHTML = "<a href=\"#\" id=\"cws-ytp-logo\"><img class=\"cws-ytp-channel-logo\" src=\"" + channelThumb + "\" alt=\"\"></a>";
            

            // Add YouTube Channel Logo
            if( config.showChannelHeader == 1 ) {
                // Append Channel Thumbnail to the header div
                header.append( channelThumbHTML );
            }

            // Add a social container to help with responsive layout on smaller devices...
            var elSocialContainer = $( '<div class="cws-ytp-social-container">' );

            // Add YouTube subscribe icon
            if( config.showSubscribe == 1 ) {
                var subscribeHTML = self.getSubscribeLink();
                
                elSocialContainer.append( subscribeHTML );
                header.append( elSocialContainer );
            }

            // Add social icons
            if( config.showSocial == 1 ) {
                var socialHTML = self.getSocialLinks( php_vars.arrSocial );

                elSocialContainer.append( socialHTML );
                header.append( elSocialContainer );                
            }

            // Add Channel Description Div
            if( config.showChannelDescription == 1 ) {
                var descHTML = self.displayChannelDescription( channelDescription );
                header.after( descHTML );
            }

            // Toggle Description - Should this be here or some place else?
            // https://codepen.io/JTParrett/pen/CAglw
            var description = $( '#cws-ytp-channel-description' );
            //console.log(description);
            var animateTime = 500;
            var descriptionLink = $( '#cws-ytp-logo' );

            descriptionLink.click( function(e) {
                //console.log('click');
                e.preventDefault();
                if( description.height() === 0 ) {
                    self.autoHeightAnimate( description, animateTime );
                } else {
                    description.stop().animate( { height: '0' }, animateTime );
                }
            });            
        };


    /**------------------------------------------------------------------------
     * Display Channel Description - NOTE should really call it getChannelDescHTML or similar!
     *
     * Assemble HTML markup for Channel Description
     *
     * @since 1.0.0
     * 
     * @param string channelDescription
     * 
     * @return descHTML HTML markup containing channel description text
     ------------------------------------------------------------------------*/
        self.displayChannelDescription= function( channelDescription ) {
            var descHTML;

            descHTML = '<div id="cws-ytp-channel-description">';
            descHTML += '<p>' + channelDescription + '</p>';
            descHTML += '</div>';

            return descHTML;
        }


     /**------------------------------------------------------------------------
     * Get Subscribe Link
     *
     * Assemble HTML markup for Subscribe Link
     *
     * @since 1.0.0
     * 
     * @param
     * 
     * @return subscribeHTML HTML markup containing channel subscribe link
     ------------------------------------------------------------------------*/
        self.getSubscribeLink = function() {
            var subscribeHTML;

            subscribeHTML = '<div id="cws-ytp-subscribe-link">';

            var link = '//youtube.com/channel/' + self.channelId + '?sub_confirmation=1';            
            var icon = '<i class="fab fa-youtube-square"></i>';
            subscribeHTML += '<a href="'+link+'" target="_blank">'+icon+'</a>';

            return subscribeHTML;
        }


     /**------------------------------------------------------------------------
     * Get Social Links
     *
     * Assemble HTML markup for Social Links
     *
     * @since 1.0.0
     * 
     * @param array arrSocial
     * 
     * @return string socialHTML HTML markup containing social links
     ------------------------------------------------------------------------*/
        self.getSocialLinks = function( arrSocial ) {
            
            var socialHTML;

            socialHTML = '<div id="cws-ytp-social-links">';

            $.each( arrSocial, function( media, val ) {

                // Add social links to usernames
                switch (media) {
                    case 'instagram':
                        var link = '//instagram.com/' + val;
                        var icon = '<i class="fab fa-instagram"></i>';
                        socialHTML += '<a href="'+link+'" target="_blank">'+icon+'</a>';
                    break;
                    case 'twitter':
                        var link = '//twitter.com/' + val;
                        var icon = '<i class="fab fa-twitter"></i>';
                        socialHTML += '<a href="'+link+'" target="_blank">'+icon+'</a>';
                    break;
                    case 'facebook':
                        var link = '//facebook.com/' + val;
                        var icon = '<i class="fab fa-facebook"></i>';
                        socialHTML += '<a href="'+link+'" target="_blank">'+icon+'</a>';
                    break;
                    case 'pinterest':
                        var link = '//pinterest.com/' + val;
                        var icon = '<i class="fab fa-pinterest"></i>';
                        socialHTML += '<a href="'+link+'" target="_blank">'+icon+'</a>';
                    break;
                    case 'tumblr':
                        var link = '//' + val + '.tumblr.com/';
                        var icon = '<i class="fab fa-tumblr"></i>';
                        socialHTML += '<a href="'+link+'" target="_blank">'+icon+'</a>';
                    break;
                    case 'flickr':
                        var link = '//flickr.com/' + val;
                        var icon = '<i class="fab fa-flickr"></i>';
                        socialHTML += '<a href="'+link+'" target="_blank">'+icon+'</a>';                    
                    break; 

                    default:
                }    
            });


            socialHTML += '</div>';
            
            if( debug == true ) { 
                console.log(arrSocial);
                //console.log(entries);
                console.log(socialHTML);
            }

            return socialHTML;
        }


     /**------------------------------------------------------------------------
     * Get Channel Playlists
     *
     * Assemble request to get playlists
     * Show/hide Next/Previous page if there nextPageToken / prevPageToken
     * Pass array response.items to function createPlaylistObjects()
     *
     * @since 1.0.0
     * 
     * @link https://developers.google.com/youtube/v3/docs/playlists/list
     *     
     * @param string channelID
     * @param string pageToken     
     * 
     * @return void
     ------------------------------------------------------------------------*/
        self.getChannelPlaylists = function( channelID, pageToken ) {

            var pageTokenUrl = "";
            var videoArray;

            // Assemble options to send to API
            var requestOptions = {
                part: 'snippet,contentDetails',
                channelId: channelID,
                maxResults: config.maxResults,
            };

            // Add in pagetoken to options if we have one
            if( pageToken ) {
                requestOptions.pageToken = pageToken;
            }

            var request = gapi.client.youtube.playlists.list( requestOptions );

            // Send request to API server
            request.execute( function( response ) {

                // Only show pagination buttons if there is a pagination token for the
                // next or previous page of results.
                self.nextPageToken = response.result.nextPageToken;
                var nextVis = self.nextPageToken ? 'visible' : 'hidden';
                $( '#cws-ytp-next-button' ).css( 'visibility', nextVis );

                self.prevPageToken = response.result.prevPageToken
                var prevVis = self.prevPageToken ? 'visible' : 'hidden';
                $( '#cws-ytp-prev-button' ).css( 'visibility', prevVis );

                // NOTE: Do I even need these?
                //self.handleTokenNext( response.nextPageToken );
                //self.handleTokenPrev( response.prevPageToken );

                if( response.result ) {
                    videoArray = self.createPlaylistObjects( response.items );
                    self.displayItems( videoArray );
                } else {
                    alert( 'ERROR: getPlaylistVideos' );
                }
            });

        };



     /**------------------------------------------------------------------------
     * Get Playlist Videos
     *
     * Assemble request to get playlist videos
     * Show/hide Next/Previous page if there nextPageToken / prevPageToken
     * Pass array response.items to function createVideoObjects()
     *
     * [cws_ytp_playlistitems plid='PLRqwX-V7Uu6ajGB2OI3hl5DZsD1Fw1WzR']
     *
     * @since 1.0.0
     * 
     * @link https://developers.google.com/youtube/v3/docs/playlistitems/list
     *     
     * @param string playlistID
     * @param string pageToken     
     * 
     * @return void
     ------------------------------------------------------------------------*/
        self.getPlaylistVideos = function( playlistID, pageToken ) {

            var pageTokenUrl = "";

            // Assemble options to send to API
            var requestOptions = {
                part: 'snippet',
                maxResults: config.maxResults,
            };

            // Allows it to work if called from getChannelDetails or getPlaylistVideos
            if( playlistId ) {
                requestOptions.playlistId = playlistId;
                //alert('playlistId '+playlistId);
            } else if( self.uploadsID ) {
                requestOptions.playlistId = self.uploadsID;
                //alert('self.uploadsID '+self.uploadsID);
            }

            // Add in pagetoken to options if we have one
            if( pageToken ) {
                requestOptions.pageToken = pageToken;
            }

            var request = gapi.client.youtube.playlistItems.list( requestOptions );

            request.execute( function( response ) {    
                //console.log(requestOptions);
                //console.log(response.items);

                // Only show pagination buttons if there is a pagination token for the
                // next or previous page of results.
                self.nextPageToken = response.result.nextPageToken;
                var nextVis = self.nextPageToken ? 'visible' : 'hidden';
                $('#cws-ytp-next-button').css('visibility', nextVis);

                self.prevPageToken = response.result.prevPageToken
                var prevVis = self.prevPageToken ? 'visible' : 'hidden';
                $('#cws-ytp-prev-button').css('visibility', prevVis);

                ///self.handleTokenNext(response.nextPageToken);
                ///self.handleTokenPrev(response.prevPageToken);

                var result = response.result;

                if(result) {
                    self.createVideoObjects( response.items, config.caller );
                } else {
                    alert('ERROR: getPlaylistVideos');
                }
            });
        };



     /**------------------------------------------------------------------------
     * Search list Videos
     *
     * Assemble request to get playlist videos
     * Show/hide Next/Previous page if there nextPageToken / prevPageToken
     * Pass array response.items to function createVideoObjects()
     *
     * [cws_ytp_search term=cats,dogs]
     *
     * @since 1.0.0
     * 
     * @link https://developers.google.com/youtube/v3/docs/search/list
     *     
     * @param string term
     * @param string pageToken     
     * 
     * @return void
     ------------------------------------------------------------------------*/        
        self.search = function( term, pageToken ) {
            //console.log('app term: '+term)
            
            var pageTokenUrl = "";

            // Assemble options to send to API
            var requestOptions = {
                part: 'snippet',
                q: term,
                type: 'video',
                order: 'viewCount',
               // forMine: true, // This parameter can only be used in a properly authorized request: https://developers.google.com/youtube/v3/guides/auth/client-side-web-apps
                key: config.apiKey,
                maxResults: config.maxResults,
            };

            // Add in pagetoken to options if we have one
            if( pageToken ) {
                requestOptions.pageToken = pageToken;
            }

            //console.log(requestOptions);

            var request = gapi.client.youtube.search.list( requestOptions );

            request.execute( function( response ) {    

                //console.log(response.items);

                // Only show pagination buttons if there is a pagination token for the
                // next or previous page of results.
                self.nextPageToken = response.result.nextPageToken;
                var nextVis = self.nextPageToken ? 'visible' : 'hidden';
                $('#cws-ytp-next-button').css('visibility', nextVis);

                self.prevPageToken = response.result.prevPageToken
                var prevVis = self.prevPageToken ? 'visible' : 'hidden';
                $('#cws-ytp-prev-button').css('visibility', prevVis);

                var result = response.result;

                if(result) {
                    self.createVideoObjects( response.items, config.caller );
                } else {
                    alert('ERROR: getPlaylistVideos');
                }
            });
        }


     /**------------------------------------------------------------------------
     * Create Video Objects
     *
     * @since 1.0.0
     * 
     * @link https://developers.google.com/youtube/v3/docs/videos
     *     
     * @param array itemArray
     * @param boolean isVidList     
     * 
     * @return void
     ------------------------------------------------------------------------*/
        self.createVideoObjects = function( itemArray, caller ) {

            var videoArray = [];
            var videoIdArray = [];
            var cwsSnippet;
            var isVidList;

            // Loop over video items and assemble data needed into own array
            for( var i = 0; i < itemArray.length; i++ ) {

                cwsSnippet = new Object();
                cwsSnippet.image = itemArray[i].snippet.thumbnails.medium.url;

                // Grab the highest quality thumbnail available
                if ( itemArray[i].snippet.thumbnails.maxres ) {
                    cwsSnippet.highImage = itemArray[i].snippet.thumbnails.maxres.url;
                } else if ( itemArray[i].snippet.thumbnails.high ) {
                    cwsSnippet.highImage = itemArray[i].snippet.thumbnails.high.url;
                } else if ( itemArray[i].snippet.thumbnails.standard ) {
                    cwsSnippet.highImage = itemArray[i].snippet.thumbnails.maxres.url;                
                } else {
                    cwsSnippet.highImage = itemArray[i].snippet.thumbnails.default.url;
                }

                cwsSnippet.title = itemArray[i].snippet.title;
                cwsSnippet.description = itemArray[i].snippet.description;

                // added this IF when adding into search!!!!!!
                if( itemArray[i].snippet.playlistId ) {
                    cwsSnippet.playlistId = itemArray[i].snippet.playlistId;
                }

                // Need to check what caller is as the id is stored in various places in response array
                if( caller == 'channel' || caller == 'playlistitems' ) {
                    cwsSnippet.videoId = itemArray[i].snippet.resourceId.videoId;
                } else if( caller == 'video' ) {
                   cwsSnippet.videoId = itemArray[i].id;                     
                } else if(caller == 'search' ) {
                    cwsSnippet.videoId = itemArray[i].id.videoId;
                }

                cwsSnippet.link = "https://www.youtube.com/watch?v=" + cwsSnippet.videoId + "&list=" + cwsSnippet.playlistId;
                cwsSnippet.date = itemArray[i].snippet.publishedAt;
                cwsSnippet.formattedDate = self.convertDate( cwsSnippet.date );

                videoArray.push( cwsSnippet );
                videoIdArray.push( cwsSnippet.videoId );  
            }

            // console.log(videoArray);
            self.getVideoStatistics( videoIdArray, videoArray );
        };


     /**------------------------------------------------------------------------
     * Create Playlist Objects
     *
     * @since 1.0.0
     * 
     *     
     * @param array itemArray
     * 
     * @return array videoArray
     ------------------------------------------------------------------------*/
        self.createPlaylistObjects = function( itemArray ) {

            var videoArray = [];
            var videoIdArray = [];
            var cwsSnippet;

            // Loop over playlist items and assemble data needed into own array
            for( var i = 0; i < itemArray.length; i++ ) {

                cwsSnippet = new Object();
                
                cwsSnippet.image = itemArray[i].snippet.thumbnails.medium.url;
                cwsSnippet.title = itemArray[i].snippet.title;
                cwsSnippet.description = itemArray[i].snippet.description;
                cwsSnippet.playlistId = itemArray[i].id;
                cwsSnippet.videoId = itemArray[i].id;
                cwsSnippet.link = "//www.youtube.com/playlist?list=" + cwsSnippet.playlistId;
                cwsSnippet.isPlaylist = true;

                // Do not show playlist with no videos
                if( itemArray[i].contentDetails.itemCount < 1 ) {
                    continue;
                }

                videoArray.push( cwsSnippet );
                videoIdArray.push( cwsSnippet.videoId );  
            }

            return videoArray;
        };


     /**------------------------------------------------------------------------
     * Get Video Statistics
     *
     * @since 1.0.0
     * 
     *     
     * @param array videoIdArray
     * @param array videoArray     
     * 
     * @return 
     ------------------------------------------------------------------------*/
        self.getVideoStatistics = function( videoIdArray, videoArray ) {

            var videoArray; // DO I need this?
            var strVideoIds;

            // Concatenate videoIds to comma seperated list to use in request
            strVideoIds = videoIdArray.join( ", " );

            //console.log( strVideoIds );

            var request = gapi.client.youtube.videos.list({
                part: 'statistics,contentDetails',
                id: strVideoIds
            });

            request.execute( function( response ){    

                if ( response.result ) {
                    //console.log(response.items);
                    videoArray = self.addStatisticsToVideos( response.items, videoArray );
                    self.displayItems( videoArray );

                }else {
                    alert('ERROR: getVideoStatistics');
                }
            });
            //console.log(videoArray);
        };


     /**------------------------------------------------------------------------
     * Add Statistics to Videos Array
     *
     * @since 1.0.0
     * 
     *     
     * @param array statisticsArray
     * @param array videoArray     
     * 
     * @return array videoArray
     ------------------------------------------------------------------------*/
        self.addStatisticsToVideos = function( statisticsArray, videoArray ) {

            var views, likes, duration, comments, published;

            // Loop over statistics and add to video array
            for( var i = 0; i < statisticsArray.length; i++ ) {

                // Convert Views into Human Friendly Format, then add to videoArray
                views = statisticsArray[i].statistics.viewCount;
                views = self.humanFriendlyViews(views);
                videoArray[i].views = views;

                // Add likes to videoArray
                likes = statisticsArray[i].statistics.likeCount;
                videoArray[i].likes = likes;

                // Convert duration into Human Friendly Format, then add to videoArray
                duration = self.convertDuration(statisticsArray[i].contentDetails.duration);
                videoArray[i].duration = duration;  

                // Add comments to videoArray
                comments = statisticsArray[i].statistics.commentCount;
                videoArray[i].comments = comments;
            }

            return videoArray;
        };


     /**------------------------------------------------------------------------
     * Display Items
     *
     * @since 1.0.0
     * 
     *     
     * @param array videoArray     
     * 
     * @return 
     ------------------------------------------------------------------------*/
        self.displayItems = function( videoArray ) {

            //console.log(videoArray);

            var strClass = 'gridView'; // Thinkning about this I probably don;t need to prefix the css grid styles with 'gridView' as they are the default!!!!!!!

            //var strView = $.cookie("view"); //  taken tis out as not haing it in ui and will take val from shortcode instead!

            if( strView == 'list' ) {
                strClass = 'cws-ytp-listview';
            }

            var list = videoArray;
            var $container = $(config.youtube);
            var containerHTML = '';

            var strVideoItemContainerOpen = "<div id='cws-ytp-video-item-container' class='cws-ytp-video-item-container " + strClass + "'>";
            var strVideoItemContainerClose = "</div>";
            var strHTML = '';
            var strVideoItemContainer;

            var description, image, bgImage, likes, link, playlistId, title, videoId, views, duration, date;

            var isPlaylist;

            var strVideoImageLink, strVideoTitleLink, strVideoMeta, strVideoDesc, strVideoItem, strVideoDetails;

            if( $container.length ) {

                for( var i = 0; i < list.length; i++ ) {

                    description = list[i].description;
                    image = list[i].image;
                    bgImage = list[i].highImage;
                    likes = list[i].likes;
                    link = list[i].link;
                    playlistId = list[i].playlistId;
                    title = list[i].title;
                    videoId = list[i].videoId;
                    views = list[i].views;
                    duration = list[i].duration;
                    isPlaylist = list[i].isPlaylist;
                    date = list[i].formattedDate;

                    // need to add check to see if playlist or not
                    if( isPlaylist ) {
                        alert('Come find me on like 840ish - why am I here?');
                        console.log('I am a playlist');
                    }
                    /* 
                        Logic to refresh page if on carousel view 
                        required to show / hide duration, title, play icon
                    */
                    var windowWidth = window.innerWidth || $(window).width();

                    // Define some var for style
                    var strDurationStyle = "style=''", strPlayIconStyle = "style=''", strTitleStyle = "style=''";

                    // bit cacky 
                    if( windowWidth > config.breakpoint_adv1 ) {
                        // slider options

                        if( strView == 'carousel' && config.duration == false ) {
                            var strDurationStyle = " style=display:none; "; 
                        }

                        if( strView == 'carousel' && config.playIcon == false ) {
                            var strPlayIconStyle = " style=display:none; "; 
                        }

                        if( strView == 'carousel' && config.title == false ){
                            var strTitleStyle = " style=display:none; "; 
                        }

                    } else if( windowWidth > config.breakpoint_adv2 && windowWidth < config.breakpoint_adv1 ) {
                        // adv1
                        if( strView == 'carousel' && config.duration_adv1 == false ) {
                            var strDurationStyle = " style=display:none; "; 
                        }

                        if( strView == 'carousel' && config.playIcon_adv1 == false ) {
                            var strPlayIconStyle = " style=display:none; "; 
                        }
                        if( strView == 'carousel' && config.title_adv1 == false ) {
                            var strTitleStyle = " style=display:none; "; 
                        }

                    } else if( windowWidth > config.breakpoint_adv3 && windowWidth < config.breakpoint_adv2 ) {
                        // adv2
                        if( strView == 'carousel' && config.duration_adv2 == false ) {
                            var strDurationStyle = " style=display:none; "; 
                        }

                        if( strView == 'carousel' && config.playIcon_adv2 == false ) {
                            var strPlayIconStyle = " style=display:none; "; 
                        }
                        if( strView == 'carousel' && config.title_adv2 == false ) {
                            var strTitleStyle = " style=display:none; "; 
                        }

                    } else if( windowWidth < config.breakpoint_adv3 ) {
                        //adv3
                        if( strView == 'carousel' && config.duration_adv3 == false ) {
                            var strDurationStyle = " style=display:none; "; 
                        }

                        if( strView == 'carousel' && config.playIcon_adv3 == false ) {
                            var strPlayIconStyle = " style=display:none; "; 
                        }
                        if( strView == 'carousel' && config.title_adv3 == false ) {
                            var strTitleStyle = " style=display:none; "; 
                        }    
                    }

                    // strVideoImageLink = "<a class='cws-ytp-video-image-link' data-bgurl='"+ bgImage +"' data-videoId='"+ videoId +"' href='#' style='display:block; background-image: url(" + image + ")'><span class='cws-ytp-duration'>" + duration + "</span><span class='cws-ytp-play-button'><i class='fa fa-play'></i></span></a>";
                    strVideoImageLink = "<a class='cws-ytp-video-image-link' data-bgurl='"+ bgImage +"' data-videoId='"+ videoId +"' href='#' style='display:block; background-image: url(" + image + ")'><span "+strDurationStyle+" class='cws-ytp-duration'>" + duration + "</span><span  "+strPlayIconStyle+" class='cws-ytp-play-button'><i class='fa fa-play'></i></span></a>";                    
                    strVideoTitleLink = "<a "+strTitleStyle+" class='cws-ytp-video-title-link' href='#title'><h3>" + title + "</h3></a>";
  

                    // Define some var for video meta
                    strViews = '', strLikes = '', strDate = '';

                    if( config.showViews == 1 ) {
                        //if( debug == true) {console.log("config.showViews == 1");}
                        var strViews ="<span class='cws-ytp-view-count'>"+views+" Views </span>";
                    }

                    if( config.showLikes == 1 ) {
                        //if( debug == true) {console.log("config.showLikes == 1");}
                        var strLikes ="<span class='cws-ytp-likes-count'> "+likes+" Likes</span>";
                    }

                    if( config.showDate == 1 ) {
                        //if( debug == true) {console.log("config.showDate == 1");}
                        var strDate ="<span class='cws-ytp-date'> "+date+"</span>";
                    }

                    // Stick it all together
                    strVideoMeta = "<div class='cws-ytp-video-meta'>" + strViews + strLikes + strDate + "</div>";
                    strVideoDesc = "<div class='cws-ytp-video-desc'>" + description + "</div>";
                    strVideoDetails = "<div class='cws-ytp-video-details'>" + strVideoTitleLink + strVideoMeta + strVideoDesc + "</div>";
                    strVideoItem = "<div class='cws-ytp-video-item'>" + strVideoImageLink + strVideoDetails + "</div>";

                    strHTML += strVideoItem ;
                }
                
                // If carousel 
                // needed to do this as was displaying as grid briefly before initializing slider
                if( strView == 'carousel' ) {
                    // Hide Container
                    $( $container ).fadeOut();
                    // Assemble html
                    strVideoItemContainer = strVideoItemContainerOpen + strHTML + strVideoItemContainerClose;
                    // Hide and inject html
                    $( $container ).css('opacity',0).html( strVideoItemContainer );
                    
                    // putting this here fixed the bug where first video was not having overlay etc
                    // but also causes the flash
                    self.loadFirstVideo(); 

                    // Init slider
                    self.sliderInit();

                    // Hide Prev/Next Video buttons
                    $('#cws-ytp-video-prev-button').hide();
                    $('#cws-ytp-video-next-button').hide();

                    // Fade in html
                    $( $container ).fadeIn( 10, function() {

                        // Call check title length and add overflow class if needed...
                        self.isOverflowText();

                        // Mouseenter - Scroll Title Text
                        $( '.cws-ytp-video-item-container .cws-ytp-video-title-link' ).on( "mouseenter", ".overflow-text", function() {
                           // console.log(this);
                            self.startMarquee( $( this ) );
                        });

                        // Mouseleave - Reset Title Text
                        $( '.cws-ytp-video-item-container .cws-ytp-video-title-link' ).on( "mouseleave", ".overflow-text", function() {
                            self.stopMarquee( $( this ) );
                        });

                    }); // end fadeIn
                }
                else {

                    // 1. Standard Pagination - fade content out / in
                    $( $container ).fadeOut( 400, function() {
                        //console.log('fadeOut');
                        strVideoItemContainer = strVideoItemContainerOpen + strHTML + strVideoItemContainerClose;

                        $( $container ).hide().html( strVideoItemContainer ).fadeIn( 1000, function() {

                            $('#cws-ytp-video-item-container').fadeIn(200);

                            // Call check title length and add overflow class if needed...
                            self.isOverflowText();

                            // Mouseenter - Scroll Title Text
                            $( '.cws-ytp-video-item-container .cws-ytp-video-title-link' ).on( "mouseenter", ".overflow-text", function() {
                                //console.log(this);
                                self.startMarquee( $( this ) );
                            });

                            // Mouseleave - Reset Title Text
                            $( '.cws-ytp-video-item-container .cws-ytp-video-title-link' ).on( "mouseleave", ".overflow-text", function() {
                                self.stopMarquee( $( this ) );
                            });


                            self.loadFirstVideo();
                            /*
                            // placing slider init after loadFirstVideo() FIXED playing overlay bug!!!!
                            if( strView == 'carousel' ) {
                                // Init slider
                                self.sliderInit();
                            }
                            */
                        }); // end fadeIn

                    }); // end fadOut

                }


            /*
                // 2. Show More Type - fade content in - KEEP - MAKE AN OPTION
                strHTML ;

                //var $containerNode = $("<div class='video-item'>").html(strVideoItemContainer).hide();

                var $node = $("<span>").html(strHTML).hide();


                var myDivs = $('#cws_ytp').children('.video-item-container');

                if(myDivs.length === 0) {

                    // alert('.video-item-container NOT FOUND SO CREATE IT');

                    myDivs = $('<div class="video-item-container">').append($node).appendTo('#cws_ytp');        

                    $node.fadeIn(300, function() {
                        // Call check title length and add overflow class if needed...
                        self.isOverflowText();
                        self.unwrap($node);
                    }); 

                } else {

                    $('#cws_ytp .video-item-container').append($node);
                    $node.fadeIn(300, function() {
                        // Call check title length and add overflow class if needed...
                        self.isOverflowText();
                        self.unwrap($node);
                    }); 
                }

                // Mouseenter - Scroll Title Text
                $('.video-item-container .video-title-link').on("mouseenter", ".overflow-text", function() {
                    //console.log(this);
                    self.startMarquee($(this));
                });

                // Mouseleave - Reset Title Text
                $('.video-item-container .video-title-link').on("mouseleave", ".overflow-text", function() {
                    self.stopMarquee($(this));
                });
            */

            } else {
                console.log('Could not find youtube container defined in config as: ' + config.youtube);
            }
        };

        // Slider settings
        self.sliderInit = function(){

            $('#cws-ytp-video-item-container').slick({
                
                arrows: config.arrows,
                infinite: config.infinite,
                slidesToShow: config.slidesToShow,
                slidesToScroll:  config.slidesToScroll,
                autoplay: config.autoplay,                
                dots: config.dots,
                autoplaySpeed: config.autoplaySpeed,
                speed: config.speed,

                responsive: [
                {
                    breakpoint: config.breakpoint_adv1,
                    settings: {
                       arrows: config.arrows_adv1,
                        infinite: config.infinite_adv1,
                        slidesToShow: config.slidesToShow_adv1,
                        slidesToScroll:  config.slidesToScroll_adv1,
                        autoplay: config.autoplay_adv1,                
                        dots: config.dots_adv1,
                        autoplaySpeed: config.autoplaySpeed_adv1,
                        speed: config.speed_adv1,
                    }
                },
                {
                    breakpoint: config.breakpoint_adv2,
                    settings: {
                       arrows: config.arrows_adv2,
                        infinite: config.infinite_adv2,
                        slidesToShow: config.slidesToShow_adv2,
                        slidesToScroll:  config.slidesToScroll_adv2,
                        autoplay: config.autoplay_adv2,                
                        dots: config.dots_adv2,
                        autoplaySpeed: config.autoplaySpeed_adv2,
                        speed: config.speed_adv2,
                    }
                },
                {
                    breakpoint: config.breakpoint_adv3,
                    settings: {
                       arrows: config.arrows_adv3,
                        infinite: config.infinite_adv3,
                        slidesToShow: config.slidesToShow_adv3,
                        slidesToScroll:  config.slidesToScroll_adv3,
                        autoplay: config.autoplay_adv3,                
                        dots: config.dots_adv3,
                        autoplaySpeed: config.autoplaySpeed_adv3,
                        speed: config.speed_adv3,
                    }
                }
                    // You can unslick at a given breakpoint now by adding:
                    // settings: "unslick"
                    // instead of a settings object
                
                ]


            });
        }

     /**------------------------------------------------------------------------
     * Load the Video
     *
     * @since 1.0.0
     * 
     *     
     * @param  videoItem 
     * @param  boolean isPagingVideos 
     * @param  boolean cueVideo      
     * 
     * @return 
     ------------------------------------------------------------------------*/
        // self.loadVideo = function( videoItem, isPagingVideos = 0, cueVideo = 0 ) {
        self.loadVideo = function( videoItem, isPagingVideos, cueVideo) {

            // pre es6
            isPagingVideos = typeof isPagingVideos !== 'undefined' ? isPagingVideos : 0;
            cueVideo = typeof cueVideo !== 'undefined' ? cueVideo : 0;

            //console.log(videoItem);

            videoId = videoItem.find( '.cws-ytp-video-image-link' ).attr( 'data-videoid' );

            var bgUrl = videoItem.find( '.cws-ytp-video-image-link' ).attr( 'data-bgurl' );
            var desc = videoItem.find( '.cws-ytp-video-details .cws-ytp-video-desc' ).html();

            // 1. var setup gather info
            var videoTitle = videoItem.find( 'h3' ).html();
            var videoMeta = videoItem.find( '.cws-ytp-video-meta' ).html();
            var videoDesc = videoItem.find( '.cws-ytp-video-desc' ).html();

            // 2. update content
            $( '#cws-ytp-video-description' ).html( videoDesc ); // place desc below video
            $( '#cws-ytp-video-title' ).html( '<h2>' + videoTitle + '</h2' ); // place desc below video
            $( '#cws-ytp-video-meta' ).html( videoMeta ); // place desc below video

            // TODO: isPagingVideos - needs explaining
            if( !isPagingVideos ) {
                $( '#cws-ytp-video-description' ).stop().animate( { height: '0' }, 500 ); // Don't want to do this when just paging through videos and description is expanded, do want to do this when 
            
                // reset caret if need be, HHMMM I don't  like this when pagin through videos, it would be nicer to keep open
                if ( $( '#cws-ytp-video-description-controls' ).find( 'i' ).hasClass( 'fa-caret-up' ) ) {
                    $( '#cws-ytp-video-description-controls' ).find( 'i' ).toggleClass( 'fa-caret-up fa-caret-down' );
                }
            }

            if( config.showBackgroundImage ) {

                $( '#cws-ytp-background-image' ).fadeOut( 1, function() {
                    // console.log('fade out background');
                    $( '#cws-ytp-background-image' );
                    $( '#cws-ytp-background-image' ).css( "background-image", "url( " + bgUrl + " )" );
                });

                $( '#cws-ytp-background-image' ).fadeIn( 400, function() {
                    //console.log('fade in background'); 
                });                
            }
            
            self.hideCloseButton();
            
            // Add class playing to video-item
            // Find amy element with playing class and remove
            videoItem.parents( '.cws-ytp-video-item-container' ).find( '.cws-ytp-video-item.playing' ).removeClass( 'playing' );
            videoItem.addClass( 'playing' ); // add playing class
/*
var str='';
str = JSON.stringify(videoItem);
str = JSON.stringify(videoItem, null, 4); // (Optional) beautiful indented output.
console.log(str); // Logs output to dev tools console.
alert(str); // Displays output using window.alert()
*/

            if( cueVideo === 0 ) {

                //player.loadVideoById(videoId); // auto plays video
                // console.log( 'Clicked video item play button' );

                if( player ) {
                    player.loadVideoById( videoId ); // auto plays video
                }  

                $( '#cws-ytp-video-placeholder' ).fadeIn( 1000, function() {
                     //alert('fade in wonk3'); 
                    // if(player) {
                    //     player.loadVideoById(videoId); // auto plays video
                    // }                
                });

                // Show the current video in player
                // $('#video-placeholder').show();
                // $('#video-placeholder').css('opacity',1);
            } else {

                if( player ) {
                    //player.cueVideoById(videoId); // auto plays video
                     //alert('fade in wonk4'); 

                    $( '#cws-ytp-video-placeholder' ).fadeOut( 10, function() {
                         //console.log('videoId: ' + videoId); 
                         player.cueVideoById( videoId ); // auto plays video
                    }); 
                }                
            } 

        }


    /*------------------------------------------------------------------------

    Stop the Video

    ------------------------------------------------------------------------*/
        self.stopVideo = function() {
            // stop the current video
            if( player ) {
                player.stopVideo();
            }
        }


    /*------------------------------------------------------------------------

        Utility functions

    ------------------------------------------------------------------------*/


        /* ---------------------------------------
            Check for window resize
        --------------------------------------- */   
        var width = $(window).width();
     
        $( window ).resize( function() {
            // Call check title length and add overflow class if needed...
            self.isOverflowText();

            if( debug == true ) { console.log('window was resized'); }

            // Make sure this only happens if width changes in carousel view
            // the width change check was needed to stop resize firing on mobile scroll
            if ($(window).width() != width && strView == 'carousel') {
                // Only action on screen width change
                width = $(window).width();

                //console.log('window was resized '+this.location);

                if (window.RT) clearTimeout(window.RT);
                window.RT = setTimeout(function() {
                    this.location.reload(false); 
                }, 50);
            }
        }) 
        

        /* ---------------------------------------
            Show Video Controls
        --------------------------------------- */
        self.showVideoControls = function() {
            if( debug == true ) { console.log( 'showVideoControls' ); }

            videoControls.fadeIn( 400, function() {
                //console.log( 'fadeIn: videoControls' );
            });

        } 

        /* ---------------------------------------
            Hide Video Controls
        --------------------------------------- */
        self.hideVideoControls = function() {
            if( debug == true ) { console.log( 'hideVideoControls' ); }
            videoControls.hide();
        }   

        /* ---------------------------------------
            Show Close Button X
        --------------------------------------- */
        self.showCloseButton = function() {
            if( debug == true ) { console.log( 'showCloseButton' ); }
            closeVideo.show();
        } 

        /* ---------------------------------------
            Hide Close Button X
        --------------------------------------- */
        self.hideCloseButton = function() {
            if( debug == true ) { console.log( 'hideCloseButton' ); }
            closeVideo.hide();
        }             

        /* ---------------------------------------
            Close Video WIP
        --------------------------------------- */
        self.closeVideoPlayer = function(){

            $('#cws-ytp-video-placeholder').fadeOut( 400, function() {
                if( debug == true ) { console.log('fadeOut: #cws-ytp-video-placeholder'); }

                self.stopVideo(); // Stop the current video in player
                self.hideCloseButton(); // hide stop button / x
                self.showVideoControls(); // show previous button / x

            } ); 
        }    

        /* ---------------------------------------
            Hide Channel Logo
        --------------------------------------- */
        /*
        self.hideChannelLogo = function() {
            var channelLogo = $('a#logo');
            console.log('hideChannelLogo');
            //console.log(channelLogo);
            channelLogo.hide();
        }  
        */

        /* ---------------------------------------
            Show Channel Logo
        --------------------------------------- */
        /*
        self.showChannelLogo = function() {
            var channelLogo = $('a#logo');
            console.log('showChannelLogo');
            //console.log(channelLogo);
            channelLogo.show();
        }
        */         


        /* ---------------------------------------
            Unwrap parent <span> from div.video-item to allow prev/next functionality
        --------------------------------------- */
        // This was used for 'Load More' - not currently used
        self.unwrap = function( $node ) {
            $node.contents().unwrap();
        } 


        /* ---------------------------------------
            Is Video Title Overflowing?
            http://jsfiddle.net/bryanjamesross/vsQFE/4/
        --------------------------------------- */
        self.isOverflowText = function() {

            if( $( '.cws-ytp-video-item' ).length ) {

                $( '.cws-ytp-video-details h3' ).each( function() {
                    var $this = jQuery( this );
                    var thisElScrollWidth = $this[0].scrollWidth;
                    var parentElWidth = $this.parents( '.cws-ytp-video-details' ).width();
/*
                    console.log('$this');
                    console.log($this);
                    console.log('thisElScrollWidth');
                    console.log(thisElScrollWidth);
                    console.log('parentElWidth');
                    console.log($this.parent().width());                   
*/
                    if( thisElScrollWidth > parentElWidth ) { 
                        $this.addClass('overflow-text');
                        if( debug == true ) { console.log( 'adding class overflow-text' ); }
                    } else { 
                        $this.removeClass( 'overflow-text' );
                        if( debug == true ) { console.log( 'removing class overflow-text' ); }
                    }
                });

            }
        }

        /* ---------------------------------------
            Handle Next Page Token

            NOTE: Do I even need this!?!
        --------------------------------------- */
        /*
        self.handleTokenNext = function(token) {
            //alert('handleTokenNext');
            config.nextPageToken = token;
        } */

        /* ---------------------------------------
            Handle Prev Page Token

            NOTE: Do I even need this!?!          
        --------------------------------------- */
        /*self.handleTokenPrev = function(token) {
            config.prevPageToken = token;
        }*/

        /* ---------------------------------------
            Scroll video title to show hidden text
        --------------------------------------- */
        self.startMarquee = function( el ) {

            // Find width of video title link
            var titleAreaWidth = el.parent().width();
            var titleWidth = el.get(0).scrollWidth;

            if( titleWidth > titleAreaWidth ) {
                var scrollDistance = titleWidth - titleAreaWidth;
                var title = el;
                var speed = scrollDistance * 20;

                title.stop(); // Stop any current animation
                title.animate( {textIndent: "-" + scrollDistance + "px" }, speed, 'linear' );
            }
        }

        /* ---------------------------------------
            Reposition video title
        --------------------------------------- */
        self.stopMarquee = function( el ) {

            var title = el;
            title.stop(); // Stop any current animation
            title.animate( {textIndent: 2+"px"}, 'medium', 'swing' );      
        }

        /* ---------------------------------------
            Convert video duration to display time
        --------------------------------------- */
        self.convertDuration = function( videoDuration ) {

            var duration, returnDuration;
            videoDuration = videoDuration.replace( 'PT','' ).replace( 'S','' ).replace( 'M',':' ).replace( 'H',':' );

            var videoDurationSplit = videoDuration.split( ':' );
            returnDuration = videoDurationSplit[0];

            for( var i = 1; i < videoDurationSplit.length; i++ ) {
                duration = videoDurationSplit[i];
                if( duration == "" ) {
                    returnDuration += ":00";
                } else {
                    duration = parseInt( duration,10 );
                    if( duration<10 ) {
                        returnDuration += ":0" + duration;
                    } else {
                        returnDuration += ":" + duration;
                    }
                }
            }

            if( videoDurationSplit.length == 1 ) {
                returnDuration = "0:" + returnDuration;
            }

            return returnDuration;
        };


        /* ---------------------------------------
            Convert views to human friendly display
        --------------------------------------- */
        self.humanFriendlyViews = function( views ) {
                    
            var humanFriendlyViews = '';
            var intSplit = 3;
            views = "" + views;
            
            while( views.length > 0 ) {

                if( views.length > intSplit ) {
                    humanFriendlyViews = ", " + views.substring( views.length - intSplit ) + humanFriendlyViews;
                    views = views.substring( 0,views.length - intSplit );
                } else {
                    humanFriendlyViews = views + humanFriendlyViews;
                    break;
                }
            }
            
            return humanFriendlyViews;
        },


         /* ---------------------------------------
            Handle Loading Mechanism for next/prev page and next / prev video
        --------------------------------------- */       
        self.handleLoadingMechanism = function() {

            /* Load next / previous page of videos */
            $( "#cws-ytp-prev-button" ).click( function( e ) {
                self.previousPage( self.prevPageToken,e );
                self.showVideoControls();
            })

            $( "#cws-ytp-next-button" ).click( function( e ) {
                //console.log('clicked next');
                self.nextPage( self.nextPageToken,e );
                self.showVideoControls();
            })

            /* Load next / previous video */
            $( "#cws-ytp-video-prev-button" ).click( function( e ) {
                self.showVideoControls(); // do I need this, if the user can click next video then the video controls must already be visible!
                self.previousVideo( videoId,e );        
            })

            $( "#cws-ytp-video-next-button" ).click( function( e ) {
                self.showVideoControls(); // do I need this, if the user can click next video then the video controls must already be visible!
                self.nextVideo( videoId,e );
            })

            $( "#cws-ytp-video-play-button" ).click( function() {

                $( '#cws-ytp-video-placeholder' ).fadeIn( 400, function() {
                    $( '#cws-ytp-video-placeholder').css( 'opacity',1 );
                });
                
                if( player ) {
                    player.loadVideoById( videoId );  // auto plays video
                    self.showCloseButton();         // show stop button / x
                    self.hideVideoControls();       // Hide Video Controls

                    //self.hideChannelLogo();
                }
            })

            /* Change display to grid view */
            // Don't think these should be exposed to user ui
            // rather should be an option set in shortcode list|grid|carousel
            $( "#grid-button" ).click(function(e) {
                self.gridView(e);
            })

            /* Change display to list view */
            // Don't think these should be exposed to user ui
            // rather should be an option set in shortcode list|grid|carousel            
            $( "#list-button" ).click(function(e) {
                self.listView(e);
            })  

            /* Change display to carousel view */
            // Don't think these should be exposed to user ui
            // rather should be an option set in shortcode list|grid|carousel            
            $( "#carousel-button" ).click(function(e) {
                //self.carouselView(e);
                //console.log('clicked carousel init slick');
                //alert('view value ' + strView);
                
                self.sliderInit();
            })  


            // Toggle Description - Should this be here or some place else?
            // https://codepen.io/JTParrett/pen/CAglw
            var description = $( '#cws-ytp-video-description' ),
            animateTime = 500,
            descriptionLink = $( '#cws-ytp-descToggle' );

            descriptionLink.click( function( e ) {
                e.preventDefault();
                if( description.height() === 0 ) {
                    // autoHeightAnimate(description, animateTime);
                    self.autoHeightAnimate( description, animateTime );
                } else {
                    description.stop().animate( { height: '0' }, animateTime );
                }

                // Toggle caret up / down when clicked...
                $( this ).find( 'i' ).toggleClass( 'fa-caret-up fa-caret-down' );
            });

        }


// Dont think this is implemented
/*
self.showHide = function(area, time, link) {

console.log(area + ' ' + time + ' ' + link);

            var description = area,
            animateTime = time,
            descriptionLink = $(link);

            descriptionLink.click(function(e) {
                e.preventDefault();
                if(description.height() === 0) {
                    self.autoHeightAnimate(description, animateTime);
                } else {
                    description.stop().animate({ height: '0' }, animateTime);
                }

                // Toggle caret up / down when clicked...
                //$(this).find('i').toggleClass('fa-caret-up fa-caret-down');
            });

}
*/

        /* ---------------------------------------
            Retrieve the previous page of videos in the playlist.
        --------------------------------------- */  
        self.previousPage = function( prevPageToken,e ) {
            e.preventDefault();

            if( config.caller == 'search' ){
                self.search(config.term, nextPageToken);  
            } else {
                //self.getPlaylistVideos(self.uploadsID, prevPageToken);
                self.getPlaylistVideos(self.channelId, prevPageToken);
            }
        }

        /* ---------------------------------------
            Retrieve the next page of videos in the playlist.
        --------------------------------------- */  
        self.nextPage = function( nextPageToken,e ) {

            // alert(config.caller);

            e.preventDefault();

            if( config.caller == 'search' ){
                self.search(config.term, nextPageToken);  
            } else {
                //self.getPlaylistVideos(self.uploadsID, nextPageToken);
                self.getPlaylistVideos(self.channelId, nextPageToken);
            }

            // stop the current video
            self.stopVideo();

            $( '#cws-ytp-video-placeholder' ).css( 'display','none' );

            //console.log($(this));

            self.hideCloseButton();     // show stop button / x
        } 

         /* ---------------------------------------
            Grid View Display
        --------------------------------------- */ 
        self.gridView = function( e ) {
            e.preventDefault();

            $( '.cws-ytp-video-item-container' ).addClass( 'gridView' );
            $( '.cws-ytp-video-item-container' ).removeClass( 'cws-ytp-listview' );

            // Call check title length and add overflow class if needed...
            self.isOverflowText();

            // remember users view choice
            $.cookie( 'view', 'grid', { path: '/' } );
        }

         /* ---------------------------------------
            List View Display
        --------------------------------------- */ 
        self.listView = function( e ) {
            e.preventDefault();

            $( '.cws-ytp-video-item-container' ).addClass( 'cws-ytp-listview' );
            $( '.cws-ytp-video-item-container' ).removeClass( 'gridView' );

            // Call check title length and add overflow class if needed...
            self.isOverflowText();

            // remember users view choice
            $.cookie( 'view', 'list', { path: '/' } );
        }        

         /* ---------------------------------------
            Retrieve the previous video
        --------------------------------------- */  
        self.previousVideo = function( videoId,e ) {
            e.preventDefault();

            // Find video item with videoId
            var playing = $( '.cws-ytp-video-image-link[data-videoid="'+videoId+'"]' );

            // jump up to its parent div with class playing // jump to prev div
            var previousVideo = playing.closest( '.playing' ).prev( 'div' );

            if( previousVideo.length ) {

                //console.log(previousVideo);

                if( previousVideo.length ) {
                    playing.parent().removeClass( 'playing' );
                    // add class playing
                    previousVideo.addClass('playing');
                    // update player 

                    self.loadVideo( previousVideo, 1, 1 );
                }
            } else {
                // there is no previous video so load the last video
                var videoItems = $( '.cws-ytp-video-item' );
                var lastVideo = videoItems.last();
                self.loadVideo( lastVideo, 1, 1 );
            }

        }

         /* ---------------------------------------
            Retrieve the next video
        --------------------------------------- */  
        self.nextVideo = function( videoId,e ) {
            e.preventDefault();

            // find video item with videoId
            var playing = $( '.cws-ytp-video-image-link[data-videoid="' + videoId + '"]' );
            // jump up to its parent div with class playing // jump to next div
            var nextVideo = playing.closest( '.playing' ).next( 'div' );
/*
console.log('playing: ');
console.log( playing);
console.log('nextVideo:');
console.log( nextVideo);
*/
            if( nextVideo.length ) {

                if( nextVideo.length ) {
                    playing.parent().removeClass( 'playing' );
                    // add class playing
                    nextVideo.addClass( 'playing' );
                    // update player 
                    self.loadVideo( nextVideo, 1, 1 );
                }
            } else {
                // there is no next video so load the first video
                var videoItems = $( '.cws-ytp-video-item' );
                var lastVideo = videoItems.first();
                self.loadVideo( lastVideo, 1, 1 );
            }
        }         

         /* ---------------------------------------
            Convert date to something meaningful
        --------------------------------------- */  
        self.convertDate = function ( timestamp ) {

            var numDiffMS;
            var numDiffHour;
            var numDiffDay;
            var numDiffMonth;
            var numDiffYear;

            if( timestamp == "" || timestamp == "undefined" || timestamp == null ) {
                return "";
            }
                
            // find ms between now and timestamp of video
            numDiffMS = Math.abs( new Date() - new Date( timestamp ) );

            // convert to hours
            numDiffHour = numDiffMS / 1000 / 60 / 60;

            // Is it more than 1 day?
            if( numDiffHour > 24 ) {
                numDiffDay = numDiffHour / 24;

                // Working in months
                if( numDiffDay > 30 ) {

                    numDiffMonth = numDiffDay/30;
                    
                    // Working in years
                    if( numDiffMonth > 12 ) {
                        numDiffYear = numDiffMonth / 12;
                        numDiffYear = Math.round( numDiffYear );

                        if( numDiffYear <= 1 ) {
                            return " <span>"+numDiffYear+"year ago</span>";
                        } else {
                            return " <span>"+numDiffYear+"years ago</span>";
                        }           
                    } else {
                        numDiffMonth = Math.round( numDiffMonth );
                        if( numDiffMonth <= 1 ) {
                            return  " <span>"+numDiffMonth+"month ago</span>";
                        } else {
                            return " <span>"+numDiffMonth+"months ago</span>";
                        }           
                    }
                } else {
                    // Less than a month, working in days
                    numDiffDay = Math.round( numDiffDay );
                    if( numDiffDay <= 1 ) {
                        return " <span>"+numDiffDay+" day ago</span>";
                    } else {
                        return " <span>"+numDiffDay+" days ago</span>";
                    }
                }
            } else {
                // Working in hours
                numDiffHour = Math.round( numDiffHour );
                if( numDiffHour < 1 ) {
                    return "just now";
                } else if( numDiffHour == 1 ) {
                    return " <span>"+numDiffHour+" hour ago</span>";
                } else {
                    return " <span>"+numDiffHour+" hours ago</span>";
                }
            }   
        },


        /* ---------------------------------------
            Animate height of video description
        --------------------------------------- */  
        //https://codepen.io/JTParrett/pen/CAglw
        self.autoHeightAnimate = function( element, time ) {
            var curHeight = element.height(), // Get Default Height
            autoHeight = element.css( 'height', 'auto' ).height(); // Get Auto Height
            element.height( curHeight ); // Reset to Default Height
            element.stop().animate( { height: autoHeight }, time ); // Animate to Auto Height
        }


        // load first video in list
        self.loadFirstVideo = function() {
            var videoItems = $( '.cws-ytp-video-item' );
            // var lastVideo = videoItems.first();
            var firstVideo = videoItems.first();

            self.loadVideo(firstVideo, 1, 1);    
        }
        //
/*
        //var player;
        // whenever youtube callback was called = deferred resolved
        // your custom function will be executed with YT as an argument
        YTdeferred.done(function(YT) {
            
            console.log('Player ready');
            // creating a player
            // https://developers.google.com/youtube/iframe_api_reference#Getting_Started
            player = new YT.Player(config.videoArea, {
                width: 600,
                height: 400,

                playerVars: {
                    color: 'white',
                    autoplay: 0,
                },
                events: {
                    //onReady: self.initialize
                }
            });
            console.log(player);

        });
*/


  // document ready
  $(document).ready(function() {
    //console.log('Document ready');

    //var player;
    // whenever youtube callback was called = deferred resolved
    // your custom function will be executed with YT as an argument
    YTdeferred.done(function(YT) {
      //console.log('Player ready
      // creating a player
      // https://developers.google.com/youtube/iframe_api_reference#Getting_Started
            player = new YT.Player(config.videoArea, {
        height: '390',
        width: '640',
        //videoId: 'M7lc1UVf-VE'
      });

    });

  });





        // Cue video
        $( '#cws_ytp .cws-ytp-video-image-link' ).live( 'click', function () {

            //youtube.loadVideo($(this),0,1);
            var videoItem = $( this ).parent( '.cws-ytp-video-item' );
            //youtube.loadVideo($(this),0,1);
            self.loadVideo( videoItem,0,1 );

            // stop the current video
            self.stopVideo();

            $('#cws-ytp-video-placeholder').css('display','none');

            // show stop button / x
            self.hideCloseButton();
            self.showVideoControls();
        });


        // cue if clicked title area
        $('#cws_ytp').on('click', '.cws-ytp-video-title-link', function () {

            //youtube.loadVideo($(this),0,1);
            var videoItem = $(this).parents('.cws-ytp-video-item');
            //youtube.loadVideo($(this),0,1);
             self.loadVideo(videoItem,0,1);

            // stop the current video
            self.stopVideo();

            $('#cws-ytp-video-placeholder').css('display','none');

            self.hideCloseButton();
            self.showVideoControls();
        });


        // Load video
        $('#cws_ytp .cws-ytp-play-button').live('click', function (e) { // when using this - video plays but is not visible!

            var videoItem = $(this).parents('.cws-ytp-video-item'); 
            //youtube.loadVideo($(this),0,0);
            self.loadVideo(videoItem,0,0);
            e.stopPropagation(); //stop bubbling

            // show stop button / x
            self.showCloseButton();
            self.hideVideoControls();
        });

        $( "#cws-ytp-video-stop-button" ).click( function() {
            self.closeVideoPlayer();
        });


    } // end of CWS.YouTube 


})( jQuery );