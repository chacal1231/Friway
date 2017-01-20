<?php

/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */
App::uses('AppController', 'Controller');
App::uses('BlogHelper', 'View/Helper');

class BlogAppController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
    }

}
