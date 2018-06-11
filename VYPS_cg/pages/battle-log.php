<?php
/**
 * Shows battle log
 */

global $wpdb;

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
