<?php

namespace app\service\shopify\action\rest;

class CustomerRest extends RestBase
{
    /**
     * @param array $customerRequest
     * @return array
     * @doc https://shopify.dev/docs/api/admin-rest/2024-04/resources/customer#post-customers
     * @desc 创建顾客
     */
    public function create_customer(array $customerRequest = []):array
    {
        $this->rest->first_name = $customerRequest['first_name'];
        $this->rest->last_name = $customerRequest['last_name'];
        $this->rest->email = $customerRequest['email'];
        $this->rest->phone = $customerRequest['phone'];
        $this->rest->verified_email = true;
        /*
        $this->rest->addresses = [
            [
                "address1" => "123 Oak St",
                "city" => "Ottawa",
                "province" => "ON",
                "phone" => "555-1212",
                "zip" => "123 ABC",
                "last_name" => "Lastnameson",
                "first_name" => "Mother",
                "country" => "CA"
            ]
        ];
        */
        $this->rest->addresses = $customerRequest['addresses'];
        $this->rest->password = config('shopify.customer_password');
        $this->rest->password_confirmation = config('shopify.customer_password');
        $this->rest->send_email_welcome = false;
        $this->rest->save(
            true, // Update Object
        );

        return $this->rest->toArray();
    }
}