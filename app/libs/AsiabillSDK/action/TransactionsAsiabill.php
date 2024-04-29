<?php

namespace app\libs\AsiabillSDK\action;

use app\libs\AsiabillSDK\AsiabillClient;

class TransactionsAsiabill extends AsiabillClient
{
    public function query_a_transaction(array $query)
    {
        return $this->asiabill->openapi()->request('transactions',['query'=>$query]);
    }
}