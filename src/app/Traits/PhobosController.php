<?php

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
        return response([
            'errors' => true,
            'message' => $message,
            'extra' => $extra
        ], 500);
    }
}
