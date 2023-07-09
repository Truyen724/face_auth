<?php

namespace App\Http\Controllers\Api;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\User\UserService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use  App\Http\Requests\Auth\RegisterRequest;
use App\Http\Controllers\Api\BaseApiController;
class AuthController extends BaseApiController
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    private $_userService;
    public function __construct(UserService $userService)
    {
        $this->_userService = $userService;
        $this->middleware('auth:api', ['except' => ['login','regist']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $data = $request->all();
        $user = $this->_userService->login($data['email'], $data['password']);
        if($user==null)
        {
            return response()->json(['message' => 'Sai tài khoản mật khẩu', 'code'=>'401']);
        }
        else
        {
            $token = auth()->guard('api')->login($user);
            // $token = auth()->login($user);
            return $this->respondWithToken($token);
        }
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    public function regist(Request $request)
    {
        try {
            $data = $request->all();
            $data['id'] = generateRandomString();
            $data['salt'] = generateRandomString(5);
            $data['status'] = STATUS_ACTIVE;
            $data['password'] = md5($data['password'] . $data['salt']);
            if ($this->_userService->create($data)) {
                $user = $this->_userService->getById($data['id']);
                // if ($user) {
                //     $token = auth()->guard('api')->login($user);
                //     // $token = auth()->login($user);
                //     if (!$token) {
                //         return $this->errorResponse('Unauthorized', Response::HTTP_UNAUTHORIZED);
                //     }
                //     return $this->successResponse([
                //         'access_token' => $token,
                //         'expires_in' => env('JWT_TTL')
                //     ], __('auth.login-success'));
                // }
                // return $this->errorResponse(__('user.register-fail'), Response::HTTP_NOT_FOUND);

            return response()->json([
                'access_token' => $user['name'],
                'token_type' => 'bearer',
            ]);
            }
            return $this->errorResponse(__('user.register-fail'), Response::HTTP_NOT_FOUND);
            // return response()->json([
            //     'access_token' => $data['password'],
            //     'token_type' => 'bearer',
            // ]);
        } catch (\Throwable $th) {

            return $this->errorResponse(__('auth.error'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
