<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\SalesProductTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\SalesProductTable Test Case
 */
class SalesProductTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\SalesProductTable
     */
    protected $SalesProduct;

    /**
     * Fixtures
     *
     * @var list<string>
     */
    protected array $fixtures = [
        'app.SalesProduct',
        'app.Sales',
        'app.Products',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('SalesProduct') ? [] : ['className' => SalesProductTable::class];
        $this->SalesProduct = $this->getTableLocator()->get('SalesProduct', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->SalesProduct);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @link \App\Model\Table\SalesProductTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @link \App\Model\Table\SalesProductTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
