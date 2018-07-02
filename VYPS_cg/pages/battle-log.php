<?php
/**
 * Shows battle log
 */
if ( ! defined('ABSPATH' ) ) {
    die();
}

global $wpdb;

$logs = $wpdb->get_results(
    $wpdb->prepare("SELECT * FROM $wpdb->vypsg_battles WHERE winner=%s or loser=%s ORDER BY id DESC", wp_get_current_user()->user_login, wp_get_current_user()->user_login )
);

?>

<?php if(!isset($_GET['view'])): ?>
<div class="wrap">
    <h2><?php _e('Battle Log', 'vidyen'); ?></h2>
    <table class="wp-list-table widefat fixed striped users">
        <thead>
        <tr>
            <th scope="col" class="manage-column column-name">Id</th>
            <th scope="col" class="manage-column column-name">Opponent</th>
            <th scope="col" class="manage-column column-name">Outcome</th>
            <th scope="col" class="manage-column column-name">View Loses</th>
        </tr>
        </thead>
        <tbody id="the-list" data-wp-lists="list:log">

        <?php foreach($logs as $log): ?>
            <?php
                $opponent = "";
                $outcome = "Lost";
                if($log->winner == wp_get_current_user()->user_login){
                    $opponent = $log->loser;
                    $outcome = "Won";
                } else {
                    $opponent = $log->winner;
                }

                if($log->tie == 1){
                    $outcome = "Tie";
                }
            ?>
            <tr id="log-1">
                <td>
                    <?= $log->id ?>
                </td>
                <td>
                    <?= $opponent ?>
                </td>
                <td>
                    <?= $outcome ?>
                </td>
                <td>
                    <a class="button-secondary" href="<?= site_url(); ?>/wp-admin/profile.php?page=battle-log&view=<?= $log->battle_id; ?>">View Loses</a>
                </td>

            </tr>
        <?php endforeach; ?>
        <?php if(empty($logs)): ?>
            <tr>
                <td colspan="4">You have no battles.</td>
            </tr>
        <?php endif; ?>

        </tbody>

        <tfoot>
        <tr>
            <th scope="col" class="manage-column column-name">Id</th>
            <th scope="col" class="manage-column column-name">Opponent</th>
            <th scope="col" class="manage-column column-name">Outcome</th>
            <th scope="col" class="manage-column column-name">View Loses</th>
        </tr>
        </tfoot>
    </table>
</div
<?php else: ?>

    <?php
    $user_equipment = $wpdb->get_results(
        $wpdb->prepare("SELECT * FROM $wpdb->vypsg_tracking WHERE username=%s and battle_id = %d ORDER BY id DESC", wp_get_current_user()->user_login, $_GET['view'] )
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

    ?>
    <div class="wrap">
        <h2 style="display:inline-block;">
            Equipment | <a href="<?= site_url() ?>/wp-admin/profile.php?page=battle-log">Back</a>
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
            </tr>
            </thead>
            <tbody id="the-list" data-wp-lists="list:log">
            <?php foreach($equipment as $single): ?>
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
                </tr>
            <?php endforeach; ?>
            <?php if(empty($equipment)): ?>
                <tr>
                    <td colspan="4">No equipment was lost.</td>
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
            </tr>
            </tfoot>
        </table>
    </div>
<?php endif; ?>