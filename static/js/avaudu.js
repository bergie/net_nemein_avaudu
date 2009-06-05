(function($) {
    $(document).ready(function() {
        $.getJSON('test.json',
        function(json){
            // console.log(json[0].user);
            for (var i = json.length - 1; i >= 0; i--){
                console.log(json[i].text);
            };
        });
        
        var heightview = window.innerHeight - 100;
        console.log($('#main').height());
        
        $('#main').scroll(function() {
            percent = ($('#main').scrollTop() / $('#main').height()) * 100;
            if (percent > 75)
            {
                var loading = true;
                $.getJSON('test.json',
                 function(json){
                    for (var i = json.length - 1; i >= 0; i--){
                        $('<li />').attr({class: 'post qaiku', id: json[i].id}).append(
                            $('<div />').addClass('profile').append(
                                $('<img />').attr('src', json[i].user.profile_image_url)
                            )
                        ).append(
                            $('<div />').addClass('content').append(
                                    $('<p />').addClass('author').text(json[i].user.screen_name)
                                ).append(
                                    json[i].text
                                )
                        ).appendTo('#activity');
//                        loading 
//                        console.log(json[i].text);
//                        loading = false;
                    };
                });
                
            }
        });
        // <li class="qaiku reply" id="quattro">
        //     <div class="profile">
        //         <img src="http://static.qaiku.com/blobs/1/D/1de059f85d5d784-059f-11de-8bc6-552692129e8f/smallsquare.jpeg">
        //     </div>
        //     <div class="content">
        //         <p class="author">bergie</p>
        //         Ut interdum lectus interdum elit faucibus vitae bibendum elit congue. Sed pellentesque egestas tellus. Vivamus nisl mauris, volutpat ac sed.
        //     </div>
        // </li>
        
        
        $('#main').css({height: heightview + 'px'});
        $(window).resize(function() {
            var viewheight = window.innerHeight;
            $('#main').css({height: viewheight-100 +'px'});
        });
        $messages = $('ol li');
        $('ol li').click(function() {
            $messages.removeClass('selected').filter('#'+this.id).addClass('selected');
        });
    });    
})(jQuery);