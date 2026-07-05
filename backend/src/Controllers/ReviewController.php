<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\ReviewRepository;
use App\Repositories\VendorRepository;
use App\Support\Response;
use Psr\Http\Message\ResponseInterface as PsrResponse;
use Psr\Http\Message\ServerRequestInterface as Request;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;

class ReviewController
{
    public function __construct(
        private readonly ReviewRepository $reviews,
        private readonly VendorRepository $vendors
    ) {
    }

    public function index(Request $request, PsrResponse $response, array $args): PsrResponse
    {
        $vendorId = (int) $args['id'];

        if ($this->vendors->findApprovedById($vendorId) === null) {
            return Response::error($response, 'Vendor not found.', 404);
        }

        $reviews = array_map([$this, 'present'], $this->reviews->listByVendor($vendorId));

        return Response::ok($response, $reviews);
    }

    public function create(Request $request, PsrResponse $response, array $args): PsrResponse
    {
        $vendorId = (int) $args['id'];

        if ($this->vendors->findApprovedById($vendorId) === null) {
            return Response::error($response, 'Vendor not found.', 404);
        }

        $userId = (int) $request->getAttribute('user_id');

        if (!$this->reviews->hasCollectedOrder($userId, $vendorId)) {
            return Response::error($response, 'You can only review vendors you have ordered from.', 403);
        }

        $body = (array) $request->getParsedBody();

        $rules = v::key('rating', v::intVal()->between(1, 5))
            ->key('comment', v::optional(v::stringType()->length(0, 500)), false);

        try {
            $rules->assert($body);
        } catch (NestedValidationException $e) {
            return Response::error($response, 'Validation failed.', 422, $e->getMessages());
        }

        $this->reviews->create($userId, $vendorId, (int) $body['rating'], $body['comment'] ?? null);

        return Response::ok($response, ['message' => 'Review submitted.'], 201);
    }

    private function present(array $review): array
    {
        return [
            'id' => (int) $review['id'],
            'rating' => (int) $review['rating'],
            'comment' => $review['comment'],
            'user_name' => $review['user_name'],
            'created_at' => $review['created_at'],
        ];
    }
}
