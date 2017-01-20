<?php
App::uses('AbstractPasswordHasher', 'Controller/Component/Auth');

class MooPasswordHasher extends AbstractPasswordHasher {
    public function hash($password) {
        return md5( trim( $password ) . Configure::read('Security.salt') ) ;
    }

    public function check($password, $hashedPassword) {
        return $hashedPassword === $this->hash($password);
    }
}