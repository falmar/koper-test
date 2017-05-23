<?php
/**
 * Created by PhpStorm.
 * User: falmar
 * Date: 5/15/17
 * Time: 5:52 PM
 */

namespace KoperTest\Controllers;


class BaseController
{
    protected function acceptsJSON(string $header): bool
    {
        return (
            strlen($header) > 0 && (
                strpos('application/json', $header) !== false ||
                strpos('*/*', $header) !== false
            )
        );
    }

    protected function isJSON(string $header): bool
    {
        return strlen($header) > 0 && strpos('application/json', $header) !== false;
    }

    protected function parseOrder(string $sort): array
    {
        if (!$sort) {
            return [
                'sortField' => '',
                'sortOrder' => ''
            ];
        }

        list($sortField, $sortOrder) = explode(',', $sort);

        return [
            'sortField' => $sortField ?? '',
            'sortOrder' => $sortOrder ?? ''
        ];
    }
}
