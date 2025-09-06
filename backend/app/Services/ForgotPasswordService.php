<?php

declare(strict_types=1);

namespace App\Services;

use App\Dto\ForgotPasswordDto;
use App\Exceptions\PasswordResetException;
use App\Exceptions\PasswordResetThrottledException;
use App\Exceptions\UserNotFoundException;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Contracts\Auth\PasswordBroker as PasswordBrokerAlias;

final readonly class ForgotPasswordService
{
    public function __construct(
        private PasswordBroker $passwordBroker
    ) {
    }

    /**
     * @throws PasswordResetException
     * @throws UserNotFoundException
     * @throws PasswordResetThrottledException
     */
    public function handle(ForgotPasswordDto $dto): string
    {
        $status = $this->passwordBroker->sendResetLink([
            'email' => $dto->getEmail(),
        ]);

        if ($status === PasswordBrokerAlias::RESET_LINK_SENT) {
            return __('passwords.sent');
        }

        if ($status === PasswordBrokerAlias::INVALID_USER) {
            throw new UserNotFoundException($dto->getEmail());
        }

        if ($status === PasswordBrokerAlias::RESET_THROTTLED) {
            throw new PasswordResetThrottledException(__('passwords.throttled'));
        }

        throw new PasswordResetException(__('passwords.sent'));
    }
}
