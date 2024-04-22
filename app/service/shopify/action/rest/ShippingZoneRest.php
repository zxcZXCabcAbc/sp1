<?php

namespace app\service\shopify\action\rest;

class ShippingZoneRest extends RestBase
{
    /**
     * @return array
     * @doc https://shopify.dev/docs/api/admin-rest/2024-04/resources/shippingzone#get-shipping-zones
     */
    public function get_shipping_zones()
    {
       $zone = $this->rest;
       return $zone::all($this->session);
    }
}