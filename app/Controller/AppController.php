<?php

/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */

App::uses('Controller', 'Controller');
App::uses('String', 'Utility');
App::uses('WidgetCollection', 'Lib');

class AppController extends Controller
{
    public $components = array(
        'Auth' => array(
            'loginRedirect' => array(
                'controller' => 'home',
                'action' => 'index'
            ),
            'logoutRedirect' => array(
                'controller' => 'users',
                'action' => 'member_login',

            ),
            'loginAction' => array(
                'controller' => 'users',
                'action' => 'member_login',

            ),
            'authenticate' => array(
                'Form' => array(
                    'fields' => array('username' => 'email'),
                    'contain' => 'Role',
                    'passwordHasher' => array(
                        'className' => 'Moo',
                    )
                )
            ),
            'authorize' => array(
                'Actions' => array('actionPath' => 'controllers')
            )
        ),
        'Cookie',
        'Session',
        'RequestHandler',
        'Feeds'
    );
    public $helpers = array(
        'Html' => array('className' => 'MooHtml'),
        'Text',
        'Form' => array('className' => 'MooForm'),
        'Session',
        'Time' => array('className' => 'AppTime'),
        'Moo',
        'Menu.Menu',
        'MooGMap',
        'Text' => array('className' => 'MooText'),
        'MooPeople',
        'MooPhoto',
        'MooTime',
        'MooActivity',
        'MooPopup',
        'MooRequirejs'
    );
    public $viewClass = 'Moo';
    public $check_subscription = true;

    /*
    * Initialize the system
    */
    public function __construct($request = null, $response = null)
    {
        if (!empty($request)) {
            $request->addDetector('api', array('callback' => array($this, 'isApi')));
            $request->addDetector('androidApp', array('callback' => array($this, 'isAndroidApp')));
            $request->addDetector('iosApp', array('callback' => array($this, 'isIOSApp')));
			$request->addDetector('mobile', array('callback' => array($this, 'isMobile')));
        }
        parent::__construct($request, $response);
        $this->Widgets = new WidgetCollection($this);

    }

    public function isApi($request)
    {
        if (isset($request['ext']) && $request['ext'] == "json" && strpos($request->url, 'api') !== false) {
            return true;
        }
        return false;
    }

    public function isAndroidApp($request)
    {
        if (strpos($this->request->header('User-Agent'), 'mooAndroid/1.0') !== false ||
            strpos($this->request->header('User-Agent'), 'Crosswalk/') !== false
        ) {
            return true;
        }
        return false;
    }

