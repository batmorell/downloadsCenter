<?php if ($result != null) : ?>

    <div class="row">
        <div class="col-md-12">
            <table class="table">
                <thead>
                    <th>Name</th>
                    <th>Action</th>
                </thead>
                <tbody>
                        <?php foreach($result as $res) : ?>
                        <?php echo '<tr><td>' . $res . '</td>'; ?>
                        <?php echo '<td>'; ?>

                        <?= $this->Form->create('Downloads'); ?>
                            <?= $this->Form->hidden('name', ['value' => $res]); ?>
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