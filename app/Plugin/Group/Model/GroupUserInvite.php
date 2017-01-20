<?php

/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */
App::uses('GroupAppModel', 'Group.Model');
class GroupUserInvite extends GroupAppModel {

    public $belongsTo = array(
        'Group' => array(
            'className'=> 'Group.Group',
        ));

}
