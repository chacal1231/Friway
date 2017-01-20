<?php

/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */
class BlogUploadController extends BlogAppController {

    public function beforeFilter() {
        parent::beforeFilter();
        $this->autoRender = false;
    }

    public function avatar() {
        $uid = $this->Auth->user('id');

        if (!$uid)
            return;

        // save this picture to album
        $path = 'uploads' . DS . 'tmp';
        $url = 'uploads/tmp/';

        $this->_prepareDir($path);

        $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');

        App::import('Vendor', 'qqFileUploader');
        $uploader = new qqFileUploader($allowedExtensions);

        // Call handleUpload() with the name of the folder, relative to PHP's getcwd()
        $result = $uploader->handleUpload($path);

        if (!empty($result['success'])) {
            App::import('Vendor', 'phpThumb', array('file' => 'phpThumb/ThumbLib.inc.php'));
            
            $result['thumb'] = FULL_BASE_URL . $this->request->webroot . $url . $result['filename'];
            $result['file'] = $path . DS . $result['filename'];
        }
        // to pass data through iframe you will need to encode all html tags
        echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
    }

    public function images() {
        $error = false;

        $uid = $this->Auth->user('id');

        if (!$uid) {
            return;
        }

        $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');

        App::import('Vendor', 'qqFileUploader');
        $uploader = new qqFileUploader($allowedExtensions);

        $path = 'uploads' . DS . 'tmp';

        $result = $uploader->handleUpload($path);

        if (!empty($result['success'])) {

            $this->loadModel('Photo.Photo');
            $this->Photo->create();
            $this->Photo->set(array(
                'target_id' => 0,
                'type' => 'Blog',
                'user_id' => $uid,
                'thumbnail' => $path . DS . $result['filename']
            ));
            $this->Photo->save();

            $photo = $this->Photo->read();

            $view = new View($this);
            $mooHelper = $view->loadHelper('Moo');
            $result['thumb'] = $mooHelper->getImageUrl($photo, array('prefix' => '450'));
            $result['large'] = $mooHelper->getImageUrl($photo, array('prefix' => '1500'));
        }

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