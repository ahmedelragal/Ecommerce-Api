<?php

namespace App\Http\Controllers\Api\Profile;

use App\Customs\Services\PasswordService;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserManagement\PasswordChangeRequest;
use Illuminate\Http\Request;

class PasswordController extends Controller
{
    public function __construct(private PasswordService $service) {}


    public function changePassword(PasswordChangeRequest $request)
    {
        return $this->service->changePassword($request->validated());
    }
}
