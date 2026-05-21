## nelmio config

nelmio_cors:
defaults:
origin_regex: true
allow_origin: ['%env(CORS_ALLOW_ORIGIN)%']
allow_methods: ['GET', 'OPTIONS', 'POST', 'PUT', 'PATCH', 'DELETE']
allow_headers: ['Content-Type', 'Authorization']
expose_headers: ['Link']
max_age: 3600
paths:
'^/': null

## api config

    #[Route('/api/check', methods:['GET'])]
    public function check (): Response
    {
    	$data = [
    		['userId'=>'1', 'name'=>'Jule' ],
    		['userId'=>'2', 'name'=>'Julien' ]
    	];
    	return $this->json([
    		'success'=> true,
    		'data' => $data
    	],
    	/* Response::HTTP_ACCEPTED */

    	);
    }

## routes config

    	# yaml-language-server: $schema=../vendor/symfony/routing/Loader/schema/routing.schema.json

    	# This file is the entry point to configure the routes of your app.
    	# Methods with the #[Route] attribute are automatically imported.
    	# See also https://symfony.com/doc/current/routing.html

    	# To list all registered routes, run the following command:
    	#   bin/console debug:router

    	controllers:
    		resource: routing.controllers

    	api_check:
    		path: /api/check
    		controller: App\Controller\RegistrationController::check
    		methods: GET
