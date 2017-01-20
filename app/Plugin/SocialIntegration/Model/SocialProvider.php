<?php

/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */
class SocialProvider extends SocialIntegrationAppModel {

    public $validate = array(
        'client_api' => array(
            'notEmpty' => array(
                'rule' => array('notBlank'),
                'message' => 'Provider Api must not be empty',
            ),
        ),
        'client_secret' => array(
            'notEmpty' => array(
                'rule' => array('notBlank'),
                'message' => 'Provider Secret must not be empty',
            ),
        ),
    );

    public function isIdExist($id) {
        return $this->hasAny(array('id' => $id));
    }

}
