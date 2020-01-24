<?php if (!empty($admins)) : ?>
    <?php foreach ($admins as $admin) : ?>
        <tr id="<?= $admin->id; ?>" role="row" class="odd">
            <td><?= $admin->first_name; ?></td>
            <td><?= $admin->last_name; ?></td>
            <td><?= $admin->email; ?></td>
            <td><?= $admin->last_logged_time; ?></td>
        </tr>
    <?php endforeach; ?>
<?php endif; ?>