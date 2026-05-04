<?php

namespace App\Http\Controllers\Api;

use App\Enums\OtpType;
use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RefreshTokenRequest;
use App\Http\Requests\Auth\SendPasswordOtpRequest;
use App\Http\Requests\Auth\SignupRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Models\User;
use App\Services\Auth\JwtTokenService;
use App\Services\Auth\OtpService;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function signup(SignupRequest $request, OtpService $otpService): JsonResponse
    {
        $validated = $request->validated();

        $result = DB::transaction(function () use ($validated, $otpService): array {
            $userType = UserType::from($validated['user_type']);

            $user = User::create([
                'type' => $userType,
                'username' => $validated['username'],
                'email' => $validated['email'],
                'is_verified' => false,
                'password' => $validated['password'],
            ]);

            $this->createProfileFor($user, $userType);

            $otp = $otpService->issue($user, OtpType::Signup);

            return compact('user', 'otp');
        });

        $otpService->deliver($result['user'], $result['otp']);

        return response()->json([
            'message' => 'Account created successfully. Please verify the OTP sent to your email.',
            'data' => [
                'email' => $result['user']->email,
                'type' => OtpType::Signup->value,
            ],
        ], 201);
    }

    public function login(LoginRequest $request, JwtTokenService $jwtTokenService): JsonResponse
    {
        $validated = $request->validated();

        $user = User::query()->where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'The provided credentials are incorrect.',
            ], 422);
        }

        if (is_null($user->email_verified_at) || ! $user->is_verified) {
            return response()->json([
                'message' => 'Your email address must be verified before logging in.',
            ], 403);
        }

        return response()->json([
            'message' => 'Logged in successfully.',
            'data' => array_merge(
                $jwtTokenService->issueTokenPair($user),
                [
                    'user' => $this->userPayload($user),
                ],
            ),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $user = $request->user('api');

        $user->forceFill([
            'token_version' => $user->token_version + 1,
        ])->save();

        auth('api')->logout();

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }

    public function refresh(RefreshTokenRequest $request, JwtTokenService $jwtTokenService): JsonResponse
    {
        return response()->json([
            'message' => 'Token refreshed successfully.',
            'data' => $jwtTokenService->refresh($request->validated('refresh_token')),
        ]);
    }

    public function verifyOtp(VerifyOtpRequest $request, OtpService $otpService): JsonResponse
    {
        $validated = $request->validated();

        $user = User::query()->where('email', $validated['email'])->first();

        if (! $user) {
            return response()->json([
                'message' => 'The OTP is invalid or expired.',
            ], 422);
        }

        $type = OtpType::from($validated['type']);
        $otp = $otpService->verify($user, $type, $validated['code']);

        if (! $otp) {
            return response()->json([
                'message' => 'The OTP is invalid or expired.',
            ], 422);
        }

        if ($type === OtpType::Signup) {
            $user->forceFill([
                'is_verified' => true,
                'email_verified_at' => now(),
            ])->save();

            return response()->json([
                'message' => 'OTP verified successfully.',
            ]);
        }

        $resetToken = Password::broker()->createToken($user);

        return response()->json([
            'message' => 'OTP verified successfully.',
            'data' => [
                'reset_token' => $resetToken,
                'email' => $user->email,
            ],
        ]);
    }

    public function sendEmail(SendPasswordOtpRequest $request, OtpService $otpService): JsonResponse
    {
        $user = User::query()->where('email', $request->validated('email'))->first();

        if ($user) {
            $otp = $otpService->issue($user, OtpType::PasswordReset);
            $otpService->deliver($user, $otp);
        }

        return response()->json([
            'message' => 'If the email exists, an OTP has been sent.',
            'data' => [
                'type' => OtpType::PasswordReset->value,
            ],
        ]);
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $status = Password::broker()->reset(
            [
                'email' => $validated['email'],
                'token' => $validated['token'],
                'password' => $validated['password'],
                'password_confirmation' => $validated['password_confirmation'],
            ],
            function (User $user, string $password): void {
                $user->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                    'token_version' => $user->token_version + 1,
                ])->save();

                event(new PasswordReset($user));
            },
        );

        if ($status !== Password::PASSWORD_RESET) {
            return response()->json([
                'message' => 'The password reset token is invalid or expired.',
            ], 422);
        }

        return response()->json([
            'message' => 'Password changed successfully.',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function userPayload(User $user): array
    {
        return [
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'is_verified' => $user->is_verified,
            'type' => $user->type?->value,
        ];
    }

    private function createProfileFor(User $user, UserType $userType): void
    {
        match ($userType) {
            UserType::Student => $user->studentProfile()->create([]),
            UserType::Customer => $user->customerProfile()->create([]),
            UserType::Company => $user->companyProfile()->create([]),
            default => null,
        };
    }
}