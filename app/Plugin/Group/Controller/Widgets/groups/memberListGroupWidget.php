<?php

App::uses('Widget', 'Controller/Widgets');

class memberListGroupWidget extends Widget {

    public function beforeRender(Controller $controller) {
        $data = array(
            'groupMembers' => array(),
            'groupMembersCnt' => 0
        );
        $subject = MooCore::getInstance()->getSubject();
        if ($subject) {
            $id = $subject['Group']['id'];
            $num_group_member = $this->params['num_item_show'];
            $controller->loadModel('Group.GroupUser');
            // caching
            $group_members = Cache::read('group_' . $id . '_members_widget', 'group');
            if (!$group_members) {
                $group_members = $controller->GroupUser->getUsers($id, GROUP_USER_MEMBER, null, $num_group_member);
                $group_admins = $controller->GroupUser->getUsers($id, GROUP_USER_ADMIN, null, $num_group_member);
                $group_members = array_merge($group_members, $group_admins);
                Cache::write('group_' . $id . '_members_widget', $group_members, 'group');
            }

            $member_count = $subject['Group']['group_user_count'];

            $data['groupMembers'] = $group_members;
            $data['groupMembersCnt'] = $member_count;
        }

        $this->setData('data', $data);
    }

}
