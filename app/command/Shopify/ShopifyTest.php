<?php
declare (strict_types = 1);

namespace app\command\Shopify;

use Shopify\Auth\FileSessionStorage;
use Shopify\Clients\Storefront;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use Shopify\Context;
use think\helper\Arr;

class ShopifyTest extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('shopify:test')
            ->setDescription('the shopifytest command');
    }

    protected function execute(Input $input, Output $output)
    {
        try {
            // 指令输出
            $this->setUp();
            #1.查找产品
            //$variantId = $this->getProductsList();
            $variantId = 'gid://shopify/ProductVariant/47310414577965';
            #2.创建结账
            //$checkoutId = $this->createCheckOut($variantId);
            $checkoutId = 'gid://shopify/Checkout/a66e899f49c98ffb9f28dca4210fd0b8?key=941bd5b6eab2d1b81d7d65331e707a41';
            #3.更新结账
            //$this->updateCheckout($checkoutId);
            #3.1:查询运费
            //$shippingRateHandle = $this->queryShippingFee($checkoutId);
            $shippingRateHandle = 'shopify-Heavy%20Goods%20Shipping-18.00';
            #3.2:设置运费
            //$this->setCheckoutShoppingFee($checkoutId,$shippingRateHandle);
            #4.关联客户
            #4.1: 获取用户令牌
            $customer = [
                'acceptsMarketing' => true,
                'email' => 'jesiahshaffer@gmail.com',
                'firstName' => "l",
                'lastName' => 'fx',
                'password' => 'ddhd@2024',
                'phone' => '+8617386037442'
            ];
            //$customerId = $this->createCustomer($customer);
            $customerId = 'gid://shopify/Customer/7895980048685';
            # 获取customerAccesstoken
            //$accessToken = $this->getCustomerAccessToken($customer);
            $customerAccessToken = '148b73cb85a66efb3ae3df3f212ccd8d';
            #4.2 将客户与账号关联起来
            //$this->associateCustomerWithCheckout($checkoutId,$customerAccessToken);
            #5.完成结账
            //$this->completeCheckout($checkoutId);
            #5.1: 创建支付
            $amount = '43.0';
            //$this->createPayment($checkoutId,$amount);
        }catch (\Exception $e){
            dump($e);
        }
    }

    public function setUp()
    {
        $path = runtime_path('/tmp/php_sessions');

        $scopes = [
            'unauthenticated_read_product_listings',
            'unauthenticated_read_product_tags',
            'unauthenticated_read_checkouts',
            'unauthenticated_write_checkouts'
        ];
        Context::initialize(
            apiKey: env('SHOPIFY_API_KEY'),
            apiSecretKey: env('SHOPIFY_API_SECRET'),
            scopes: $scopes,
            hostName: env('SHOPIFY_APP_HOST_NAME'),
            sessionStorage: new FileSessionStorage($path),
            apiVersion: '2023-04',
            isEmbeddedApp: true,
            isPrivateApp: false,
        );
        $this->id = 'gid://shopify/Checkout/1';
        $access_token = env('SHOPIFY_API_STOREFRONT_TOKEN');
        $domain = env('SHOPIFY_APP_HOST_NAME');
        $this->client = new Storefront($domain, $access_token);
        //$this->client = new Graphql($domain,$access_token);
    }

    //获取产品
    public function getProductsList($first = 2)
    {
        $query = <<<QUERY
query {
  products(first:$first) {
    edges {
      node {
        variants(first: $first) {
          edges {
            node {
              id
            }
          }
        }
      }
    }
  }
}
QUERY;

        $res = $this->send($query);
        dd($res);
        $data = $res['data'];
        $products = $data['products'];
        $edges = $products['edges'];
        $edge = Arr::first($edges);
        $node = $edge['node'];
        $variants = $node['variants'];
        $edges2 = Arr::first($variants['edges']);
        $varinetId = $edges2['node']['id'];
        return $varinetId;
    }

    //创建结账
    public function createCheckOut($variantId)
    {
        $items = [
            [
                'variantId'=>$variantId,
                'quantity'=>1,
            ]
        ];
        $query = <<<QUERY
mutation(\$lineItems:CheckoutCreateInput!) {
  checkoutCreate(input: \$lineItems) {
    checkout {
       id
       webUrl
       lineItems(first: 1) {
           nodes {
             title
             quantity
         }
       }
    }
  }
}

QUERY;
        $variables = [
            'lineItems'=>['lineItems'=>$items]
        ];
        $res = $this->send($query,$variables);
        dump($res);
        $this->checkoutId = $res['data']['checkoutCreate']['checkout']['id'] ?? '';
        dd($this->checkoutId);
    }

    //更新结算
    public function updateCheckout($checkoutId)
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
            'shippingAddress'=>[
                'lastName'=>'Doe',
                'firstName'=>'John',
                'address1'=>'123 Test Street',
                'province'=>'QC',
                'country'=>'Canada',
                'zip'=>'H3K0X2',
                'city'=>'Montreal',
            ],
            'checkoutId'=>$checkoutId,
        ];
        $res = $this->send($query,$variables);

        dd($res);


    }

    //查询运费
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

        $res = $this->send($query,$variables);
        dd($res);

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

        $res = $this->send($query,$variables);
        dd($res);

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
        $res = $this->send($query,$variables);
        dd($res);
    }


    //获取用户令牌
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

        $res = $this->send($query,$variables);
        dd($res);

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
        $res = $this->send($query,$variables);
        dd($res);

    }

    //完成结账
    public function completeCheckout($checkoutId)
    {
        $query = <<<QUERY
query (\$checkoutId:ID!){
  node(id:\$checkoutId) {
    ... on Checkout {
      id
      webUrl
      totalPrice{
        amount
       currencyCode
      }
    }
  }
}

QUERY;
        $variables = [
            'checkoutId'=>$checkoutId
        ];

        $res = $this->send($query,$variables);
        dd($res);

    }

    //创建支付
    public function createPayment($checkoutId,$amount)
    {
        try {
            $session_id = session_create_id();

//            Context::$API_VERSION = "2023-04";
//            $session = new Session($session_id, env('SHOPIFY_APP_HOST_NAME'), true, "test");
//            $session->setAccessToken(env('SHOPIFY_API_ADMIN_TOKEN'));

            $header = [
                'Content-Type'=> 'application/json',
                'X-Shopify-Access-Token'=>env('SHOPIFY_API_ADMIN_TOKEN')
            ];
            $cookie = [];
            $session = Utils::loadCurrentSession($header, $cookie, true);
            dd($session);
            $payment = new Payment($session);
            $payment->checkout_id = $checkoutId;
            //$payment->checkout_id = '7972465ae1127aedc3f9d4f19f6b47ff';
            $payment->request_details = [
                "ip_address" => "124.71.203.150",
                "accept_language" => "en-US,en;q=0.8,fr;q=0.6",
                "user_agent" => "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.98 Safari/537.36"
            ];
            $payment->amount = $amount;
            $payment->session_id = $session->getId();
            $payment->unique_token = uniqid();
            $payment->save(
                true, // Update Object
            );

            dump($payment->id);


            return success();
        }catch (\Exception $e){
            dump($e);
        }




    }

    protected function send($query,$variables = [])
    {
        $params = ['query'=>$query];
        if(!empty($variables)) $params['variables'] = json_encode($variables);
        $response = $this->client->query($params);
        return $response->getDecodedBody();

    }



}
