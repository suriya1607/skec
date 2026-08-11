<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateSettingsRequest;
use App\Services\SettingService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class SettingsController extends Controller
{
    use ApiResponse;

    public function __construct(private SettingService $settingService) {}

    public function public(): JsonResponse
    {
        return $this->success($this->settingService->getPublicSettings());
    }

    public function index(\Illuminate\Http\Request $request): JsonResponse
    {
        return $this->success($this->settingService->getAllSettings($request->user()));
    }

    public function update(UpdateSettingsRequest $request): JsonResponse
    {
        $this->settingService->updateSettings($request->input('settings'), $request->user());
        return $this->success($this->settingService->getAllSettings($request->user()), 'Settings updated');
    }
}
