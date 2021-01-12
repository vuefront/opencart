<?php
use SemVer\version;

class ControllerExtensionDVuefrontStoreProduct extends Controller
{
    private $codename = "d_vuefront";

    public function getList($args)
    {
        $this->load->model('catalog/product');
        $this->load->model('extension/' . $this->codename . '/product');
        $this->load->model('tool/image');

        if (in_array($args['sort'], array('sort_order', 'model', 'quantity', 'price', 'date_added'))) {
            $args['sort'] = 'p.' . $args['sort'];
        } elseif (in_array($args['sort'], array('name'))) {
            $args['sort'] = 'pd.' . $args['sort'];
        }

        $products = array();

        $filter_data = array(
            'filter_category_id' => $args['category_id'],
            'filter_filter' => $args['filter'],
            'sort' => $args['sort'],
            'order' => $args['order']
        );

        if($args['size'] != '-1') {
            $filter_data['start'] = ($args['page'] - 1) * $args['size'];
            $filter_data['limit'] = $args['size'];
        }

        if (!empty($args['search'])) {
            $filter_data['filter_name'] = $args['search'];
            $filter_data['filter_tag'] = $args['search'];
            $filter_data['filter_description'] = $args['search'];
        }

        if (!empty($args['special'])) {
            $filter_data['filter_special'] = true;
        }

        if (!empty($args['ids'])) {
            $filter_data['filter_product_ids'] = $args['ids'];
        }

        $product_total = $this->model_extension_d_vuefront_product->getTotalProducts($filter_data);

        $results = $this->model_extension_d_vuefront_product->getProducts($filter_data);

        if ($args['size'] == -1 && $product_total != 0) {
            $args['size'] = $product_total;
        } else if($args['size'] == -1 && $product_total == 0) {
            $args['size'] = 1;
        }

        foreach ($results as $result) {
            $products[] = $this->get(array('id' => $result['product_id']));
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

    public function get($args)
    {
        $this->load->model('catalog/product');
        $this->load->model('extension/'.$this->codename.'/product');
        $this->load->model('tool/image');
        $product_info = $this->model_catalog_product->getProduct($args['id']);

        if (!$product_info) {
            return array();
        }
        $product_keyword = $this->model_extension_d_vuefront_product->getProductKeyword($args['id']);

        if(!empty($product_keyword['keyword'])) {
            $keyword = $product_keyword['keyword'];
        } else {
            $keyword = '';
        }

        if(VERSION >= "3.0.0.0"){
            $width = $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width');
            $height = $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height');
            $popup_width = $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width');
            $popup_height = $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height');
            $description_length = $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length');
        }
        else if (VERSION >= '2.2.0.0') {
            $width = $this->config->get($this->config->get('config_theme') . '_image_product_width');
            $height = $this->config->get($this->config->get('config_theme') . '_image_product_height');
            $popup_width = $this->config->get($this->config->get('config_theme') . '_image_popup_width');
            $popup_height = $this->config->get($this->config->get('config_theme') . '_image_popup_height');
            $description_length = $this->config->get($this->config->get('config_theme') . '_product_description_length');
        } else {
            $width = $this->config->get('config_image_product_width');
            $height = $this->config->get('config_image_product_height');
            $popup_width = $this->config->get('config_image_popup_width');
            $popup_height = $this->config->get('config_image_popup_height');
            $description_length = $this->config->get('config_product_description_length');
        }

        if ($product_info['image']) {
            $image = $this->model_tool_image->resize($product_info['image'], $width, $height);
            $imageLazy = $this->model_tool_image->resize($product_info['image'], 10, ceil(10 * $height / $width));
            $imageBig = $this->model_tool_image->resize($product_info['image'], $popup_width, $popup_height);
        } else {
            $image = '';
            $imageLazy = '';
            $imageBig = '';
        }

        if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
            $price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
        } else {
            $price = '';
        }

        if ((float)$product_info['special']) {
            $special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
        } else {
            $special = '';
        }

        if ($this->config->get('config_review_status')) {
            $rating = (int)$product_info['rating'];
        } else {
            $rating = '';
        }

        if ($product_info['quantity'] <= 0) {
            $stock = false;
        } elseif ($this->config->get('config_stock_display')) {
            $stock = true;
        } else {
            $stock = true;
        }

        return array(
            'id' => $product_info['product_id'],
            'name' => html_entity_decode($product_info['name'], ENT_QUOTES, 'UTF-8'),
            'description' => html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8'),
            'shortDescription' => utf8_substr(trim(strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8'))), 0, $description_length) . '..',
            'price' => $price,
            'special' => $special,
            'model' => $product_info['model'],
            'image' => $image,
            'imageLazy' => $imageLazy,
            'imageBig' => $imageBig,
            'stock' => $stock,
            'rating' => (float)$rating,
            'images' => $this->vfload->resolver('store/product/images'),
            'products' => $this->vfload->resolver('store/product/relatedProducts'),
            'attributes' => $this->vfload->resolver('store/product/attribute'),
            'reviews' => $this->vfload->resolver('store/review/get'),
            'options' => $this->vfload->resolver('store/product/option'),
            'url' => $this->vfload->resolver('store/product/url'),
            'keyword' => $keyword,
            'meta' => array(
                'title' => html_entity_decode($product_info['meta_title'], ENT_QUOTES, 'UTF-8'),
                'description' => html_entity_decode($product_info['meta_description'], ENT_QUOTES, 'UTF-8'),
                'keyword' => html_entity_decode($product_info['meta_keyword'], ENT_QUOTES, 'UTF-8'),
            )
        );
    }

    public function relatedProducts($data)
    {
        $this->load->model('catalog/product');
        $product_info = $data['parent'];
        $results = $this->model_catalog_product->getProductRelated($product_info['id']);

        $products = array();

        foreach ($results as $result) {
            $products[] = $this->get(array('id' => $result['product_id']));
        }

        return $products;
    }

    public function attribute($data)
    {
        $this->load->model('catalog/product');
        $product_info = $data['parent'];

        $attributes = array();

        $attribute_groups = $this->model_catalog_product->getProductAttributes($product_info['id']);

        foreach ($attribute_groups as $attribute_group) {
            foreach ($attribute_group['attribute'] as $attribute) {
                $attributes[] = array(
                    'name' => $attribute['name'],
                    'options' => array($attribute['text'])
                );
            }
        }

        return $attributes;
    }

    public function option($data)
    {
        $this->load->model('catalog/product');
        $product_info = $data['parent'];
        $results = $this->model_catalog_product->getProductOptions($product_info['id']);
        $options = array();

        foreach ($results as $option) {
            $product_option_value_data = array();

            foreach ($option['product_option_value'] as $option_value) {
                if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
                    $product_option_value_data[] = array(
                        'id' => $option_value['product_option_value_id'],
                        'name' => $option_value['name'],
                    );
                }
            }

            $options[] = array(
                'id' => $option['product_option_id'],
                'values' => $product_option_value_data,
                'name' => $option['name'],
                'type' => $option['type']
            );
        }

        return $options;
    }

    public function images($data)
    {
        $this->load->model('catalog/product');
        $this->load->model('tool/image');
        $product_info = $data['parent'];

        $results = $this->model_catalog_product->getProductImages($product_info['id']);

        $images = array();

        foreach ($results as $result) {
            if(VERSION >= "3.0.0.0"){
                $width = $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width');
                $height = $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height');
                $popup_width = $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width');
                $popup_height = $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height');
            } else if (VERSION >= '2.2.0.0') {
                $width = $this->config->get($this->config->get('config_theme') . '_image_product_width');
                $height = $this->config->get($this->config->get('config_theme') . '_image_product_height');
                $popup_width = $this->config->get($this->config->get('config_theme') . '_image_popup_width');
                $popup_height = $this->config->get($this->config->get('config_theme') . '_image_popup_height');
            } else {
                $width = $this->config->get('config_image_product_width');
                $height = $this->config->get('config_image_product_height');
                $popup_width = $this->config->get('config_image_popup_width');
                $popup_height = $this->config->get('config_image_popup_height');
            }

            if ($product_info['image']) {
                $image = $this->model_tool_image->resize($result['image'], $width, $height);
                $imageLazy = $this->model_tool_image->resize($result['image'], 10, ceil(10 * $height / $width));
                $imageBig = $this->model_tool_image->resize($result['image'], $popup_width, $popup_height);
            } else {
                $image = $this->model_tool_image->resize('placeholder.png', $width, $height);
                $imageLazy = $this->model_tool_image->resize('placeholder.png', 10, 6);
                $imageBig = '';
            }

            $images[] = array(
                'image' => $image,
                'imageLazy' => $imageLazy,
                'imageBig' => $imageBig
            );
        }

        return $images;
    }

    public function url($data)
    {
        $product_info = $data['parent'];
        $result = $data['args']['url'];

        $result = str_replace("_id", $product_info['id'], $result);
        $result = str_replace("_name", $product_info['name'], $result);


        if ($product_info['keyword']) {
            $result = '/'.$product_info['keyword'];
        }

        return $result;
    }
}
