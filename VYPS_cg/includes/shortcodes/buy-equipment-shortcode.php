<?php

/**
 * Creates shortcode for buy equipment page
 */
function cg_buy_equipment($params = array())
{
    global $wpdb;
    $uri_parts = explode('?', $_SERVER['REQUEST_URI'], 2);
    $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" .$_SERVER['HTTP_HOST'] . $uri_parts[0];

    $return = "";
    $data = $wpdb->get_results("SELECT * FROM $wpdb->vypsg_equipment ORDER BY id DESC");

    if (isset($_POST['buy_id'])) {
        $item = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM $wpdb->vypsg_equipment WHERE id=%s", $_POST['buy_id'])
        );

        if (!empty($item)) {
            $table_name_log = $wpdb->prefix . 'vyps_points_log';
            $balance_points_query = "SELECT sum(points_amount) FROM ". $table_name_log . " WHERE user_id = %d AND point_id = %d";
            $balance_points_query_prepared = $wpdb->prepare( $balance_points_query, get_current_user_id(), $item[0]->point_type_id );
            $balance_points = $wpdb->get_var( $balance_points_query_prepared );

            if($balance_points >= $item[0]->point_cost){
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

                $data_insert = [
                    'reason' => 'Buying item',
                    'point_id' => $item[0]->point_type_id,
                    'points_amount' => -$item[0]->point_sell,
                    'user_id' => get_current_user_id(),
                    'time' => date('Y-m-d H:i:s')
                ];
                $wpdb->insert($table_name_log, $data_insert);

                unset($_POST['buy_id']);
                echo '<script type="text/javascript">document.location = document.location;</script>';
            } else {
                $return .= "<div class=\"notice notice-error is-dismissible\">";
                $return .= "<p><strong>You do not have enough points to buy this.</strong></p>";
                $return .= "</div>";
            }

        } else {
            $return .= "<div class=\"notice notice-error is-dismissible\">";
            $return .= "<p><strong>This equipment does not exist.</strong></p>";
            $return .= "</div>";
        }
    }

    $return .= "
     <div class=\"wrap\">
        <h2>Buy Equipment</h2>
        <table class=\"wp-list-table widefat fixed striped users\">
            <thead>
            <tr>
                <th scope=\"col\" class=\"manage-column column-name\">Name</th>
                <th scope=\"col\" class=\"manage-column column-name\">Icon</th>
                <th scope=\"col\" class=\"manage-column column-name\">Point Type</th>
                <th scope=\"col\" class=\"manage-column column-name\">Point Cost</th>
                <th scope=\"col\" class=\"manage-column column-name\">Action</th>
            </tr>
            </thead>
            <tbody id=\"the-list\" data-wp-lists=\"list:equipment\">
    ";

    if (!empty($data)) {
        foreach ($data as $d) {
            $point_system = $wpdb->get_results(
                $wpdb->prepare("SELECT * FROM $wpdb->vyps_points WHERE id=%d", $d->point_type_id)
            );

            if ($d->support == 1) {
                $d->support = 'Yes';
            } else {
                $d->support = 'No';
            }

            $d->point_cost = (float)$d->point_cost;
            $nonce = wp_nonce_field( 'vyps-nonce-buy' );

            $return .= "
                                        <tr>
                        <td class=\"column-primary\">$d->name</td>
                        <td class=\"column-primary\"><img width=\"42\" src=\"$d->icon\"/></td>
                        <td class=\"column-primary\">{$point_system[0]->name}</td>
                        <td class=\"column-primary\">$d->point_cost</td>
                        <td class=\"column-primary\">
                            <form method=\"post\">
                                $nonce
                                <input type=\"hidden\" value=\"$d->id\" name=\"buy_id\"/>
                                <input type=\"submit\" class=\"button-secondary\" value=\"Buy\" onclick=\"return confirm('Are you sure want to buy one $d->name?');\" />
                            </form>
                        </td>
                    </tr>
                    ";
        }
    } else {
        $return .= "
                <tr>
                    <td colspan=\"18\">No equipment created yet.</td>
                </tr>
                ";
    }

    $return .= "
              </tbody>

            <tfoot>
            <tr>
               <th scope=\"col\" class=\"manage-column column-name\">Name</th>
                <th scope=\"col\" class=\"manage-column column-name\">Icon</th>
                <th scope=\"col\" class=\"manage-column column-name\">Point Type</th>
                <th scope=\"col\" class=\"manage-column column-name\">Point Cost</th>
                <th scope=\"col\" class=\"manage-column column-name\">Action</th>
            </tr>
            </tfoot>
        </table>
    </div
            ";
    if (!is_user_logged_in()) {
        $return = "You must log in.<br />";
    }
    return $return;
}
add_shortcode('cg-buy-equipment', 'cg_buy_equipment');