<?php
class PaymentGatewayCore extends Object
{
	protected $_request;
	protected $_setting = null;
	protected $_plugin = '';

	public function __construct($setting = null)
	{
		if ($setting)
		{
			$this->_setting = $setting;
		}
		else
		{
			if ($this->_plugin)
			{
				$helper = MooCore::getInstance()->getHelper($this->_plugin . '_' . $this->_plugin);
				if (method_exists($helper, 'getSetting'))
					$this->_setting = $helper->getSetting();
			}
		}
		$this->_request = Router::getRequest();
	}
} 
?>