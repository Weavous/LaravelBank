<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

use Tests\TestCase;

use Symfony\Component\HttpFoundation\Response;

class WalletControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_should_create_an_wallet()
    {
        $user = $this->post("/api/register", [
            "name" => "John Doe",
            "email" => "example@email.com",
            "password" => "john-doe-password"
        ]);

        $auth = $this->post("/api/auth/login", [
            "email" => "example@email.com",
            "password" => "john-doe-password"
        ]);

        $data = [
            "email" => json_decode($user->getContent())->data->email
        ];

        $headers = [
            "Authorization" => sprintf("Bearer %s", json_decode($auth->getContent())->token)
        ];

        $wallet = $this->post("api/wallets", $data, $headers);

        $this->assertEquals($wallet->status(), Response::HTTP_CREATED);
    }

    public function test_it_should_delete_an_wallet()
    {
        $this->assertTrue(true);
    }

    public function test_it_should_show_an_wallet()
    {
        $this->assertTrue(true);
    }

    public function test_it_should_display_informations_about_a_wallet()
    {
        $this->assertTrue(true);
    }

    public function test_it_should_update_an_wallet()
    {
        $this->assertTrue(true);
    }
}
