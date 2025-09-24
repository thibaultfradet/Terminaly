<!-- in /templates/Users/login.php -->
<div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="w-full max-w-md p-8 space-y-6 bg-white rounded-lg shadow-md">
        <?= $this->Flash->render() ?>
        <h3 class="text-2xl font-bold text-center text-gray-800">Login</h3>
        <?= $this->Form->create() ?>
        <fieldset class="space-y-4">
            <legend class="text-sm text-gray-600 mb-2"><?= __('Please enter your username and password') ?></legend>
            <div>
                <?= $this->Form->control('email', [
                    'required' => true,
                    'label' => 'Email',
                    'class' => 'w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500'
                ]) ?>
            </div>
            <div>
                <?= $this->Form->control('password', [
                    'required' => true,
                    'label' => 'Password',
                    'class' => 'w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500'
                ]) ?>
            </div>
        </fieldset>
        <div>
            <?= $this->Form->submit(__('Login'), [
                'class' => 'w-full py-2 px-4 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700 transition duration-200'
            ]) ?>
        </div>
        <?= $this->Form->end() ?>
        <div class="text-center">
            <?= $this->Html->link("Add User", ['action' => 'add'], [
                'class' => 'text-blue-600 hover:underline'
            ]) ?>
        </div>
    </div>
</div>
