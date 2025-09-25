<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Actions\AuthAction;
use App\Modules\Auth\Dtos\UserRegistrationDto;
use App\Modules\Auth\Requests\LoginRequest;
use App\Modules\Auth\Requests\RegisterRequest;
use App\Modules\Auth\Resource\UserResource;
use Illuminate\Http\Request;

class AuthController extends Controller
{
  use \App\Traits\ApiResponsesTrait;

  public function __construct(private readonly AuthAction $authAction) {}

  /**
   * Register
   */
  public function register(RegisterRequest $request)
  {
    $validatedBody = $request->validated();

    $result = $this->authAction->create(UserRegistrationDto::fromArray($validatedBody));

    return $this->successApiResponse("Registered successfully", [
      "token" => $result['token'],
      "user" => new UserResource($result['user'])
    ], 201);
  }

  /**
   * Login with rate limiting
   */
  public function login(LoginRequest $request)
  {
    $credentials = $request->validated();

    $result = $this->authAction->authenticate($credentials);

    return $this->successApiResponse('Login successfully', [
      "token" => $result['token'],
      "user " => new UserResource($result['user'])
    ], 200);
  }

  /**
   * Logout
   */
  public function logout(Request $request)
  {
    $request->user()->tokens()->delete();

    return $this->successApiResponse("Logged out successfully", null, 200);
  }
}
