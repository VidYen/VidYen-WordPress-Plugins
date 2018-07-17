<?php

/**
 * Creates shortcode for battle log page
 */
function cg_battle_log_all($params = array())
{

    global $wpdb;
    $logs = $wpdb->get_results(
        "SELECT * FROM $wpdb->vypsg_battles"
    );
    $uri_parts = explode('?', $_SERVER['REQUEST_URI'], 2);
    $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" .$_SERVER['HTTP_HOST'] . $uri_parts[0];

    if (!isset($_GET['view_log'])) {
        $return = "
        <div class=\"wrap\">
        <h2>All Battle Log</h2>
        <table class=\"wp-list-table widefat fixed striped users\">
            <thead>
            <tr>
                <th scope=\"col\" class=\"manage-column column-name\">Id</th>
                <th scope=\"col\" class=\"manage-column column-name\">Person One</th>
                <th scope=\"col\" class=\"manage-column column-name\">Person Two</th>
                <th scope=\"col\" class=\"manage-column column-name\">Outcome</th>
                <th scope=\"col\" class=\"manage-column column-name\">View Loses</th>
            </tr>
            </thead>
            <tbody data-wp-lists=\"list:log\">
            ";

        foreach ($logs as $log) {

            $outcome = "{$log->winner} won";

            if ($log->tie == 1) {
                $outcome = "Tie";
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
                        $log->winner
                    </td>
                        <td>
                        $log->loser
                    </td>
                    <td>
                        $outcome
                    </td>
                    <td>
                        <a class=\"button - secondary\" href=\"$log_url\">View Loses</a>
                    </td>
    
                </tr>
                ";
        }

        if (empty($logs)) {
            $return .= "<tr>
                    <td colspan=\"4\">No battles.</td>
                </tr>";
        }
        $return .= "

            </tbody>

            <tfoot>
            <tr>
                <th scope=\"col\" class=\"manage-column column-name\">Id</th>
                <th scope=\"col\" class=\"manage-column column-name\">Person One</th>
                <th scope=\"col\" class=\"manage-column column-name\">Person Two</th>
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
            $wpdb->prepare("SELECT * FROM $wpdb->vypsg_tracking WHERE battle_id = %d ORDER BY id DESC", $_GET['view_log'])
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
                        {$single['amount']}
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
add_shortcode('cg-battle-log-all', 'cg_battle_log_all');