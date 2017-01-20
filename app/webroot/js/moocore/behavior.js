// global.js - createItem
// global.js - moreResults
// global.js - toggleCheckboxes
// global.js - toggleMenu
// global.js - registerImageComment
(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD
        define(['jquery', 'button'], factory);
    } else if (typeof exports === 'object') {
        // Node, CommonJS-like
        module.exports = factory(require('jquery'), require('button'));
    } else {
        // Browser globals (root is window)
        root.mooBehavior = factory(root.jQuery, root.button);
    }
}(this, function ($, button) {
    //var flagScroll = true;
    //    methods
    function initAutoLoadMore(){
        if(typeof autoLoadMore!== 'undefined' && autoLoadMore != ''){
            $(window).scroll(function(){
                var $elem = $('.view-more');
                if($elem.length > 0){
                    var $window = $(window);

                    var docViewTop = $window.scrollTop();
                    var docViewBottom = docViewTop + $window.height();

                    var elemTop = $elem.offset().top;
                    var elemBottom = elemTop + $elem.height();

                    if(( elemBottom <= docViewBottom) && (elemTop >= docViewTop) && flagScroll)
                    {
                        $elem.before('<div style="text-align: center" class="loading"><img src="'+baseUrl+'/img/loading.gif" /></div>');
                        $elem.find('a').trigger('click');
                        flagScroll = false;
                    }
                }
            });
        }
    };    //    private because it's not returned (see below)

    //    exposed public methods
    return {
        initAutoLoadMore:initAutoLoadMore
    }
}));
