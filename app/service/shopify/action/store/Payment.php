<?php

namespace app\service\shopify\action\store;

use app\service\shopify\ShopifyApiService;

class Payment extends ShopifyApiService
{
    public function createCheckOut($lineItems)
    {

        $query = <<<QUERY
mutation(\$lineItems:CheckoutCreateInput!,\$first:Int) {
  checkoutCreate(input: \$lineItems) {
    checkout {
       id
       webUrl
       lineItems(first: \$first) {
           nodes {
             title
             quantity
         }
       }
    }
    checkoutUserErrors{
        code
        field
        message
       }
  }
}

QUERY;
        $variables = [
            'lineItems'=>['lineItems'=>$lineItems],
            'first'=>count($lineItems)
        ];
        return $this->send($query,$variables);
    }

    //绑定用户
    //更新结算
    public function updateCheckout($checkoutId,$shippingAddress)
    {
        $query = <<<QUERY
mutation checkoutShippingAddressUpdateV2(\$shippingAddress: MailingAddressInput!, \$checkoutId: ID!) {
  checkoutShippingAddressUpdateV2(shippingAddress: \$shippingAddress, checkoutId: \$checkoutId) {
    checkoutUserErrors {
      code
      field
      message
    }
    checkout {
      id
      shippingAddress {
        firstName
        lastName
        address1
        province
        country
        zip
      }
    }
  }
}

QUERY;
        $variables = [
            'shippingAddress'=>$shippingAddress,
            'checkoutId'=>$checkoutId,
        ];
        return $this->send($query,$variables);
    }
    //查运费
    public function queryShippingFee($checkoutId)
    {
        $query = <<<QUERY
query(\$checkoutId:ID!){
  node(id: \$checkoutId) {
    ... on Checkout {
      id
      webUrl
      availableShippingRates {
        ready
        shippingRates {
          handle
          price {
            amount
          }
          title
        }
      }
    }
  }
}

QUERY;
        $variables = [
            'checkoutId'=>$checkoutId
        ];

       return $this->send($query,$variables);

    }

    //设置运费
    public function setCheckoutShoppingFee($checkoutId,$shippingRateHandle)
    {
        $query = <<<QUERY
mutation checkoutShippingLineUpdate(\$checkoutId: ID!, \$shippingRateHandle: String!) {
  checkoutShippingLineUpdate(checkoutId: \$checkoutId, shippingRateHandle: \$shippingRateHandle) {
    checkout {
      id
    }
    checkoutUserErrors {
      code
      field
      message
    }
  }
}

QUERY;
        $variables = [
            'checkoutId'=>$checkoutId,
            'shippingRateHandle'=>$shippingRateHandle
        ];

      return $this->send($query,$variables);
    }
    //创建用户
    public function createCustomer($customer)
    {
        $query = <<<QUERY
mutation customerCreate(\$input: CustomerCreateInput!) {
  customerCreate(input: \$input) {
    customer {
      id
      acceptsMarketing
      createdAt
      displayName
    }
    customerUserErrors {
     code
     field
     message
    }
    userErrors {
      field
      message
    }
  }
}
QUERY;
        $variables = [
            "input"=>$customer
        ];
       return $this->send($query,$variables);
    }
    //获取用户token
    public function getCustomerAccessToken($customer)
    {
        $query = <<<QUERY
mutation customerAccessTokenCreate(\$input: CustomerAccessTokenCreateInput!) {
  customerAccessTokenCreate(input: \$input) {
    customerAccessToken {
      accessToken
      expiresAt
    }
    customerUserErrors {
     code
     field
     message
    }
    userErrors {
      field
      message
    }
  }
}
QUERY;

        $variables = [
            'input'=>[
                'email'=>$customer['email'],
                'password'=>$customer['password'],
            ],
        ];

       return  $this->send($query,$variables);
    }
    //关联用户token 和checkouID
    public function associateCustomerWithCheckout($checkoutId,$customerAccessToken)
    {
        $query = <<<QUERY
mutation associateCustomerWithCheckout(\$checkoutId: ID!, \$customerAccessToken: String!) {
  checkoutCustomerAssociateV2(checkoutId: \$checkoutId, customerAccessToken: \$customerAccessToken) {
    checkout {
      id
    }
    checkoutUserErrors {
      code
      field
      message
    }
    customer {
      id
    }
  }
}

QUERY;
        $variables = [
            'checkoutId'=>$checkoutId,
            'customerAccessToken'=>$customerAccessToken
        ];
       return $this->send($query,$variables);
    }
}