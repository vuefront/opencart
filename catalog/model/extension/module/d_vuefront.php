<?php

class ModelExtensionModuleDVuefront extends Model
{
    private $codename = "d_vuefront";

    public function getResolvers()
    {
        $result = Array();
        $files = glob(DIR_APPLICATION . 'controller/extension/' . $this->codename . '_type/*.php', GLOB_BRACE);
        foreach ($files as $file) {
            $filename = basename($file, '.php');
            $output = $this->load->controller('extension/' . $this->codename . '_type/' . $filename . '/resolver');
            if ($output) {
                $result = array_merge($result, $output);
            }
        }

        return $result;
    }
}