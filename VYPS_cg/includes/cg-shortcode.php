<?php


/**
 * Creates shortcode for my equipment page
 */
function cg_my_equipment($params = array())
{
    global $wpdb;

    $return = "";
    $user_equipment = $wpdb->get_results(
        $wpdb->prepare("SELECT * FROM $wpdb->vypsg_tracking WHERE username=%s ORDER BY id DESC", wp_get_current_user()->user_login)
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

        if (!empty($total)) {
            $return .= "<div class=\"notice notice-success is-dismissible\">";
            $return .= "<p><strong>One sold.</strong></p>";
            $return .= "</div>";
        }

        unset($_POST['sell_id']);
        echo '<script type="text/javascript">window.location.href = window.location.href;</script>';
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
                    <td class=\"column-primary\">
                        <form method=\"post\">
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
        unset($_POST['buy_id']);
        echo '<script type="text/javascript">window.location.href = window.location.href;</script>';
    }

    $return .= "
     <div class=\"wrap\">
        <h2 style=\"display:inline-block;\">Buy Equipment</h2>
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

            $return .= "
                                        <tr>
                        <td class=\"column-primary\">$d->name</td>
                        <td class=\"column-primary\"><img width=\"42\" src=\"$d->icon\"/></td>
                        <td class=\"column-primary\">{$point_system[0]->name}</td>
                        <td class=\"column-primary\">$d->point_cost</td>
                        <td class=\"column-primary\">
                            <form method=\"post\">
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
        <h2 style=\"display:inline-block;\">Battle Log</h2>
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
            $outcome = "Lost";
            if ($log->winner == wp_get_current_user()->user_login) {
                $opponent = $log->loser;
                $outcome = "Won";
            } else {
                $opponent = $log->winner;
            }

            if ($log->tie == 1) {
                $outcome = "Tie";
            }

            $params = $_GET;
            unset($params["view_log"]);
            $params["view_log"] = $log->id;
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

        if (empty($logs)) {
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
            $wpdb->prepare("SELECT * FROM $wpdb->vypsg_tracking WHERE username=%s and battle_id = %d ORDER BY id DESC", wp_get_current_user()->user_login, $_GET['view_log'])
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
add_shortcode('cg-battle-log', 'cg_battle_log');

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
    if (isset($_GET['battle'])) {
        $pending_battles = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM $wpdb->vypsg_pending_battles WHERE id = %d", $_GET['battle'])
        );
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        if ($pending_battles[0]->user_one == wp_get_current_user()->user_login || $pending_battles[0]->user_two == wp_get_current_user()->user_login) {
            include __DIR__ . '/../includes/Battle.php';
            $battle = new Battle(5000, [$pending_battles[0]->user_one, $pending_battles[0]->user_two], $pending_battles[0]->id);
            $battle->startBattle();
        }

        if (isset($_GET['return_battle'])) {
            $return_url = urldecode($_GET['return_battle']);
            echo '<script type="text/javascript">document.location = "' . $return_url . '";</script>';
        }
    }

    /**
     * Cancel a battle
     */
    if (isset($_GET['cancel'])) {
        $battle = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM $wpdb->vypsg_pending_battles WHERE id = %d and (user_one=%s or user_two = %s)", $_GET['cancel'], wp_get_current_user()->user_login, wp_get_current_user()->user_login)
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

        if (isset($_GET['return_battle'])) {
            $return_url = urldecode($_GET['return_battle']);
            echo '<script type="text/javascript">document.location = "' . $return_url . '";</script>';
        }
    }

    /**
     * Set battle as ready
     */
    if (isset($_GET['ready'])) {
        $battle = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM $wpdb->vypsg_pending_battles WHERE id = %d and (user_one=%s or user_two = %s)", $_GET['ready'], wp_get_current_user()->user_login, wp_get_current_user()->user_login)
        );

        if ($battle[0]->user_one == wp_get_current_user()->user_login) {
            $data = array('user_one_accept' => 1);
            $wpdb->update($wpdb->vypsg_pending_battles, $data, ['id' => $battle[0]->id]);
        } else {
            $data = array('user_two_accept' => 1);
            $wpdb->update($wpdb->vypsg_pending_battles, $data, ['id' => $battle[0]->id]);
        }

        if (isset($_GET['return_battle'])) {
            $return_url = urldecode($_GET['return_battle']);
            echo '<script type="text/javascript">document.location = "' . $return_url . '";</script>';
        }
    }

    /**
     * Create battle
     */
    if (isset($_POST['battle']) && count($pending_battles) == 0) {
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
            $return .= "
                        <tr>
                            <td colspan=\"4\">
                                <form method=\"post\" action=\"\">
                                    <input type=\"hidden\" name=\"battle\" value=\"true\" />
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
                $user = 2;

                if ($opponent == wp_get_current_user()->user_login) {
                    $opponent = $pending_battle->user_two;
                    $user = 1;
                }

                if ($opponent == '' or is_null($opponent)) {
                    $opponent = "Searching for opponent...";
                    $status = false;
                }

                $user_one_accept = false;
                if ($pending_battle->user_one_accept == true) {
                    $user_one_accept = true;
                }

                $user_two_accept = false;
                if ($pending_battle->user_two_accept == true) {
                    $user_two_accept = true;
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
                    if (!$user_two_accept && $user == 2
                        || !$user_one_accept && $user == 1) {
                        $params = $_GET;
                        unset($params["ready"]);
                        $params["ready"] = $pending_battle->id;
                        $params["return_battle"] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";;

                        $new_query_string = http_build_query($params);
                        $battle_url = $url . '?' . $new_query_string;

                        $params = $_GET;
                        unset($params["cancel"]);
                        $params["cancel"] = $pending_battle->id;
                        $params["return_battle"] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";;

                        $new_query_string = http_build_query($params);
                        $cancel_url = $url . '?' . $new_query_string;

                        $return .= "<td>
                                                <a href=\"$battle_url\"
                                                   class=\"button-primary\">Ready</a>
                                                <a href=\"$cancel_url\"
                                                   class=\"button-secondary\">Cancel</a>
                                            </td>";
                    }
                    if (!$user_one_accept && $user_two_accept && $user == 2) {
                        $return .= "<td>Waiting for opponent to accept.</td>";
                    }
                    if (!$user_two_accept && $user_one_accept && $user == 1) {
                        $return .= "<td>Waiting for opponent to accept.</td>";
                    }
                    if ($user_one_accept && $user_two_accept) {
                        $params = $_GET;
                        unset($params["battle"]);
                        $params["battle"] = $pending_battle->id;
                        $params["return_battle"] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";;

                        $new_query_string = http_build_query($params);
                        $battle_url = $url . '?' . $new_query_string;

                        $return .="
                                            <td>
                                                <a href=\"$battle_url\"
                                                   class=\"button-primary\">Battle</a>
                                            </td>";
                    }
                } else {
                    $params = $_GET;
                    unset($params["cancel"]);
                    $params["cancel"] = $pending_battle->id;
                    $params["return_battle"] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";;

                    $new_query_string = http_build_query($params);
                    $cancel_url = $url . '?' . $new_query_string;


                    $return .= "<td>
                                            <a href=\"$cancel_url\" class=\"button-secondary\">Cancel</a>
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
            $wpdb->prepare("SELECT * FROM $wpdb->vypsg_tracking WHERE username=%s ORDER BY id DESC", $_GET['view'] )
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
