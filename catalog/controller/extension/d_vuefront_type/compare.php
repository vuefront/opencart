<?php

use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\ListType\ListType;

class ControllerExtensionDVuefrontTypeCompare extends Controller
{
    private $codename = "d_vuefront";

    public function query()
    {
        $product_type = $this->load->controller('extension/'.$this->codename.'_type/product/productType');
        return array(
                'compare' => array(
                    'type' => new ListType($product_type),
                    'resolve' => function ($store, $args) {
                        return $this->load->controller('extension/'.$this->codename.'/compare/compare');
                    }
                )
            );
    }

    public function mutation()
    {
        $product_type = $this->load->controller('extension/'.$this->codename.'_type/product/productType');
        return array(
            'addToCompare'  => array(
                'type'    => new ListType($product_type),
                'args'    => array(
                    'id'       => array(
                        'type' => new IntType(),
                    )
                ),
                'resolve' => function ($store, $args) {
                    return $this->load->controller('extension/'.$this->codename.'/compare/addToCompare', $args);
                }
            ),
            'removeCompare' => array(
                'type'    => new ListType($product_type),
                'args'    => array(
                    'id' => array(
                        'type' => new StringType()
                    )
                ),
                'resolve' => function ($store, $args) {
                    return $this->load->controller('extension/'.$this->codename.'/compare/removeCompare', $args);
                }
            )
        );
    }
}
