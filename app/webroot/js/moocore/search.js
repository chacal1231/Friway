(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD
        define(['jquery'], factory);
    } else if (typeof exports === 'object') {
        // Node, CommonJS-like
        module.exports = factory(require('jquery'));
    } else {
        // Browser globals (root is window)
        root.mooSearch = factory(root.jQuery);
    }
}(this, function ($) {
    //    methods
    function globalSearchMore(filter){
        jQuery('#filter-' + filter).trigger('click');
    };
    function init(){
        jQuery('#global-search-filters a:not(.no-ajax)').click(function(){
            jQuery(this).spin('tiny');
            jQuery('#global-search-filters .current').removeClass('current');
            jQuery(this).parent().addClass('current');

            switch ( jQuery(this).attr('id') )
            {
                case 'filter-blogs':
                case 'filter-groups':
                case 'filter-topics':
                    jQuery('#search-content').html('<ul class="list6 comment_wrapper" id="list-content">Loading...</ul>');
                    //jQuery('#center').html('<ul class="list6 comment_wrapper" id="list-content">Loading...</ul>');
                    break;

                case 'filter-albums':
                case 'filter-videos':
                    jQuery('#search-content').html('<ul class="list4 albums" id="list-content">Loading...</ul>');
                    break;

                case 'filter-users':
                    jQuery('#search-content').html('<ul class="list1 users_list" id="list-content">Loading...</ul>');
                    break;
                default :
                    jQuery('#search-content').html('<ul class="list6 comment_wrapper" id="list-content">Loading...</ul>');
            }

            var obj = $(this);
            var type = jQuery(this).hasClass('json-view');
            jQuery('#center').load( encodeURI( jQuery(this).attr('data-url') ), {noCache: 1}, function(response){
                obj.spin(false);
                MooResponsive.init();
            });

            return false;
        });
    };
    function hashInit(params){
        init();
        tabs = params.tabs;
        if(tabs != '')
        {
            if ($("#filter-"+tabs).length > 0)
            {
                $("#filter-"+tabs).spin('tiny');
                $('#global-search-filters .current').removeClass('current');
                $("#filter-"+tabs).parent().addClass('current');
                $('#center').html('Loading...');
                $('#center').load( $('#filter-'+tabs).attr('data-url'), function(response){
                    //$('#home-content').html($.parseJSON(response).data);
                    $('#filter-'+tabs).spin(false);
                    MooResponsive.init();
                });
            }else
                window.location =  params.link;
        }
    };
    //    exposed public method
    return {
        globalSearchMore:globalSearchMore,
        init:init,
        hashInit:hashInit
    };
}));

