<?php

it('can access the login page', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

it('can access the register page', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});
