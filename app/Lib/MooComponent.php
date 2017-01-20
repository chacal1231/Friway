<?php
class MooComponent
{
   	protected static $_values = array();
   	
   	public static function register($key,$settings = array())
   	{
   		self::$_values[$key] = $settings;
   	}
   	
   	public static function getAll()
   	{
   		return self::$_values;
   	}
}