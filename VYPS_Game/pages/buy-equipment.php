<?php
/**
 * Shows equipment available to buy
 */

global $wpdb;

?>

<div class="wrap">
    <h2><?php _e('Buy Equipment', 'vidyen'); ?></h2>
    <table class="wp-list-table widefat fixed striped users">
        <thead>
        <tr>
            <th scope="col" class="manage-column column-name">Id</th>
            <th scope="col" class="manage-column column-name">Description</th>
            <th scope="col" class="manage-column column-name">Icon</th>
            <th scope="col" class="manage-column column-name">Point Type</th>
            <th scope="col" class="manage-column column-name">Point Cost</th>
            <th scope="col" class="manage-column column-name">Point Sell Cost</th>
            <th scope="col" class="manage-column column-name">Manpower</th>
            <th scope="col" class="manage-column column-name">Manpower Use</th>
            <th scope="col" class="manage-column column-name">Speed Modifier</th>
            <th scope="col" class="manage-column column-name">Combat Range</th>
            <th scope="col" class="manage-column column-name">Soft Attack</th>
            <th scope="col" class="manage-column column-name">Hard Attack</th>
            <th scope="col" class="manage-column column-name">Armor</th>
            <th scope="col" class="manage-column column-name">Entrenchment</th>
            <th scope="col" class="manage-column column-name">Support</th>
            <th scope="col" class="manage-column column-name">Faction</th>
            <th scope="col" class="manage-column column-name">Model Year</th>
            <th scope="col" class="manage-column column-name">Action</th>
        </tr>
        </thead>
        <tbody id="the-list" data-wp-lists="list:equipment">
        <?php if (!empty($data)): ?>
            <?php foreach ($data as $d):
                ?>
                <tr>
                    <td class="column-primary"><?= $d->name; ?></td>
                    <td class="column-primary"><a href="<?php echo $d->icon; ?>" target="_blank"><img src="<?php echo $d->icon; ?>" width="42" hight="36"></a></td>
                    <td class="column-primary"><?= $d->id; ?></td>
                    <td class="column-primary"><a href="<?= site_url(); ?>/wp-admin/admin.php?page=vyps_points_list&edit_vyps=<?= $d->id; ?>">Edit</a> | <a onclick="return confirm('Are you sure want to do this ?');" href="<?= site_url(); ?>/wp-admin/admin.php?page=vyps_points_list&delete_vyps=<?= $d->id; ?>">Delete</a></td>
                </tr>

            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="18">No equipment available for purchase yet.</td>
            </tr>
        <?php endif; ?>
        </tbody>

        <tfoot>
        <tr>
            <th scope="col" class="manage-column column-name">Id</th>
            <th scope="col" class="manage-column column-name">Description</th>
            <th scope="col" class="manage-column column-name">Icon</th>
            <th scope="col" class="manage-column column-name">Point Type</th>
            <th scope="col" class="manage-column column-name">Point Cost</th>
            <th scope="col" class="manage-column column-name">Point Sell Cost</th>
            <th scope="col" class="manage-column column-name">Manpower</th>
            <th scope="col" class="manage-column column-name">Manpower Use</th>
            <th scope="col" class="manage-column column-name">Speed Modifier</th>
            <th scope="col" class="manage-column column-name">Combat Range</th>
            <th scope="col" class="manage-column column-name">Soft Attack</th>
            <th scope="col" class="manage-column column-name">Hard Attack</th>
            <th scope="col" class="manage-column column-name">Armor</th>
            <th scope="col" class="manage-column column-name">Entrenchment</th>
            <th scope="col" class="manage-column column-name">Support</th>
            <th scope="col" class="manage-column column-name">Faction</th>
            <th scope="col" class="manage-column column-name">Model Year</th>
            <th scope="col" class="manage-column column-name">Action</th>
        </tr>
        </tfoot>
    </table>
</div
