<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthorizeUserByEmailRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class TokenController extends Controller
{
    /**
     * @param AuthorizeUserByEmailRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createToken(AuthorizeUserByEmailRequest $request): JsonResponse
    {
        $user = $this->getUserByRequest($request);
        $deviceName = 'baseApiRequest';

        // remove old non-used tokens
        $user->tokens()
            ->where('name', $deviceName)
            ->whereNull('last_used_at')
            ->delete();

        // define base abilities
        $abilities = [
            'api:access'
        ];

        $newToken = $user->createToken($deviceName, $abilities);
        $response = [
            'access_token' => $newToken->plainTextToken
        ];

        return response()->json($response);

    }


    public function deleteToken(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        $user->currentAccessToken()->delete();

        $response = [
            'success' => true,
            'message' =>__('auth.tokenDeleted')
        ];
        return response()->json($response);
    }


    private function getUserByRequest(AuthorizeUserByEmailRequest $request):?User
    {
        return $this->getUserByEmailAndPassword($request->email, $request->password);
    }

    private function getUserByEmailAndPassword(string $email, string $password):?User
    {
        /** @var User $user */
        $user = User::where('email', $email)->first();

        throw_if(!$user,
            ValidationException::withMessages([
                'email' => [__('auth.failed')]
            ])
        );

        throw_if(!Hash::check($password, $user->password),
            ValidationException::withMessages([
                'email' => [__('auth.failed')]
            ])
        );

        throw_if(!$user->hasVerifiedEmail(),
            ValidationException::withMessages([
                'email' => [__('auth.emailNotVerified')]
            ])
        );

        return $user;
    }

}
