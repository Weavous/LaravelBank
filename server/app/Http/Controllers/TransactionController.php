<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Response;

use Symfony\Component\HttpFoundation\Response as ResponseCode;

use App\Models\Wallet;
use App\Models\Transaction;
use App\Models\User;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    public function withdraw(Request $request, Response $response): JsonResponse
    {
        $data = $request->only('code', 'amount');

        $validator = Validator::make($data, [
            'code' => 'required|string',
            'amount' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => json_decode($validator->errors()->toJson())], 200)->setEncodingOptions(JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }

        $wallet = Wallet::where("code", $request->code)->first();

        if (Transaction::where("wallet_id", $wallet->id)->get()->sum("amount") < $request->amount) {
            return response()->json([
                "error" => "Não há saldo suficiente para realizar essa operação."
            ], ResponseCode::HTTP_OK);
        }

        Transaction::create([
            "wallet_id" => $wallet->id,
            "amount" => $request->amount * (-1)
        ]);

        $response = [
            "wallet" => [
                "id" => $wallet->id,
                "users_id" => $wallet->users_id,
                "code" => $wallet->code,
                "deleted_at" => $wallet->deleted_at,
                "created_at" => $wallet->created_at,
                "updated_at" => $wallet->updated_at,
            ],
            "amount" => $wallet->transactions->sum("amount"),
            "transactions" => $wallet->transactions,
            "user" => User::find($wallet->users_id)
        ];

        return response()->json($response, 201);
    }

    public function deposit(Request $request, Response $response): JsonResponse
    {
        $data = $request->only('code', 'amount');

        $validator = Validator::make($data, [
            'code' => 'required|string',
            'amount' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => json_decode($validator->errors()->toJson())], 200)->setEncodingOptions(JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }

        $wallet = Wallet::where("code", $request->code)->first();

        Transaction::create([
            "wallet_id" => $wallet->id,
            "amount" => $request->amount
        ]);

        $response = [
            "wallet" => [
                "id" => $wallet->id,
                "users_id" => $wallet->users_id,
                "code" => $wallet->code,
                "deleted_at" => $wallet->deleted_at,
                "created_at" => $wallet->created_at,
                "updated_at" => $wallet->updated_at,
            ],
            "amount" => $wallet->transactions->sum("amount"),
            "transactions" => $wallet->transactions,
            "user" => User::find($wallet->users_id)
        ];

        return response()->json($response, 201);
    }
}
