<?php

/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */
App::import("Model", "Menu.CoreMenu");
App::import("Model", "Menu.CoreMenuItem");

class MenuHelper extends AppHelper {

    var $helpers = array('Html');
    var $items = array('main' => array());
    var $exclude_active = array('users.register', 'users.member_login', 'users.recover');

    public function generateDepth($objModel, $parent_id) {
        $out = '';
        $liAttributes = '';

        $coreMenuItem = $objModel->find('all', array('conditions' => array('parent_id' => $parent_id, 'is_active' => true), 'order' => 'menu_order ASC'));

        foreach ($coreMenuItem as $key => $item) {
            // check group access
            $aRole = json_decode($item['Coremenuitem']['role_access'],true);
            $viewer = MooCore::getInstance()->getViewer();
            $viewerRole = isset($viewer['Role']['id']) ? $viewer['Role']['id'] : ROLE_GUEST;
            //if (isset($item['type']) && $item['type'] != 'header') {
                if (!$aRole || !in_array($viewerRole, $aRole)){
                    continue;
                }
            //}
            $name = $this->Html->tag('i', '', array('class' => $item['Coremenuitem']['font_class'])) . $item['Coremenuitem']['name'];
            $title = $item['Coremenuitem']['title_attribute'] ? $item['Coremenuitem']['title_attribute'] : '';
            if ($item['Coremenuitem']['type'] == 'header') {
                $liLabel = $this->Html->tag('span', $name, array('title' => $title, 'class' => 'header'));
            } else {
                $liLabel = $this->Html->link(
                        $name, $item['Coremenuitem']['url'], array('no_replace_ssl'=>true, 'title' => $title, 'target' => $item['Coremenuitem']['new_blank'] ? '_blank' : '', 'escape' => false)
                );
            }
            $childGenerate = $this->generateDepth($objModel, $item['Coremenuitem']['id']);
            $hasChildChild = $childGenerate ? 'hasChild' : '';
            $liAttributes .= $this->Html->tag('li', $liLabel . $childGenerate, array('class' => $hasChildChild));
        }

        if ($liAttributes) {
            $out = $this->Html->tag('ul', $liAttributes, array('style' => 'display:none;'));
        }

        return $out;
    }

    function generate($alias = null, $menu_id = null, $options = array()) {
        $default = array(
            'id' => ''
        );
        $default = array_merge($default, $options);

        $currentLang = Configure::read('Config.language');

        $out = Cache::read('menu_' . $alias . '_' . $menu_id . str_replace('/', '_', $this->here) . '_' . $currentLang, 'menu');
        $out = null;
        $liAttributes = '';

        $coremenuModel = MooCore::getInstance()->getModel('Menu_CoreMenu');
        $coremenuitemModel = MooCore::getInstance()->getModel('Menu_CoreMenuItem');

        if (!$out) {
            $coreMenu = array();

            if (!$alias && !$menu_id) {
                return false;
            } else if ($alias) {
                $coreMenu = $coremenuModel->getMenuByAlias($alias);
            } else {
                $coreMenu = $coremenuModel->getMenuById($menu_id);
            }

            if (empty($coreMenu['CoreMenuItem'])) {
                return false;
            }

            $coreMenuItem = $coreMenu['CoreMenuItem'];

            foreach ($coreMenuItem as $key => $item) {
                // check group access
                $aRole = json_decode($item['role_access'],true);
                $viewer = MooCore::getInstance()->getViewer();
                $viewerRole = isset($viewer['Role']['id']) ? $viewer['Role']['id'] : ROLE_GUEST;
                if (isset($item['type']) && $item['type'] != 'header') {
                    if (!$aRole || !in_array($viewerRole, $aRole)){
                        continue;
                    }
                }

                if (!$item['parent_id']) {
                    $name = $this->Html->tag('i', '', array('class' => $item['font_class'])) . $item['name'];
                    $title = $item['title_attribute'] ? $item['title_attribute'] : '';
                    if ($item['type'] == 'header') {
                        $liLabel = $this->Html->tag('span', $name, array('class' => 'header', 'title' => $title));
                    } else {
                        $active = '';

                        if (strstr($this->here, $item['url'])) {
                            if (!in_array($this->getUri(), $this->exclude_active))
                                $active = 'active';
                        }
                        $liLabel = $this->Html->link(
                                $name, $item['url'], array('no_replace_ssl'=>true, 'title' => $title, 'class' => $active, 'target' => $item['new_blank'] ? '_blank' : '', 'escape' => false)
                        );
                    }

                    $generate = $this->generateDepth($coremenuitemModel, $item['id']);
                    $hasChild = $generate ? 'hasChild' : '';
                    $liAttributes .= $this->Html->tag('li', $liLabel . $generate, array('class' => $hasChild));
                }
            }

            $out = $this->Html->tag('ul', $liAttributes, array('class' => $default['class'] . ' ' . $coreMenu['Coremenu']['style'], 'id' => $default['id']));
            Cache::write('menu_' . $alias . '_' . $menu_id . str_replace('/', '_', $this->here) . '_' . $currentLang, $out, 'menu');
        }


        return $out;
    }

}
