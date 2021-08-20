<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

use Tests\TestCase;

use Symfony\Component\HttpFoundation\Response;

class JWTAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_should_not_create_user_if_email_already_exists(): void
    {
        $this->post("/api/register", [
            "name" => "John Doe",
            "email" => "example@email.com",
            "password" => "john-doe-password"
        ]);

        $response = $this->post("/api/register", [
            "name" => "Jane Doe",
            "email" => "example@email.com",
            "password" => "jane-doe-password"
        ]);

        $this->assertEquals($response->status(), Response::HTTP_OK);
    }

    public function test_it_should_not_create_user_if_password_is_smaller_than_six_characters(): void
    {
        $response = $this->post("/api/register", [
            "name" => "Wesley Flôres",
            "email" => "wesleyfloresterres@email.com",
            "password" => "pass"
        ]);

        $this->assertEquals($response->status(), Response::HTTP_OK);
    }

    public function test_it_should_create_an_user(): void
    {
        $response = $this->post("/api/register", [
            "name" => "Wesley Flôres",
            "email" => "wesleyfloresterres@email.com",
            "password" => "password"
        ]);

        $response->assertJsonStructure([
            "success",
            "message",
            "data" => [
                "id",
                "name",
                "email",
                "updated_at",
                "created_at"
            ]
        ]);

        $this->assertEquals($response->status(), Response::HTTP_CREATED);
    }

    public function test_it_should_authenticate_an_recently_created_user(): void
    {
        $this->post("/api/register", [
            "name" => "Ethan",
            "email" => "ethan@email.com",
            "password" => "ethan-password"
        ]);

        $response = $this->post("/api/auth/login", [
            "email" => "ethan@email.com",
            "password" => "ethan-password"
        ]);

        $response->assertJsonStructure([
            "success",
            "token"
        ]);

        $this->assertEquals($response->status(), Response::HTTP_OK);
    }

    public function test_it_should_create_an_user_and_be_get_api_config_information_on_authenticate_request(): void
    {
        $this->post("api/register", [
            "name" => "Anthonieta",
            "email" => "anthonieta@email.com",
            "password" => "anthonieta-password"
        ]);

        $response = $this->post("api/auth/login", [
            "email" => "anthonieta@email.com",
            "password" => "anthonieta-password"
        ]);

        $response = $this->get("api/config", [
            "Authorization" => sprintf("Bearer %s", json_decode($response->getContent())->token)
        ]);

        $this->assertEquals($response->status(), Response::HTTP_OK);
    }

    public function test_it_should_login_an_user_and_logout_it_and_after_that_the_token_must_to_be_rejected()
    {
        $this->post("api/register", [
            "name" => "Bia",
            "email" => "bia@email.com",
            "password" => "bia-password"
        ]);

        $login = $this->post("api/auth/login", [
            "email" => "bia@email.com",
            "password" => "bia-password"
        ]);

        $logout = $this->post("api/auth/logout", [
            "Authorization" => sprintf("Bearer %s", json_decode($login->getContent())->token),
            "token" => json_decode($login->getContent())->token
        ]);

        $resource = $this->get("api/config", [
            "Authorization" => sprintf("Bearer %s", json_decode($login->getContent())->token),
        ]);

        $this->assertEquals($login->status(), Response::HTTP_OK);
        $this->assertEquals($logout->status(), Response::HTTP_OK);
        $this->assertEquals($resource->status(), Response::HTTP_UNAUTHORIZED);

        $this->assertTrue(json_decode($login->getContent())->success);
        $this->assertTrue(json_decode($logout->getContent())->success);
    }
}
