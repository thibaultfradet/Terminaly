<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CategorysTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CategorysTable Test Case
 */
class CategorysTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\CategorysTable
     */
    protected $Categorys;

    /**
     * Fixtures
     *
     * @var list<string>
     */
    protected array $fixtures = [
        'app.Categorys',
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
        $config = $this->getTableLocator()->exists('Categorys') ? [] : ['className' => CategorysTable::class];
        $this->Categorys = $this->getTableLocator()->get('Categorys', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Categorys);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @link \App\Model\Table\CategorysTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
