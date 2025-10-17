<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Exceptions\PasswordResetException;
use App\Exceptions\PasswordResetThrottledException;
use App\Exceptions\UserNotFoundException;
use App\Factory\ForgotPasswordDtoFactory;
use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Resources\ForgotPasswordResource;
use App\Services\ForgotPasswordService;
use Illuminate\Http\JsonResponse;

final class ForgotPasswordController extends Controller
{
    /**
     * Handle the forgot password request.
     */
    public function __invoke(
        ForgotPasswordService $forgotPasswordService,
        ForgotPasswordDtoFactory $dtoFactory,
        ForgotPasswordRequest $request
    ): JsonResponse {
        try {
            $dto = $dtoFactory->fromRequest($request);
            $message = $forgotPasswordService->handle($dto);

            return (new ForgotPasswordResource([
                'message' => $message,
            ]))->response()->setStatusCode(200);
        } catch (UserNotFoundException $e) {
            return $this->responseFactory->errorResponse($e->getMessage(), 404);
        } catch (PasswordResetThrottledException $e) {
            return $this->responseFactory->errorResponse($e->getMessage(), 429);
        } catch (PasswordResetException $e) {
            return $this->responseFactory->errorResponse($e->getMessage(), 500);
        }
    }
}
