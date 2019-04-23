<?php

use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQL\Type\Scalar\BooleanType;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\Object\ObjectType;

class ControllerExtensionDVuefrontTypeLanguage extends Controller
{
    private $codename = "d_vuefront";

    public function query()
    {
        return array(
                'language' => array(
                    'type' => new ListType($this->getLanguageType()),
                    'resolve' => function ($store, $args) {
                        return $this->load->controller('extension/'.$this->codename.'/language/language');
                    }
                )
            );
    }

    public function mutation()
    {
        return array(
            'editLanguage'  => array(
                'type'    => new ListType($this->getLanguageType()),
                'args'    => array(
                    'code'       => array(
                        'type' => new StringType(),
                    )
                ),
                'resolve' => function ($store, $args) {
                    return $this->load->controller('extension/'.$this->codename.'/language/edit', $args);
                }
            )
        );
    }

    public function getLanguageType() {
        return new ObjectType(array(
            'name' => 'Language',
            'description' => 'Language',
            'fields' => array(
                'name' => new StringType(),
                'code' => new StringType(),
                'image' => new StringType(),
                'active' => new BooleanType()
            )
        ));
    }
}
