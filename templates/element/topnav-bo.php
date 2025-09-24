<div class="bg-gray-100 border-b border-gray-200 mb-3">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between h-16">
      <div class="flex items-center space-x-4">
        <?= $this->Html->link('Boulangerie Admin', ['controller' => 'Dashboard', 'action' => 'index'], ['class' => 'text-lg font-bold text-gray-900 hover:opacity-90']) ?>
      </div>

      <nav class="flex items-center space-x-2">
        <?= $this->Html->link('Tableau de bord', ['controller' => 'Dashboard', 'action' => 'index'], ['class' => 'px-3 py-2 rounded text-gray-900 hover:bg-gray-200']) ?>
        <?= $this->Html->link('Catégories', ['controller' => 'Category', 'action' => 'index'], ['class' => 'px-3 py-2 rounded text-gray-900 hover:bg-gray-200']) ?>
        <?= $this->Html->link('Produits', ['controller' => 'Products', 'action' => 'index'], ['class' => 'px-3 py-2 rounded text-gray-900 hover:bg-gray-200']) ?>
      </nav>

      <div class="flex items-center space-x-3">
        <?= $this->Form->postLink('Déconnexion', ['controller' => 'Users', 'action' => 'logout'], ['class' => 'px-3 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm']) ?>
      </div>
    </div>
  </div>
</div>