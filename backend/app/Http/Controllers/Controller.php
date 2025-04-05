<?php

namespace App\Http\Controllers;

use App\Factory\ResponseFactory;
use Illuminate\Auth\AuthManager;
use OpenApi\Annotations as OA;
use Psr\Log\LoggerInterface;

abstract class Controller
{
    /**
     * @OA\Info(title="Telemain API", version="0.1")
     *
     * @OA\Server(
     *      url="http://localhost:8199/api/",
     *      description="Telemain API Server"
     * )
     *
     * @OA\Components(
     *     @OA\SecurityScheme(
     *         securityScheme="bearerAuth",
     *         type="http",
     *         scheme="bearer",
     *         bearerFormat="JWT"
     *     )
     * )
     */

    public function __construct(
        protected ResponseFactory $responseFactory,
        protected AuthManager $authManager,
        protected LoggerInterface $logger,
    ) {
    }

}
