<?php

class ControllerDVuefrontStoreReview extends Controller
{
    private $codename = "d_vuefront";

    public function add($args)
    {
        $this->load->model('catalog/review');

        $reviewData = array(
            'name' => $args['author'],
            'text' => $args['content'],
            'rating' => $args['rating']
        );

        $this->model_catalog_review->addReview($args['id'], $reviewData);

        return $this->vfload->data('store/product/get', $args);
    }

    public function get($data)
    {
        $this->load->model('catalog/review');
        $product = $data['parent'];

        $results = $this->model_catalog_review->getReviewsByProductId($product['id']);

        $reviews = array();

        foreach ($results as $result) {
            $reviews[] = array(
                'author' => $result['author'],
                'author_email' => '',
                'content' => nl2br($result['text']),
                'rating' => (float)$result['rating'],
                'created_at' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),

            );
        }

        return $reviews;
    }
}
