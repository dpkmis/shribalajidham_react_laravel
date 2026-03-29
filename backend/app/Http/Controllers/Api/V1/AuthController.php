<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\V1\LoginRequest;
use App\Http\Requests\Api\V1\RegisterRequest;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use OpenApi\Annotations as OA;

class AuthController extends ApiBaseController
{
    /**
     * @OA\Post(
     *     path="/api/v1/auth/login",
     *     operationId="authLogin",
     *     tags={"Auth"},
     *     summary="Login and get bearer token",
     *     security={},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="admin@shribalajidham.com"),
     *             @OA\Property(property="password", type="string", example="password"),
     *             @OA\Property(property="device_name", type="string", example="web-browser")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Login successful"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="token", type="string"),
     *                 @OA\Property(property="token_type", type="string", example="Bearer"),
     *                 @OA\Property(property="user", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Invalid credentials"),
     *     @OA\Response(response=423, description="Account locked")
     * )
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            if ($user) {
                $user->incrementLoginAttempts();
            }
            return $this->unauthorized('Invalid email or password');
        }

        if ($user->is_locked) {
            return $this->error('Account is locked. Try again later.', 423);
        }

        if (!$user->is_active) {
            return $this->forbidden('Account is deactivated. Contact administrator.');
        }

        $user->resetLoginAttempts();
        $user->updateLastLogin($request->ip());

        $deviceName = $request->device_name ?? ($request->userAgent() ?: 'api-client');
        $token = $user->createToken($deviceName, ['*'], now()->addDays(30));

        $user->load('roles', 'property');

        return $this->success([
            'token' => $token->plainTextToken,
            'token_type' => 'Bearer',
            'expires_at' => $token->accessToken->expires_at?->toISOString(),
            'user' => new UserResource($user),
        ], 'Login successful');
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/register",
     *     operationId="authRegister",
     *     tags={"Auth"},
     *     summary="Register a new user",
     *     security={},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password","password_confirmation"},
     *             @OA\Property(property="name", type="string", example="Deepak Kumar"),
     *             @OA\Property(property="email", type="string", format="email", example="deepak@example.com"),
     *             @OA\Property(property="password", type="string", example="password123"),
     *             @OA\Property(property="password_confirmation", type="string", example="password123"),
     *             @OA\Property(property="phone", type="string", example="+919639066602"),
     *             @OA\Property(property="property_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(response=201, description="Registration successful"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'phone' => $request->phone,
            'property_id' => $request->property_id,
        ]);

        $token = $user->createToken($request->userAgent() ?: 'api-client');
        $user->load('roles', 'property');

        return $this->created([
            'token' => $token->plainTextToken,
            'token_type' => 'Bearer',
            'user' => new UserResource($user),
        ], 'Registration successful');
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/logout",
     *     operationId="authLogout",
     *     tags={"Auth"},
     *     summary="Logout and revoke current token",
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Logged out successfully"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return $this->success(null, 'Logged out successfully');
    }

    /**
     * @OA\Get(
     *     path="/api/v1/auth/profile",
     *     operationId="authProfile",
     *     tags={"Auth"},
     *     summary="Get authenticated user profile",
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="User profile"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function profile(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->load('roles', 'property');
        return $this->success(new UserResource($user));
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/refresh",
     *     operationId="authRefresh",
     *     tags={"Auth"},
     *     summary="Refresh token (revoke old, issue new)",
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Token refreshed"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function refresh(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->currentAccessToken()->delete();

        $token = $user->createToken($request->userAgent() ?: 'api-client', ['*'], now()->addDays(30));

        return $this->success([
            'token' => $token->plainTextToken,
            'token_type' => 'Bearer',
            'expires_at' => $token->accessToken->expires_at?->toISOString(),
        ], 'Token refreshed');
    }
}
