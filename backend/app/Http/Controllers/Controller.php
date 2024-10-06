<?php

namespace App\Http\Controllers;

use App\Factory\ResponseFactory;
use Illuminate\Auth\AuthManager;

abstract class Controller
{
    /**
     * @OA\Info(title="Skytech Whitelabel API", version="0.1")
     *
     * @OA\Server(
     *      url="http://localhost:8199/api/documentation",
     *      description="Skytech Whitelabel API Server"
     * )
     */

    public function __construct(
        protected ResponseFactory $responseFactory,
        protected AuthManager $authManager
    ) {
    }

}
