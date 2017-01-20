// global.js - mooAjax
define(['jquery'],function($){
    return {
        mooAjax:function(sUrl, sType, aData, callback){
            if(sType.toLowerCase() == 'get'){
                $.ajax({
                    url: sUrl,
                    data: aData,
                    success: callback
                });
            }else if(sType.toLowerCase() == 'post'){
                $.ajax({
                    type: "POST",
                    url: sUrl,
                    data: aData,
                    success: callback
                });
            }
        }
    }
});