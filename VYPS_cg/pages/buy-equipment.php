<?php
/**
 * Shows equipment available to buy
 */
if ( ! defined('ABSPATH' ) ) {
    die();
}

global $wpdb;

$data = $wpdb->get_results("SELECT * FROM $wpdb->vypsg_equipment ORDER BY id DESC" );

if(isset($_POST['id'])){

    $item = $wpdb->get_results(
        $wpdb->prepare("SELECT * FROM $wpdb->vypsg_equipment WHERE id=%s", $_POST['id'])
    );

    if(!empty($item)){
        $wpdb->insert(
            $wpdb->vypsg_tracking,
            array(
                'item_id' => $item[0]->id,
                'username' => wp_get_current_user()->user_login,
            ),
            array(
                '%d',
                '%s',
            )
        );

        echo "<div class=\"notice notice-success is-dismissible\">";
        echo "<p><strong>Thank you for your purchase.</strong></p>";
        echo "</div>";

    } else {
        echo "<div class=\"notice notice-error is-dismissible\">";
        echo "<p><strong>This equipment does not exist.</strong></p>";
        echo "</div>";
    }

}

?>

<div class="wrap">
    <h2><?php _e('Buy Equipment', 'vidyen'); ?></h2>
    <table class="wp-list-table widefat fixed striped users">
        <thead>
        <tr>
            <th scope="col" class="manage-column column-name">Name</th>
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
                $point_system = $wpdb->get_results(
                    $wpdb->prepare("SELECT * FROM $wpdb->vyps_points WHERE id=%d", $d->point_type_id)
                );

                if($d->support == 1){
                    $d->support = 'Yes';
                } else {
                    $d->support = 'No';
                }
                ?>
                <tr>
                    <td class="column-primary"><?= $d->name; ?></td>
                    <td class="column-primary"><?= $d->description; ?></td>
                    <td class="column-primary"><img width="42" src="<?= $d->icon; ?>"/></td>
                    <td class="column-primary"><?= $point_system[0]->name; ?></td>
                    <td class="column-primary"><?= (float)$d->point_cost; ?></td>
                    <td class="column-primary"><?= (float)$d->point_sell; ?></td>
                    <td class="column-primary"><?= $d->manpower; ?></td>
                    <td class="column-primary"><?= $d->manpower_use; ?></td>
                    <td class="column-primary"><?= $d->speed_modifier; ?></td>
                    <td class="column-primary"><?= $d->combat_range; ?></td>
                    <td class="column-primary"><?= $d->soft_attack; ?></td>
                    <td class="column-primary"><?= $d->hard_attack; ?></td>
                    <td class="column-primary"><?= $d->armor; ?></td>
                    <td class="column-primary"><?= $d->entrenchment; ?></td>
                    <td class="column-primary"><?= $d->support; ?></td>
                    <td class="column-primary"><?= $d->faction; ?></td>
                    <td class="column-primary"><?= $d->model_year; ?></td>
                    <td class="column-primary">
                        <form method="post">
                            <input type="hidden" value="<?= $d->id ?>" name="id"/>
                            <input type="submit" class="button-secondary" value="Buy" onclick="return confirm('Are you sure want to buy one <?= $d->name ?>?');" />
                        </form>
                    </td>
                </tr>

            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="18">No equipment created yet.</td>
            </tr>
        <?php endif; ?>
        </tbody>

        <tfoot>
        <tr>
            <th scope="col" class="manage-column column-name">Name</th>
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
