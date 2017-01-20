<?php

/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */

class Role extends AppModel {
    
    public $validate = array(   
                        'name' =>   array(   
                            'rule' => 'notBlank',
                            'message' => 'Name is required'
                        )                                  
    );
    
}