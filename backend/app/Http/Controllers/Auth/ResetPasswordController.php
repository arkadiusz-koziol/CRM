<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Exceptions\InvalidPasswordResetTokenException;
use App\Exceptions\PasswordResetException;
use App\Exceptions\UserNotFoundException;
use App\Factory\ResetPasswordDtoFactory;
use App\Http\Controllers\Controller;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Resources\ResetPasswordResource;
use App\Services\ResetPasswordService;
use Illuminate\Http\JsonResponse;

final class ResetPasswordController extends Controller
{
    /**
     * Handle the reset password request.
     *
     * @param ResetPasswordService $resetPasswordService
     * @param ResetPasswordDtoFactory $dtoFactory
     * @param ResetPasswordRequest $request
     * @return JsonResponse
     */
    public function __invoke(
        ResetPasswordService $resetPasswordService,
        ResetPasswordDtoFactory $dtoFactory,
        ResetPasswordRequest $request
    ): JsonResponse {
        try {
            $dto = $dtoFactory->fromRequest($request);
            $message = $resetPasswordService->handle($dto);

            return (new ResetPasswordResource([
                'message' => $message
            ]))->response()->setStatusCode(200);
        } catch (InvalidPasswordResetTokenException $e) {
            return $this->responseFactory->errorResponse($e->getMessage(), 400);
        } catch (UserNotFoundException $e) {
            return $this->responseFactory->errorResponse($e->getMessage(), 404);
        } catch (PasswordResetException $e) {
            return $this->responseFactory->errorResponse($e->getMessage(), 500);
        }
    }
}
