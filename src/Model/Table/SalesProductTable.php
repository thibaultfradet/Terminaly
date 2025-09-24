<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * SalesProduct Model
 *
 * @property \App\Model\Table\SalesTable&\Cake\ORM\Association\BelongsTo $Sales
 * @property \App\Model\Table\ProductsTable&\Cake\ORM\Association\BelongsTo $Products
 *
 * @method \App\Model\Entity\SalesProduct newEmptyEntity()
 * @method \App\Model\Entity\SalesProduct newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\SalesProduct> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\SalesProduct get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\SalesProduct findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\SalesProduct patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\SalesProduct> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\SalesProduct|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\SalesProduct saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\SalesProduct>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\SalesProduct>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\SalesProduct>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\SalesProduct> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\SalesProduct>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\SalesProduct>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\SalesProduct>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\SalesProduct> deleteManyOrFail(iterable $entities, array $options = [])
 */
class SalesProductTable extends Table
{
    /**
     * Initialize method
     *
     * @param array<string, mixed> $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('sales_product');
        $this->setDisplayField('unit');
        $this->setPrimaryKey('id');

        $this->belongsTo('Sales', [
            'foreignKey' => 'sale_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Products', [
            'foreignKey' => 'product_id',
            'joinType' => 'INNER',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('sale_id')
            ->notEmptyString('sale_id');

        $validator
            ->integer('product_id')
            ->notEmptyString('product_id');

        $validator
            ->decimal('quantity')
            ->requirePresence('quantity', 'create')
            ->notEmptyString('quantity');

        $validator
            ->scalar('unit')
            ->maxLength('unit', 20)
            ->requirePresence('unit', 'create')
            ->notEmptyString('unit');

        $validator
            ->decimal('price')
            ->requirePresence('price', 'create')
            ->notEmptyString('price');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['sale_id'], 'Sales'), ['errorField' => 'sale_id']);
        $rules->add($rules->existsIn(['product_id'], 'Products'), ['errorField' => 'product_id']);

        return $rules;
    }
}
