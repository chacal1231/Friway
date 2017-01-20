<?php
class RatingUser extends AppModel{
    public $belongsTo = array(
        'Rating' => array(
            'className' => 'Rating',
            'counterCache' => true,
        )
    );
}