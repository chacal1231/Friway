<?php

/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */

class Hook extends AppModel 
{
    public $validate = array(   
                        'name' =>   array(   
                            'rule' => 'notBlank',
                            'message' => 'Name is required'
                        ),
                        'key' =>   array(   
                            'rule' => 'notBlank',
                            'message' => 'Key is required'
                        )                                      
    );
    
    public $order = 'Hook.weight asc';
    
    public function loadAll( $controller, $action, $role_id )
    {        
        $res = array();
        $cond = array( 'OR' => array( array( 'Hook.controller' => '' ),
                                      array( 'Hook.controller' => $controller, 'Hook.action' => $action )
                                    ),
                       'enabled' => 1
        );
        
        $hooks = $this->find('all', array( 'conditions' => $cond ) );

        foreach ( $hooks as $hook )
		{
			$permissions = explode(',', $hook['Hook']['permission']);
			
			if ( $hook['Hook']['permission'] === '' || in_array( strval($role_id), $permissions, true ) )
            	$res[$hook['Hook']['position']][] = $hook;	
		}
        
        return $res;
    }
}
