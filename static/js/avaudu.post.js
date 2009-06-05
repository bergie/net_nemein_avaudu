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
                        $activity.prepend('<li class="" id="'+json.id+'"><div class="profile"><img src="'+json.user.profile_image_url+'" /></div><div class="content"><p class="author">'+json.user.screen_name+'</p><div class="entry">'+json.text+'</div></div></li>');
                    }
                },
                "json");
            }
            return false;
        });
    });
})(jQuery);