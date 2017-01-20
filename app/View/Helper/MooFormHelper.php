<?php
App::uses('FormHelper', 'View/Helper');

class MooFormHelper extends FormHelper
{
    public $helpers = array('Html');
    private $isLoaded = array(
        'javascript'=>array(
            'autoGrow'=>false,
            'mention' => false,
            'emoji' => false,
        ),
        'mooInit'=>array(
            'autoGrow'=>false,
        )
    );
    private $_userTaggingScript = <<<javaScript
    $(function() {
        var friends_str_replace_userTagging = new Bloodhound({
                        datumTokenizer:function(d){
                            return Bloodhound.tokenizers.whitespace(d.name);
                        },
                        queryTokenizer: Bloodhound.tokenizers.whitespace,
                        prefetch: {
                            url: '#urlSuggestion',
                            cache: false,
                            filter: function(list) {
            
                                return $.map(list.data, function(obj) {
                                    return obj;
                                });
                            }
                        },
                        
                        identify: function(obj) { return obj.id; },
        });
        friends_str_replace_userTagging.initialize();

        $('#str_replace_userTagging').tagsinput({
            freeInput: false,
            itemValue: 'id',
            itemText: 'name',
            typeaheadjs: {
                name: 'friends_str_replace_userTagging',
                displayKey: 'name',
                //valueKey: 'name',
                highlight: true,
                limit:100,
                source: friends_str_replace_userTagging.ttAdapter(),
                templates:{
                    notFound:[
                        '<div class="empty-message">',
                            '#str_replace_typeadadjs_notFound',
                        '</div>'
                    ].join(' '),
                    suggestion: function(data){
                    if($('#userTagging').val() != '')
                    {
                        var ids = $('#str_replace_userTagging').val().split(',');
                        if(ids.indexOf(data.id) != -1 )
                        {
                            return '<div class="empty-message" style="display:none">#str_replace_typeadadjs_notFound</div>';
                        }
                    }
                        return [
                            '<div class="suggestion-item">',
                                '<img alt src="'+data.avatar+'"/>',
                                '<span class="text">'+data.name+'</span>',
                            '</div>',
                        ].join('')
                    }
                }
            }
        });
        $('#str_replace_container_userTagging_id').find('.tt-input').bind('typeahead:select', function(ev, suggestion) {
            console.log(suggestion);
        });
    });
javaScript;

    private $_tinyMCE= <<<javaScript
$(document).ready(function(){
    if(typeof window.orientation === 'undefined' || window.innerWidth > 600)
    {
        tinymce.init({
            selector: "#selector",
            theme: "#modern",
            skin: '#light',
            plugins: ["#plugins"],
            toolbar1: "#toolbar1",
            image_advtab: #image_advtab,
            directionality: "#site_rtl",
            width: #width,
            height: #height,
            menubar: #menubar,
            forced_root_block : '#forced_root_block',
            relative_urls : #relative_urls,
            remove_script_host : #remove_script_host,
            document_base_url : '#document_base_url',
        });
    }


});
javaScript;

    private $_editTexarea = <<<javaScript
    <script>alert('ho')</script>

javaScript;

