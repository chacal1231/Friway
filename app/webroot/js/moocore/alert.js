// global.js - mooAlert
// global.js - mooConfirm
// global.js - confirmSubmitForm
define(['jquery'],function($){
    return {
        mooAlert:function(msg){
            $.fn.SimpleModal({btn_ok: mooPhraseVars['btn_ok'], title: mooPhraseVars['message'], hideFooter: false, closeButton: false, model: 'alert', contents: msg}).showModal();
        },
        mooConfirm:function(msg, url){
            // Set title
            $($('#portlet-config  .modal-header .modal-title')[0]).html(mooPhraseVars['confirm_title']);
            // Set content
            $($('#portlet-config  .modal-body')[0]).html(msg);
            // OK callback
            $('#portlet-config  .modal-footer .ok').click(function(){
                window.location = url;
            });
            $('#portlet-config').modal('show');
        },
        confirmSubmitForm:function(msg, form_id){
            $.fn.SimpleModal({
                btn_ok: 'OK',
                model: 'confirm',
                callback: function(){
                    document.getElementById(form_id).submit();
                },
                title: 'Please Confirm',
                contents: msg,
                hideFooter: false,
                closeButton: false
            }).showModal();
        }
    }
});