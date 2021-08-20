<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use App\Models\User;

use Illuminate\Support\Str;

use Illuminate\Support\Facades\Validator;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->only('email');

        $validator = Validator::make($data, [
            'email' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => json_decode($validator->errors()->toJson())], 200)->setEncodingOptions(JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }

        $user = User::where("email", $request->email)->first();

        $wallet = Wallet::create([
            "code" => Str::uuid(),
            "users_id" => $user->id
        ]);

        return response()->json($wallet, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
