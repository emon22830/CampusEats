<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

class MenuItemRepository
{
    public function __construct(private readonly PDO $db)
    {
    }

    public function findByVendor(int $vendorId): array
    {
        $stmt = $this->db->prepare(
            'SELECT id, vendor_id, name, description, price, category, image_url, in_stock
             FROM menu_items
             WHERE vendor_id = :vendor_id
             ORDER BY category ASC, name ASC'
        );
        $stmt->execute(['vendor_id' => $vendorId]);

        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM menu_items WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);

        $item = $stmt->fetch();

        return $item === false ? null : $item;
    }

    public function create(
        int $vendorId,
        string $name,
        ?string $description,
        float $price,
        string $category,
        ?string $imageUrl,
        bool $inStock
    ): int {
        $stmt = $this->db->prepare(
            'INSERT INTO menu_items (vendor_id, name, description, price, category, image_url, in_stock)
             VALUES (:vendor_id, :name, :description, :price, :category, :image_url, :in_stock)'
        );

        $stmt->execute([
            'vendor_id' => $vendorId,
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'category' => $category,
            'image_url' => $imageUrl,
            'in_stock' => $inStock ? 1 : 0,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $fields): void
    {
        if ($fields === []) {
            return;
        }

        $set = implode(', ', array_map(static fn (string $col) => "{$col} = :{$col}", array_keys($fields)));

        $stmt = $this->db->prepare("UPDATE menu_items SET {$set} WHERE id = :id");
        $stmt->execute([...$fields, 'id' => $id]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM menu_items WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
