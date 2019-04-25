<?php

class ControllerExtensionDVuefrontStoreCompare extends Controller
{
    private $codename = "d_vuefront";

    public function get($args)
    {
        $this->load->model("extension/".$this->codename."/compare");
        $compare = array();
        $results = $this->model_extension_d_vuefront_compare->getCompare();

        foreach ($results as $product_id) {
            $compare[] = $this->vfload->data('store/product/get', array('id' => $product_id));
        }

        return $compare;
    }

    public function add($args)
    {
        $this->load->model("extension/".$this->codename."/compare");
        $this->request->post['product_id'] = $args['id'];

        $this->model_extension_d_vuefront_compare->addCompare($args['id']);


        return $this->get(array());
    }

    public function remove($args)
    {
        $this->load->model("extension/".$this->codename."/compare");
        $this->model_extension_d_vuefront_compare->deleteCompare($args['id']);

        return $this->fegett(array());
    }
}
