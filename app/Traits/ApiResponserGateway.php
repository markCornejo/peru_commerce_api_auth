<?php
namespace App\Traits;

use Illuminate\Http\Response;

trait ApiResponserGateway {

    /**
     * Buila a success json format
     *
     * @param  string|array $data
     * @param  boolean $ok // adicional para el ok
     * @param  int $code
     * @param  string $message
     * @return Illuminate\Http\JsonResponse
     */
    public function successResponse($ok, $data = [], string $message = '', $code = Response::HTTP_OK) {
        return response()->json(['ok' => $ok, 'data' => $data, 'message' => $message], $code);
    }


    /**
     * Return an error in Json format
     *
     * @param  string $message
     * @param  int $code
     * @return Illuminate\Http\JsonResponse
     */
    public function errorResponse($message, $code) {
        return response()->json(['error', $message, 'code' => $code], $code);
    }


}
