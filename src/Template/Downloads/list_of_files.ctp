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
                        <?php echo $this->Html->link('Download', ['controller' => 'Downloads', 'action' => 'download', 'test']); ?>
                        <?php echo '</td>'; ?>
                        <?php echo '</th>'; ?>
                        <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php endif; ?>