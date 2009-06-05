(function($) {
    $(document).ready(function() {
        $('#footer form').submit(function() {
            $input = $(this).children('#new');
            $message = $input.val();
            $input.val('');
            if ($message)
            {
                $.post('/statuses/update.json',{
                status: $message},
                function(json){
                    $input.blur();
                    $activity = $('#main #activity');
                    if ($activity.size() > 0)
                    {
                        $activity.prepend('<li class="" id="'+json[0].id+'"><div class="profile"><img src="'+json[0].user.profile_image_url+'" /></div><div class="content"><p class="author">'+json[0].user.screen_name+'</p><div class="entry">'+json[0].text_html+'</div></div></li>');
                    }
                },
                "json");
            }
            return false;
        });
    });
})(jQuery);