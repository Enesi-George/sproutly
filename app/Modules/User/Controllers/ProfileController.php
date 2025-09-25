<?php

namespace App\Modules\User\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Auth\Models\User;
use App\Modules\User\Requests\UserProfileRequest;
use App\Traits\ApiResponsesTrait;

class ProfileController extends Controller
{
    use ApiResponsesTrait;
    /**
     * get user profile.
     */
    public function me(Request $request)
    {
        return $this->successApiResponse("User profile fetched successfully", $request->user(), 200);
    }

    /**
     * Update user profile.
     */
    public function update(UserProfileRequest $request)
    {
        $validatedBody = $request->validated();
        $user = $request->user();
        $user->update($validatedBody);
        return $this->successApiResponse("User profile updated successfully", $user, 200);
    }
}
