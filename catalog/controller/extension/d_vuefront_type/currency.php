<?php

use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQL\Type\Scalar\BooleanType;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\Object\ObjectType;

class ControllerExtensionDVuefrontTypeCurrency extends Controller
{
    private $codename = "d_vuefront";

    public function query()
    {
        return array(
                'currency' => array(
                    'type' => new ListType($this->getCurrencyType()),
                    'resolve' => function ($store, $args) {
                        return $this->load->controller('extension/'.$this->codename.'/currency/currency');
                    }
                )
            );
    }

    public function mutation()
    {
        return array(
            'editCurrency'  => array(
                'type'    => new ListType($this->getCurrencyType()),
                'args'    => array(
                    'code'       => array(
                        'type' => new StringType(),
                    )
                ),
                'resolve' => function ($store, $args) {
                    return $this->load->controller('extension/'.$this->codename.'/currency/edit', $args);
                }
            )
        );
    }

    public function getCurrencyType() {
        return new ObjectType(array(
            'name' => 'Currency',
            'description' => 'Currency',
            'fields' => array(
                'title' => new StringType(),
                'code' => new StringType(),
                'symbol_left' => new StringType(),
                'symbol_right' => new StringType(),
                'active' => new BooleanType()
            )
        ));
    }
}
