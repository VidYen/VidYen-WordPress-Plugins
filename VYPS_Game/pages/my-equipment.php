<?php
/**
 * Shows user individual equipment list.
 */

global $wpdb;

$available_equipment = $wpdb->get_results(
    "SELECT * FROM $wpdb->vypsg_equipment"
);

$user_equipment = $wpdb->get_results(
    $wpdb->prepare("SELECT * FROM $wpdb->vypsg_tracking WHERE username=%s ORDER BY id DESC", wp_get_current_user()->user_login )
);


//add counting
$equipment = [];

?>
<div class="wrap">
    <h2 style="display:inline-block;">
        My Equipment
    </h2>
    <table class="wp-list-table widefat fixed striped">
        <thead>
        <tr>
            <th scope="col" class="manage-column column-primary">
                <span>Name</span>
            </th>
            <th scope="col" class="manage-column column-primary">
                <span>Amount</span>
            </th>
            <th scope="col" class="manage-column column-primary">
                <span>Action</span>
            </th>
        </tr>
        </thead>
        <tbody id="the-list" data-wp-lists="list:log">
        <?php foreach($equipment as $single): ?>
            <tr id="log-1">
                <td>
                    <?= $equipment->name ?>
                </td>
                <td>
                    <?= $equipment->amount ?>
                </td>
            </tr>
        <?php endforeach; ?>

        </tbody>
        <tfoot>
        <tr>
        <tr>
            <th scope="col" class="manage-column column-primary">
                <span>Name</span>
            </th>
            <th scope="col" class="manage-column column-primary">
                <span>Amount</span>
            </th>
            <th scope="col" class="manage-column column-primary">
                <span>Action</span>
            </th>
        </tr>
        </tfoot>
    </table>
</div>