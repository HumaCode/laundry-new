<?php

namespace App\Services\Contracts;

interface LaporanServiceInterface
{
    /**
     * Get processed reports data based on filters.
     *
     * @param array $filters
     * @return array
     */
    public function getReportsData(array $filters): array;
}
