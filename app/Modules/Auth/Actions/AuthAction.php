<?php

namespace App\Modules\Auth\Actions;

use App\Models\User;
use App\Modules\Auth\Dtos\UserRegistrationDto;
use App\Modules\Auth\Resource\UserResource;
use App\Modules\Transaction\Models\Wallet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthAction
{

  public function create(UserRegistrationDto $dto): array
  {
    return  DB::transaction(
      function () use ($dto) {
        $userDto = $dto->toArray();
        $userDto['password'] = Hash::make($dto->password);

        $user = User::create($userDto);

        //create wallet for user
        $wallet = Wallet::create([
          'user_id' => $user->id,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
          'token'  => $token,
          'user'   => $user,
        ];
      }
    );
  }

  public function authenticate($credentials): array
  {
    $user = User::where('email', $credentials['email'])->first();

    if (!$user || !Hash::check($credentials['password'], $user->password)) {
      throw ValidationException::withMessages([
        'email' => ['Invalid credentials provided.'],
      ]);
    }

    $token = $user->createToken("auth_token")->plainTextToken;

    return [
          'token'  => $token,
          'user'   => $user,
        ];
  }
}
