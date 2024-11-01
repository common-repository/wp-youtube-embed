// Called automatically when YouTube API interface is loaded
function onYouTubeApiLoad() {
    //console.log( 'onYouTubeApiLoad...' );

    var player,
    time_update_interval = 0;

    // This is a sample configuration
    var config =
    {
        caller: 'channel',
        youtube: '#cws_ytp',
        channelHeaderStart: "<div class='cws-ytp-header'>",
        channelHeaderEnd: "</div>",
        videoArea: 'cws-ytp-video-placeholder',

        apiKey: php_vars.api_key,
        
        channelID: php_vars.channelid,
        username: php_vars.username,

        maxResults: php_vars.maxResults,
        showLayout: php_vars.showLayout,
        showBackgroundImage: true,

        // Header
        showChannelHeader: php_vars.showChannelHeader,        
        showSocial: php_vars.showSocial,
        arrSocial: php_vars.arrSocial,
        showSubscribe: php_vars.showSubscribe,
        showChannelDescription: php_vars.showChannelDescription,

        // video meta
        showLikes: php_vars.showLikes,
        showViews: php_vars.showViews,
        showDate: php_vars.showDate,
/*
        // Slider
        arrows: php_vars_slick.inner.arrows,
        infinite: php_vars_slick.inner.infinite,
        slidesToShow: php_vars_slick.inner.slidesToShow,        
        slidesToScroll:  php_vars_slick.inner.slidesToScroll,
        autoplay: php_vars_slick.inner.autoplay,        
        autoplaySpeed: php_vars_slick.inner.autoplaySpeed,
        speed: php_vars_slick.inner.speed,
        dots: php_vars_slick.inner.dots,

        duration: php_vars_slick.inner.duration,
        playIcon: php_vars_slick.inner.playIcon,
        title: php_vars_slick.inner.title,


        // Slider Advanced 1
        breakpoint_adv1: php_vars_slick.inner.breakpoint_adv1,
        arrows_adv1: php_vars_slick.inner.arrows_adv1,
        infinite_adv1: php_vars_slick.inner.infinite_adv1,
        slidesToShow_adv1: php_vars_slick.inner.slidesToShow_adv1,       
        slidesToScroll_adv1:  php_vars_slick.inner.slidesToScroll_adv1,
        autoplay_adv1: php_vars_slick.inner.autoplay_adv1,      
        autoplaySpeed_adv1: php_vars_slick.inner.autoplaySpeed_adv1,
        speed_adv1: php_vars_slick.inner.speed_adv1,
        dots_adv1: php_vars_slick.inner.dots_adv1,

        duration_adv1: php_vars_slick.inner.duration_adv1,
        playIcon_adv1: php_vars_slick.inner.playIcon_adv1,
        title_adv1: php_vars_slick.inner.title_adv1,

        // Slider Advanced 2
        breakpoint_adv2: php_vars_slick.inner.breakpoint_adv2,
        arrows_adv2: php_vars_slick.inner.arrows_adv2,
        infinite_adv2: php_vars_slick.inner.infinite_adv2,
        slidesToShow_adv2: php_vars_slick.inner.slidesToShow_adv2,       
        slidesToScroll_adv2:  php_vars_slick.inner.slidesToScroll_adv2,
        autoplay_adv2: php_vars_slick.inner.autoplay_adv2,      
        autoplaySpeed_adv2: php_vars_slick.inner.autoplaySpeed_adv2,
        speed_adv2: php_vars_slick.inner.speed_adv2,
        dots_adv2: php_vars_slick.inner.dots_adv2,

        duration_adv2: php_vars_slick.inner.duration_adv2,
        playIcon_adv2: php_vars_slick.inner.playIcon_adv2,
        title_adv2: php_vars_slick.inner.title_adv2,

        // Slider Advanced 3
        breakpoint_adv3: php_vars_slick.inner.breakpoint_adv3,
        arrows_adv3: php_vars_slick.inner.arrows_adv3,
        infinite_adv3: php_vars_slick.inner.infinite_adv3,
        slidesToShow_adv3: php_vars_slick.inner.slidesToShow_adv3,       
        slidesToScroll_adv3:  php_vars_slick.inner.slidesToScroll_adv3,
        autoplay_adv3: php_vars_slick.inner.autoplay_adv3,      
        autoplaySpeed_adv3: php_vars_slick.inner.autoplaySpeed_adv3,
        speed_adv3: php_vars_slick.inner.speed_adv3,
        dots_adv3: php_vars_slick.inner.dots_adv3,   

        duration_adv3: php_vars_slick.inner.duration_adv3,
        playIcon_adv3: php_vars_slick.inner.playIcon_adv3,
        title_adv3: php_vars_slick.inner.title_adv3,
*/        
    };
//alert(config.duration);
/*
var str='';
str = JSON.stringify(config);
str = JSON.stringify(config, null, 4); // (Optional) beautiful indented output.
//console.log(str); // Logs output to dev tools console.
alert(str); // Displays output using window.alert()
*/

    // See https://goo.gl/PdPA1 to get a key for your own applications.
    var resp = gapi.client.setApiKey( config.apiKey );

    // Create and initialise the YouTube object
    var youtubeChannel = new CWS.YouTube( config );
    youtubeChannel.init(); 

    // Get Channel Header
    youtubeChannel.getChannelDetails();
}