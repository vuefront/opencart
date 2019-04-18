<?php

class ControllerExtensionDVuefrontCompare extends Controller
{
    private $codename = "d_vuefront";

    public function compare($args)
    {
        $this->load->model("extension/".$this->codename."/compare");
        $compare = array();
        $results = $this->model_extension_d_vuefront_compare->getCompare();

        foreach ($results as $product_id) {
            $compare[] = $this->load->controller('extension/'.$this->codename.'/product/product', array('id' => $product_id));
        }

        return $compare;
    }

    public function addToCompare($args)
    {
        $this->load->model("extension/".$this->codename."/compare");
        $this->request->post['product_id'] = $args['id'];

        $this->model_extension_d_vuefront_compare->addCompare($args['id']);


        return $this->compare(array());
    }

    public function removeCompare($args)
    {
        $this->load->model("extension/".$this->codename."/compare");
        $this->model_extension_d_vuefront_compare->deleteCompare($args['id']);

        return $this->compare(array());
    }
}
