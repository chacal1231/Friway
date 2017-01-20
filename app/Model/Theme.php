<?php

/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */

class Theme extends AppModel 
{
    //public $tablePrefix = 'ms_';
    public $hasMany = array(
        'MyCorePage'=>array(
            'className' => 'CorePage',
            'foreignKey' => 'theme_id',
            //'dependent' => true,
        )
    );

	public $validate = array(	
						'name' => 	array( 	 
							'rule' => 'notBlank',
							'message' => 'Name is required'
						),
						'key' => 	array( 	 
							'key' => array(
								  'rule' => 'alphaNumeric',
								  'allowEmpty' => false,
								  'message' => 'Key must only contain letters and numbers'
							),
							'uniqueKey' => array(
								  'rule' => 'isUnique',
								  'message' => 'Key already exists'
						    )
						)											
	);
	
	public function getThemes()
	{
		$site_themes = Cache::read('site_themes');
        
        if ( empty($site_themes) ) 
        {
            $site_themes = $this->find('list', array( 'fields' => array( 'Theme.key', 'Theme.name' ) ) );
            Cache::write('site_themes', $site_themes);
        }
		
		return $site_themes;
	}

}
