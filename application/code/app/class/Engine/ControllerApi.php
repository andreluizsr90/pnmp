<?php
namespace App\Engine;

class ControllerApi {

	private $response;

	protected function setResponse($data, $isSucess = true, $message = null) {
		$this->response = [
            'data' => $data,
            'success' => $isSucess,
            'message' => $message
        ];
	}

	public function getResponse() {
		header('Content-Type: application/json; charset=utf-8');
		return json_encode($this->response);
	}
}
