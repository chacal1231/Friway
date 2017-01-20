<?php
App::uses('Widget','Controller/Widgets');

class Home_activityCoreWidget extends Widget {
    public function beforeRender(Controller $controller) {
        $controller->set('homeActivityWidgetParams',$controller->Feeds->get());

    }
}