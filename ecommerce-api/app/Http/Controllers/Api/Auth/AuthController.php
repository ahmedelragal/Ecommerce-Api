<?php

namespace App\Http\Controllers\Api\Auth;

use App\Customs\Services\EmailVerificationService;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserManagement\LoginRequest;
use App\Http\Requests\UserManagement\RegisterationRequest;
use App\Http\Requests\UserManagement\ResendEmailRequest;
use App\Http\Requests\UserManagement\VerifyEmailRequest;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private EmailVerificationService $service) {}

    public function login(LoginRequest $request)
    {
        $token = auth()->attempt($request->validated());
        if ($token) {
            return $this->responseWithToken($token, auth()->user());
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Invalid credentials',
            ], 401);
        }
    }
    public function register(RegisterationRequest $request)
    {
        $user = User::create($request->except(['role', 'password_confirmation']));

        if ($user) {
            // Assign role based on request input or default to 'customer'
            $role = $request->input('role', 'customer');
            $user->assignRole($role);

            $this->service->sendVerificationLink($user);
            $token = auth()->login($user);
            return $this->responseWithToken($token, $user);
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Error occurred while creating user',
            ], 500);
        }
    }
    public function verifyUserEmail(VerifyEmailRequest $request)
    {
        return $this->service->verifyEmail($request->email, $request->token);
    }
    public function resendEmail(ResendEmailRequest $request)
    {
        return $this->service->resendLink($request->email);
    }
    public function responseWithToken($token, $user)
    {
        return response()->json([
            'status' => 'success',
            'user' => $user,
            'access_token' => $token,
            'type' => 'bearer'
        ], 201);
    }
}
