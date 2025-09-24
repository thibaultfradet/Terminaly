<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * SalesProduct Entity
 *
 * @property int $id
 * @property int $sale_id
 * @property int $product_id
 * @property string $quantity
 * @property string $unit
 * @property string $price
 *
 * @property \App\Model\Entity\Sale $sale
 * @property \App\Model\Entity\Product $product
 */
class SalesProduct extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'sale_id' => true,
        'product_id' => true,
        'quantity' => true,
        'unit' => true,
        'price' => true,
        'sale' => true,
        'product' => true,
    ];
}
