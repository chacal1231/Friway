<?php
class CoreComponent extends AppModel{
    public $validate = array(
        'name' => 	array(
            'rule' => 'notBlank',
            'message' => 'Name is required'
        ),
        'path' => 	array(
            'path' => array(
                'rule' => 'notBlank',
                'message' => 'Path is required'
            )

        )
    );
}