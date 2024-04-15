<?php

namespace app\libs\AsiabillSDK\action;

use app\libs\AsiabillSDK\AsiabillClient;

class SessionAsiabill extends AsiabillClient
{
    public function get_session_token()
    {
        return $this->setRequestType('sessionToken')->send();
    }
}