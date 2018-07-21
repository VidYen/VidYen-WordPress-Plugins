<?php

/**
 * Creates shortcode for battle log page
 */
function cg_battle_log($params = array())
{
    if (!is_user_logged_in()) {
        $return = "You must log in.<br />";
        return $return;
    }

    global $wpdb;
    $logs = $wpdb->get_results(
        $wpdb->prepare("SELECT * FROM $wpdb->vypsg_battles WHERE winner=%s or loser=%s ORDER BY id DESC", wp_get_current_user()->user_login, wp_get_current_user()->user_login )
    );

    $uri_parts = explode('?', $_SERVER['REQUEST_URI'], 2);
    $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" .$_SERVER['HTTP_HOST'] . $uri_parts[0];

    if (!isset($_GET['view_log'])) {
        $return = "
        <div class=\"wrap\">
        <h2>Battle Log</h2>
        <table class=\"wp-list-table widefat fixed striped users\">
            <thead>
            <tr>
                <th scope=\"col\" class=\"manage-column column-name\">Id</th>
                <th scope=\"col\" class=\"manage-column column-name\">Opponent</th>
                <th scope=\"col\" class=\"manage-column column-name\">Outcome</th>
                <th scope=\"col\" class=\"manage-column column-name\">View Loses</th>
            </tr>
            </thead>
            <tbody data-wp-lists=\"list:log\">
            ";
        foreach ($logs as $log) {
            $opponent = "";
            $outcome = "You Lost";
            if ($log->winner == wp_get_current_user()->user_login) {
                $opponent = $log->loser;
                $outcome = "You Won";
            } else {
                $opponent = $log->winner;
            }

            if ($log->tie == 1) {
                $outcome = "You Tied";
            }

            $params = $_GET;
            unset($params["view_log"]);
            $params["view_log"] = $log->battle_id;
            $params["return_log"] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";;

            $new_query_string = http_build_query($params);
            $log_url = $url . '?' . $new_query_string;

            $return .= "
                <tr>
                    <td>
                      $log->id
                    </td>
                    <td>
                        $opponent
                    </td>
                    <td>
                        $outcome
                    </td>
                    <td>
                        <a class=\"button-secondary\" href=\"$log_url\">View Loses</a>
                    </td>
    
                </tr>
                ";
        }

        if (!count($logs)) {
            $return .= "<tr>
                    <td colspan=\"4\">You have no battles .</td>
                </tr>";
        }
        $return .= "

            </tbody>

            <tfoot>
            <tr>
                <th scope=\"col\" class=\"manage-column column-name\">Id</th>
                <th scope=\"col\" class=\"manage-column column-name\">Opponent</th>
                <th scope=\"col\" class=\"manage-column column-name\">Outcome</th>
                <th scope=\"col\" class=\"manage-column column-name\">View Loses</th>
            </tr>
            </tfoot>
        </table>
    </div>
    ";
        return $return;
    } else {
        $user_equipment = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM $wpdb->vypsg_tracking WHERE username=%s and (battle_id = %d or captured_id = %d) ORDER BY id DESC", wp_get_current_user()->user_login, $_GET['view_log'], $_GET['view_log'])
        );

        //add counting
        $equipment = [];

        foreach ($user_equipment as $indiv) {

            if (array_key_exists($indiv->item_id, $equipment)) {
                $equipment[$indiv->item_id]['amount'] += 1;
                if(!is_null($indiv->captured_id)){
                    $equipment[$indiv->item_id]['captured']++;
                }
            } else {
                $new = $wpdb->get_results(
                    $wpdb->prepare("SELECT * FROM $wpdb->vypsg_equipment WHERE id=%d", $indiv->item_id)
                );

                $equipment[$indiv->item_id]['item'] = $indiv->item_id;
                $equipment[$indiv->item_id]['amount'] = 1;
                $equipment[$indiv->item_id]['name'] = $new[0]->name;
                $equipment[$indiv->item_id]['icon'] = $new[0]->icon;
                if(!is_null($indiv->captured_id)){
                    $equipment[$indiv->item_id]['captured'] = 1;
                } else {
                    $equipment[$indiv->item_id]['captured'] = 0;
                }
            }
        }

        $return_url = urldecode($_GET['return_log']);

        $return = "
           <div class=\"wrap\">
        <h2>
            Equipment | <a href=\"{$return_url}\">Back</a>
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

        foreach ($equipment as $single) {
            $return .= "
                  <tr id=\"log-1\">
                    <td>
                        <img width=\"42\" src=\"{$single['icon']}\"/>
                    </td>
                    <td>
                        {$single['name']}
                    </td>
                    <td>
                        {$single['amount']} Lost, {$single['captured']} Captured
                    </td>
                </tr>
                ";
        }

        if (empty($equipment)) {
            $return .= "
                    <tr>
                        <td colspan=\"4\">No equipment was lost.</td>
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
            </tr>
            </tfoot>
        </table>
    </div>
            ";

        return $return;
    }
}
add_shortcode('cg-battle-log', 'cg_battle_log');