    public function isIOSApp($request)
    {
        if (strpos($this->request->header('User-Agent'), 'mooIOS/1.0') !== false ) {
            return true;
        }
        return false;
    }
	public function isMobile($request)
	{
		$useragent = $_SERVER['HTTP_USER_AGENT'];

		if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4)))
		{
			return true;
		}
		
		return false;
	}

    public function initialize()
    {
        echo "initialize";
    }

    public function beforeFilter()
    {
        $this->Auth->allow();
        if ($this->request->is('requested')) {
            // MOOSOCIAL-1366 - Duplicate query on landing page
            // Do nothing
            return;
        }

        //Todo Refactor
        // 1. Loading Application Settings process
        // 2. Identifying viewer process
        // 2.1 Loading viewer settings process
        // 3. Executing ban process
        // 4. Executing theme process
        // 5. Executing viewer process
        $this->loadingApplicationSettings();
        $this->identifyingViewer();
        $this->doBanUsersProcess();
        $this->doThemeProcess();
        $this->doViewerProcess();


    }

    private function loadComponent()
    {
        $components = MooComponent::getAll();
        if (count($components)) {
            foreach ($components as $class => $settings) {
                list($plugin, $name) = pluginSplit($class);
                $this->{$name} = $this->Components->load($class, $settings);
            }
        }
    }

    private function loadUnBootSetting()
    {
        $this->loadModel('Setting');
        Configure::write('core.prefix', $this->Setting->tablePrefix);

        $settingDatas = Cache::read('site.settings');
        if (!$settingDatas) {
            $this->loadModel('SettingGroup');

            //load all unboot setting
            $settings = $this->Setting->find('all', array(
                'conditions' => array('is_boot' => 0),
            ));
            //parse setting value
            $settingDatas = array();
            if ($settings != null) {
                foreach ($settings as $k => $setting) {
                    //parse value
                    $value = $setting['Setting']['value_actual'];
                    switch ($setting['Setting']['type_id']) {
                        case 'radio':
                        case 'select':
                            $value = '';
                            $multiValues = json_decode($setting['Setting']['value_actual'], true);
                            if ($multiValues != null) {
                                foreach ($multiValues as $multiValue) {
                                    if ($multiValue['select'] == 1) {
                                        $value = $multiValue['value'];
                                    }
                                }
                            }
                            break;
                        case 'checkbox':
                            $value = '';
                            $multiValues = json_decode($setting['Setting']['value_actual'], true);
                            if ($multiValues != null) {
                                foreach ($multiValues as $multiValue) {
                                    if ($multiValue['select'] == 1) {
                                        $value[] = $multiValue['value'];
                                    }
                                }
                                if (is_array($value) && count($value) == 1) {
                                    $value = $value[0];
                                }
                            }
                            break;
                    }

                    //parse module
                    $data['module_id'] = $setting['SettingGroup']['module_id'];
                    $data['name'] = $setting['Setting']['name'];
                    $data['value'] = $value;
                    $settingDatas[] = $data;
                }
            }
            Cache::write('site.settings', $settingDatas);
        }

        if ($settingDatas != null) {
            foreach ($settingDatas as $setting) {
                Configure::write($setting['module_id'] . '.' . $setting['name'], $setting['value']);
            }
        }
    }

    /**
     * Get the current logged in user
     * @return array
     */
    public function _getUser() {
        // Hacking MOOSOCIAL-2298, cache issue of Auth Component
        $uid = $this->Auth->user('id');
        $cuser = array();
        if (!empty($uid)) { // logged in users

            $this->loadModel('User');
            $this->User->cacheQueries = true;

            $user = $this->User->findById($uid);

            $cuser = $user['User'];
            $cuser['Role'] = $user['Role'];
        }
        
        return $cuser;
    }

    /**
     * Get the current logged in user's role id
     * @return int
     */
    protected function _getUserRoleId()
    {
        $cuser = $this->_getUser();
        $role_id = (empty($cuser)) ? ROLE_GUEST : $cuser['role_id'];

        return $role_id;
    }

    /**
     * Get the current logged in user's role params
     * @return array
     */
    public function _getUserRoleParams()
    {
        $cuser = $this->_getUser();

        if (!empty($cuser)) {
            $params = explode(',', $cuser['Role']['params']);
        } else {
            $params = Cache::read('guest_role');

            if (empty($params)) {
                $this->loadModel('Role');
                $guest_role = $this->Role->findById(ROLE_GUEST);

                $params = explode(',', $guest_role['Role']['params']);
                Cache::write('guest_role', $params);
            }
        }

        return $params;
    }

    /**
     * Get global site settings
     * @return array
     */
    public function _getSettings()
    {
        $this->loadModel('Setting');
        $this->Setting->cacheQueries = true;

        $settings = $this->Setting->find('list', array('fields' => array('field', 'value')));

        return $settings;
    }

    /**
     * Check if user has permission to view page
     * @param array $options - array( 'roles' => array of role id to check
     *                                  'confirm' => boolean to check email confirmation
     *                                  'admins' => array of user id to check ownership
     *                                  'admin' => boolean to check if logged in user is admin
     *                                  'super_admin' => boolean to check if logged in user is super admin
     *                                'aco' => string of aco to check against user's role
     *                                 )
     */
    protected function _checkPermission($options = array())
    {
        $viewer = MooCore::getInstance()->getViewer();
        if (!empty($viewer) && $viewer['Role']['is_admin']) {
            return true;
        }

        $cuser = $this->_getUser();
        $authorized = true;
        $hash = '';
        $return_url = '/return_url:' . base64_encode($this->request->here);

        //check normal subscription
        $this->options = $options;
        //$this->getEventManager()->dispatch(new CakeEvent('AppController.validNormalSubscription', $this));

        // check aco
        if (!empty($options['aco'])) {
            $acos = $this->_getUserRoleParams();

            if (!in_array($options['aco'], $acos)) {
                $authorized = false;
                $msg = __('Access denied');
            }
        } else {
            // check login
            if (!$cuser) {
                $authorized = false;
                $msg = __('Please login or register');
            } else {
                // check role
                if (!empty($options['roles']) && !in_array($cuser['role_id'], $options['roles'])) {
                    $authorized = false;
                    $msg = __('Access denied');
                }

                // check admin
                if (!empty($options['admin']) && !$cuser['Role']['is_admin']) {
                    $authorized = false;
                    $msg = __('Access denied');
                }

                // check super admin
                if (!empty($options['super_admin']) && !$cuser['Role']['is_super']) {
                    $authorized = false;
                    $msg = __('Access denied');
                }


                // check approval
                if (Configure::read('core.approve_users') && !$cuser['approved']) {
                    $authorized = false;
                    $msg = __('Your account is pending approval.');
                }

                // check confirmation
                if (Configure::read('core.email_validation') && !empty($options['confirm']) && !$cuser['confirmed']) {
                    $authorized = false;
                    $msg = __('You have not confirmed your email address! Check your email (including junk folder) and click on the validation link to validate your email address');
                }

                // check owner
                if (!empty($options['admins']) && !in_array($cuser['id'],
                        $options['admins']) && !$cuser['Role']['is_admin']
                ) {
                    $authorized = false;
                    $msg = __('Access denied');
                }
            }
        }

        if (!$authorized) {
            if (empty($this->layout)) {
                $this->autoRender = false;
                echo $msg;
            } else {
                if ($this->request->is('ajax')) {
                    $this->set(compact('msg'));
                    echo $this->render('/Elements/error');

                } else {
                    if (!empty($msg)) {
                        $this->Session->setFlash($msg, 'default', array('class' => 'error-message'));
                    }

                    $this->redirect('/pages/no-permission' . $return_url);
                }
            }
            exit;
        }
    }

    /**
     * Check if an item exists
     * @param mixed $item - array or object to check
     */
    protected function _checkExistence($item = null)
    { 
        if (!empty($item['User']) && empty($item['User']['active'])){
            $this->_showError(__('Item does not exist'));
            return;
        }
        
        if (empty($item)) {
            $this->_showError(__('Item does not exist'));
            return;
        }
    }

    protected function _showError($msg)
    {
        $this->Session->setFlash($msg, 'default', array('class' => 'error-message'));
        $this->redirect('/pages/error');
        return;
    }

    protected function _jsonError($msg)
    {
        $this->autoRender = false;

        $response['result'] = 0;
        $response['message'] = $msg;

        echo json_encode($response);
        return;
    }

    /**
     * Validate submitted data
     * @param object $model - Cake model
     */
    protected function _validateData($model = null)
    {
        if (!$model->validates()) {
            $errors = $model->invalidFields();

            $response['result'] = 0;
            $response['message'] = current(current($errors));

            echo json_encode($response);
            exit;
        }
    }

    /**
     * Check if current user is allowed to view item
     * @param string $privacy - privacy setting
     * @param int $owner - user if of the item owner
     * @param boolean $areFriends - current user and owner are friends or not
     */
    protected function _checkPrivacy($privacy, $owner, $areFriends = null)
    {
        $uid = $this->Auth->user('id');
        if ($uid == $owner) // owner
        {
            return;
        }

        $viewer = MooCore::getInstance()->getViewer();
        if (!empty($viewer) && $viewer['Role']['is_admin']) {
            return true;
        }

        switch ($privacy) {
            case PRIVACY_FRIENDS:
                if (empty($areFriends)) {
                    $areFriends = false;

                    if (!empty($uid)) //  check if user is a friend
                    {
                        $this->loadModel('Friend');
                        $areFriends = $this->Friend->areFriends($uid, $owner);
                    }
                }

                if (!$areFriends) {
                    $this->Session->setFlash(__('Only friends of the poster can view this item'), 'default',
                        array('class' => 'error-message'));
                    $this->redirect('/pages/no-permission');
                }

                break;

            case PRIVACY_ME:
                $this->Session->setFlash(__('Only the poster can view this item'), 'default',
                    array('class' => 'error-message'));
                $this->redirect('/pages/no-permission');
                break;
        }
    }

    /**
     * Log the user in
     * @param string $email - user's email
     * @param string $password - user's password
     * @param boolean $remember - remember user or not
     * @return uid if successful, false otherwise
     */
    protected function _logMeIn($email, $password, $remember = false)
    {
        if (!is_string($email) || !is_string($password)) {
            return false;
        }

        $this->loadModel('User');

        // find the user
        $user = $this->User->find('first', array('conditions' => array('email' => trim($email))));

        if (!empty($user)) // found
        {
            if ($user['User']['password'] != md5(trim($password) . Configure::read('Security.salt'))) // wrong password
            {
                return false;
            }

            if (!$user['User']['active']) {
                
                $this->Session->setFlash(__('This account has been disabled'), 'default',
                    array('class' => 'error-message'));
                $this->logout();
                return $this->redirect($this->Auth->logout());
            } else {
                // save user id and user data in session
                //$this->Session->write('uid', $user['User']['id']);


                // handle cookies
                if ($remember) {
                    $this->Cookie->write('email', $email, true, 60 * 60 * 24 * 30);
                    $this->Cookie->write('password', $password, true, 60 * 60 * 24 * 30);
                }

                //renew allow cookie
                $accepted_cookie = $this->Cookie->read('accepted_cookie');
                if($accepted_cookie)
                    $this->Cookie->write('accepted_cookie',1,true,60*60*24*30);

                // update last login
                $this->User->id = $user['User']['id'];
                $this->User->save(array('last_login' => date("Y-m-d H:i:s")));
                return $user['User']['id'];
            }
        } else {
            return false;
        }
    }
    
    protected function logout() {
        // delete session from database
        $current_session = $this->Session->id();
        $this->loadModel('CakeSession');
        $this->CakeSession->delete($current_session);

        // Process provider logout
        $this->Social = $this->Components->load('SocialIntegration.Social');
        if ($this->Session->read('provider')) {
            $this->Social->socialLogout($this->Session->read('provider'));
            SocialIntegration_Auth::storage()->set("hauth_session.{$this->Session->read('provider')}.is_logged_in", 0);
            $this->Session->delete('provider');
        }

        // clean the sessions
        $this->Session->delete('uid');
        $this->Session->delete('admin_login');
        $this->Session->delete('Message.confirm_remind');

        // delete cookies
        $this->Cookie->delete('email');
        $this->Cookie->delete('password');
    }

    private function _runCron()
    {


    }

    /**
     * System wide send email method
     * @param string $to - recipient's email address
     * @param string $subject
     * @param string $template - email template to use
     * @param array $vars - array of vars to set in email
     * @param string $from_email - sender's email address
     */
    protected function _sendEmail($to, $subject, $template, $vars, $from_email = '', $from_name = '', $body = '')
    {
        App::uses('CakeEmail', 'Network/Email');

        $vars['request'] = $this->request;

        if (empty($from_email)) {
            $from_email = Configure::read('core.site_email');
        }

        if (empty($from_name)) {
            $from_name = Configure::read('core.site_name');
        }

        $email = new CakeEmail();
        $email->from($from_email, $from_name)
            ->to($to)
            ->subject($subject)
            ->template($template)
            ->viewVars($vars)
            ->helpers(array('Moo'))
            ->emailFormat('html')
            ->transport(Configure::read('core.mail_transport'));

        if (Configure::read('core.mail_transport') == 'Smtp') {
            $config = array('host' => Configure::read('core.smtp_host'), 'timeout' => 30);
            $smtp_username = Configure::read('core.smtp_username');
            $smtp_password = Configure::read('core.smtp_password');
            $smtp_port = Configure::read('core.smtp_port');
            if (!empty($smtp_username) && !empty($smtp_password)) {
                $config['username'] = $smtp_username;
                $config['password'] = $smtp_password;
            }

            if (!empty($smtp_port)) {
                $config['port'] = $smtp_port;
            }

            $email->config($config);
        }
        try {
            $email->send($body);
        } catch (Exception $ex) {
            $ret_msg = $ex->getMessage();
            $this->log($ex->getLine(), 'emailError');
        }

    }

    private function _getLocales($lang)
    {
        // Loading the L10n object
        App::uses('L10n', 'I18n');
        $l10n = new L10n();

        // Iso2 lang code
        $iso2 = $l10n->map($lang);
        $catalog = $l10n->catalog($lang);

        $locales = array(
            $iso2 . '_' . strtoupper($iso2) . '.' . strtoupper(str_replace('-', '', $catalog['charset'])),
            // fr_FR.UTF8
            $iso2 . '_' . strtoupper($iso2),
            // fr_FR
            $catalog['locale'],
            // fre
            $catalog['localeFallback'],
            // fre
            $iso2
            // fr
        );
        return $locales;
    }

    protected function currentUri()
    {
        $uri = empty($this->params['controller']) ? "" : $this->params['controller'];
        $uri .= empty($this->params['action']) ? "" : "." . $this->params['action'];

        if ($uri == 'pages.display') {
            $uri .= empty($this->params['pass'][0]) ? "" : "." . $this->params['pass'][0];
        }
        return $uri;
    }

    protected function doLoadingBlocks($uri)
    {
        if ($this->layout != '' && $this->autoRender != false && !$this->request->is('post') && !$this->request->is('requested')) {


            $blocks = Cache::read("$uri.blocks");
            if (!$blocks) {
                $this->loadModel('Page.Page');
                $row = $this->Page->find('first', array(
                    'conditions' => array('Page.uri' => $uri),
                    'recursive' => 2
                ));
                Cache::write("$uri.blocks", $row);
                $blocks = $row;
            }
            $this->loadModel('CoreContent');
            $this->loadModel('CoreBlock');

            $rowHeader = Cache::read('rowHeader');

            if (!$rowHeader) {
                $rowHeader = $this->CoreContent->getCoreContentByPageName('header');
                if (!$rowHeader) {
                    $rowHeader = array(0);
                }
                Cache::write('rowHeader', $rowHeader);
            }

            $rowFooter = Cache::read('rowFooter');

            if (!$rowFooter) {
                $rowFooter = $this->CoreContent->getCoreContentByPageName('footer');
                if (!$rowFooter) {
                    $rowFooter = array(0);
                }
                Cache::write('rowFooter', $rowFooter);
            }

            if (count($blocks) > 0) {
                $rowPageDescription = $blocks['Page']['description'];
                $rowPageKeyword = $blocks['Page']['keywords'];
                $rowPageTitle = $blocks['Page']['title'];

                $this->set('mooPageDescription', $rowPageDescription);
                $this->set('mooPageKeyword', $rowPageKeyword);
                $this->set('mooPageTitle', $rowPageTitle);
                foreach ($blocks['CoreContent'] as $block) {
                    if ((isset($block['type'])) && $block['type'] == 'widget') {
                        if ($block['name'] != 'invisiblecontent') {

                            $widget = str_replace('.', DS, $block['name']);
                            $params = json_decode($block['params'], true);
                            $params['content_id'] = $block['id'];
                            $oWidget = false;
                            if ($block['plugin']) {
                                $oWidget = $this->Widgets->load($block['plugin'] . '.' . $widget,
                                    array('params' => $params));
                            } else {
                                $oWidget = $this->Widgets->load($widget, array('params' => $params));
                            }

                        }
                    }

                }

            }
            $this->set('mooPage', $blocks);
            $this->set('mooHeader', $rowHeader);
            $this->set('mooFooter', $rowFooter);
        }
    }

    public function render($view = null, $layout = null)
    {
        $this->response = parent::render($view, $layout);
        $event = new CakeEvent('Controller.afterRender', $this);
        $this->getEventManager()->dispatch($event);
        return $this->response;
    }

    public function implementedEvents()
    {
        $events = parent::implementedEvents();
        $events['MooView.beforeRender'] = 'beforeMooViewRender';

        return $events;
    }

    public function beforeMooViewRender()
    {
    }

    public function setNgController($event)
    {
        $v = $event->subject();
        try {
            if ($v instanceof MooView) {
                $v->setNgController();
            }
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
            die();
        }
    }

    public function get($name, $value)
    {
        return ((!empty($this->request->named[$name])) ? $this->request->named[$name] : $value);
    }

    public function getPluginModel($plugin, $model)
    {
        App::import('Model', $plugin . '.' . $model);
        return $objModel = new $model();
    }

    public function isModelInPlugin($model)
    {
        if (in_array($model, array('Blog', 'Video', 'Page', 'Photo', 'Event'))) {
            return true;
        }
        return false;
    }

    public function getEventManager()
    {
        if (empty($this->_eventManager)) {
            $this->_eventManager = new CakeEventManager();
            $this->_eventManager->attach($this->Components);
            $this->_eventManager->attach($this->Widgets);
            $this->_eventManager->attach($this);
        }
        return $this->_eventManager;
    }


    public function beforeRender()
    {
        $ids = MooPeople::getInstance()->get();
        MooPeople::getInstance()->setStatus('onBeforeRender');
        if (!empty($ids)) {
            $this->loadModel('User');
            $users = $this->User->find('all', array('conditions' => array("User.id" => $ids)));
            MooPeople::getInstance()->add(Hash::combine($users, '{n}.User.id', '{n}'));
        }
        MooPeople::getInstance()->setStatus('onRender');
    }

    public function getGuest()
    {
        return array(
            'id' => '0',
            'name' => 'Guest',
            'email' => 'guest@local.com',
            'role_id' => '3',
            'avatar' => '',
            'photo' => '',
            'created' => '2014-12-16 09:19:31',
            'last_login' => '0000-00-00 00:00:00',
            'photo_count' => '0',
            'friend_count' => '0',
            'notification_count' => '0',
            'friend_request_count' => '0',
            'blog_count' => '0',
            'topic_count' => '0',
            'conversation_user_count' => '0',
            'video_count' => '0',
            'gender' => 'Male',
            'birthday' => '2014-12-16',
            'active' => true,
            'confirmed' => true,
            'code' => '2bfd6099852afc1b09d86c27eb3c136a',
            'notification_email' => true,
            'timezone' => 'Africa/Abidjan',
            'ip_address' => '',
            'privacy' => '1',
            'username' => '',
            'about' => '',
            'featured' => false,
            'lang' => '',
            'hide_online' => false,
            'cover' => '',
            'Role' =>
                array(
                    'id' => '3',
                    'name' => 'Guest',
                    'is_admin' => false,
                    'is_super' => false,
                    'params' => 'global_search,user_username,blog_view,blog_create,album_create,album_view,event_create,event_view,group_create,group_view,group_delete,photo_upload,photo_view,topic_create,topic_view,video_share,video_view,attachment_upload,attachment_download',
                    'core' => true,
                ),
        );
    }

    private function loadingApplicationSettings()
    {
        // Todo Refactor
        if (file_exists(APP . 'Config/config.php')) {
            //load unboot settings
            $this->loadUnBootSetting();

            //load component resgister
            $this->loadComponent();

            if ((!empty($this->request->query['access_token']) || !empty($this->request->data['access_token']))) {

                $this->OAuth2 = $this->Components->load('OAuth2');

                if ($this->OAuth2->verifyResourceRequest()) {
                    $this->Auth->login($this->OAuth2->getOwnerResourceRequest(false));;
                }
            }
            $this->getEventManager()->dispatch(new CakeEvent('AppController.doBeforeFilter', $this));
        }

        // check for config file
        if (!file_exists(APP . 'Config/config.php')) {
            $this->redirect('/install');
            exit;
        }

        $this->Cookie->name = 'mooSocial';
        $this->Cookie->key = Configure::read('Security.salt');
        $this->Cookie->time = 60 * 60 * 24 * 30;

        // return url
        if (!empty($this->request->named['return_url'])) {
            $this->set('return_url', $this->request->named['return_url']);
        }

        $maxFileSize = $this->_getMaxFileSize();
        
        $videoMaxUpload = $this->_getMaxVideoUpload();
        $this->set('sizeLimit', $maxFileSize);
        $this->set('videoMaxUpload', $videoMaxUpload);

    }

    private function identifyingViewer()
    {
        // Todo Refactor

        $uid = $this->Auth->user('id');
        // auto login
        if (empty($uid) && $this->Cookie->read('email') && $this->Cookie->read('password')) {
            $uid = $this->_logMeIn($this->Cookie->read('email'), $this->Cookie->read('password'));
            if ($uid)
            {
            	$user = $this->User->findById($uid);		        
		        $cuser = $user['User'];
		        $cuser['Role'] = $user['Role'];
		        unset($cuser['password']);
		        $this->Auth->login($cuser);		        
            }
        }
        $accepted_cookie = $this->Cookie->read('accept_cookie');
        $this->set('accepted_cookie', $accepted_cookie);
    }

    private function loadingViewerSetting()
    {
        // Todo Refactor
    }

    private function doBanUsersProcess()
    {
        // Todo Refactor
        // ban ip addresses
        $ban_ips = Configure::read('core.ban_ips');
        if (!empty($ban_ips)) {
            $ips = explode("\n", $ban_ips);
            foreach ($ips as $ip) {
                if (!empty($ip) && strpos($_SERVER['REMOTE_ADDR'], trim($ip)) === 0) {
                    $this->autoRender = false;
                    echo __('You are not allowed to view this site');
                    exit;
                }
            }
        }
    }
    
    // check if $email is banned by system
    // @return : true or false
    protected function isBanned($email = null){
        
        if (empty($email)){
            return false;
        }
        
        $ban_emails = Configure::read('core.ban_emails');
        $emails = explode( "\n", $ban_emails );
        
        if (empty($ban_emails)){
            return false;
        }
        
        foreach ($emails as $item){
            if (trim($email) == trim($item)){
                return true;
            }else{
                $list1 = explode("@*", $item); //   abc@*
                $list2 = explode("*@", $item); //   *@abc.com
                $list3 = explode("@", $email);
                
                // case 1
                if (isset($list1[0]) && isset($list3[0])){
                   if (trim($list1[0]) == trim($list3[0])){ // compared name
                       return true;
                   } 
                }
                
                // case 2
                if (isset($list2[1]) && isset($list3[1])){
                    
                    if (trim($list2[1]) == trim($list3[1])){ // compared domain
                       return true;
                   }
                }
            }
        }
        
        return false;
    }

    private function doThemeProcess()
    {
        // Todo Refactor
        // get langs
        $this->loadModel('Language');
        $site_langs = $this->Language->getLanguages();

        // select lang
        if ($this->Cookie->check('language') && array_key_exists($this->Cookie->read('language'), $site_langs)) {
            $language = $this->Cookie->read('language');
        }

        if (empty($language)) {
            $language = Configure::read('core.default_language');
        }
        //get rtl setting
        $site_rtl = '';
        $language_rtl = $this->Language->getRtlOption();

        if (!empty($language_rtl)) {
            foreach ($language_rtl as $rtl) {
                if ($rtl['Language']['key'] == $language) {
                    $site_rtl = $rtl['Language']['rtl'];
                }
            }
        }

        Configure::write('Config.language', $language);


        // set locale
        $locales = $this->_getLocales($language);
        setlocale(LC_ALL, $locales);

        $uid = $this->Auth->user('id');

        // themes
        $this->loadModel('Theme');
        $site_themes = $this->Theme->getThemes();

        // select theme
        //none-login user
        if (Configure::read('core.select_theme') && empty($uid)) {
            if (!$this->Session->read('non_login_user_default_theme')) {
                $this->Session->write('non_login_user_default_theme', Configure::read('core.default_theme'));
            }
            if ($this->Session->read('non_login_user_theme') && array_key_exists($this->Session->read('non_login_user_theme'),
                    $site_themes) && $this->Session->read('non_login_user_default_theme') == Configure::read('core.default_theme')
            ) {
                $this->theme = $this->Session->read('non_login_user_theme');

            }
        }

        if (Configure::read('core.select_theme') && !empty($uid)) {
            if ($this->Cookie->check('theme') && array_key_exists($this->Cookie->read('theme'), $site_themes)) {
                $this->theme = $this->Cookie->read('theme');
            }
        }

        if (empty($this->theme)) {
            $this->theme = Configure::read('core.default_theme');
            if (!empty($uid) && !($this->Cookie->check('theme') && array_key_exists($this->Cookie->read('theme'),
                        $site_themes))
            ) {
                $this->Cookie->write('theme', Configure::read('core.default_theme'));
            }
        }

        if (empty($this->theme)) {
            $this->theme = 'default';
        }

        // site is offline?
        $site_offline = Configure::read('core.site_offline');
        $cuser = $this->_getUser();
        if (!empty($site_offline) && $this->request->action != 'login' && empty($cuser['Role']['is_super'])) {
            $this->layout = '';
            $this->set('offline_message', Configure::read('core.offline_message'));
            $this->render('/Elements/misc/offline');
            return;
        }

        // detect ajax request
        if ($this->request->is('ajax')) {
            $this->layout = '';
        }

        if (strpos($this->request->action, 'do_') !== false) {
            $this->autoRender = false;
        }
        $this->getEventManager()->dispatch(new CakeEvent('AppController.doSetTheme', $this));

        if (isset($this->request->params['admin'])) // admin area
        {
            // v3.0.0 - Theme engine upgrade
            $this->theme = 'adm';
            $this->_checkPermission(array('admin' => true));

            if ($this->request->action != 'admin_login' && !$this->Session->read('admin_login')) {
                $this->redirect('/admin/home/login');
                exit;
            }

            if ($this->Session->read('admin_login')) {
                $this->Session->write('admin_login', 1);
            }
        }
        // hooks - refactor
        // just loading content only for determine page
        // Using $this->currentUri()
        //       $this->doLoadingComponent(uri);
        $this->doLoadingBlocks($this->currentUri());
        $this->set('site_themes', $site_themes);
        $this->set('site_langs', $site_langs);
        $this->set('site_rtl', $site_rtl);
    }

    private function doViewerProcess()
    {
        // Todo Refactor
        $uid = $this->Auth->user('id');
        // get current user
        if (empty($cuser)) {
            $cuser = $this->_getUser();
            // Set guest user
            if (empty($cuser)) {

            }
            $this->set('cuser', $cuser);
        }

        // set lang to user's chosen lang
        if (!empty($cuser['lang'])) {
            Configure::write('Config.language', $cuser['lang']);
        }
        // force login
        if (empty($uid) && ($this->request->here != $this->request->webroot)
            && Configure::read('core.force_login')
            && !in_array($this->request->controller, array('pages', 'home'))
            && !in_array($this->request->action, array(
                'preview',
                'member_verify',
                'ajax_browse',
                'browse',
                'signup_step2',
                'register',
                'endpoint',
                'login',
                'member_login',
                'avatar_tmp',
                'do_logout',
                'ajax_signup_step1',
                'ajax_signup_step2',
                'fb_register',
                'do_fb_register',
                'recover',
                'resetpass',
                'do_confirm'
            ))
        ) {
            $this->redirect('/users/member_login');
            exit;
        }

        if (empty($uid) && Configure::read('core.force_login')) {
            $this->set('no_right_column', true);
        }

        // remind email validation
        if (!empty($cuser) && !$cuser['confirmed'] && Configure::read('core.email_validation')) {
            $this->Session->setFlash(__('An email has been sent to your email address<br />Please click the validation link to confirm your email'),
                'default', array('class' => 'Metronic-alerts alert alert-success fade in'), 'confirm_remind');
        }

        //remind pending status
        if (!empty($cuser) && !$cuser['approved'] && Configure::read('core.approve_users')) {
            $this->Session->setFlash(__('Your account is pending approval.'),
                'default', array('class' => 'Metronic-alerts alert alert-success fade in'), 'confirm_remind');
        }

        $role_id = $this->_getUserRoleId();
        // site timezone
        $utz = (!is_numeric(Configure::read('core.timezone'))) ? Configure::read('core.timezone') : 'UTC';

        // user timezone
        if (!empty($cuser['timezone'])) {
            $utz = $cuser['timezone'];
        }
        // set viewer
        if ($uid) {
            $this->loadModel('User');
            $user = $this->User->findById($uid);
            MooCore::getInstance()->setViewer($user);
        }

        //hide dislike or not
        $hide_dislike = Configure::read('core.hide_dislike');

        $this->set('role_id', $role_id);
        $this->set('uid', $uid);
        $this->set('uacos', $this->_getUserRoleParams());
        $this->set('utz', $utz);
        $this->set('hide_dislike', $hide_dislike);
    }
    public function afterFilter(){
        // Hacking for thrown exceptions in session::destory problem
        if ($this->request->is('api') ) {
            $this->Session->destroy();
        }
    }
    
    protected function _sendNotificationToMentionUser($content,$url,$action,$editUsers = array())
    {
        //notification for user mention
        $uid = $this->Auth->user('id');
        preg_match_all(REGEX_MENTION,$content,$matches);
        if(!empty($matches)){
            foreach($matches[0] as $key => $value){
                $this->loadModel('Notification');
                if(!empty($editUsers) && !in_array($matches[1][$key],$editUsers)){
                    continue;
                }
                if($matches[1][$key] != $uid){
                    $this->Notification->record(array('recipients' => $matches[1][$key],
                            'sender_id' => $uid,
                            'action' => $action,
                            'url' => $url
                        ));
                }
            }
        }
    }
    
    protected function _getUserIdInMention($content){
        preg_match_all(REGEX_MENTION,$content,$matches);
        if(!empty($matches)){
            return $matches[1];
        }else
            return false;
    }

    protected  function _getMaxFileSize()
    {
        $max_upload = $this->__return_bytes(ini_get('upload_max_filesize'));
        //select post limit
        $max_post = $this->__return_bytes(ini_get('post_max_size'));
        //select memory limit
        $memory_limit = $this->__return_bytes(ini_get('memory_limit'));
        // return the smallest of them, this defines the real limit
        return min($max_upload, $max_post, $memory_limit);
    }
    
    protected  function _getMaxVideoUpload()
    { 
        $max_upload = $this->__return_bytes(ini_get('upload_max_filesize'));
        //select post limit
        $max_post = $this->__return_bytes(ini_get('post_max_size'));
        //select memory limit
        $memory_limit = $this->__return_bytes(ini_get('memory_limit'));
        
        $uploadEnable = Configure::read('UploadVideo.uploadvideo_enabled');
        if ($uploadEnable){
            // max video upload from setting
            $upload_setting = Configure::read('UploadVideo.video_common_setting_max_upload') . 'M';
            $max_upload_setting = $this->__return_bytes($upload_setting);
            return min($max_upload, $max_post, $memory_limit, $max_upload_setting);
        }
        
        
        // return the smallest of them, this defines the real limit
        return min($max_upload, $max_post, $memory_limit);
    }

    private function __return_bytes($val) {
        $val = trim($val);
        $last = strtolower($val[strlen($val)-1]);
        switch($last)
        {
            case 'g':
                $val *= 1024;
            case 'm':
                $val *= 1024;
            case 'k':
                $val *= 1024;
        }
        return $val;
    }
}
