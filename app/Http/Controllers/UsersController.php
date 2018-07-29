<?php

namespace App\Http\Controllers;

use App\User;
use Faker\Provider\Uuid;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request)
    {
        return response()
            ->json($request->user());
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|min:3|max:64',
            'email' => 'required|email|max:64|unique:users,email'
        ]);

        $item = new User();
        $item->name = $request->input('name');
        $item->email = $request->input('email');
        $item->api_token = Uuid::uuid();
        $item->ip = $request->ip();
        $item->save();

        return response()
            ->json($item, 201);
    }
}