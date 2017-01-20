<?php
App::uses('CakeEventListener', 'Event');

class MooViewListener implements CakeEventListener
{
    public $v;
    public $libraryLoaded = array();

    public function implementedEvents()
    {
        return array(
            'mooView.loadLibrary' => 'loadLibrary',

        );
    }

    public function setView($v)
    {
        $this->v = $v;
    }

    public function getView()
    {
        return $this->v;
    }

    public function loadLibrary($event)
    {
        $version = Configure::read('core.version');
        $v = $event->subject();
        $this->setView($v);
        $libs = $event->data['libs'];
        foreach ($libs as $lib) {
            if (!$this->isLoaded($lib)) {
                $this->setLoaded($lib);
                switch ($lib) {
                    case 'requireJS':
                        $this->loadRequireJs();
                        break;
                    case 'bootstrap':
                        $v->Helpers->Html->script(
                            array('global/bootstrap/js/bootstrap.min.js'), array('block' => 'mooScript')
                        );
                        break;
                    case 'foundation':
                        break;
                    case 'jquery':
                        $v->Helpers->Html->script(
                            array('global/jquery-1.11.1.min.js'), array('block' => 'mooScript')
                        );
                        break;
                    case 'mooCore':
                        $this->initMentionOverLay();

			if (!$v->request->is('mobile'))
			{				
                            $this->initUserMention();
                            $v->Helpers->Html->script(
                                array('moocore/mention.js'), array('block' => 'mooScript')
                            );
                            
			}
                        
                        if ($v->Helpers->Auth->user('id')) {
                            $viewer = MooCore::getInstance()->getViewer();
                            $confirmed = $viewer['User']['confirmed'] ? $viewer['User']['confirmed'] : 0;
                            $approved = $viewer['User']['approved'] ? $viewer['User']['approved'] : 0;
                            $v->Html->scriptBlock(
                                'var mooViewer = {'
                                . '"is_confirmed":' . $confirmed . ','
                                . '"is_approved":' . $approved . ','
                                . '};',
                                array('inline' => false)
                            );
                        }
                        $require_email_validation = Configure::read('core.email_validation') ? Configure::read('core.email_validation') : 0;
                        $approve_users = Configure::read('core.approve_users') ? Configure::read('core.approve_users') : 0;
                        $v->Html->scriptBlock(
                            'var mooCore = {'
                            . '"setting.require_email_validation":' . $require_email_validation . ','
                            . '"setting.approve_users":' . $approve_users . ','
                            . '};',
                            array('inline' => false)
                        );
                        if($v->isEnableJS('Requirejs'))
                            return $this->loadRequireJs();
                        $v->Helpers->Html->script(
                            array(
                                'moophrase.js',

                            ),
                            array('block' => 'mooPhrase')
                        );
                        $this->initPhraseJs();
                        $js = $this->initJss($version);
                        $js .= $this->initJssBootstrap($version);
                        $this->getView()->prepend('mooScript', $js);
                        $css = $this->initCssBootstrap($version);
                        $css .= $v->Helpers->Html->css(array(
                                'fontello/css/fontello.css'
                            )
                        );
                        $css .= $this->initCss($version);
                        $this->getView()->prepend('css', $css);
                        if (!empty($v->viewVars['uid'])) {
                            $v->addInitJs('$(function() { MooNotification.init(); });');
                        }
                        $v->addInitJs('ServerJS.init();');
                        $this->renderPhraseJS();
                        $v->Helpers->MooPopup->register('themeModal');
                        
                        $v->addInitJs('$(function() { MooPhoto.init(); });');
                        $v->addInitJs('$(function() { mooBehavior.initAutoLoadMore(); });');
                        $v->getEventManager()->dispatch(new CakeEvent('MooView.afterLoadMooCore', $v, $version));
                        break;
                    case 'googleMap':
                        $v->Helpers->Html->script(
                            array('https://maps.google.com/maps/api/js?sensor=false'), array('block' => 'mooScript')
                        );
                        $data =
                            'var map;'
                            . 'var myLatlng;'
                            . 'var geocoder = new google.maps.Geocoder();'
                            . 'geocoder.geocode( { "address": "' . $v->viewVars['address'] . '"}, function(results, status) {'
                            . 'if (status == google.maps.GeocoderStatus.OK) {'
                            . '    myLatlng = new google.maps.LatLng(results[0].geometry.location.lat(),results[0].geometry.location.lng());'
                            . '}else{'
                            . '    myLatlng = new google.maps.LatLng(0,0);'
                            . '}';
                        $afterGetGeoCode =
                            '});';
                        if (!$v->request->is('ajax')) {
                            if (!($v->viewVars['isAjaxModal'])) {
                                $v->Helpers->Html->scriptBlock(
                                    $data . $afterGetGeoCode, array('block' => 'mooScript')
                                );
                            }
                        } else {
                            $data .= 'if (typeof initialize == \'function\') {initialize();}';
                            echo $v->Helpers->Html->scriptBlock($data . $afterGetGeoCode);
                        }
                        break;
                    case 'userTagging':
                        $this->initUserTagging();
                        break;
                    case 'userMention':
                        $this->initUserMention();
                        break;
                    case 'userEmoji':
                        $this->initUserEmoji();
                        break;
                    case 'mentionOverLay':
                        $this->initMentionOverLay();
                        break;
                    case 'tagCloud':
                        $this->initTagCloud();
                        break;
                    case 'adm':
                        $this->initPhraseJs();
                        $this->renderPhraseJS();
                        break;

                }
            }

        }
    }

// Initialing the mooSocial phrases for javascript functions
    public function initPhraseJs()
    {

        return $this->getView()->addPhraseJs(array(
            'btn_ok' => __("OK"),
            'message' => __('Message'),
            'btn_cancel' => __("Cancel"),
            'users' => __('users'),
            'btn_upload' => __("Upload a file"),
            'btn_retry' => __("Retry"),
            'failed_upload' => __("Upload failed"),
            'drag_zone' => __("Drag Photo Here"),
            'format_progress' => __("of"),
            'waiting_for_response' => __("Processing..."),
            'loading' => __("Loading..."),
            'warning' => __("Warning"),
            'comment_empty' => __("Comment can not empty"),
            'share_whats_new_can_not_empty' => __("Share whats new can not empty"),
            'please_login' => __("Please login to continue"),
        	'please_confirm' => __("Please confirm"),
            'please_confirm_your_email' => __("Please confirm your email address."),
            'your_account_is_pending_approval' => __("Your account is pending approval."),
            'confirm_title' => __("Please Confirm"),
            'send_email_progress' => __('Sending Emails Progress'),
            'fineupload_uploadbutton' => __('Upload a file'),
            'fineupload_cancel' => __('Cancel'),
            'fineupload_retry' => __('Retry'),
        	'fineupload_title_file' => __('Attach a photo'),
            'fineupload_failupload' => __('Upload failed'),
            'fineupload_dragzone' => __('Drop files here to upload'),
            'fineupload_dropprocessing' => __('Processing dropped files...'),
            'fineupload_formatprogress' => __('{percent}% of {total_size}'),
            'fineupload_waitingforresponse' => __('Processing...'),
            'confirm_delete_comment' => __('Are you sure you want to remove this comment?'),
            'confirm_login_as_user' => __('Are you sure you want to login as this user?'),
             'are_you_sure_leave_this_page' => __("The files are being uploaded, if you leave now the upload will be cancelled."),
            'processing_video' => __("Processing Video"),
            'processing_video_msg' => __("Your video is now being processed. We will send you a notification when it's ready to view"),
        ));
    }

