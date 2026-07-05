<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

class OrderItemRepository
{
    public function __construct(private readonly PDO $db)
    {
    }

    /** @param array<int, array{menu_item_id: int, qty: int, unit_price: float}> $items */
    public function createMany(int $orderId, array $items): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO order_items (order_id, menu_item_id, qty, unit_price)
             VALUES (:order_id, :menu_item_id, :qty, :unit_price)'
        );

        foreach ($items as $item) {
            $stmt->execute([
                'order_id' => $orderId,
                'menu_item_id' => $item['menu_item_id'],
                'qty' => $item['qty'],
                'unit_price' => $item['unit_price'],
            ]);
        }
    }

    public function findByOrder(int $orderId): array
    {
        $stmt = $this->db->prepare(
            'SELECT oi.id, oi.menu_item_id, oi.qty, oi.unit_price, mi.name
             FROM order_items oi
             JOIN menu_items mi ON mi.id = oi.menu_item_id
             WHERE oi.order_id = :order_id'
        );
        $stmt->execute(['order_id' => $orderId]);

        return $stmt->fetchAll();
    }
}
