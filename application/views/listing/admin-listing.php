<?php if (!empty($admins)) : ?>
    <?php foreach ($admins as $admin) : ?>
        <tr id="<?= $admin->id; ?>" role="row" class="odd">
            <td data-name="first_name" class="editable"><?= $admin->first_name; ?></td>
            <td data-name="last_name" class="editable"><?= $admin->last_name; ?></td>
            <td><?= $admin->email; ?></td>
            <td><?= $admin->last_logged_time; ?></td>
            <td>
                <button style="display: none" class="update_<?= $admin->id; ?> update" onclick="submitHandler('<?= $admin->id; ?>');" data-toggle="tooltip" title="Update"></button>
                <button class="edit_<?= $admin->id; ?> edit" onclick="updateHandler('<?= $admin->id; ?>');" data-toggle="tooltip" title="Edit"></button>
                <button style="display: none" class="reset_<?= $admin->id; ?> reset" onclick="resetHandler('<?= $admin->id; ?>');" data-toggle="tooltip" title="Reset"></button>
                <button onclick="deleteHandler('<?= $admin->id; ?>')" class="delete" data-toggle="tooltip" title="Delete"></button>
            </td>
        </tr>
    <?php endforeach; ?>
<?php endif; ?>