<?php

class AuthHelper extends AppHelper {

    public function user($field){
        return (empty($this->_View->viewVars['cuser'])) ? null:$this->_View->viewVars['cuser'][$field];
    }
}

?>
