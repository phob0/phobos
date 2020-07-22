<?php

namespace Phobos\Framework\App\Traits;

trait PhobosController
{
    public function apiSuccess($extra = [])
    {
        return [
            'success' => true,
            'extra' => $extra
        ];
    }

    public function apiError($message = 'A server error has occurred', $extra = [])
    {
        cdd($message);
        return response([
            'errors' => true,
            'message' => $message,
            'extra' => $extra
        ], 500);
    }
}
