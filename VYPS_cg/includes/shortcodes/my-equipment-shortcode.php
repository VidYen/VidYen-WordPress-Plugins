<?php

/**
 * Creates shortcode for my equipment page
 */
function cg_my_equipment($params = array())
{
    global $wpdb;

    $return = "";
    $user_equipment = $wpdb->get_results(
        $wpdb->prepare("SELECT * FROM $wpdb->vypsg_tracking WHERE username=%s and battle_id is NULL", wp_get_current_user()->user_login)
    );

    //add counting
    $equipment = [];


    foreach ($user_equipment as $indiv) {
        if (array_key_exists($indiv->item_id, $equipment)) {
            $equipment[$indiv->item_id]['amount'] += 1;
        } else {
            $new = $wpdb->get_results(
                $wpdb->prepare("SELECT * FROM $wpdb->vypsg_equipment WHERE id=%d", $indiv->item_id)
            );

            $equipment[$indiv->item_id]['item'] = $indiv->item_id;
            $equipment[$indiv->item_id]['amount'] = 1;
            $equipment[$indiv->item_id]['name'] = $new[0]->name;
            $equipment[$indiv->item_id]['icon'] = $new[0]->icon;
        }
    }



    if (isset($_POST['sell_id'])) {
        $user_equipment = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM $wpdb->vypsg_tracking WHERE username=%s and item_id=%d", wp_get_current_user()->user_login, $_POST['sell_id'])
        );

        if(!empty($user_equipment)){
            $equipment = $wpdb->get_results(
                $wpdb->prepare("SELECT * FROM $wpdb->vypsg_equipment WHERE id=%d", $user_equipment[0]->item_id)
            );
            $table_name_log = $wpdb->prefix . 'vyps_points_log';


            $data_insert = [
                'reason' => 'Selling item',
                'point_id' => $equipment[0]->point_type_id,
                'points_amount' => $equipment[0]->point_sell,
                'user_id' => get_current_user_id(),
                'time' => date('Y-m-d H:i:s')
            ];
            $wpdb->insert($table_name_log, $data_insert);

            $wpdb->delete(
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
        }

        unset($_POST['sell_id']);
        echo '<script type="text/javascript">window.location.href = window.location.href;</script>';
    }


    $return .= "
    <div class=\"wrap\">
        <h2>
            My Equipment
        </h2>
        <table class=\"wp-list-table widefat fixed striped\">
            <thead>
            <tr>
                <th scope=\"col\" class=\"manage-column column-primary\">
                    <span>Icon</span>
                </th>
                <th scope=\"col\" class=\"manage-column column-primary\">
                    <span>Name</span>
                </th>
                <th scope=\"col\" class=\"manage-column column-primary\">
                    <span>Amount</span>
                </th>
                <th scope=\"col\" class=\"manage-column column-primary\">
                    <span>Action</span>
                </th>
            </tr>
            </thead>
            <tbody id=\"the-list\" data-wp-lists=\"list:log\">
            ";

    foreach ($equipment as $single) {
        $nonce = wp_nonce_field( 'vyps-nonce-'.$single['item'] );
        $return .= "
                <tr id=\"log-1\">
                    <td>
                        <img width=\"42\" src=\"{$single['icon']}\"/>
                    </td>
                    <td>
                        {$single['name']}
                    </td>
                    <td>
                        {$single['amount']}
                    </td>
                    <td class=\"column-primary\">
                        <form method=\"post\">
                            $nonce
                            <input type=\"hidden\" value=\"{$single['item']}\" name=\"sell_id\"/>
                            <input type=\"submit\" class=\"button-secondary\" value=\"Sell\" onclick=\"return confirm('Are you sure want to sell one {$single['name']}?');\" />
                        </form>
                    </td>
                </tr>
            ";
    }

    if (empty($equipment)) {
        $return .= "
                    <tr>
                        <td colspan=\"4\">You have no equipment or manpower.</td>
                    </tr>
                ";
    }

    $return .= "
                </tbody>
            <tfoot>
            <tr>
                <th scope=\"col\" class=\"manage-column column-primary\">
                    <span>Icon</span>
                </th>
                <th scope=\"col\" class=\"manage-column column-primary\">
                    <span>Name</span>
                </th>
                <th scope=\"col\" class=\"manage-column column-primary\">
                    <span>Amount</span>
                </th>
                <th scope=\"col\" class=\"manage-column column-primary\">
                    <span>Action</span>
                </th>
            </tr>
            </tfoot>
        </table>
    </div>
            ";

    if (!is_user_logged_in()) {
        $return = "You must log in.<br />";
    }
    return $return;
}
add_shortcode('cg-my-equipment', 'cg_my_equipment');