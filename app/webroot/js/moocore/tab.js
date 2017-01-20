// global.js - initTabs
define(['jquery'],function($){
    return{
        initTabs:function(tab){
            jQuery('#' + tab + ' .tabs > li').click(function(){
                jQuery('#' + tab + ' li').removeClass('active');
                jQuery(this).addClass('active');
                jQuery('#' + tab + ' .tab').hide();
                jQuery('#'+jQuery(this).attr('id')+'_content').show();
            });
        }
    }
});