<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Cars;

use App\Http\Controllers\Controller;
use App\Http\Resources\CarListResource;
use App\Services\CarService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class ListCarController extends Controller
{
    /**
     * @OA\Get(
     *     path="/v1/admin/cars/list",
     *     summary="Get list of cars",
     *     description="Retrieve a paginated list of all available cars with filtering and sorting capabilities.",
     *     operationId="getCarsList",
     *     tags={"Admin Cars"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *
     *         @OA\Schema(type="integer", minimum=1, default=1)
     *     ),
     *
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *
     *         @OA\Schema(type="integer", minimum=1, maximum=100, default=10)
     *     ),
     *
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search query for filtering cars by name or registration number",
     *         required=false,
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="name", type="string"),
     *                     @OA\Property(property="description", type="string"),
     *                     @OA\Property(property="registration_number", type="string"),
     *                     @OA\Property(property="technical_details", type="string", nullable=true),
     *                     @OA\Property(property="created_at", type="string", format="date-time", nullable=true),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", nullable=true),
     *                     @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true)
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="pagination",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer"),
     *                 @OA\Property(property="per_page", type="integer"),
     *                 @OA\Property(property="total", type="integer"),
     *                 @OA\Property(property="last_page", type="integer"),
     *                 @OA\Property(property="from", type="integer"),
     *                 @OA\Property(property="to", type="integer")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     )
     * )
     */
    public function __invoke(Request $request, CarService $carService): JsonResponse
    {
        try {
            $page = (int) $request->query('page', '1');
            $limit = (int) $request->query('limit', '10');
            $search = $request->query('search', '');

            $page = max(1, $page);
            $limit = max(1, min(100, $limit));

            $result = $carService->getPaginatedCars($page, $limit, $search);
            
            return $this->responseFactory->json([
                'data' => array_map(fn($car) => new CarListResource($car), $result['data']),
                'pagination' => $result['pagination']
            ]);
        } catch (Throwable $e) {
            $this->logger->error('Error retrieving cars', [
                'exception' => $e,
            ]);

            return $this->responseFactory->json(
                ['message' => __('app.action.failed')],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
