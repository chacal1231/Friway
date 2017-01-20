// global.js  registerOverlay()
// global.js registerImageOverlay()
define(['jquery'],function($){
    return{
        registerOverlay:function(){
            $('.overlay').unbind('click');
            $('.overlay').click(function()
            {
                overlay_title = $(this).attr('title');
                overlay_url = $(this).attr('href');
                overlay_div = $(this).attr('rel');

                if (overlay_div)
                {
                    sModal = $.fn.SimpleModal({
                        btn_ok : mooPhraseVars['btn_ok'],
                        model: 'modal',
                        title: overlay_title,
                        contents: $('#' + overlay_div).html()
                    });
                }
                else
                {
                    sModal = $.fn.SimpleModal({
                        width: 600,
                        model: 'modal-ajax',
                        title: overlay_title,
                        offsetTop: 100,
                        param: {
                            url: overlay_url,
                            onRequestComplete: function() {
                                $(".tip").tipsy({ html: true, gravity: 's' });
                            },
                            onRequestFailure: function() { }
                        }
                    });
                }

                sModal.showModal();

                return false;
            });
        },
        registerImageOverlay:function(){
            $('.attached-image').magnificPopup({
                type:'image',
                gallery: { enabled: true },
                zoom: {
                    enabled: true,
                    opener: function(openerElement) {
                        return openerElement;
                    }
                }
            });
        },
    }
});