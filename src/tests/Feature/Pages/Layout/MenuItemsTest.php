<?php

// TODO: add tests for menu items
test('example', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});
