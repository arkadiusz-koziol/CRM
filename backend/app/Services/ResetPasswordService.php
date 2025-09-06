<?php

declare(strict_types=1);

namespace App\Services;

use App\Dto\ResetPasswordDto;
use App\Exceptions\InvalidPasswordResetTokenException;
use App\Exceptions\PasswordResetException;
use App\Exceptions\UserNotFoundException;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Contracts\Auth\PasswordBroker as PasswordBrokerAlias;

final readonly class ResetPasswordService
{
    public function __construct(
        private PasswordBroker $passwordBroker
    ) {
    }

    /**
     * @throws InvalidPasswordResetTokenException
     * @throws PasswordResetException
     * @throws UserNotFoundException
     */
    public function handle(ResetPasswordDto $dto): string
    {
        $status = $this->passwordBroker->reset([
            'email' => $dto->getEmail(),
            'password' => $dto->getPassword(),
            'password_confirmation' => $dto->getPassword(),
            'token' => $dto->getToken(),
        ], function ($user, $password) {
            $user->forceFill([
                'password' => bcrypt($password)
            ])->save();
        });

        if ($status === PasswordBrokerAlias::PASSWORD_RESET) {
            return __('passwords.reset');
        }

        if ($status === PasswordBrokerAlias::INVALID_TOKEN) {
            throw new InvalidPasswordResetTokenException(__('passwords.token'));
        }

        if ($status === PasswordBrokerAlias::INVALID_USER) {
            throw new UserNotFoundException($dto->getEmail());
        }

        throw new PasswordResetException(__('passwords.reset'));
    }
}
