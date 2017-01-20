// global.js - disableButton
// global.js - enableButton
define(['jquery'],function($){
    return {
        disableButton:function(button){
            tmp_class = $("#" + button + " i").attr("class");
            $("#" + button + " i").attr("class", "icon-refresh icon-spin");
            $("#" + button).addClass('disabled');
        },
        enableButton:function(button){
            $("#" + button + " i").attr("class", tmp_class);
            $("#" + button).removeClass('disabled');
        }
    }
});