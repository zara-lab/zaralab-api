<?php

// API routes group
$app->group('/api', function() {

    $this->get('/ping', function(Slim\Http\Request $request, Slim\Http\Response $response) {
        return $response;
    });

    $this->post('/echo', function(Slim\Http\Request $request, Slim\Http\Response $response) {
        $body = $request->getParsedBody();
        if (empty($body)) {
            return $response->write('');
        }

        return $response->write(json_encode($request->getParsedBody(), JSON_PRETTY_PRINT));
    });

    // REST Members group
    $this->group('/members', function() {

        // List
        $this->get('', 'ApiMemberController:listAction')->setName('api_member_list');

        // Show
        $this->get('/{id:[\d]+}', 'ApiMemberController:showAction')->setName('api_member_show');

        // Create
        $this->post('', 'ApiMemberController:createAction')->setName('api_member_create');

        // Update
        $this->put('/{id:[\d]+}', 'ApiMemberController:updateAction')->setName('api_member_update');

        // Patch
        $this->patch('/{id:[\d]+}', 'ApiMemberController:patchAction')->setName('api_member_patch');

        // Delete
        $this->delete('/{id:[\d]+}', 'ApiMemberController:deleteAction')->setName('api_member_update_delete');
    });

    // TODO move to class, move logic to the security manager
    $this->post('/authenticate', function(Slim\Http\Request $request, Slim\Http\Response $response){
        $c = $this;

        $params = $request->getParsedBody();

        if (empty($params)) {
            throw new \Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException('Authentication credentials could not be found.', 400);
        }

        /** @var \Zaralab\Service\SecurityManager $sm */
        $sm = $c['security.manager'];
        $params['email'] = isset($params['email']) ? rawurldecode($params['email']) : '';

        $member = $sm->authenticate($params['email'], varset($params['password']));

        if ($member) {
            /** @var \Slim\App $app */
            $tokenId    = \Zaralab\Service\SecurityManager::random();
            $issuedAt   = time();
            $notBefore  = $issuedAt;
            $expire     = $notBefore + 60;

            $serverName = $request->getUri()->getHost();

            $token = [
                'iat'  => $issuedAt,         // Issued at: time when the token was generated
                'jti'  => $tokenId,          // Json Token Id: an unique identifier for the token
                'iss'  => $serverName,       // Issuer
                'nbf'  => $notBefore,        // Not before
                'exp'  => $expire,           // Expire
                'data' => [                  // Data related to the signer user
                'id'   => $member->getId(), // userid from the users table
                'email' => $member->getEmail(), // User email
                ]
            ];

            $jwt = \Firebase\JWT\JWT::encode($token, $sm->secret());
            return $response->write(json_encode(['token' => $jwt]));
        } else {
            throw new \Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException('Authentication credentials could not be found.', 400);
        }

    })->setName('api_auth');
});

