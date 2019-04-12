<?php

use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\FloatType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\IdType;


class ControllerExtensionDVuefrontTypePage extends Controller
{
    private $codename = "d_vuefront";

    public function query()
    {
        $this->load->model('extension/module/' . $this->codename);

        return array(
            'page' => array(
                'type' => $this->pageType(),
                'args' => array(
                    'id' => array(
                        'type' => new IntType()
                    )
                ),
                'resolve' => function ($store, $args) {
                    return $this->load->controller('extension/' . $this->codename . '/page/page', $args);
                }
            ),
            'pagesList' => array(
                'type' => $this->model_extension_module_d_vuefront->getPagination($this->pageType()),
                'args' => array(
                    'page' => array(
                        'type' => new IntType(),
                        'defaultValue' => 1
                    ),
                    'size' => array(
                        'type' => new IntType(),
                        'defaultValue' => 10
                    ),
                    'search' => array(
                        'type' => new StringType(),
                        'defaultValue' => ''
                    ),
                    'sort' => array(
                        'type' => new StringType(),
                        'defaultValue' => "sort_order"
                    ),
                    'order' => array(
                        'type' => new StringType(),
                        'defaultValue' => 'ASC'
                    )
                ),
                'resolve' => function ($store, $args) {
                    return $this->load->controller('extension/' . $this->codename . '/page/pageList', $args);
                }
            )
        );
    }

    private function pageType()
    {
        return new ObjectType(array(
            'name' => 'Page',
            'description' => 'Page',
            'fields' => array(
                'id' => new IdType(),
                'title' => new StringType(),
                'description' => new StringType(),
                'sort_order' => new IntType(),
            )
        ));
    }
}