    // Initialing the core css need to be loaded
    public function initCss($version = 1)
    {
        return $this->getView()->Helpers->Html->css(array(
                'sqllog.css',
                'common.css',
                'feed.css',
                'video.css',
                'blog.css',
                'event.css',
                'group.css',
                'photo.css',
                'topic.css',
                'button.css',
                'subscription.css',
                'main.css',
                'custom.css',
                'elastislide.css'

            )
        );
    }

    public function initCssBootstrap($version = 1)
    {
        return $this->getView()->Helpers->Html->css(array(
                'font-awesome/css/font-awesome.min.css',
                'bootstrap.3.2.0/css/bootstrap.min.css',
            )
        );
    }

    // Initialing the core javascripts need to be loaded
    public function initJss($version = 1)
    {
        $js = array(
            'global/jquery-1.11.1.min.js',
            'mooajax.js',
            'jquery.kinetic.min.js',
            //'scripts.js',
            //'vendor/jquery.elastic.js',
            'vendor/jquery.autogrow-textarea.js',
            'vendor/jquery.tipsy.js',
            'vendor/tinycon.min.js',
            'vendor/jquery.multiselect.js',
            'vendor/jquery.menubutton.js',
            'vendor/spin.js',
            'vendor/spin.custom.js',
            'vendor/jquery.placeholder.js',
            'vendor/jquery.simplemodal.js',
            'vendor/jquery.hideshare.js',
            'global.js',
            'moocore/ServerJS.js',
            'moocore/behavior.js',
            'notification.js',
			'photo_theater.js',
        	'photo.js',
            'elastislide/jquerypp.custom.js',
            'elastislide/modernizr.custom.17475.js',
            'elastislide/jquery.elastislide.js',
            
        );


        if ($this->getView()->ngController) {
            $js[] = 'global/angular.min.js';
            $js[] = 'global/angular-route.min.js';
            $js[] = 'global/angular-sanitize.min.js';
            $js[] = 'global/angular-lodash.compat.min.js';
            $js[] = 'global/angular-restangular.min.js';

            $js[] = 'angular/app.js?' . $version;
            $js[] = "angular/" . (empty($this->getView()->params['plugin']) ? "" : $this->getView()->params['plugin'] . "/") . $this->getView()->ngController . ".js";
        }
        $js = $this->getView()->Helpers->Html->script($js);
        return $js;

    }

