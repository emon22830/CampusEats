<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\MenuItemRepository;
use App\Repositories\OrderItemRepository;
use App\Repositories\OrderRepository;
use App\Repositories\VendorRepository;
use App\Support\Response;
use PDO;
use Psr\Http\Message\ResponseInterface as PsrResponse;
use Psr\Http\Message\ServerRequestInterface as Request;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;

class OrderController
{
    private const STATUS_SEQUENCE = ['placed', 'preparing', 'ready', 'collected'];

    public function __construct(
        private readonly OrderRepository $orders,
        private readonly OrderItemRepository $orderItems,
        private readonly MenuItemRepository $menuItems,
        private readonly VendorRepository $vendors,
        private readonly PDO $db
    ) {
    }

    public function create(Request $request, PsrResponse $response): PsrResponse
    {
        $body = (array) $request->getParsedBody();

        $rules = v::key('vendor_id', v::intVal())
            ->key('pickup_at', v::stringType()->length(1, 60))
            ->key('items', v::arrayType()->notEmpty());

        try {
            $rules->assert($body);
        } catch (NestedValidationException $e) {
            return Response::error($response, 'Validation failed.', 422, $e->getMessages());
        }

        $vendor = $this->vendors->findApprovedById((int) $body['vendor_id']);

        if ($vendor === null) {
            return Response::error($response, 'Vendor not found or not currently available.', 404);
        }

        $lineItems = [];
        $total = 0.0;

        foreach ($body['items'] as $line) {
            if (!is_array($line) || !isset($line['menu_item_id'], $line['qty'])) {
                return Response::error($response, 'Each item needs menu_item_id and qty.', 422);
            }

            $qty = (int) $line['qty'];

            if ($qty < 1 || $qty > 50) {
                return Response::error($response, 'Item quantity must be between 1 and 50.', 422);
            }

            $menuItem = $this->menuItems->findById((int) $line['menu_item_id']);

            if ($menuItem === null || (int) $menuItem['vendor_id'] !== (int) $vendor['id']) {
                return Response::error($response, 'One of the items does not belong to this vendor.', 422);
            }

            if (!(bool) $menuItem['in_stock']) {
                return Response::error($response, "\"{$menuItem['name']}\" is currently out of stock.", 422);
            }

            $unitPrice = (float) $menuItem['price'];
            $total += $unitPrice * $qty;

            $lineItems[] = [
                'menu_item_id' => (int) $menuItem['id'],
                'qty' => $qty,
                'unit_price' => $unitPrice,
            ];
        }

        $this->db->beginTransaction();

        try {
            $orderId = $this->orders->create(
                (int) $request->getAttribute('user_id'),
                (int) $vendor['id'],
                round($total, 2),
                $body['pickup_at']
            );

            $this->orderItems->createMany($orderId, $lineItems);

            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }

        return Response::ok($response, $this->present($this->orders->findById($orderId)), 201);
    }

    public function index(Request $request, PsrResponse $response): PsrResponse
    {
        $params = $request->getQueryParams();
        $status = $params['status'] ?? null;
        $date = $params['date'] ?? null;

        $role = $request->getAttribute('user_role');
        $userId = (int) $request->getAttribute('user_id');

        $orders = match ($role) {
            'customer' => $this->orders->listForCustomer($userId, $status, $date),
            'vendor' => $this->orders->listForVendors(
                array_map(static fn (array $v) => (int) $v['id'], $this->vendors->findByOwner($userId)),
                $status,
                $date
            ),
            'admin' => $this->orders->listAll($status, $date),
            default => [],
        };

        return Response::ok($response, array_map([$this, 'present'], $orders));
    }

    public function show(Request $request, PsrResponse $response, array $args): PsrResponse
    {
        $order = $this->orders->findById((int) $args['id']);

        if ($order === null) {
            return Response::error($response, 'Order not found.', 404);
        }

        if (!$this->canAccess($request, $order)) {
            return Response::error($response, 'You do not have access to this order.', 403);
        }

        return Response::ok($response, $this->present($order));
    }

    public function updateStatus(Request $request, PsrResponse $response, array $args): PsrResponse
    {
        $order = $this->orders->findById((int) $args['id']);

        if ($order === null) {
            return Response::error($response, 'Order not found.', 404);
        }

        if (!$this->canManage($request, $order)) {
            return Response::error($response, 'You do not have access to this order.', 403);
        }

        $body = (array) $request->getParsedBody();

        try {
            v::key('status', v::in(['preparing', 'ready', 'collected']))->assert($body);
        } catch (NestedValidationException $e) {
            return Response::error($response, 'Validation failed.', 422, $e->getMessages());
        }

        $currentIndex = array_search($order['status'], self::STATUS_SEQUENCE, true);
        $nextIndex = array_search($body['status'], self::STATUS_SEQUENCE, true);

        if ($currentIndex === false || $nextIndex !== $currentIndex + 1) {
            return Response::error(
                $response,
                "Cannot move order from \"{$order['status']}\" to \"{$body['status']}\".",
                409
            );
        }

        $this->orders->updateStatus((int) $args['id'], $body['status']);

        return Response::ok($response, $this->present($this->orders->findById((int) $args['id'])));
    }

    public function cancel(Request $request, PsrResponse $response, array $args): PsrResponse
    {
        $order = $this->orders->findById((int) $args['id']);

        if ($order === null) {
            return Response::error($response, 'Order not found.', 404);
        }

        if ((int) $order['user_id'] !== (int) $request->getAttribute('user_id')) {
            return Response::error($response, 'You do not have access to this order.', 403);
        }

        if ($order['status'] !== 'placed') {
            return Response::error($response, 'Only orders that have not started preparing can be cancelled.', 409);
        }

        $this->orders->updateStatus((int) $args['id'], 'cancelled');

        return Response::ok($response, $this->present($this->orders->findById((int) $args['id'])));
    }

    private function canAccess(Request $request, array $order): bool
    {
        $role = $request->getAttribute('user_role');
        $userId = (int) $request->getAttribute('user_id');

        if ($role === 'admin') {
            return true;
        }

        if ($role === 'customer') {
            return (int) $order['user_id'] === $userId;
        }

        if ($role === 'vendor') {
            $vendor = $this->vendors->findById((int) $order['vendor_id']);

            return $vendor !== null && (int) $vendor['owner_id'] === $userId;
        }

        return false;
    }

    private function canManage(Request $request, array $order): bool
    {
        $role = $request->getAttribute('user_role');

        if ($role === 'admin') {
            return true;
        }

        if ($role === 'vendor') {
            $vendor = $this->vendors->findById((int) $order['vendor_id']);

            return $vendor !== null && (int) $vendor['owner_id'] === (int) $request->getAttribute('user_id');
        }

        return false;
    }

    private function present(array $order): array
    {
        return [
            'id' => (int) $order['id'],
            'user_id' => (int) $order['user_id'],
            'vendor_id' => (int) $order['vendor_id'],
            'status' => $order['status'],
            'total' => (float) $order['total'],
            'pickup_at' => $order['pickup_at'],
            'created_at' => $order['created_at'],
            'items' => array_map(static fn (array $i) => [
                'id' => (int) $i['id'],
                'menu_item_id' => (int) $i['menu_item_id'],
                'name' => $i['name'],
                'qty' => (int) $i['qty'],
                'unit_price' => (float) $i['unit_price'],
            ], $this->orderItems->findByOrder((int) $order['id'])),
        ];
    }
}
