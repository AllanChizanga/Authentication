<?php

namespace App\Http\Controllers;

use App\Actions\Drivers\RegisterDriverAction;
use App\DataTransferObjects\CreateDriverDataDTO;
use App\Http\Requests\StoreDriverRequest;
use App\Models\Driver;
use App\Services\DriverService;
use Illuminate\Http\JsonResponse;

class DriverController extends Controller
{
    protected $driverService;

    public function __construct(DriverService $driverService)
    {
        $this->driverService = $driverService;
    }
    public function store(StoreDriverRequest $request, RegisterDriverAction $registerDriverAction): JsonResponse {
        $driverData = CreateDriverDataDTO::fromRequest($request->validated());
        
        $driver = $registerDriverAction->execute($driverData);

        return response()->json([
            'message' => 'Driver registered successfully',
            'driver' => $driver
        ], 201);
    }

    public function update(StoreDriverRequest $request,Driver $driver, DriverService $driverService): JsonResponse {
        $driverData = CreateDriverDataDTO::fromRequest($request->validated());
        
        $updatedDriver = $driverService->updateDriver($driver, $driverData);

        return response()->json([
            'message' => 'Driver updated successfully',
            'driver' => $updatedDriver
        ]);
    }
}