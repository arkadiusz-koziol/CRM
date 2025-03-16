<?php

namespace App\Services;

use App\Dto\AuthDto;
use App\Enums\UserStatus;
use App\Models\User;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Auth\AuthManager;
use Illuminate\Hashing\HashManager;

readonly class AuthService
{
    public function __construct(
        private UserRepository $repository,
        private AuthManager $authManager,
        private HashManager $hashManager,
    ) {
    }

    /**
     * @throws Exception
     */
    public function authUser(
        AuthDto $authDto,
        bool $remember = false,
        array $allowedRoles = [],
    ): string {
        $user = User::where('email', $authDto->email)->first();
        if (!$user ||
            !$user->hasAnyRole($allowedRoles) ||
            !$this->hashManager->check($authDto->password, $user->password) ||
            !$this->repository->userCanPerformAction($user) ||
            !$this->authManager->attempt(
                array_merge(
                    $authDto->toArray(),
                    [
                        'status' => [
                            UserStatus::ACTIVE->value,
                        ],
                    ]
                ),
                $remember
            )
        ) {
            throw new Exception(__('auth.failed'));
        }

        return $user->createToken($user->name . '-AuthToken')->plainTextToken;
    }
}
