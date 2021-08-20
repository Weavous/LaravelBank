<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

use Symfony\Component\HttpFoundation\Response;

use App\Models\Wallet;

use Tests\TestCase;

class TransactionControllerTest extends TestCase
{
    use RefreshDatabase;

    private Wallet $wallet;

    private string $jwt;

    private const USER = [
        "name" => "John Doe",
        "email" => "example@email.com",
        "password" => "john-doe-password"
    ];

    public function setUp(): void
    {
        parent::setUp();

        $user = $this->post("/api/register", static::USER);

        $auth = $this->post("/api/auth/login", static::USER);

        $token = json_decode($auth->getContent())->token;

        $data = ["email" => json_decode($user->getContent())->data->email];

        $headers = ["Authorization" => sprintf("Bearer %s", $token)];

        $wallet = $this->post("api/wallets", $data, $headers);

        $this->assertEquals($wallet->status(), Response::HTTP_CREATED);

        $this->jwt = $token;

        $this->wallet = new Wallet((array) json_decode($wallet->getContent()));
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function test_it_should_not_allow_withdraw_when_there_is_no_enough_money()
    {
        $headers = ["Authorization" => sprintf("Bearer %s", $this->jwt)];

        $deposit = $this->post("/api/deposit", ["code" => $this->wallet->code, "amount" => 400], $headers);

        $this->assertEquals($deposit->status(), Response::HTTP_CREATED);

        $post = $this->post("/api/withdraw", ["code" => $this->wallet->code, "amount" => 600], $headers);

        $this->assertEquals($post->status(), Response::HTTP_OK);
    }

    public function test_it_should_withdraw_when_there_is_enough_money()
    {
        $headers = ["Authorization" => sprintf("Bearer %s", $this->jwt)];

        $deposit = $this->post("/api/deposit", ["code" => $this->wallet->code, "amount" => 800], $headers);

        $this->assertEquals($deposit->status(), Response::HTTP_CREATED);

        $post = $this->post("/api/withdraw", ["code" => $this->wallet->code, "amount" => 200], $headers)->assertJsonStructure([
            "wallet" => [
                "id",
                "users_id",
                "code",
                "deleted_at",
                "created_at",
                "updated_at"
            ],
            "amount",
            "transactions" => [],
            "user" => [
                "id",
                "name",
                "email",
                "email_verified_at",
                "created_at",
                "updated_at"
            ]
        ]);

        $this->assertEquals(json_decode($post->getContent())->amount, 600);
    }

    public function test_it_should_increment_wallet_when_make_an_deposit()
    {
        $headers = ["Authorization" => sprintf("Bearer %s", $this->jwt)];

        $post = $this->post("/api/deposit", ["code" => $this->wallet->code, "amount" => 1000], $headers);

        $this->assertEquals($post->status(), Response::HTTP_CREATED);

        $this->assertEquals(json_decode($post->getContent())->amount, 1000);
    }
}
