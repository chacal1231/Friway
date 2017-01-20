<?php
interface MooGateway
{
    public function renderHtmlForm($params, $recurrence);
    public function validateTransaction();
}