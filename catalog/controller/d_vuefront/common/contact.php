<?php

class ControllerDVuefrontCommonContact extends Controller {
    public function send($args) {
        $this->load->language('information/contact');

        $mail = new Mail($this->config->get('config_mail_engine'));
        $mail->parameter = $this->config->get('config_mail_parameter');
        $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
        $mail->smtp_username = $this->config->get('config_mail_smtp_username');
        $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
        $mail->smtp_port = $this->config->get('config_mail_smtp_port');
        $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

        $mail->setTo($this->config->get('config_email'));
        $mail->setFrom($this->config->get('config_email'));
        $mail->setReplyTo($args['email']);
        $mail->setSender(html_entity_decode($args['name'], ENT_QUOTES, 'UTF-8'));
        $mail->setSubject(html_entity_decode(sprintf($this->language->get('email_subject'), $args['name']), ENT_QUOTES, 'UTF-8'));
        $mail->setText($args['message']);
        $mail->send();

        return array(
            "status" => true
        );
    }

    public function get() {
        $data['store'] = $this->config->get('config_name');
		$data['address'] = nl2br($this->config->get('config_address'));
		$data['telephone'] = $this->config->get('config_telephone');
		$data['fax'] = $this->config->get('config_fax');
		$data['open'] = nl2br($this->config->get('config_open'));
        $data['comment'] = $this->config->get('config_comment');
        $data['email'] = $this->config->get('config_email');

        $data['locations'] = array();
        
        return $data;
    }
}