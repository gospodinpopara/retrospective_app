<?php

declare(strict_types=1);

namespace App\Utils;

use App\DTO\Pagination;

class ApiHelper
{
    public static function convertJsonLdPagination(array $response): Pagination
    {
        $currentPage = 1;
        $itemsPerPage = 0;
        $totalItems = $response['totalItems'] ?? 0;

        if (isset($response['view']['@id'])) {
            $query = parse_url($response['view']['@id'], \PHP_URL_QUERY);
            if ($query) {
                parse_str($query, $params);
                $currentPage = (int) ($params['page'] ?? 1);
                $itemsPerPage = (int) ($params['itemsPerPage'] ?? 0);
            }
        }

        $totalPages = $itemsPerPage > 0
            ? (int) ceil($totalItems / $itemsPerPage)
            : 1;

        return new Pagination(
            currentPage: $currentPage,
            itemsPerPage: $itemsPerPage,
            totalItems: $totalItems,
            totalPages: $totalPages,
        );
    }
}
