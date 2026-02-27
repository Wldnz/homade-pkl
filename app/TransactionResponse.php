<?php

namespace App;

use App\Http\Resources\PaginationResource;
use App\Http\Resources\TransactionResource;
use Illuminate\Pagination\LengthAwarePaginator;

trait TransactionResponse
{
    public function formatPaginationData(
        LengthAwarePaginator $rawData
    ) {
        return [
            'pagination' => [
                'current_page' => $rawData->currentPage(),
                'first_page' => 1,
                'last_page' => $rawData->lastPage(),
                'per_page' => $rawData->perPage(),
                'url' => [
                    // 'first_page' =>  $rawData->firstPageUrl,
                    // 'last_page' => $rawData->lastPage(),
                    'next_page' => $rawData->nextPageUrl(),
                    'previus_page' => $rawData->previousPageUrl(),
                ],
                'total' => $rawData->total(),
            ],
            'data' => TransactionResource::collection($rawData),
        ];
    }
}
