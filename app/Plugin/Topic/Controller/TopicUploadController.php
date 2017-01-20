<?php

class TopicUploadController extends TopicAppController {

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

            $photo = PhpThumbFactory::create($path . DS . $result['filename']);

            $result['thumb'] = FULL_BASE_URL . $this->request->webroot . $url . $result['filename'];
            $result['file_path'] = $path . DS . $result['filename'];
        }
        // to pass data through iframe you will need to encode all html tags
        echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
    }

    public function attachments($plugin_id, $target_id = 0) {
        $uid = $this->Auth->user('id');

        if (!$plugin_id || !$uid) {
            return;
        }

        $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif', 'zip', 'txt', 'pdf', 'doc', 'docx');

        App::import('Vendor', 'qqFileUploader');
        $uploader = new qqFileUploader($allowedExtensions);

        // Call handleUpload() with the name of the folder, relative to PHP's getcwd()
        $path = 'uploads' . DS . 'attachments';
        $url = 'uploads/attachments';

        $original_filename = $this->request->query['qqfile'];
        $ext = $this->_getExtension($original_filename);

        $result = $uploader->handleUpload($path);

        if (!empty($result['success'])) {
            if (in_array(strtolower($ext), array('jpg', 'jpeg', 'png', 'gif'))) {
                
                $this->loadModel('Photo.Photo');

                $this->Photo->create();
                $this->Photo->set(array(
                    'target_id' => 0,
                    'type' => 'Topic',
                    'user_id' => $uid,
                    'thumbnail' => $path . DS . $result['filename']
                ));
                $this->Photo->save();

                $photo = $this->Photo->read();

                $view = new View($this);
                $mooHelper = $view->loadHelper('Moo');
                $result['thumb'] = $mooHelper->getImageUrl($photo, array('prefix' => '450'));
                $result['large'] = $mooHelper->getImageUrl($photo, array('prefix' => '1500'));
                $result['attachment_id'] = 0;
            }else {
                // save to db
                $this->loadModel('Attachment');
                $this->Attachment->create();
                $this->Attachment->set(array('user_id' => $uid,
                    'target_id' => $target_id,
                    'plugin_id' => $plugin_id,
                    'filename' => $result['filename'],
                    'original_filename' => $original_filename,
                    'extension' => $ext
                ));
                $this->Attachment->save();

                $result['attachment_id'] = $this->Attachment->id;
                $result['original_filename'] = $original_filename;
            }
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