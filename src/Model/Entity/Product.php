<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Product Entity
 *
 * @property int $id
 * @property string $name
 * @property int $category_id
 * @property string $price
 * @property \Cake\I18n\DateTime|null $created_at
 *
 * @property \App\Model\Entity\Category $category
 * @property \App\Model\Entity\SalesProduct[] $sales_product
 */
class Product extends Entity
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
        'name' => true,
        'category_id' => true,
        'price' => true,
        'created_at' => true,
        'category' => true,
        'sales_product' => true,
    ];
}