    // Initialing the bootstrap logic javascripts need to be loaded
    public function initJssBootstrap($version = 1)
    {
        return $this->getView()->Helpers->Html->script(
            array(
                'global/bootstrap/js/bootstrap.min.js',
                'jquery.slimscroll.min.js',
                'responsive.js',
            )
        );
    }

    // Initialing the typehead and tag manager for user tagging
    public function initUserTagging()
    {
        $v = $this->getView();
        $v->Helpers->Html->css(array(
            'global/typehead/bootstrap-tagsinput.css',
        ),
            array('block' => 'css')
        );
        if(!$v->isEnableJS('Requirejs')){
            $v->Helpers->Html->script(
                array(
                    'global/typeahead/typeahead.bundle.js',
                    'global/typeahead/bootstrap-tagsinput.js',
                ),
                array('block' => 'mooScript')
            );
        }else{
            $v->Helpers->MooRequirejs->addPath(array(
                'typeahead'=>$v->Helpers->MooRequirejs->assetUrlJS('js/global/typeahead/typeahead.jquery.js'),
                'bloodhound'=>$v->Helpers->MooRequirejs->assetUrlJS('js/global/typeahead/bloodhound.min.js'),
                'tagsinput'=>$v->Helpers->MooRequirejs->assetUrlJS('js/global/typeahead/bootstrap-tagsinput.js'),
            ));
            $v->Helpers->MooRequirejs->addShim(array(
                //'typeahead'=>array("deps" =>array('jquery'),'exports'=> 'typeahead'),
                //'bloodhound'=>array("deps" =>array('jquery'),'exports'=> "bloodhound"),
                'tagsinput'=>array("deps" =>array('jquery','typeahead','bloodhound')),
            ));
            //$v->Helpers->MooRequirejs->addToFirst(array('typeahead','tagsinput'));
        }

    }

