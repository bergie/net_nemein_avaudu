(function($) {
    $(document).ready(function() {
        // Add padding to top and bottom of the container so that the contents are not hidden.
        //$('#main').css({'padding-top': $('#header').height(), 'padding-bottom': $('#footer').height() });
        
        // Load some initial test data to play with, later this will be replaced by a call to the actual page route
        $.getJSON('/latest/20.json',
        function(json) {
            var entries = '';
            for (var i = json.length - 1; i >= 0; i--){
                entries += '<li class="" id="'+json[i].id+'"><div class="profile"><img src="'+json[i].user.profile_image_url+'" /></div><div class="content"><p class="author">'+json[i].user.screen_name+'</p><div class="entry">'+json[i].text_html+'</div></div></li>';
            };
            $activity = $('<ol />').attr('id','activity').append(entries);
            $('#main').html($activity);
        });
        
        // In case we want to do something interesting with each message, we can select it by binding a live click event on the selector
        // $('#main li').live('click', function() {
        //     $('#main li').removeClass('selected').filter('#'+this.id).addClass('selected');
        // });
    });    
})(jQuery);