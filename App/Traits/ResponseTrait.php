<?php

namespace App\Traits;

trait ResponseTrait {
    public function sendResponse($data=null, $message = '', $error = false, $status = 200) {
        $response = [
            'data' => $data,
            'message' => $message,
            'error' => $error,
            'status' => $status
        ];


        http_response_code($status);
        echo json_encode($response);
    }
}