    // Initialing the typehead and mention manager for user mention
    public function initUserMention()
    {
        $v = $this->getView();
//        $v->Helpers->Html->css(array(
//                'global/typehead/bootstrap-tagsinput.css',
//            ),
//            array('block' => 'css')
//        );
        if(!$v->isEnableJS('Requirejs')){
            $v->Helpers->Html->script(
                array(
                    'global/typeahead/typeahead.bundle.js',
                    //'global/typeahead/bootstrap-tagsinput.js',
                    'global/jquery-textcomplete/jquery.textcomplete.js',
                ),
                array('block' => 'mooScript')
            );
        }else{
            $v->Helpers->MooRequirejs->addPath(array(
                    'typeahead'=>$v->Helpers->MooRequirejs->assetUrlJS('js/global/typeahead/typeahead.jquery.js'),
                    'bloodhound'=>$v->Helpers->MooRequirejs->assetUrlJS('js/global/typeahead/bloodhound.min.js'),
                    //'tagsinput'=>$v->Helpers->MooRequirejs->assetUrlJS('js/global/typeahead/bootstrap-tagsinput.js'),
                    'textcomplete'=>$v->Helpers->MooRequirejs->assetUrlJS('js/global/jquery-textcomplete/jquery.textcomplete.js'),
                ));
            $v->Helpers->MooRequirejs->addShim(array(
                    //'typeahead'=>array("deps" =>array('jquery'),'exports'=> 'typeahead'),
                    //'bloodhound'=>array("deps" =>array('jquery'),'exports'=> "bloodhound"),
                    //'tagsinput'=>array("deps" =>array('jquery','typeahead','bloodhound')),
                ));
            //$v->Helpers->MooRequirejs->addToFirst(array('typeahead','tagsinput'));
        }

    }

    public function initUserEmoji()
    {
        $v = $this->getView();
        if(!$v->isEnableJS('Requirejs')){
            $v->Helpers->Html->script(
                array(
                    'global/jquery-textcomplete/jquery.textcomplete.js',
                ),
                array('block' => 'mooScript')
            );
        }else{
            $v->Helpers->MooRequirejs->addPath(array(
                    'textcomplete'=>$v->Helpers->MooRequirejs->assetUrlJS('js/global/jquery-textcomplete/jquery.textcomplete.js'),
                ));
        }

    }

    public function initMentionOverLay()
    {
        $v = $this->getView();
        if(!$v->isEnableJS('Requirejs')){
            $v->Helpers->Html->script(
                array(
                    'global/jquery-overlay/jquery.overlay.js'
                ),
                array('block' => 'mooScript')
            );
        }else{
            $v->Helpers->MooRequirejs->addPath(array(
                    'overlay'=>$v->Helpers->MooRequirejs->assetUrlJS('js/global/jquery-overlay/jquery.overlay.js'),
                ));
        }
    }

    // Initialing the typehead and tag manager for user tagging
    public function initTagCloud()
    {
        $this->getView()->Helpers->Html->css(array(
                'jqcloud.css',
            ),
            array('block' => 'css')
        );
        $this->getView()->Helpers->Html->script(
            array(
                'jqcloud-1.0.4.min.js',
            ),
            array('block' => 'mooScript')
        );
    }
    public function initRequireJs(){
        return $this->getView()->Helpers->Html->script('moocore/require.js',
            //array(
            //    'moocore/global.js',
            //    'moocore/require.js',
            //),
            array('block' => 'mooScript')//,'data-main'=>Configure::read('App.jsBaseUrl').'main')
        );
    }
    public function isLoaded($name)
    {
        if (empty($this->libraryLoaded[$name])) return false;
        return $this->libraryLoaded[$name];
    }

    public function setLoaded($name)
    {
        $this->libraryLoaded[$name] = true;
    }

