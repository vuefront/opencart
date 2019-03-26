<?php

use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\IdType;


class ControllerExtensionDVuefrontTypeProduct extends Controller
{
    private $codename = "d_vuefront";

    public function query()
    {
        $this->load->model('extension/module/' . $this->codename);
        $productType = $this->model_extension_module_d_vuefront->getProductType();

        return [
            'productsList' => $this->productsType(),
            'product' => $productType
        ];
    }

    public function productsType()
    {
        $this->load->model('extension/module/' . $this->codename);
        $productType = $this->model_extension_module_d_vuefront->getProductType();

        return [
            'type' => $this->model_extension_module_d_vuefront->getPagination($productType),
            'args' => [
                'page' => [
                    'type' => new IntType(),
                    'defaultValue' => 1
                ],
                'size' => [
                    'type' => new IntType(),
                    'defaultValue' => $this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit')
                ],
                'filter' => [
                    'type' => new StringType(),
                    'defaultValue' => ''
                ],
                'category_id' => [
                    'type' => new IntType(),
                    'defaultValue' => null
                ],
                'sort' => [
                    'type' => new StringType(),
                    'defaultValue' => "sort_order"
                ],
                'order' => [
                    'type' => new StringType(),
                    'defaultValue' => 'ASC'
                ],
            ],
            'resolve' => function ($store, $args) {
                return $this->load->controller('extension/' . $this->codename . '/category/products', $args);
            }

        ];
    }
    public function productType()
    {
        $this->load->model('extension/module/' . $this->codename);
        $productType = $this->model_extension_module_d_vuefront->getProductType();

        return [
            'type' => $productType,
            'args' => [
                'id' => [
                    'type' => new IdType(),
                    'defaultValue' => null
                ]
            ],
            'resolve' => function ($store, $args) {
                return $this->load->controller('extension/' . $this->codename . '/category/product', $args);
            }

        ];
    }
}
