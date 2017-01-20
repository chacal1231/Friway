// global validateUser()
define(['jquery'],function($){
    return{
        validateUser:function(){
            if (typeof(mooViewer) == 'undefined'){
                $.fn.SimpleModal({
                    btn_ok : mooPhraseVars['btn_ok'],
                    model: 'modal',
                    title: mooPhraseVars['warning'],
                    contents: mooPhraseVars['please_login']
                }).showModal();
                return false;
            }
            else if (mooCore['setting.require_email_validation'] && !mooViewer['is_confirmed']){
                $.fn.SimpleModal({
                    btn_ok : mooPhraseVars['btn_ok'],
                    model: 'modal',
                    title: mooPhraseVars['warning'],
                    contents: mooPhraseVars['please_confirm_your_email']
                }).showModal();
                return false;
            }
            else if (mooCore['setting.approve_users'] && !mooViewer['is_approved']){
                $.fn.SimpleModal({
                    btn_ok : mooPhraseVars['btn_ok'],
                    model: 'modal',
                    title: mooPhraseVars['warning'],
                    contents: mooPhraseVars['your_account_is_pending_approval']
                }).showModal();
                return false;
            }

            return true;
        }
    }
});