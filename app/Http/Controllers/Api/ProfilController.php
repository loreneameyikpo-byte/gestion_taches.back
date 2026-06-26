<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class ProfilController extends Controller
{
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();
        $validatedData = $request->validated();

       if (!empty($validatedData['password'])) {
    $validatedData['password'] = Hash::make($validatedData['password']);
} else {
    unset($validatedData['password']);
}
        $user->update($validatedData);

        return response()->json([
            'message' => 'Profil mis à jour avec succès.',
            'user' => new UserResource($user),
        ]);
    }
}
