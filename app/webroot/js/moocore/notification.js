// global.js - removeNotification
define(['jquery','ajax'],function($,ajax){
    return{
        removeNotification:function(id){
            ajax.mooAjax(baseUrl + '/notifications/ajax_remove/'+id, 'get', '', function(data) {
                $("#noti_"+id).slideUp();

                if ( $('#noti_' + id).hasClass('unread') && jQuery("#notification_count").html() != '0' )
                {
                    var noti_count = parseInt($(".notification_count").html()) - 1;

                    if(noti_count == 0)
                    {
                        $(".notification_count").remove();
                    }
                    else
                    {
                        $(".notification_count").html( noti_count );
                    }
                    $("#notification_count").html( noti_count );

                    Tinycon.setBubble( noti_count );
                }
            });
        }
    }
});