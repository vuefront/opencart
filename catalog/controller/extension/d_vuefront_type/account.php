<?php

use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\InputObject\InputObjectType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\Scalar\BooleanType;

class ControllerExtensionDVuefrontTypeAccount extends Controller
{
    private $codename = "d_vuefront";

    public function query()
    {
        return array();
    }

    public function mutation()
    {
        return array(
            'accountLogin'  => array(
                'type'    => $this->customerType(),
                'args'    => array(
                    'email'       => array(
                        'type' => new StringType(),
                    ),
                    'password' => array(
                        'type'         => new StringType()
                    )
                ),
                'resolve' => function ($store, $args) {
                    return $this->load->controller('extension/'.$this->codename.'/account/login', $args);
                }
            ),
            'accountLogout'  => array(
                'type'    => new ObjectType(
                    array(
                        'name' => 'LogoutResult',
                        'description' => 'LogoutResult',
                        'fields' => array(
                            'status' => new BooleanType()
                        )
                    )
                ),
                'resolve' => function ($store, $args) {
                    return $this->load->controller('extension/'.$this->codename.'/account/logout', $args);
                }
            ),
            'accountRegister' => array(
                'type'    => $this->customerType(),
                'args'    => array(
                    'customer'      => array(
                        'type' =>$this->customerInputType()
                    )
                ),
                'resolve' => function ($store, $args) {
                    return $this->load->controller('extension/'.$this->codename.'/account/register', $args);
                }
            ),
            'accountEdit' => array(
	            'type'    => $this->customerType(),
	            'args'    => array(
		            'customer'      => array(
			            'type' =>$this->customerInputType()
		            )
	            ),
	            'resolve' => function ($store, $args) {
		            return $this->load->controller('extension/'.$this->codename.'/account/edit', $args);
	            }
            ),
            'accountCheckLogged' => array(
                'type' => new ObjectType(
                    array(
                        'name' => 'LoggedResult',
                        'description' => 'LoggedResult',
                        'fields' => array(
                            'status' => new BooleanType(),
                            'customer' => $this->customerType()
                        )
                    )
                ),
                'resolve' => function ($parent, $args) {
                    return $this->load->controller('extension/'.$this->codename.'/account/isLogged', $args);
                }
            )
        );
    }

    public function customerType()
    {
        return new ObjectType(
            array(
                'name'        => 'Customer',
                'description' => 'Customer',
                'fields'      => array(
                    'id'    => new StringType(),
                    'firstName' => new StringType(),
                    'lastName' => new StringType(),
                    'email' => new StringType()
                )
            )
        );
    }

    private function customerInputType()
    {
        return new InputObjectType(
            array(
                'name'        => 'CustomerInput',
                'description' => 'CustomerInput',
                'fields'      => array(
                    'firstName' => new StringType(),
                    'lastName' => new StringType(),
                    'email' => new StringType(),
                    'password' => new StringType()
                )
            )
        );
    }

    public function cartProductType()
    {
        return new ObjectType(
            array(
                'name'        => 'CartProduct',
                'description' => 'CartProduct',
                'fields'      => array(
                    'key'      => new StringType(),
                    'product'  => $this->load->controller('extension/'.$this->codename.'_type/product/productType'),
                    'quantity' => new IntType(),
                    'total'    => new StringType()
                )
            )
        );
    }
}
