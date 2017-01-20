(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD
        define(['jquery'], factory);
    } else if (typeof exports === 'object') {
        // Node, CommonJS-like
        module.exports = factory(require('jquery'));
    } else {
        // Browser globals (root is window)
        root.MooPhoto = factory(root.jQuery,root.MooPhotoTheater);
    }
}(this, function ($,MooPhotoTheater) {
    return{
        init: function() {
    		MooPhotoTheater.init();
        }
    }
}));