<?php
/**
 * Shows user individual equipment list.
 */
if ( ! defined('ABSPATH' ) ) {
    die();
}

global $wpdb;

$user_equipment = $wpdb->get_results(
    $wpdb->prepare("SELECT * FROM $wpdb->vypsg_tracking WHERE username=%s and battle_id is null ORDER BY id DESC", wp_get_current_user()->user_login )
);

//add counting
$equipment = [];


foreach($user_equipment as $indiv){

    if(array_key_exists($indiv->item_id, $equipment)){
        $equipment[$indiv->item_id]['amount'] += 1;
    } else {
        $new = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM $wpdb->vypsg_equipment WHERE id=%d", $indiv->item_id )
        );

        $equipment[$indiv->item_id]['item'] = $indiv->item_id;
        $equipment[$indiv->item_id]['amount'] = 1;
        $equipment[$indiv->item_id]['name'] = $new[0]->name;
        $equipment[$indiv->item_id]['icon'] = $new[0]->icon;
    }
}

if(isset($_POST['id'])){
    $user_equipment = $wpdb->get_results(
        $wpdb->prepare("SELECT * FROM $wpdb->vypsg_tracking WHERE username=%s and item_id=%d and battle_id is null", wp_get_current_user()->user_login, $_POST['id'] )
    );

    $total = $wpdb->delete(
        $wpdb->vypsg_tracking,
        array(
            'id' => $user_equipment[0]->id,
            'username' => wp_get_current_user()->user_login,
        ),
        array(
            '%d',
            '%s',
        )
    );

    if(!empty($total)){
        echo "<div class=\"notice notice-success is-dismissible\">";
        echo "<p><strong>One sold.</strong></p>";
        echo "</div>";
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
                <span>Icon</span>
            </th>
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
            <?php
                if(isset($_POST['id'])){
                    $single['amount']--;
                }
            ?>
            <tr id="log-1">
                <td>
                    <img width="42" src="<?= $single['icon']; ?>"/>
                </td>
                <td>
                    <?= $single['name'] ?>
                </td>
                <td>
                    <?= $single['amount'] ?>
                </td>
                <td class="column-primary">
                    <form method="post">
                        <input type="hidden" value="<?= $single['item'] ?>" name="id"/>
                        <input type="submit" class="button-secondary" value="Sell" onclick="return confirm('Are you sure want to sell one <?= $single['name'] ?>?');" />
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if(empty($equipment)): ?>
            <tr>
                <td colspan="4">You have no equipment or manpower.</td>
            </tr>
        <?php endif; ?>
        </tbody>
        <tfoot>
        <tr>
        <tr>
            <th scope="col" class="manage-column column-primary">
                <span>Icon</span>
            </th>
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