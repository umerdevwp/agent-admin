<?php if (!empty($admins)) : ?>
    <?php foreach ($admins as $admin) : ?>
        <tr id="<?= $admin->id; ?>" role="row" class="odd">
            <td data-name="first_name" class="editable"><?= $admin->first_name; ?></td>
            <td data-name="last_name" class="editable"><?= $admin->last_name; ?></td>
            <td><?= $admin->email; ?></td>
            <td><?= $admin->last_logged_time; ?></td>
            <td>
                <button style="display: none" class="update_<?= $admin->id; ?>" onclick="submitHandler('<?= $admin->id; ?>');">Update</button>
                <button class="edit_<?= $admin->id; ?>" onclick="updateHandler('<?= $admin->id; ?>');">Edit</button>
                <button style="display: none" class="reset_<?= $admin->id; ?>" onclick="resetHandler('<?= $admin->id; ?>');">Reset</button>
                <button onclick="deleteHandler('<?= $admin->id; ?>')">Delete</button>
            </td>
        </tr>
    <?php endforeach; ?>
<?php endif; ?>