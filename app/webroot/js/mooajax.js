(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD
        define(['jquery'], factory);
    } else if (typeof exports === 'object') {
        // Node, CommonJS-like
        module.exports = factory(require('jquery'));
    } else {
        // Browser globals (root is window)
        root.MooAjax = factory(root.jQuery);
    }
}(this, function ($) {
    return {
        post: function (options, callback) {
            $.ajax({
                type: 'post',
                url: options.url,
                data: options.data,
                success: callback
            });
        },

        get: function (options, callback) {
            $.ajax({
                type: 'get',
                url: options.url,
                data: options.data,
                success: callback
            });
        }
    }
}));
