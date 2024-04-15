<?php

namespace app\service\shopify\action\store;

use app\service\shopify\ShopifyApiService;

class Products extends ShopifyApiService
{
    public function getProductList($first)
    {
        $query = <<<QUERY
query getProducts(\$first: Int) {
  products(first: \$first) {
      nodes {
        title
        id
        availableForSale
        featuredImage{
          url
        }
        priceRange{
          maxVariantPrice{
            currencyCode
            amount
          }
          minVariantPrice{
            currencyCode
            amount
          }
        }
        compareAtPriceRange{
           maxVariantPrice{
            currencyCode
            amount
          }
          minVariantPrice{
            currencyCode
            amount
          }
        }
        
        }        
      }
    
}
QUERY;

        return $this->send($query,['first'=>$first]);
    }
}