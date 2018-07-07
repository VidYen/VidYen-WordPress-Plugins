<?php

/**
 * Creates shortcode for battle page
 */
function cg_battle($params = array())
{
    global $wpdb;
    $pending_battles = $wpdb->get_results(
        $wpdb->prepare("SELECT * FROM $wpdb->vypsg_pending_battles WHERE ((user_one = %s) or (user_two = %s)) and battled = 0", wp_get_current_user()->user_login, wp_get_current_user()->user_login)
    );

    /**
     * Battles two users
     */
    if (isset($_POST['battle'])) {
        $pending_battles = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM $wpdb->vypsg_pending_battles WHERE id = %d", $_POST['battle'])
        );

        if ($pending_battles[0]->user_one == wp_get_current_user()->user_login || $pending_battles[0]->user_two == wp_get_current_user()->user_login) {
            include_once plugin_dir_path(__file__) . '../Battle.php';
            $battle = new Battle(5000, [$pending_battles[0]->user_one, $pending_battles[0]->user_two], $pending_battles[0]->id);
            $battle->startBattle();
        }
        echo '<script type="text/javascript">document.location = document.location;</script>';
    }

    /**
     * Cancel a battle
     */
    if (isset($_POST['cancel'])) {
        $battle = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM $wpdb->vypsg_pending_battles WHERE id = %d and (user_one=%s or user_two = %s)", $_POST['cancel'], wp_get_current_user()->user_login, wp_get_current_user()->user_login)
        );

        if ($battle[0]->user_one == wp_get_current_user()->user_login) {
            $total = $wpdb->delete(
                $wpdb->vypsg_pending_battles,
                array(
                    'id' => $battle[0]->id,
                ),
                array(
                    '%d'
                )
            );
        } else {
            $data = array('user_two' => null, 'user_two_accept' => null);
            $wpdb->update($wpdb->vypsg_pending_battles, $data, ['id' => $battle[0]->id]);
        }
        echo '<script type="text/javascript">document.location = document.location;</script>';
    }

    /**
     * Create battle
     */
    if (isset($_POST['create_battle']) && count($pending_battles) == 0) {
        $ongoing = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM $wpdb->vypsg_pending_battles WHERE user_one != %s and user_two is null", wp_get_current_user()->user_login)
        );

        if (count($ongoing) == 0) {
            $wpdb->insert(
                $wpdb->vypsg_pending_battles,
                array(
                    'user_one' => wp_get_current_user()->user_login,
                ),
                array(
                    '%s',
                )
            );
        } else {
            $data = [
                'user_two' => wp_get_current_user()->user_login,
            ];

            $wpdb->update($wpdb->vypsg_pending_battles, $data, ['id' => $ongoing[0]->id]);
        }

        echo '<script type="text/javascript">document.location = document.location;</script>';

    }

    if (isset($_GET['return_battle'])) {
        $return_url = urldecode($_GET['return_battle']);
        echo '<script type="text/javascript">document.location = "' . $return_url . '";</script>';
    }

    $uri_parts = explode('?', $_SERVER['REQUEST_URI'], 2);
    $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" .$_SERVER['HTTP_HOST'] . $uri_parts[0];

    if(!isset($_GET['view_army'])){
        $return = "
   <div class=\"wrap\">
        <h2>Pending Battles</h2>
        <table class=\"wp-list-table widefat fixed striped users\">
            <thead>
            <tr>
                <th scope=\"col\" class=\"manage-column column-name\">Id</th>
                <th scope=\"col\" class=\"manage-column column-name\">Opponent</th>
                <th scope=\"col\" class=\"manage-column column-name\">Strength</th>
                <th scope=\"col\" class=\"manage-column column-name\">Action</th>
            </tr>
            </thead>
            <tbody id=\"the-list\" data-wp-lists=\"list:log\">";

        if (count($pending_battles) == 0) {
            $nonce = wp_nonce_field( 'vyps-nonce-create-battle' );
            $return .= "
                        <tr>
                            <td colspan=\"4\">
                                <form method=\"post\" action=\"\">
                                    $nonce
                                    <input type=\"hidden\" name=\"create_battle\" value=\"true\" />
                                    <input type=\"submit\" class=\"button-primary\" value=\"Random Battle\"/>
                                </form>
                            </td>
                        </tr>
                        ";
        } else {
            foreach ($pending_battles as $pending_battle) {
                $return .= "<tr><td>$pending_battle->id</td>";

                $opponent = $pending_battle->user_one;
                $status = true;

                if ($opponent == wp_get_current_user()->user_login) {
                    $opponent = $pending_battle->user_two;
                }

                if ($opponent == '' or is_null($opponent)) {
                    $opponent = "Searching for opponent...";
                    $status = false;
                }

                $return .= "<td>$opponent</td>";
                if ($status) {
                    $params = $_GET;
                    unset($params["view_log"]);
                    unset($params["return_army_log"]);
                    $params["view_army"] = $opponent;
                    $params["return_army_log"] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";;

                    $new_query_string = http_build_query($params);
                    $battle_url = $url . '?' . $new_query_string;

                    $return .= "<td><a href=\"$battle_url\" class=\"button-secondary\">View Opponent Army</a></td>";
                } else {
                    $return .= "<td></td>";
                }
                if ($status) {
                        $nonce = wp_nonce_field( 'vyps-nonce-battle' );

                        $return .="
                                            <td>
                                                <form method=\"POST\">
                                                    $nonce
                                                    <input name=\"battle\" value=\"{$pending_battle->id}\" type=\"hidden\"/>
                                                    <input type=\"submit\" value=\"Battle\"/>
                                                </form>
                                            </td>";
                } else {
                    $nonce = wp_nonce_field( 'vyps-nonce-cancel' );
                    $return .= "<td>
                                        <form method=\"POST\">
                                            $nonce
                                            <input name=\"cancel\" value=\"{$pending_battle->id}\" type=\"hidden\"/>
                                            <input type=\"submit\" value=\"Cancel\"/>
                                        </form>
                                        </td>";
                }
                $return .= "</tr>";
            }
        }

        $return .= " </tbody>

            <tfoot>
            <tr>
                <th scope=\"col\" class=\"manage-column column-name\">Id</th>
                <th scope=\"col\" class=\"manage-column column-name\">Opponent</th>
                <th scope=\"col\" class=\"manage-column column-name\">Strength</th>
                <th scope=\"col\" class=\"manage-column column-name\">Action</th>
            </tr>
            </tfoot>
        </table>
    </div
    ";
    } else {
        $user_equipment = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM $wpdb->vypsg_tracking WHERE username=%s ORDER BY id DESC", $_GET['view_army'] )
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

        $return ="
        <div class=\"wrap\">
    <h2 style=\"display:inline-block;\">
        Equipment | <a href=\"{$_GET['return_army_log']}\">Back</a>
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
                </tr>
            ";
        }

        if(empty($equipment)){
            $return .= "
                    <tr>
                <td colspan=\"4\">This user has no equipment or manpower.</td>
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
        </tr>
        </tfoot>
    </table>
</div>
        ";
    }

    if (!is_user_logged_in()) {
        $return = "You must log in.<br />";
    }
    return $return;
}
add_shortcode('cg-battle', 'cg_battle');
