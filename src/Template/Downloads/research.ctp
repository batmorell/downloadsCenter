
<div class='row'>
<?= $this->Form->create('Research') ?>

            <div class=col-md-3>

            <?= $this->Form->hidden('form', ['value' => 'Research']) ?>

            <?= $this->Form->input('search',
              ['type'=>'text',
              'label'=>false,
              'placeholder'=>'Rechercher...',
              'class'=>'form-control'])
            ?>
            </div>
            <div class=col-md-3>
            <?=
            $this->Form->select('season', $options, ['empty' => true]);
            ?>
            </div>
            <div class=col-md-6>
            <?= $this->Form->input('Rechercher',
              ['type'=>'submit',
              'value'=>'Submit',
              'class'=>'btn btn-primary form-control top-10'])
            ?>

            </div>

<?= $this->Form->end() ?>

</div>
<?php if ($result != null) : ?>

    <div class="row">
        <div class="col-md-12">
            <table class="table">
                <thead>
                    <th>Name</th>
                    <th>Size</th>
                    <th>Action</th>
                </thead>
                <tbody>
                        <?php foreach($result['torrents'] as $res) : ?>
                        <?php echo '<tr><td>' . $res['name'] . '</td><td>' . $res['size'] . '</td>'; ?>
                        <?php echo '<td>'; ?>
                        <?= $this->Form->create('Action'); ?>
                            <?= $this->Form->hidden('form', ['value' => 'Action']); ?>
                            <?= $this->Form->hidden('id', ['value' => $res['id']]); ?>
                            <?= $this->Form->button('Download',['class'=>'form-control btn btn-success']); ?>
                        <?= $this->Form->end(); ?>
                        <?php echo '</td>'; ?>
                        <?php echo '</th>'; ?>
                        <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php endif; ?>