<?php

namespace App\Customs\Services;

use Illuminate\Support\Facades\Hash;

class PasswordService
{

    private function validateCurrent($currentPass)
    {
        if (!password_verify($currentPass, auth()->user()->password)) {
            response()->json([
                'status' => 'failed',
                'message' => 'Password doesnt match current password',
            ])->send();
            exit;
        }
    }
    public function changePassword($data)
    {
        $this->validateCurrent($data['current_password']);
        $updatePassword = auth()->user()->update([
            'password' => Hash::make($data['password']),
        ]);
        if ($updatePassword) {
            return response()->json([
                'status' => 'success',
                'message' => 'Password changed successfully',
            ]);
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Error occured while changing password',
            ]);
        }
    }
}
