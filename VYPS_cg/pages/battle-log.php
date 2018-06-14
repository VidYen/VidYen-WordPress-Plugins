<?php
/**
 * Shows battle log
 */

global $wpdb;

$logs = $wpdb->get_results(
    $wpdb->prepare("SELECT * FROM $wpdb->vypsg_battles WHERE username=%s ORDER BY id DESC", wp_get_current_user()->user_login )
);

?>

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
                    $opponent = $log->winner();
                }
            ?>
            <tr id="log-1">
                <td>
                    <?= $log->id ?>
                </td>
                <td>
                    <?= $log->amount ?>
                </td>
                <td>
                    <?= $opponent ?>
                </td>
                <td>
                    <?= $outcome ?>
                </td>
                <td>
                    <a href="<?= site_url(); ?>/wp-admin/profile.php?page=battle-log&view=<?= $log->id; ?>">View Loses</a>
                </td>

            </tr>
        <?php endforeach; ?>
        <?php if(empty($equipment)): ?>
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
