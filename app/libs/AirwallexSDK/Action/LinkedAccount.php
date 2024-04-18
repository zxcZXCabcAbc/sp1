<?php

namespace app\libs\AirwallexSDK\Action;

use app\libs\AirwallexSDK\AirwallexClient;

class LinkedAccount extends AirwallexClient
{
    public function get_linked_accounts()
    {
        return $this->setMethod('GET')
                    ->setPath('/api/v1/linked_accounts')
                    ->send();
    }
}