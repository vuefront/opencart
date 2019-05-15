<?php

class ControllerExtensionDVuefrontCommonFile extends Controller {
	public function upload($args) {
		$this->load->language('tool/upload');

		if (!empty($args['file']['name']) && is_file($args['file']['tmp_name'])) {
			// Sanitize the filename
			$filename = basename(preg_replace('/[^a-zA-Z0-9\.\-\s+]/', '', html_entity_decode($args['file']['name'], ENT_QUOTES, 'UTF-8')));

			// Validate the filename length
			if ((utf8_strlen($filename) < 3) || (utf8_strlen($filename) > 64)) {
				throw new Exception($this->language->get('error_filename'));
			}

			// Allowed file extension types
			$allowed = array();

			$extension_allowed = preg_replace('~\r?\n~', "\n", $this->config->get('config_file_ext_allowed'));

			$filetypes = explode("\n", $extension_allowed);

			foreach ($filetypes as $filetype) {
				$allowed[] = trim($filetype);
			}

			if (!in_array(strtolower(substr(strrchr($filename, '.'), 1)), $allowed)) {
				throw new Exception($this->language->get('error_filetype'));
			}

			// Allowed file mime types
			$allowed = array();

			$mime_allowed = preg_replace('~\r?\n~', "\n", $this->config->get('config_file_mime_allowed'));

			$filetypes = explode("\n", $mime_allowed);

			foreach ($filetypes as $filetype) {
				$allowed[] = trim($filetype);
			}

			if (!in_array($args['file']['type'], $allowed)) {
				throw new Exception($this->language->get('error_filetype'));
			}

			// Check to see if any PHP files are trying to be uploaded
			$content = file_get_contents($args['file']['tmp_name']);

			if (preg_match('/\<\?php/i', $content)) {
				throw new Exception($this->language->get('error_filetype'));
				$json['error'] = $this->language->get('error_filetype');
			}

			// Return any upload error
			if ($args['file']['error'] != UPLOAD_ERR_OK) {
				throw new Exception($this->language->get('error_upload_' . $args['file']['error']));
			}
		} else {
			throw new Exception($this->language->get('error_upload'));
		}

		$file = $filename . '.' . token(32);

		move_uploaded_file($args['file']['tmp_name'], DIR_UPLOAD . $file);

		$this->load->model('tool/upload');

		$code = $this->model_tool_upload->addUpload($filename, $file);

		return array(
			'code' => $code
		);
	}
}