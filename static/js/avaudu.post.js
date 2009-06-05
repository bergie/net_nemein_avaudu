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
                 function(){
                    $input.blur();
                });
            }
            return false;
        });
    });
})(jQuery);