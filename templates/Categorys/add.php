<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Category $category
 */
?>
<div class="flex justify-center">
    <div class="w-full max-w-2xl bg-white p-6 rounded shadow">
        <?= $this->Form->create($category, ['class' => 'space-y-4']) ?>
        <fieldset>
            <legend class="text-xl font-semibold mb-4">Ajouter une catégorie</legend>
            <?php
                echo $this->Form->control('name', [
                    'label' => 'Nom',
                    'class' => 'block w-full border-gray-300 rounded p-2'
                ]);
                echo $this->Form->control('created_at', [
                    'label' => 'Date de création',
                    'empty' => true,
                    'class' => 'block w-full border-gray-300 rounded p-2'
                ]);
            ?>
        </fieldset>
        <?= $this->Form->button('Valider', ['class' => 'bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700']) ?>
        <?= $this->Form->end() ?>
    </div>
</div>
