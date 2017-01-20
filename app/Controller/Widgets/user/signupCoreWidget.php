<?php
App::uses('Widget','Controller/Widgets');

class signupCoreWidget extends Widget {
    public function beforeRender(Controller $controller) {
        // load spam challenge if enabled
        if ( Configure::read('core.enable_spam_challenge') )
        {
            $controller->loadModel('SpamChallenge');
            $challenges = $controller->SpamChallenge->findAllByActive(1);

            if ( !empty( $challenges ) )
            {
                $rand = array_rand( $challenges );

                $controller->Session->write('spam_challenge_id', $challenges[$rand]['SpamChallenge']['id']);
                $controller->set('challenge', $challenges[$rand]);
            }
        }
    }
}