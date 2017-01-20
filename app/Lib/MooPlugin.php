<?php

interface MooPlugin
{
    public function install();
    public function uninstall();
    public function settingGuide();
    public function menu();
}