(function($) {
    $(document).ready(function() {
        $main = $('#main');
        // Add padding to top and bottom of the container so that the contents are not hidden.
        var count = 0;

        //$main.css({'padding-top': $('#header').height(), 'padding-bottom': $('#footer').height() });        
        // Load some initial test data to play with, later this will be replaced by a call to the actual page route
        $.getJSON('/latest/20.json',
        function(json) {
            var entries = '';
            for (var i = json.length - 1; i >= 0; i--){
                entries += '<li class="" id="'+json[i].id+'"><div class="profile"><img src="'+json[i].user.profile_image_url+'" /></div><div class="content"><p class="author">'+json[i].user.screen_name+'</p><div class="entry">'+json[i].text_html+'</div></div></li>';
            };
            $activity = $('<ol />').attr('id','activity').append(entries);
            $main.html($activity);
        });
        
//        setInterval(get_latest, 10000);
        
        function sequential_fadein(elem)
        {
            console.log(elem);
            elem.fadeIn('def',function(){
                $(this).prev().length && sequential_fadein($(this).prev()); 
            });
        }
        
        
        function get_latest()
        {
            $.getJSON('/latest/5.json',
            function(json) {
                // var entries = '';
                window.fluid.dockBadge = "5";
                $.each(json, function(i, val) {
                    $to_be_added = $('<li class="" id="'+val.id+'"><div class="profile"><img src="'+val.user.profile_image_url+'" /></div><div class="content"><p class="author">'+val.user.screen_name+'</p><div class="entry">'+val.text_html+'</div></div></li>').css('display','none').prependTo('#main #activity');
                });
                sequential_fadein($('li:hidden:last', $to_be_added.parent()));
                // for (var i = json.length - 1; i >= 0; i--){
                //     entries += '<li class="" id="'+json[i].id+'"><div class="profile"><img src="'+json[i].user.profile_image_url+'" /></div><div class="content"><p class="author">'+json[i].user.screen_name+'</p><div class="entry">'+json[i].text_html+'</div></div></li>';
                // };
                // $('#main #activity').prepend($(entries).fadeIn('slow'));
            });
        }
                
        // In case we want to do something interesting with each message, we can select it by binding a live click event on the selector
        // $('#main li').live('click', function() {
        //     $('#main li').removeClass('selected').filter('#'+this.id).addClass('selected');
        // });
    });    
})(jQuery);