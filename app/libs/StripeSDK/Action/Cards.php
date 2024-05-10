<?php

namespace app\libs\StripeSDK\Action;

use app\libs\StripeSDK\Builder\CardsBuilder;
use app\libs\StripeSDK\StripeAPI;

class Cards extends StripeAPI
{
    public function create_payment_method(CardsBuilder $builder = null)
    {
       return $this->client->paymentMethods->create($builder->toArray());
    }

    public function create_card_token(array $params)
    {
        return $this->client->tokens->create($params);
    }


}