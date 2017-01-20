<?php
App::uses('AppController', 'Controller');
class MooCore
{
   	private $_subject = null;
   	private $_models = array();
	private $_items = array();
	private $_component = array();
	private $_helpers = array();	
	private $_viewer = null;
	private $_moo_view = null;
	private $_plugins = null;
	
	public function getMooView()
	{
		if ($this->_moo_view === null)
		{
			$this->_moo_view = new MooView(new AppController());
		}
		return $this->_moo_view;
	}
   	
	public static function getInstance()
    {
        static $instance = null;
        if (null === $instance) {
            $instance = new MooCore();
        }

        return $instance;
    }
   	
	public function getSubject()
    {
    	return $this->_subject;
    }
    
    public function setSubject($subject)
    {
    	$this->_subject = $subject;
    }
    
    public function getSubjectType()
    {
    	$subject = $this->getSubject();
    	if ($subject)
    	{
    		return key($subject);
    	}
    	return null;
    }
    
    public function getViewer($idOnly = false)
    {
        if(empty($this->_viewer)) return false;
        if(empty($this->_viewer['User']['id'])) return false;
        if($idOnly) return $this->_viewer['User']['id'];
    	return $this->_viewer;
    }
    
    public function setViewer($user)
    {
    	$this->_viewer = $user;
    }
    
	public function getModel($type)
    {
    	if (!isset($this->_models[$type]))
    	{
	    	list($plugin, $modelClass) = mooPluginSplit($type, true);
	
			$model = ClassRegistry::init(array(
				'class' => $plugin . $modelClass,
			));
			
			$this->_models[$type] = $model;
    	}
    	else
    	{
    		$model = $this->_models[$type];
    	}
    	return $model;
    }
    
    public function getComponent($key,$setting = array())
    {
    	if (!isset($this->_component[$key]))
    	{
	    	list($plugin, $name) = pluginSplit($key, true);
			$componentClass = $name . 'Component';
			App::uses($componentClass, $plugin . 'Controller/Component');
			
			$component = new $componentClass(new ComponentCollection(),$setting);
			$this->_component[$key] = $component;
    	}
    	else 
    	{
    		$component = $this->_component[$key];
    	}
    	
    	return $component;
    }
    
    public function getHelper($plugin,$settings = array())
    {
        $helper = null;
        $array  = explode('_', $plugin);
        $plugin_name = $array[0];
        $helper_name = $array[1];

        if (!isset($this->_helpers[$helper_name])) {
            $plugin_helper_name = $helper_name . 'Helper';
            if ($plugin_name == 'Core'){
                App::uses($plugin_helper_name, 'View/Helper');
            }else{
                App::uses($plugin_helper_name, $plugin_name . '.View/Helper');
            }
            if (class_exists($plugin_helper_name)) {
                $helper = new $plugin_helper_name($this->getMooView(), $settings);
                $this->_helpers[$helper_name] = $helper;
            }
        } else {
            $helper = $this->_helpers[$helper_name];
        }
        
        return $helper;
    }
    
	public function getItemByType($type,$id)
    {
    	$model = $this->getModel($type);
    	
    	if (!isset($this->_items[$type][$id]))
    	{
    		$object = $model->findById($id);
    	}
    	else
    	{
    		$object = $this->_items[$type][$id];
    	}
		
		
		return $object;
    }
    
    public function checkShowSubjectActivity($subject)
    {
    	$name = key($subject);
		$show_subject = true;
	    $subject_view = $this->getSubject();
	    if ($subject_view)
	    {
	    	$type = MooCore::getInstance()->getSubjectType();
	    	$show_subject = ($subject[$name]['moo_type'] !=$subject_view[$type]['moo_type']) || ($subject[$name]['id'] != $subject_view[$type]['id']);
	    }
	    
	    return $show_subject;
    }
    
    public function getListPluginEnable()
    {
    	if ($this->_plugins === null)
    	{
	    	$plugins = CakePlugin::loaded();
	    	$tmp = array();
	    	foreach ($plugins as $plugin)
	    	{
	    		$helper = $this->getHelper($plugin.'_'.$plugin);
	    		if ($helper)
	    		{
	    			if (method_exists($helper,'getEnable'))
	    			{
	    				$enable = $helper->getEnable();
	    				if (!$enable)
	    				{
	    					continue;
	    				}
	    			}    			
	    		}
	    		$tmp[] = $plugin;
	    	}
	    	$this->_plugins = $tmp;
    	}
    	
    	return $this->_plugins;
    }
    
    public function exportTranslate($array_message,$path)
    {
    	$output = "# LANGUAGE translation of CakePHP Application\n";
		$output .= "# Copyright YEAR NAME <EMAIL@ADDRESS>\n";
		$output .= "#\n";
		$output .= "#, fuzzy\n";
		$output .= "msgid \"\"\n";
		$output .= "msgstr \"\"\n";
		$output .= "\"Project-Id-Version: PROJECT VERSION\\n\"\n";
		$output .= "\"PO-Revision-Date: YYYY-mm-DD HH:MM+ZZZZ\\n\"\n";
		$output .= "\"Last-Translator: NAME <EMAIL@ADDRESS>\\n\"\n";
		$output .= "\"Language-Team: LANGUAGE <EMAIL@ADDRESS>\\n\"\n";
		$output .= "\"MIME-Version: 1.0\\n\"\n";
		$output .= "\"Content-Type: text/plain; charset=utf-8\\n\"\n";
		$output .= "\"Content-Transfer-Encoding: 8bit\\n\"\n";

		$tmp = array();
    	foreach ($array_message as $message)
    	{
    		$sentence = '';
    		$sentence .= "msgid \"{$message}\"\n";
			$sentence .= "msgstr \"{$message}\"\n\n";
			$tmp[] = $sentence;
    	}
    	
    	$array_message = $tmp;
    	
    	foreach ($array_message as $header) {
			$output .= $header;
		}
		
		$File = new File($path);
		$File->write($output);
		$File->close();
    }
    
    
    // return full content
    public function getHtmlContent($url) {
        if (filter_var($url, FILTER_VALIDATE_URL) === false){
            return false;
        }
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        $data = curl_exec($ch);
        curl_close($ch);
        
        return $data;
    }
    
    // get header
    public function getHeader($url){
        if (filter_var($url, FILTER_VALIDATE_URL) === false){
            return false;
        }
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        $content = curl_exec($ch);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        $contentLength = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
        curl_close($ch);
        
        return array(
            'contentType' => $contentType,
            'contentLength' => $contentLength
        );
    }
    
    public function isRecaptchaEnabled(){
        $recaptcha_publickey = Configure::read('core.recaptcha_publickey');
        $recaptcha_privatekey = Configure::read('core.recaptcha_privatekey');
        
        if ( Configure::read('core.recaptcha') && !empty($recaptcha_publickey) && !empty($recaptcha_privatekey) ){
            return true;
        }
        
        return false;
    }
}