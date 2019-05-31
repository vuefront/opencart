<?php
class ModelDVuefrontCompare extends Model
{
    public function getCompare()
    {
        $result = array();

        if (!empty($this->session->data['compare'])) {
            $result = $this->session->data['compare'];
        }

        return $result;
    }

    public function addCompare($product_id)
    {
        $this->load->model('catalog/product');

        $product_info = $this->model_catalog_product->getProduct($product_id);

        if ($product_info) {
            if (!isset($this->session->data['compare'])) {
                $this->session->data['compare'] = array();
            }
            if (!in_array($product_id, $this->session->data['compare'])) {
                if (count($this->session->data['compare']) >= 4) {
                    array_shift($this->session->data['compare']);
                }
                $this->session->data['compare'][] = (int)$product_id;
            }
        }
    }

    public function deleteCompare($product_id)
    {
        if (!empty($this->session->data['compare'])) {
            $key = array_search($product_id, $this->session->data['compare']);

            if ($key !== false) {
                unset($this->session->data['compare'][$key]);
            }
        }
    }
}
