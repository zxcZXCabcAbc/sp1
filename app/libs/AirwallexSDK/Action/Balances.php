<?php

namespace app\libs\AirwallexSDK\Action;

use app\libs\AirwallexSDK\AirwallexClient;

class Balances extends AirwallexClient
{
    public function get_current_balances()
    {
        return $this->setMethod('GET')
                    ->setPath('/api/v1/balances/current')
                    ->send();
    }
}