<?phpApp::import('Lib/SocialIntegration', 'SocialIntergration');class SocialIntegration_Providers_LinkedIn extends SocialIntergration {    /**     * IDp wrappers initializer     */    function initialize() {        if (!$this->config["keys"]["key"] || !$this->config["keys"]["secret"]) {            throw new Exception("Your application key and secret are required in order to connect to {$this->providerId}.", 4);        }        if (!class_exists('OAuthConsumer')) {            require_once SocialIntegration_Auth::$config["path_libraries"] . "OAuth/OAuth.php";        }        require_once SocialIntegration_Auth::$config["path_libraries"] . "LinkedIn/LinkedIn.php";        $this->api = new LinkedIn(array('appKey' => $this->config["keys"]["key"], 'appSecret' => $this->config["keys"]["secret"], 'callbackUrl' => $this->endpoint));        if ($this->token("access_token_linkedin")) {            $this->api->setTokenAccess($this->token("access_token_linkedin"));        }    }    /**     * begin login step     */    function loginBegin() {        // send a request for a LinkedIn access token        $response = $this->api->retrieveTokenRequest();        if (isset($response['success']) && $response['success'] === TRUE) {            $this->token("oauth_token", $response['linkedin']['oauth_token']);            $this->token("oauth_token_secret", $response['linkedin']['oauth_token_secret']);            # redirect user to LinkedIn authorisation web page            SocialIntegration_Auth::redirect(LINKEDIN::_URL_AUTH . $response['linkedin']['oauth_token']);        } else {            throw new Exception("Authentication failed! {$this->providerId} returned an invalid Token.", 5);        }    }    /**     * finish login step     */    function loginFinish() {        $oauth_token = $_REQUEST['oauth_token'];        $oauth_verifier = $_REQUEST['oauth_verifier'];        if (!$oauth_verifier) {            throw new Exception("Authentication failed! {$this->providerId} returned an invalid Token.", 5);        }        $response = $this->api->retrieveTokenAccess($oauth_token, $this->token("oauth_token_secret"), $oauth_verifier);        if (isset($response['success']) && $response['success'] === TRUE) {            $this->deleteToken("oauth_token");            $this->deleteToken("oauth_token_secret");            $this->token("access_token_linkedin", $response['linkedin']);            $this->token("access_token", $response['linkedin']['oauth_token']);            $this->token("access_token_secret", $response['linkedin']['oauth_token_secret']);            // set user as logged in            $this->setUserConnected();        } else {            throw new Exception("Authentication failed! {$this->providerId} returned an invalid Token.", 5);        }    }    /**     * load the user profile from the IDp api client     */    function getUserProfile() {        try {            // http://developer.linkedin.com/docs/DOC-1061            $response = $this->api->profile('~:(id,first-name,last-name,public-profile-url,picture-url,email-address,date-of-birth,phone-numbers,summary)');        } catch (LinkedInException $e) {            throw new Exception("User profile request failed! {$this->providerId} returned an error: $e", 6);        }        if (isset($response['success']) && $response['success'] === TRUE) {            $data = @ new SimpleXMLElement($response['linkedin']);            if (!is_object($data)) {                throw new Exception("User profile request failed! {$this->providerId} returned an invalid xml data.", 6);            }            $this->user->profile->identifier = (string) $data->{'id'};            $this->user->profile->firstName = (string) $data->{'first-name'};            $this->user->profile->lastName = (string) $data->{'last-name'};            $this->user->profile->displayName = trim($this->user->profile->firstName . " " . $this->user->profile->lastName);            $this->user->profile->email = (string) $data->{'email-address'};            $this->user->profile->emailVerified = (string) $data->{'email-address'};            $this->user->profile->photoURL = (string) $data->{'picture-url'};            $this->user->profile->profileURL = (string) $data->{'public-profile-url'};            $this->user->profile->description = (string) $data->{'summary'};            if ($data->{'phone-numbers'} && $data->{'phone-numbers'}->{'phone-number'}) {                $this->user->profile->phone = (string) $data->{'phone-numbers'}->{'phone-number'}->{'phone-number'};            } else {                $this->user->profile->phone = null;            }            if ($data->{'date-of-birth'}) {                $this->user->profile->birthDay = (string) $data->{'date-of-birth'}->day;                $this->user->profile->birthMonth = (string) $data->{'date-of-birth'}->month;                $this->user->profile->birthYear = (string) $data->{'date-of-birth'}->year;            }            return $this->user->profile;        } else {            throw new Exception("User profile request failed! {$this->providerId} returned an invalid response.", 6);        }    }    /**     * load the user contacts     */    function getUserContacts() {        try {            $response = $this->api->connections('~/connections:(id,first-name,last-name,picture-url)?start=0&count=1000');        } catch (LinkedInException $e) {            throw new Exception("User contacts request failed! {$this->providerId} returned an error: $e");        }        if (!$response || !$response['success']) {            return ARRAY();        }        $connections = new SimpleXMLElement($response['linkedin']);        $contactInfo = array();        if ((int) $connections['total'] > 0) {            $key = 0;            foreach ($connections->person as $connection) {                $contactInfo[$key]['name'] = $connection->{'first-name'} . ' ' . $connection->{'last-name'};                $contactInfo[$key]['id'] = (string) $connection->{'id'};                $contactInfo[$key]['picture'] = (string) $connection->{'picture-url'};                $key++;            }        }        return $contactInfo;    }    /**     * update user status     */    function setUserStatus($status) {        $parameters = array();        $private = true; // share with your connections only        if (is_array($status)) {            if (isset($status[0]) && !empty($status[0]))                $parameters["title"] = $status[0]; // post title            if (isset($status[1]) && !empty($status[1]))                $parameters["comment"] = $status[1]; // post comment            if (isset($status[2]) && !empty($status[2]))                $parameters["submitted-url"] = $status[2]; // post url            if (isset($status[3]) && !empty($status[3]))                $parameters["submitted-image-url"] = $status[3]; // post picture url            if (isset($status[4]) && !empty($status[4]))                $private = $status[4]; // true or false        }        else {            $parameters["comment"] = $status;        }        try {            $response = $this->api->share('new', $parameters, $private);        } catch (LinkedInException $e) {            throw new Exception("Update user status update failed!  {$this->providerId} returned an error: $e");        }        if (!$response || !$response['success']) {            throw new Exception("Update user status update failed! {$this->providerId} returned an error.");        }    }    /**     * load the user latest activity     *    - timeline : all the stream     *    - me       : the user activity only     */    function getUserActivity($stream) {        try {            if ($stream == "me") {                $response = $this->api->updates('?type=SHAR&scope=self&count=25');            } else {                $response = $this->api->updates('?type=SHAR&count=25');            }        } catch (LinkedInException $e) {            throw new Exception("User activity stream request failed! {$this->providerId} returned an error: $e");        }        if (!$response || !$response['success']) {            return ARRAY();        }        $updates = new SimpleXMLElement($response['linkedin']);        $activities = ARRAY();        foreach ($updates->update as $update) {            $person = $update->{'update-content'}->person;            $share = $update->{'update-content'}->person->{'current-share'};            $ua = new SocialIntegration_User_Activity();            $ua->id = (string) $update->id;            $ua->date = (string) $update->timestamp;            $ua->text = (string) $share->{'comment'};            $ua->user->identifier = (string) $person->id;            $ua->user->displayName = (string) $person->{'first-name'} . ' ' . $person->{'last-name'};            $ua->user->profileURL = (string) $person->{'site-standard-profile-request'}->url;            $ua->user->photoURL = NULL;            $activities[] = $ua;        }        return $activities;    }    /**     *  Parse the contacts in two parts :     *      * 1) Contacts which are already at site     *      * 2) Contacts which are not on this site     *      */    public function parseUserContacts($contactInfo, $moduletype = null, $Subject = null) {        $viewer = Engine_Api::_()->user()->getViewer();        $user_id = $viewer->getIdentity();        $table_user = Engine_Api::_()->getitemtable('user');        $tableName_user = $table_user->info('name');        $inviteTable = Engine_Api::_()->getDbtable('invites', 'invite');        $inviteTableName = $inviteTable->info('name');        $table_user_memberships = Engine_Api::_()->getDbtable('membership', 'user');        $tableName_user_memberships = $table_user_memberships->info('name');        $SiteNonSiteFriends[] = '';        foreach ($contactInfo as $userid => $name) {            //FIRST WE WILL FIND IF THIS USER IS SITE MEMBER            $select = $table_user->select()                    ->setIntegrityCheck(false)                    ->from($tableName_user, array('user_id', 'displayname', 'photo_id'))                    ->join($inviteTableName, "$inviteTableName.new_user_id = $tableName_user.user_id", null)                    ->where($inviteTableName . '.new_user_id <>?', 0)                    ->limit(1)                    ->where($inviteTableName . '.social_profileid = ?', $userid);            $is_site_members = $table_user->fetchRow($select);            if (empty($user_id)) {                if (!empty($is_site_members->user_id)) {                    continue;                }            }            //NOW IF THIS USER IS SITE MEMBER THEN WE WILL FIND IF HE IS FRINED OF THE OWNER.            if (!empty($is_site_members->user_id) && $is_site_members->user_id != $user_id) {                $contact = Engine_Api::_()->user()->getUser($is_site_members->user_id);                // check that user has not blocked the member                if (!$viewer->isBlocked($contact)) {                    //SENDING PAGE JOIN SUGGESTION IF THE USER IS SITE MEMBER.                    if (!empty($moduletype)) {                        $is_suggenabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('suggestion');                        // IF SUGGESTION PLUGIN IS INSTALLED, A SUGGESTION IS SEND                        if ($is_suggenabled) {                            Engine_Api::_()->sitepageinvite()->sendSuggestion($is_site_members, $viewer, $Subject->page_id);                        }                        // IF SUGGESTION PLUGIN IS NOT INSTALLED, A NOTIFICATION IS SEND                        else {                            Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($is_site_members, $viewer, $Subject, $moduletype . '_suggested');                        }                        return;                    }                    // The contact should not be my friend, and neither of us should have sent a friend request to the other.                    $select = $table_user_memberships->select()                            ->setIntegrityCheck(false)                            ->from($tableName_user_memberships, array('user_id'))                            ->where($tableName_user_memberships . '.resource_id = ' . $user_id . ' AND ' . $tableName_user_memberships . '.user_id = ' . $is_site_members->user_id)                            ->orwhere($tableName_user_memberships . '.resource_id = ' . $is_site_members->user_id . ' AND ' . $tableName_user_memberships . '.user_id = ' . $user_id);                    $already_friend = $table_user->fetchRow($select);                    //IF THIS QUERY RETURNS EMPTY RESULT MEANS THIS USER IS SITE MEMBER BUT NOT FRIEND OF CURRENTLY LOGGEDIN USER SO WE WILL SEND HIM TO FRIENDSHIP REQUEST.                    if (empty($already_friend->user_id)) {                        $SiteNonSiteFriends[0][] = $is_site_members->toArray();                    }                }            }            //IF USER IS NOT SITE MEMBER .            else if (empty($is_site_members->user_id)) {                $SiteNonSiteFriends[1][] = $name;            }        }        $result[0] = '';        $result[1] = '';        if (!empty($SiteNonSiteFriends[1]))            $result[1] = array_map("unserialize", array_unique(array_map("serialize", $SiteNonSiteFriends[1])));        if (!empty($SiteNonSiteFriends[0]))            $result[0] = array_map("unserialize", array_unique(array_map("serialize", $SiteNonSiteFriends[0])));        return $result;    }    function sendInvite($friendsToJoin, $user_data = null) {                if(isset($user_data['subject']) && !empty($user_data['subject'])){            $subject = $user_data['subject'];        }else{            $subject = 'You have received an invitation to join our social network.';        }                if(isset($user_data['body']) && !empty($user_data['body'])){            $body = $user_data['body'];        }else{            $body = 'You have received an invitation to join our social network.';        }        foreach ($friendsToJoin as $recipient => $recipient_name) {            if(!empty($recipient))            $response_linkedin = $this->api->message(array('0' => $recipient), $subject, $body, FALSE);        }            }}