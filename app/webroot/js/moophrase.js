(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD
        define(['jquery'], factory);
    } else if (typeof exports === 'object') {
        // Node, CommonJS-like
        module.exports = factory(require('jquery'));
    } else {
        // Browser globals (root is window)
        root.MooPhrase = factory();
    }
}(this, function () {
    var mooPhraseVars = {};
    function add(name, value){
        mooPhraseVars[name] = value;
    };
    function set(obj){
        mooPhraseVars = obj;
    };
    function __(name){
        return mooPhraseVars[name] ;
    };
    //    exposed public methods
    return {
        add:add,
        __:__,
        set:set
    }
}));