    public function userTagging($friends="", $id = "userTagging" ,$hide_icon_add = false)
    {
        $this->_View->loadLibrary('userTagging');

        $urlSuggestion = $this->Html->url(array("controller"=>"users","action"=>"friends","plugin"=>false),true).".json";
        $jsReplace = str_replace('#urlSuggestion',$urlSuggestion,$this->_userTaggingScript);
        $jsReplace = str_replace('str_replace_userTagging',$id,$jsReplace);
        $jsReplace = str_replace('#str_replace_typeadadjs_notFound',__('unable to find any friend'),$jsReplace);
        $jsReplace = str_replace('#str_replace_container_userTagging_id','#userTagging-id-'.$id,$jsReplace);
        
        if($this->_View->isEnableJS('Requirejs')){
            $jsReplace = "require(['jquery','typeahead','bloodhound','tagsinput'], function($){".$jsReplace."});";
        }
        
        $this->_View->addInitJs($jsReplace);
        $out = $this->input('userTagging', array(
            'id' => $id,
            'value' => $friends,
            'type' => 'text',
            'label' => false,
            'placeholder'=>__('Who are you with ?'),
            'div' => array(
                'class' => 'user-tagging-container',
                'id' => 'userTagging-id-'.$id,
            ),
            'before'=>'<i '.($hide_icon_add ? 'style="display:none;"':'').' class="icon-user-add" onclick="$(this).parent().find(\'.userTagging-'.$id.'\').toggleClass(\'hidden\')"></i> <div class="userTagging-'.$id.' hidden">',
            'after' =>'</div>',
        ));
        return $out;
    }
    
    public function friendSuggestion($friends="", $id = "friendSuggestion"){
        $this->_View->loadLibrary('userTagging');

        $urlSuggestion = $this->Html->url(array("controller"=>"users","action"=>"friends","plugin"=>false),true).".json";
        $jsReplace = str_replace('#urlSuggestion',$urlSuggestion,$this->_userTaggingScript);
        $jsReplace = str_replace('str_replace_userTagging',$id,$jsReplace);
        $jsReplace = str_replace('#str_replace_typeadadjs_notFound',__('unable to find any friend'),$jsReplace);
        $jsReplace = str_replace('#str_replace_container_userTagging_id','#userTagging-id-'.$id,$jsReplace);
        
        if($this->_View->isEnableJS('Requirejs')){
            
            $jsReplace = "require(['jquery','typeahead','bloodhound','tagsinput'], function($){".$jsReplace."});";
        }
        $this->_View->addInitJs($jsReplace);
        $out = $this->input('friendSuggestion', array(
            'id' => $id,
            'value' => $friends,
            'type' => 'text',
            'label' => false,
            'placeholder'=>__('Friend\'s name ?'),
            'div' => array(
                'class' => 'user-tagging-container',
            ),
          //  'after' =>'</div>',
        ));
        return $out;
    }
    
    public function groupSuggestion($friends="", $id = "groupSuggestion"){
        $this->_View->loadLibrary('userTagging');

        $urlSuggestion = $this->Html->url(array(
            "controller" => "groups",
            "action" => "my_joined_group",
            "plugin" => 'group'
            ),true).".json";
        $jsReplace = str_replace('#urlSuggestion',$urlSuggestion,$this->_userTaggingScript);
        $jsReplace = str_replace('str_replace_userTagging',$id,$jsReplace);
        $jsReplace = str_replace('#str_replace_typeadadjs_notFound',__('unable to find any group'),$jsReplace);
        $jsReplace = str_replace('#str_replace_container_userTagging_id','#userTagging-id-'.$id,$jsReplace);
        
        if($this->_View->isEnableJS('Requirejs')){
            
            $jsReplace = "require(['jquery','typeahead','bloodhound','tagsinput'], function($){".$jsReplace."});";
        }
        $this->_View->addInitJs($jsReplace);
        $out = $this->input('groupSuggestion', array(
            'id' => $id,
            'value' => $friends,
            'type' => 'text',
            'label' => false,
            'placeholder'=>__('Group\'s name'),
            'div' => array(
                'class' => 'user-tagging-container',
            ),
         //   'after' =>'</div>',
        ));
        return $out;
    }

