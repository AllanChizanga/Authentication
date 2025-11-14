<?php

namespace App\Http\Controllers;

use App\Actions\Drivers\RegisterDriverAction;
use App\DataTransferObjects\CreateDriverDataDTO;
use App\Http\Requests\DriverRequest;
use App\Http\Requests\StoreDriverRequest;
use App\Models\Driver;
use App\Services\DriverService;
use Illuminate\Http\JsonResponse;

class DriverController extends Controller
{
    protected DriverService $driverService;

    public function __construct(DriverService $driverService)
    {
        $this->driverService = $driverService;
    }
    public function register_driver(DriverRequest $request){
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $dataDTO = CreateDriverDataDTO::fromRequest($data);
        
        $driver = $this->driverService->createDriver($dataDTO);

        if ($driver) {
            return response()->json([
                'message' => 'Driver registered successfully',
                'driver' => $driver
            ], 201);
        } else {
            return response()->json([
                'message' => 'Failed to register driver'
            ], 500);
        }
    
    }

    public function update_driver(DriverRequest $request){
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $dataDTO = CreateDriverDataDTO::fromRequest($data);
        
        $driver = $this->driverService->updateDriver($dataDTO);

        if ($driver) {
            return response()->json([
                'message' => 'Driver updated successfully',
                'driver' => $driver
            ], 200);
        } else {
            return response()->json([
                'message' => 'Failed to update driver'
            ], 500);
        }
    
    }

    public function get_driver(){
        $user = auth()->user()->driver();
    
        if ($user) {
            return response()->json([
                'message' => 'Driver retrieved successfully',
                'driver' => $user
            ], 200);
        } else {
            return response()->json([
                'message' => 'Driver not found'
            ], 404);
        }
    
    }
    
}