<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * SalesProductFixture
 */
class SalesProductFixture extends TestFixture
{
    /**
     * Table name
     *
     * @var string
     */
    public string $table = 'sales_product';
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'sale_id' => 1,
                'product_id' => 1,
                'quantity' => 1.5,
                'unit' => 'Lorem ipsum dolor ',
                'price' => 1.5,
            ],
        ];
        parent::init();
    }
}
