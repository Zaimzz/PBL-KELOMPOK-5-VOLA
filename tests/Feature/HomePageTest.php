<?php

it('can access the home page', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});
