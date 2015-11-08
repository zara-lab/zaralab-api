<?php

// API routes group
$app->group('/api', function() {

    // REST Members group
    $this->group('/member', function() {

        // List
        $this->get('/', 'ApiMemberController:listAction')->setName('api_member_list');

        // Show
        $this->get('/{id:[\d]+}', 'ApiMemberController:showAction')->setName('api_member_show');

        // Create
        $this->post('/', 'ApiMemberController:createAction')->setName('api_member_create');

        // Update
        $this->put('/{id:[\d]+}', 'ApiMemberController:updateAction')->setName('api_member_update');

        // Patch
        $this->patch('/{id:[\d]+}', 'ApiMemberController:patchAction')->setName('api_member_patch');

        // Delete
        $this->delete('/{id:[\d]+}', 'ApiMemberController:deleteAction')->setName('api_member_update_delete');
    })->add(function ($request, $response, $next) {
        $jsonResponse = $next($request, $response);
        // Force JSON content type
        return $jsonResponse->withHeader('Content-type', 'application/json');
    });
});
