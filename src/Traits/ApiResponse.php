<?php

namespace Laradium\Laradium\Traits;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

trait ApiResponse
{
    /**
     * @param callable $func
     * @return \Illuminate\Http\JsonResponse
     */
    public function response(callable $func)
    {
        try {
            return call_user_func($func);
        } catch (\Exception $e) {
            if ($e instanceof ModelNotFoundException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not found.'
                ], 404);
            }

            if ($e instanceof ValidationException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors'  => $e->errors()
                ], 422);
            }

            logger()->error($e);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error! Please, try again.'
            ], 503);
        }
    }

    /**
     * @param $data
     * @return mixed
     */
    protected function parseData($data)
    {
        if ($data instanceof LengthAwarePaginator) {
            return [
                'items' => $data->getCollection(),
                'meta'  => [
                    'per_page'     => $data->perPage(),
                    'current_page' => $data->currentPage(),
                    'last_page'    => $data->lastPage(),
                    'total'        => $data->total()
                ]
            ];
        }

        return $data;
    }
}