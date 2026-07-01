<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\MenuItemRepository;
use App\Repositories\VendorRepository;
use App\Support\Response;
use Psr\Http\Message\ResponseInterface as PsrResponse;
use Psr\Http\Message\ServerRequestInterface as Request;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;

class MenuItemController
{
    public function __construct(
        private readonly MenuItemRepository $menuItems,
        private readonly VendorRepository $vendors
    ) {
    }

    public function byVendor(Request $request, PsrResponse $response, array $args): PsrResponse
    {
        $vendorId = (int) $args['id'];

        if ($this->vendors->findApprovedById($vendorId) === null) {
            return Response::error($response, 'Vendor not found.', 404);
        }

        $items = array_map(
            [$this, 'present'],
            $this->menuItems->findByVendor($vendorId)
        );

        return Response::ok($response, $items);
    }

    public function create(Request $request, PsrResponse $response, array $args): PsrResponse
    {
        $vendorId = (int) $args['id'];
        $vendor = $this->vendors->findById($vendorId);

        if ($vendor === null) {
            return Response::error($response, 'Vendor not found.', 404);
        }

        if (!$this->ownsVendor($request, $vendor)) {
            return Response::error($response, 'You do not own this vendor.', 403);
        }

        $body = (array) $request->getParsedBody();

        $rules = v::key('name', v::stringType()->length(2, 150))
            ->key('description', v::optional(v::stringType()->length(0, 500)), false)
            ->key('price', v::numericVal()->between(0.01, 9999.99))
            ->key('category', v::stringType()->length(1, 60))
            ->key('image_url', v::optional(v::stringType()->length(0, 255)), false)
            ->key('in_stock', v::optional(v::boolType()), false);

        try {
            $rules->assert($body);
        } catch (NestedValidationException $e) {
            return Response::error($response, 'Validation failed.', 422, $e->getMessages());
        }

        $id = $this->menuItems->create(
            $vendorId,
            $body['name'],
            $body['description'] ?? null,
            (float) $body['price'],
            $body['category'],
            $body['image_url'] ?? null,
            $body['in_stock'] ?? true
        );

        return Response::ok($response, $this->present($this->menuItems->findById($id)), 201);
    }

    public function update(Request $request, PsrResponse $response, array $args): PsrResponse
    {
        $item = $this->menuItems->findById((int) $args['id']);

        if ($item === null) {
            return Response::error($response, 'Menu item not found.', 404);
        }

        $vendor = $this->vendors->findById((int) $item['vendor_id']);

        if (!$this->ownsVendor($request, $vendor)) {
            return Response::error($response, 'You do not own this menu item.', 403);
        }

        $body = (array) $request->getParsedBody();

        $rules = v::key('name', v::optional(v::stringType()->length(2, 150)), false)
            ->key('description', v::optional(v::stringType()->length(0, 500)), false)
            ->key('price', v::optional(v::numericVal()->between(0.01, 9999.99)), false)
            ->key('category', v::optional(v::stringType()->length(1, 60)), false)
            ->key('image_url', v::optional(v::stringType()->length(0, 255)), false)
            ->key('in_stock', v::optional(v::boolType()), false);

        try {
            $rules->assert($body);
        } catch (NestedValidationException $e) {
            return Response::error($response, 'Validation failed.', 422, $e->getMessages());
        }

        $allowed = ['name', 'description', 'price', 'category', 'image_url', 'in_stock'];
        $fields = array_intersect_key($body, array_flip($allowed));

        if (array_key_exists('price', $fields)) {
            $fields['price'] = (float) $fields['price'];
        }

        if (array_key_exists('in_stock', $fields)) {
            $fields['in_stock'] = $fields['in_stock'] ? 1 : 0;
        }

        $this->menuItems->update((int) $args['id'], $fields);

        return Response::ok($response, $this->present($this->menuItems->findById((int) $args['id'])));
    }

    public function delete(Request $request, PsrResponse $response, array $args): PsrResponse
    {
        $item = $this->menuItems->findById((int) $args['id']);

        if ($item === null) {
            return Response::error($response, 'Menu item not found.', 404);
        }

        $vendor = $this->vendors->findById((int) $item['vendor_id']);

        if (!$this->ownsVendor($request, $vendor)) {
            return Response::error($response, 'You do not own this menu item.', 403);
        }

        $this->menuItems->delete((int) $args['id']);

        return $response->withStatus(204);
    }

    private function ownsVendor(Request $request, ?array $vendor): bool
    {
        if ($vendor === null) {
            return false;
        }

        if ($request->getAttribute('user_role') === 'admin') {
            return true;
        }

        return (int) $vendor['owner_id'] === (int) $request->getAttribute('user_id');
    }

    private function present(array $item): array
    {
        return [
            'id' => (int) $item['id'],
            'vendor_id' => (int) $item['vendor_id'],
            'name' => $item['name'],
            'description' => $item['description'],
            'price' => (float) $item['price'],
            'category' => $item['category'],
            'image_url' => $item['image_url'],
            'in_stock' => (bool) $item['in_stock'],
        ];
    }
}