    public function tinyMCE($fieldName, $options = array()){
        $this->_View->loadJs(array('tinymce/tinymce.min.js'));
        if(empty($this->_View->viewVars['isMobile'])){
            $search = array(
                '#document_base_url',
                '#selector',
                '#modern',
                '#light',
                '#plugins',
                '#toolbar1',
                '#image_advtab',
                '#width',
                '#height',
                '#menubar',
                '#forced_root_block',
                '#relative_urls',
                '#remove_script_host',
                '#site_rtl'
            );
            $replace = array(
                FULL_BASE_URL . $this->_View->request->root,
                ((isset($options['id']) ? 'textarea#'.$options['id'] : 'textarea')),
                ((isset($options['modern']) ? $options['modern'] : 'modern')),
                ((isset($options['light']) ? $options['light'] : 'light')),
                ((isset($options['plugins']) ? $options['plugins'] : 'emoticons link image')),
                ((isset($options['toolbar1']) ? $options['toolbar1'] : 'bold italic underline strikethrough | bullist numlist | link unlink image emoticons blockquote')),
                ((isset($options['image_advtab']) ? $options['image_advtab'] : 'true')),
                ((isset($options['width']) ? $options['width'] : '580')),
                ((isset($options['height']) ? $options['height'] : '400')),
                ((isset($options['menubar']) ? $options['menubar'] : 'false')),
                ((isset($options['forced_root_block']) ? $options['forced_root_block'] : 'div')),
                ((isset($options['relative_urls']) ? $options['relative_urls'] : 'false')),
                ((isset($options['remove_script_host']) ? $options['remove_script_host'] : 'true')),
                ((!empty($this->_View->viewVars['site_rtl']) ? 'rtl' : 'ltr')),
            );

            $jsReplace = str_replace($search,$replace,$this->_tinyMCE);
            $this->_View->addInitJs($jsReplace);
        }

        return $this->textarea($fieldName, $options);
    }

    public function textarea1($fieldName, $options = array()) {
        // Feature 1 : This textarea is going to grow when you fill it with text. Just type a few more words in it and you will see.
        // --- Check feature 1 is enable
        // --- Register for including the script moocore/lib/jquery-elastic.js
        // --- Reigster for mooinit
        $autoGrow = isset($options['autoGrow']) ? $options['autoGrow'] : true;
        if(isset($options['class']) && strpos('no-grow',$options['class'])!== false)
            $autoGrow = false;
        if($autoGrow){
            // load script and make sure that  load it one
            if(!$this->isLoaded['javascript']['autoGrow']){
                $this->isLoaded['javascript']['autoGrow'] = true;
                $this->_View->Helpers->Html->script(
                    array('moocore/lib/jquery-elastic'), array('block' => 'mooScript')
                );
            }
            if(!$this->isLoaded['mooInit']['autoGrow']){
                $this->isLoaded['mooInit']['autoGrow'] = true;
                $this->_View->addInitJs('$(function() {$("textarea.autoGrow").autogrow();});');
            }
            if(!isset($options['class'])){
                $options['class'] = "autoGrow";
            }else{
                $options['class'] .= " autoGrow";
            }

        }
        return parent::textarea($fieldName,$options);
    }
    public function textarea($fieldName, $options = array(), $userMention = false, $userEmoji = true){

        $options = $this->_initInputField($fieldName, $options);
        $name = $options['name'];
        $textarea_id = !empty($options['id']) ? $options['id'] : $name;

        if($userMention){
            $this->_View->loadLibrary('mentionOverLay');

			if (!$this->request->is('mobile'))
			{				
				$this->_View->loadLibrary('userMention');

				if(!$this->isLoaded['javascript']['mention']){
					$this->isLoaded['javascript']['mention'] = true;
					$this->_View->requireJs('moocore/mention.js','var textAreaId = "'.$textarea_id.'"; var type = "activity"; mooMention.init(textAreaId,type);');
				}
			}
        }

        if ($userEmoji && $this->_View->theme != 'adm') {
            $this->_View->loadLibrary('userEmoji');

            if(!$this->isLoaded['javascript']['emoji']){
                $this->isLoaded['javascript']['emoji'] = true;
                $this->_View->requireJs('moocore/emoji.js','var textAreaId = "'.$textarea_id.'"; var type = "activity"; mooEmoji.init(textAreaId,type);');
            }
        }
        return parent::textarea($fieldName, $options);
    }
}