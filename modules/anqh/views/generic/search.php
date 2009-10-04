
<?= form::open(null, array('id' => 'search')) ?>

<?= form::dropdown('from', $providers) ?>

<?= form::input('query', '', 'title="' . __('Search...') . '" class="hint"') ?>

<?= form::close() ?>
