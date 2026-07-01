<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\VendorRepository;
use App\Support\Response;
use Psr\Http\Message\ResponseInterface as PsrResponse;
use Psr\Http\Message\ServerRequestInterface as Request;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;

class VendorController
{
    public function __construct(private readonly VendorRepository $vendors)
    {
    }

    public function index(Request $request, PsrResponse $response): PsrResponse
    {
        $search = $request->getQueryParams()['search'] ?? null;

        $vendors = array_map(
            [$this, 'present'],
            $this->vendors->listApproved($search)
        );

        return Response::ok($response, $vendors);
    }

    public function show(Request $request, PsrResponse $response, array $args): PsrResponse
    {
        $vendor = $this->vendors->findApprovedById((int) $args['id']);

        if ($vendor === null) {
            return Response::error($response, 'Vendor not found.', 404);
        }

        return Response::ok($response, $this->present($vendor));
    }

    /** Vendors owned by the current user, regardless of approval status. */
    public function mine(Request $request, PsrResponse $response): PsrResponse
    {
        $vendors = array_map(
            [$this, 'present'],
            $this->vendors->findByOwner((int) $request->getAttribute('user_id'))
        );

        return Response::ok($response, $vendors);
    }

    public function create(Request $request, PsrResponse $response): PsrResponse
    {
        $body = (array) $request->getParsedBody();

        $rules = v::key('name', v::stringType()->length(2, 150))
            ->key('location', v::stringType()->length(2, 190))
            ->key('opening_hours', v::optional(v::stringType()->length(0, 120)), false)
            ->key('image_url', v::optional(v::stringType()->length(0, 255)), false)
            ->key('prep_time_mins', v::optional(v::intVal()->between(1, 240)), false);

        try {
            $rules->assert($body);
        } catch (NestedValidationException $e) {
            return Response::error($response, 'Validation failed.', 422, $e->getMessages());
        }

        $id = $this->vendors->create(
            (int) $request->getAttribute('user_id'),
            $body['name'],
            $body['location'],
            $body['opening_hours'] ?? null,
            $body['image_url'] ?? null,
            isset($body['prep_time_mins']) ? (int) $body['prep_time_mins'] : null
        );

        $vendor = $this->vendors->findById($id);

        return Response::ok($response, $this->present($vendor), 201);
    }

    public function update(Request $request, PsrResponse $response, array $args): PsrResponse
    {
        $vendor = $this->vendors->findById((int) $args['id']);

        if ($vendor === null) {
            return Response::error($response, 'Vendor not found.', 404);
        }

        $isAdmin = $request->getAttribute('user_role') === 'admin';

        if (!$isAdmin && (int) $vendor['owner_id'] !== (int) $request->getAttribute('user_id')) {
            return Response::error($response, 'You do not own this vendor.', 403);
        }

        $body = (array) $request->getParsedBody();

        $rules = v::key('name', v::optional(v::stringType()->length(2, 150)), false)
            ->key('location', v::optional(v::stringType()->length(2, 190)), false)
            ->key('opening_hours', v::optional(v::stringType()->length(0, 120)), false)
            ->key('image_url', v::optional(v::stringType()->length(0, 255)), false)
            ->key('prep_time_mins', v::optional(v::intVal()->between(1, 240)), false)
            ->key('is_active', v::optional(v::boolType()), false);

        try {
            $rules->assert($body);
        } catch (NestedValidationException $e) {
            return Response::error($response, 'Validation failed.', 422, $e->getMessages());
        }

        $allowed = ['name', 'location', 'opening_hours', 'image_url', 'prep_time_mins', 'is_active'];
        $fields = array_intersect_key($body, array_flip($allowed));

        if (array_key_exists('is_active', $fields)) {
            $fields['is_active'] = $fields['is_active'] ? 1 : 0;
        }

        $this->vendors->update((int) $args['id'], $fields);

        return Response::ok($response, $this->present($this->vendors->findById((int) $args['id'])));
    }

    public function delete(Request $request, PsrResponse $response, array $args): PsrResponse
    {
        $vendor = $this->vendors->findById((int) $args['id']);

        if ($vendor === null) {
            return Response::error($response, 'Vendor not found.', 404);
        }

        $this->vendors->delete((int) $args['id']);

        return $response->withStatus(204);
    }

    private function present(array $vendor): array
    {
        return [
            'id' => (int) $vendor['id'],
            'owner_id' => isset($vendor['owner_id']) ? (int) $vendor['owner_id'] : null,
            'name' => $vendor['name'],
            'location' => $vendor['location'],
            'opening_hours' => $vendor['opening_hours'],
            'image_url' => $vendor['image_url'],
            'prep_time_mins' => $vendor['prep_time_mins'] !== null ? (int) $vendor['prep_time_mins'] : null,
            'is_active' => (bool) $vendor['is_active'],
            'status' => $vendor['status'],
            'rating' => isset($vendor['rating']) && $vendor['rating'] !== null ? (float) $vendor['rating'] : null,
            'review_count' => isset($vendor['review_count']) ? (int) $vendor['review_count'] : 0,
        ];
    }
}
