<?php

class ControllerExtensionDVuefrontCategory extends Controller
{
    public function products($args)
    {
        $this->load->model('catalog/product');
        $this->load->model('tool/image');

        if(in_array($args['sort'], array('sort_order', 'model', 'quantity', 'price', 'date_added'))) {
            $args['sort'] = 'p.'.$args['sort'];
        } else if(in_array($args['sort'], array('name'))) {
            $args['sort'] = 'pd.'.$args['sort'];
        }


        $products = array();

        $filter_data = array(
            'filter_category_id' => $args['category_id'],
            'filter_filter' => $args['filter'],
            'sort' => $args['sort'],
            'order' => $args['order'],
            'start' => ($args['page'] - 1) * $args['size'],
            'limit' => $args['size']
        );

        $product_total = $this->model_catalog_product->getTotalProducts($filter_data);

        $results = $this->model_catalog_product->getProducts($filter_data);

        foreach ($results as $result) {
            $width=$this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width');
            $height=$this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height');
            if ($result['image']) {
                $image = $this->model_tool_image->resize($result['image'], $width, $height);
                $imageLazy = $this->model_tool_image->resize($result['image'], 10, ceil(10* $height/$width));
            } else {
                $image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
                $imageLazy = $this->model_tool_image->resize('placeholder.png', 10, 6);
            }

            if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
                $price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
            } else {
                $price = false;
            }

            if ((float)$result['special']) {
                $special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
            } else {
                $special = false;
            }

            if ($this->config->get('config_tax')) {
                $tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price'], $this->session->data['currency']);
            } else {
                $tax = false;
            }

            $products[] = array(
                'id' => (int)$result['product_id'],
                'thumb' => $image,
                'thumbLazy' => $imageLazy,
                'name' => $result['name'],
                'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
                'price' => $price,
                'special' => $special,
                'tax' => $tax,
                'minimum' => $result['minimum'] > 0 ? (int)$result['minimum'] : 1,
                'rating' => (int)$result['rating']
            );
        }

        return array(
            'content' => $products,
            'first' => $args['page'] === 1,
            'last' => $args['page'] === ceil($product_total / $args['size']),
            'number' => (int)$args['page'],
            'numberOfElements' => count($products),
            'size' => (int)$args['size'],
            'totalPages' => (int)ceil($product_total / $args['size']),
            'totalElements' => (int)$product_total,
        );
    }

    public function product($args) {

    }
}
