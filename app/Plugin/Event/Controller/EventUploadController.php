<?php

class EventUploadController extends EventAppController {

    public function beforeFilter() {
        parent::beforeFilter();
        $this->autoRender = false;
    }

    public function avatar() {
        $this->loadModel('Event.Event');
        $uid = $this->Auth->user('id');

        if (!$uid)
            return;

        $this->loadModel('Event.Event');

        $path = 'uploads' . DS . 'tmp';
        $url = 'uploads/tmp/';

        $this->_prepareDir($path);

        $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');

        App::import('Vendor', 'qqFileUploader');
        $uploader = new qqFileUploader($allowedExtensions);

        // Call handleUpload() with the name of the folder, relative to PHP's getcwd()
        $result = $uploader->handleUpload($path);

        if (!empty($result['success'])) {
            // resize image
            App::import('Vendor', 'phpThumb', array('file' => 'phpThumb/ThumbLib.inc.php'));

            $result['thumb'] = FULL_BASE_URL . $this->request->webroot . $url . $result['filename'];
            $result['file_path'] = $path . DS . $result['filename'];
        }

        // to pass data through iframe you will need to encode all html tags
        echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
    }

    public function _getExtension($filename = null) {
        $tmp = explode('.', $filename);
        $re = array_pop($tmp);
        return $re;
    }

    private function _prepareDir($path) {
        $path = WWW_ROOT . $path;

        if (!file_exists($path)) {
            mkdir($path, 0755, true);
            file_put_contents($path . DS . 'index.html', '');
        }
    }

}

?>