    public function removeLoaded($name)
    {
        $this->libraryLoaded[$name] = false;
    }
    public function renderPhraseJS(){
        if(!empty($this->getView()->phraseJs)){

                $this->getView()->Helpers->Html->scriptBlock(
                    "MooPhrase.set(".json_encode($this->getView()->phraseJs,true).")",
                    array(
                        'inline' => false,
                        'block' => 'mooPhrase'
                    )
                );

        }
    }
    public function loadRequireJs(){
        $version = Configure::read('core.version');
        $v = $this->getView();
        $this->initRequireJs();
        $css = $this->initCssBootstrap($version);
        $css .= $v->Helpers->Html->css(array(
                'fontello/css/fontello.css'
            )
        );
        $css .= $this->initCss($version);
        $v->prepend('css', $css);

        $v->Helpers->MooRequirejs->addPath(array(
            'jquery'=>$v->Helpers->MooRequirejs->assetUrlJS('js/global/jquery-1.11.1.min.js'),
            'bootstrap'=>$v->Helpers->MooRequirejs->assetUrlJS('js/global/bootstrap/js/bootstrap.min.js'),


            'server'=>$v->Helpers->MooRequirejs->assetUrlJS('js/moocore/ServerJS.js'),
            'moophrase'=>$v->Helpers->MooRequirejs->assetUrlJS('js/moophrase.js'),
            'multiselect'=>$v->Helpers->MooRequirejs->assetUrlJS('js/vendor/jquery.multiselect.js'),
            'hideshare'=>$v->Helpers->MooRequirejs->assetUrlJS('js/vendor/jquery.hideshare.js'),
            'simplemodal'=>$v->Helpers->MooRequirejs->assetUrlJS('js/vendor/jquery.simplemodal.js'),
            'spin'=>$v->Helpers->MooRequirejs->assetUrlJS('js/vendor/spin.js'),
            //'elastic'=>$v->Helpers->MooRequirejs->assetUrlJS('js/vendor/jquery.elastic.js'),
            'autogrow'=>$v->Helpers->MooRequirejs->assetUrlJS('js/vendor/jquery.autogrow-textarea.js'),
            'tipsy'=>$v->Helpers->MooRequirejs->assetUrlJS('js/vendor/jquery.tipsy.js'),
            'tinycon'=>$v->Helpers->MooRequirejs->assetUrlJS('js/vendor/tinycon.min.js'),
            'magnificPopup'=>$v->Helpers->MooRequirejs->assetUrlJS('js/jquery.mp.min.js'),
            //'global'=>$v->Helpers->MooRequirejs->assetUrlJS('js/moocore/global.js'),
            'notification'=>$v->Helpers->MooRequirejs->assetUrlJS('js/notification.js'),
            'notification'=>$v->Helpers->MooRequirejs->assetUrlJS('js/notification.js'),
            'tinymce'=>$v->Helpers->MooRequirejs->assetUrlJS('js/tinymce/tinymce.min.js'),
            'mooPhoto'=>$v->Helpers->MooRequirejs->assetUrlJS('js/photo.js'),
            'responsive'=>$v->Helpers->MooRequirejs->assetUrlJS('js/responsive.js'),
            'mooajax'=>$v->Helpers->MooRequirejs->assetUrlJS('js/mooajax.js'),

        ));
        $v->Helpers->MooRequirejs->addShim(array(
            'notification'=>array('exports'=>'MooNotification'),
            'global'=>array("deps" =>array(
                'jquery',
                'magnificPopup',
                'autogrow',
                'tipsy',
                'tinycon',
                'multiselect',
                'vendor/jquery.menubutton',
                'spin',
                'vendor/spin.custom',
                'vendor/jquery.placeholder',
                'simplemodal',
                'hideshare',
        )),
            'server'=>array("exports"=>'server'),

            'bootstrap'=>array("deps" =>array('jquery')),
            'autogrow'=>array("deps" =>array('jquery')),
            'magnificPopup'=>array("deps" =>array('jquery')),
            'tipsy'=>array("deps" =>array('jquery')),
            'jquery.slimscroll.min'=>array("deps" =>array('jquery')),

            'multiselect'=>array("deps" =>array('jquery')),
            'hideshare'=>array("deps" =>array('jquery')),
            'simplemodal'=>array("deps" =>array('jquery','moophrase')),
            'tinymce'=>array("deps" =>array('jquery')),
        ));
        $v->Helpers->MooRequirejs->addToFirst(array('jquery','bootstrap','moophrase','mooajax','notification','server' ));

        $v->Helpers->Html->scriptBlock( "requirejs.config({$v->Helpers->MooRequirejs->config()});require({$v->Helpers->MooRequirejs->first()}, function($){require(['server'],function(server){server.init();});});", array( 'inline' => false, 'block' => 'mooScript' ) );

        return true;
    }
}