<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Services\Api\DashboardService;
use Illuminate\Http\JsonResponse;

class DashboardApiController extends ApiController
{
    protected DashboardService $service;

    /**
     * DashboardApiController constructor.
     */
    public function __construct(DashboardService $service)
    {
        $this->service = $service;
    }

    /**
     * Get dashboard data for the authenticated user.
     */
    public function index(): JsonResponse
    {
        return $this->service->getDashboardData();
    }
}
