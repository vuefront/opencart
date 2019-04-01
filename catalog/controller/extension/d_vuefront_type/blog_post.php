<?php

use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\FloatType;
use Youshido\GraphQL\Type\Scalar\StringType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\IdType;


class ControllerExtensionDVuefrontTypeBlogPost extends Controller
{
    private $codename = "d_vuefront";

    public function query()
    {
        $this->load->model('extension/module/' . $this->codename);

        return array(
            'post' => array(
                'type' => $this->postType(),
                'args' => array(
                    'id' => array(
                        'type' => new IntType()
                    )
                ),
                'resolve' => function ($store, $args) {
                    return $this->load->controller('extension/' . $this->codename . '/blog_post/post', $args);
                }
            ),
            'postsList' => array(
                'type' => $this->model_extension_module_d_vuefront->getPagination($this->postType()),
                'args' => array(
                    'page' => array(
                        'type' => new IntType(),
                        'defaultValue' => 1
                    ),
                    'size' => array(
                        'type' => new IntType(),
                        'defaultValue' => 10
                    ),
                    'filter' => array(
                        'type' => new StringType(),
                        'defaultValue' => ''
                    ),
                    'search' => array(
                        'type' => new StringType(),
                        'defaultValue' => ''
                    ),
                    'category_id' => array(
                        'type' => new IntType(),
                        'defaultValue' => 0
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
                    return $this->load->controller('extension/' . $this->codename . '/blog_post/postList', $args);
                }
            )
        );
    }

    public function mutation()
    {
        return array(
            'addBlogPostReview' => array(
                'type' => $this->postType(),
                'args' => array(
                    'id' => new IntType(),
                    'rating' => new FloatType(),
                    'author' => new StringType(),
                    'content' => new StringType()
                ),
                'resolve' => function ($store, $args) {
                    return $this->load->controller('extension/' . $this->codename . '/blog_post/addReview', $args);
                }
            )
        );
    }

    private function postType()
    {
        return new ObjectType(array(
            'name' => 'Post',
            'description' => 'Blog Post',
            'fields' => array(
                'id' => new IdType(),
                'title' => new StringType(),
                'shortDescription' => new StringType(),
                'description' => new StringType(),
                'image' => new StringType(),
                'imageLazy' => new StringType(),
                'reviews' => array(
                    'type' => new ListType(
                        new ObjectType(
                            array(
                                'name' => 'postReview',
                                'fields' => array(
                                    'author' => new StringType(),
                                    'author_email' => new StringType(),
                                    'content' => new StringType(),
                                    'created_at' => new StringType(),
                                    'rating' => new FloatType()
                                )
                            )
                        )
                    ),
                    'resolve' => function ($parent, $args) {
                        return $this->load->controller('extension/' . $this->codename . '/blog_post/postReview', array(
                            'parent' => $parent,
                            'args' => $args
                        ));
                    }
                ),
            )
        ));
    }
}