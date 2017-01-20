(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD
        define(['jquery','tipsy','autogrow'], factory);
    } else if (typeof exports === 'object') {
        // Node, CommonJS-like
        module.exports = factory(require('jquery'));
    } else {
        // Browser globals (root is window)
        root.ServerJS = factory(root.jQuery);
    }
}(this, function ($) {
    //    methods
    function init(){

            $('textarea:not(.no-grow)').autogrow();
            //$('input, textarea').placeholder();
            $(".tip").tipsy({ html: true, gravity: 's' });
            $('.truncate').each(function(){
                if ( parseInt($(this).css('height')) >= 145 )
                    $(this).after('<a href="javascript:void(0)" onclick="showMore(this)" class="show-more">' + $(this).data('more-text') + '</a>');
            });

            $('.comment-truncate').each(function(){
                if ( parseInt($(this).css('height')) >= 45 )
                    $(this).after('<a href="javascript:void(0)" onclick="showMore(this)" class="show-more">' + $(this).data('more-text') + '</a>');
            });


            registerOverlay();
            registerImageComment();


            $('#browse a:not(.overlay):not(.no-ajax)').click(function(){
                $(this).children('.badge_counter').hide();
                $(this).spin('tiny');

                $('#browse .current').removeClass('current');
                $(this).parent().addClass('current');

                var div = jQuery(this).attr('rel');
                if ( div == undefined )
                    div = 'list-content';

                var el = $(this);

                $('#' + div).load( $(this).attr('data-url')+'?'+$.now(), function(response){

                    var res = '';
                    try {
                        res = $.parseJSON(response).data;
                    } catch(error) {
                        res = response
                    }
                    $('#'+ div).html(res);

                    el.children('.badge_counter').fadeIn();
                    el.spin(false);

                    // reattach events
                    $('textarea:not(.no-grow)').autogrow();
                    $(".tip").tipsy({ html: true, gravity: 's' });

                    registerOverlay();
                    $('.truncate').each(function(){
                        if ( parseInt($(this).css('height')) >= 145 )
                            $(this).after('<a href="javascript:void(0)" onclick="showMore(this)" class="show-more">' + $(this).data('more-text') + '</a>');
                    });
                    
                    window.history.pushState({},"", el.attr('href'));

                    MooResponsive.init();
                });

                return false;
            });

            jQuery('#keyword').keyup(function(event) {
                if (event.keyCode == '13') {
                    $('#browse_all').spin('tiny');
                    jQuery('#browse .current').removeClass('current');
                    jQuery('#browse_all').addClass('current');

                    var ajax_browse = 'ajax_browse';
                    var ext = '';
                    if(jQuery(this).hasClass('json-view'))
                    {
                        ajax_browse = 'browse';
                        //ext = '.json';
                    }
                    var type = jQuery(this).hasClass('json-view');

                    var contentId = ''
                    if (jQuery(this).attr('rel') == 'albums'){
                        contentId = '#album-list-content';
                    }else {
                        contentId = '#list-content';
                    }

                    jQuery(contentId).load( baseUrl + '/' + jQuery(this).attr('rel') + '/'+ajax_browse+'/search/' + encodeURI( jQuery(this).val()+ext ), {noCache: 1}, function(response){

                        $('#browse_all').spin(false);
                        jQuery('#keyword').val('');
                    });
                }
            });

        initSearch();






    };
    function initSearch(){
        if($('.suggestionInitSlimScroll').height() > 500){
            $('.suggestionInitSlimScroll').slimScroll({ height: '500px' });
        }

        jQuery('#global-search').keyup(function(event) {
            var searchVal = $(this).val();
            if(searchVal != ''){
                $.post(baseUrl + "/search/suggestion/all", {searchVal: searchVal}, function(data){
                    $('.global-search .slimScrollDiv').show();
                    $('#display-suggestion').html(data).show();
                });
            }

            if (event.keyCode == '13') {
                if(jQuery(this).val()!=''){
                    var searchStr = jQuery(this).val().replace('#', '');
                    if (jQuery(this).val().indexOf('#') > -1){
                        window.location = baseUrl + '/search/hashtags/' + encodeURIComponent(searchStr);
                    }else{
                        window.location = baseUrl + '/search/index/' + encodeURIComponent(searchStr);
                    }
                }
            }
        });

        jQuery('#global-search').focusout(function(event) {
            if($('#display-suggestion').is(":hover")==false){
                $('#display-suggestion').html('').hide();
                $('.global-search .slimScrollDiv').hide();
            }
        });

        jQuery('#global-search').focus(function(event) {

            jQuery('#global-search').trigger('keyup');

        });
    }
    //    exposed public method
    return {init:init};
}));