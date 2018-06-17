<?php
/**
 * Creates shortcode for my equipment page
 */
function cg_my_equipment($params = array()) {

    global $wpdb;

    $return = "";
    $user_equipment = $wpdb->get_results(
        $wpdb->prepare("SELECT * FROM $wpdb->vypsg_tracking WHERE username=%s ORDER BY id DESC", wp_get_current_user()->user_login )
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
            $wpdb->prepare("SELECT * FROM $wpdb->vypsg_tracking WHERE username=%s and item_id=%d", wp_get_current_user()->user_login, $_POST['id'] )
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
            $return .= "<div class=\"notice notice-success is-dismissible\">";
            $return .= "<p><strong>One sold.</strong></p>";
            $return .= "</div>";
        }
    }

    $return .= "
    <div class=\"wrap\">
        <h2 style=\"display:inline-block;\">
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

        foreach($equipment as $single){
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
                            <input type=\"hidden\" value=\"{$single['item']}\" name=\"id\"/>
                            <input type=\"submit\" class=\"button-secondary\" value=\"Sell\" onclick=\"return confirm('Are you sure want to sell one {$single['name']}?');\" />
                        </form>
                    </td>
                </tr>
            ";
        }

            if(empty($equipment)){
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
        return $return;
}
add_shortcode('cg-my-equipment', 'cg_my_equipment');

/**
 * Creates shortcode for buy equipment page
 */
function cg_buy_equipment($params = array()) {

    global $wpdb;

    $return = "";
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

            $return .= "<div class=\"notice notice-success is-dismissible\">";
            $return .= "<p><strong>Thank you for your purchase.</strong></p>";
            $return .= "</div>";

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

            if (!empty($data)){
                               foreach ($data as $d){
                         $point_system = $wpdb->get_results(
                        $wpdb->prepare("SELECT * FROM $wpdb->vyps_points WHERE id=%d", $d->point_type_id)
                    );

                    if($d->support == 1){
                        $d->support = 'Yes';
                    } else {
                        $d->support = 'No';
                    }

                    $d->point_cost = (float)$d->point_cost;

                    $return .= "
                                        <tr>
                        <td class=\"column-primary\"><a href=\"/wp-admin/profile.php?page=buy-equipment\">$d->name</a></td>
                        <td class=\"column-primary\"><img width=\"42\" src=\"$d->icon\"/></td>
                        <td class=\"column-primary\">{$point_system[0]->name}</td>
                        <td class=\"column-primary\">$d->point_cost</td>
                        <td class=\"column-primary\">
                            <form method=\"post\">
                                <input type=\"hidden\" value=\"$d->id\" name=\"id\"/>
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

            return $return;

}
add_shortcode('cg-buy-equipment', 'cg_buy_equipment');

/**
 * Creates shortcode for battle log page
 */
function cg_battle_log($params = array()) {


}
add_shortcode('cg-battle-log', 'cg_battle_log');

/**
 * Creates shortcode for battle page
 */
function cg_battle($params = array()) {


}
add_shortcode('cg-battle', 'cg_battle');
