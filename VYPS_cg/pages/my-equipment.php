<?php
/**
 * Shows user individual equipment list.
 */

global $wpdb;

$user_equipment = $wpdb->get_results(
    $wpdb->prepare("SELECT * FROM $wpdb->vypsg_tracking WHERE username=%s ORDER BY id DESC", wp_get_current_user()->user_login )
);

//add counting
$equipment = [];
foreach($user_equipment as $indiv){
    if(array_key_exists($indiv->name, $equipment)){
        $equipment[$indiv->name]['amount'] += 1;
    } else {
        $equipment[$indiv->name]['id'] = $indiv->item_id;
        $equipment[$indiv->name]['amount'] = 1;
    }
}

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
                <td class="column-primary">
                    <form method="post">
                        <input type="hidden" value="<?= $equipment->id ?>" name="id"/>
                        <input type="submit" class="button-secondary" value="Buy" onclick="return confirm('Are you sure want to sell one <?= $d->name ?>?');" />
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if(empty($equipment)): ?>
            <tr>
                <td colspan="3">You have no equipment or manpower.</td>
            </tr>
        <?php endif; ?>
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