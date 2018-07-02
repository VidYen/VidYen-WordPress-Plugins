<?php
/**
 * Shows battle options
 */

global $wpdb;

$pending_battles = $wpdb->get_results(
    $wpdb->prepare("SELECT * FROM $wpdb->vypsg_pending_battles WHERE ((user_one = %s) or (user_two = %s)) and battled = 0", wp_get_current_user()->user_login, wp_get_current_user()->user_login)
);

$url = site_url();

/**
 * Battles two users
 */
if(isset($_GET['battle'])){
    $pending_battles = $wpdb->get_results(
        $wpdb->prepare("SELECT * FROM $wpdb->vypsg_pending_battles WHERE id = %d", $_GET['battle'])
    );
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    if($pending_battles[0]->user_one == wp_get_current_user()->user_login || $pending_battles[0]->user_two == wp_get_current_user()->user_login){
        include __DIR__ . '/../includes/Battle.php';
        $battle = new Battle(5000, [$pending_battles[0]->user_one, $pending_battles[0]->user_two], $pending_battles[0]->id);
        $battle->startBattle();
    }

    if(!isset($_GET['return'])){
        echo '<script type="text/javascript">document.location = "' . $url .'/wp-admin/admin.php?page=battle-log";</script>';
    }
}

/**
 * Cancel a battle
 */
if(isset($_GET['cancel'])){

    $battle = $wpdb->get_results(
        $wpdb->prepare("SELECT * FROM $wpdb->vypsg_pending_battles WHERE id = %d and (user_one=%s or user_two = %s)", $_GET['cancel'], wp_get_current_user()->user_login, wp_get_current_user()->user_login )
    );

    if($battle[0]->user_one == wp_get_current_user()->user_login){
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

    if(!isset($_GET['return'])){
        echo '<script type="text/javascript">document.location = "' . $url . '/wp-admin/admin.php?page=battle";</script>';
    }

}

/**
 * Set battle as ready
 */
if(isset($_GET['ready'])){

    $battle = $wpdb->get_results(
        $wpdb->prepare("SELECT * FROM $wpdb->vypsg_pending_battles WHERE id = %d and (user_one=%s or user_two = %s)", $_GET['ready'], wp_get_current_user()->user_login, wp_get_current_user()->user_login )
    );

    if($battle[0]->user_one == wp_get_current_user()->user_login){
        $data = array('user_one_accept' => 1);
        $wpdb->update($wpdb->vypsg_pending_battles, $data, ['id' => $battle[0]->id]);
    } else {
        $data = array('user_two_accept' => 1);
        $wpdb->update($wpdb->vypsg_pending_battles, $data, ['id' => $battle[0]->id]);
    }

    if(!isset($_GET['return'])){
        echo '<script type="text/javascript">document.location = "' . $url . '/wp-admin/admin.php?page=battle";</script>';
    }

}

/**
 * Create battle
 */
if(isset($_POST['battle']) && count($pending_battles) == 0){

    $ongoing = $wpdb->get_results(
        $wpdb->prepare("SELECT * FROM $wpdb->vypsg_pending_battles WHERE user_one != %s and user_two is null", wp_get_current_user()->user_login )
    );

    if(count($ongoing) == 0){
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

    if(!isset($_GET['return'])){
        echo '<script type="text/javascript">location.reload(true);</script>';
    }
}

if(isset($_POST['battle_user']) && count($pending_battles) == 0){

    $ongoing = $wpdb->get_results(
        $wpdb->prepare("SELECT * FROM $wpdb->vypsg_pending_battles WHERE user_one != %s and user_two = ''", wp_get_current_user()->user_login )
    );

    if(count($ongoing) == 0){
        $wpdb->insert(
            $wpdb->vypsg_pending_battles,
            array(
                'user_one' => wp_get_current_user()->user_login,
                'user_two' => $_POST['username']
            ),
            array(
                '%s',
                '%s',
            )
        );
    }

    if(!isset($_GET['return'])){
        echo '<script type="text/javascript">location.reload(true);</script>';
    }
}

if(isset($_GET['return'])){
    echo '<script type="text/javascript">window.location.href = "' . $_GET['return'] . '";</script>';
}

?>

<div class="wrap">
    <h2><?php _e('Battle', 'vidyen'); ?></h2>
    <?php
        if(count($pending_battles) == 0){
            ?>
            <form method="post" action="">
                <input type="hidden" name="battle" value="true" />
               You have no ongoing battles... <input type="submit" class="button-primary" value="Random Battle"/>
            </form>
            <br />
            <form method="post" action="">
                <input type="hidden" name="battle_user" value="true" />
                <input placeholder="Enter Username" type="text" name="username" />
                <input type="submit" class="button-primary" value="Battle"/>
            </form>
            <?php
        } else {
            ?>
                You have a pending battle request... Looking for a suitable match.
            <?php
        }
    ?>
</div>

<?php if(!isset($_GET['view'])): ?>
    <div class="wrap">
        <h2><?php _e('Pending Battles', 'vidyen'); ?></h2>
        <table class="wp-list-table widefat fixed striped users">
            <thead>
            <tr>
                <th scope="col" class="manage-column column-name">Id</th>
                <th scope="col" class="manage-column column-name">Opponent</th>
                <th scope="col" class="manage-column column-name">Strength</th>
                <th scope="col" class="manage-column column-name">Action</th>
            </tr>
            </thead>
            <tbody id="the-list" data-wp-lists="list:log">
                <?php
                    if(count($pending_battles) == 0){
                        ?>
                        <tr>
                            <td colspan="4">No pending battles.</td>
                        </tr>
                        <?php
                    } else {
                        foreach($pending_battles as $pending_battle){
                            ?>
                            <tr>
                                <td><?= $pending_battle->id ?></td>
                                <?php

                                $opponent = $pending_battle->user_one;
                                $status = true;
                                $user = 2;

                                if($opponent == wp_get_current_user()->user_login){
                                    $opponent = $pending_battle->user_two;
                                    $user = 1;
                                }

                                if($opponent == '' or is_null($opponent)){
                                    $opponent = "Searching for opponent...";
                                    $status = false;
                                }

                                $user_one_accept = false;
                                if($pending_battle->user_one_accept == true){
                                    $user_one_accept = true;
                                }

                                $user_two_accept = false;
                                if($pending_battle->user_two_accept == true){
                                    $user_two_accept = true;
                                }

                                ?>
                                <td><?= $opponent ?></td>
                                <?php
                                    if($status){
                                        ?>
                                            <td><a href="<?= site_url() ?>/wp-admin/admin.php?page=battle&view=<?= $opponent ?>" class="button-secondary">View Opponent Army</a></td>
                                        <?php
                                    } else {
                                        ?>
                                        <td></td>
                                        <?php
                                    }
                                    if($status){
                                        if(!$user_two_accept && $user == 2
                                            || !$user_one_accept && $user == 1) {
                                            ?>
                                            <td>
                                                <a href="<?= site_url(); ?>/wp-admin/admin.php?page=battle&ready=<?= $pending_battle->id; ?>"
                                                   class="button-primary">Ready</a>
                                                <a href="<?= site_url(); ?>/wp-admin/admin.php?page=battle&cancel=<?= $pending_battle->id; ?>"
                                                   class="button-secondary">Cancel</a>
                                            </td>
                                            <?php
                                        }
                                        if(!$user_one_accept && $user_two_accept && $user == 2){
                                            ?>
                                            <td>Waiting for opponent to accept.</td>
                                            <?php
                                        }
                                        if(!$user_two_accept && $user_one_accept && $user == 1){
                                            ?>
                                            <td>Waiting for opponent to accept.</td>
                                            <?php
                                        }
                                        if($user_one_accept && $user_two_accept){
                                            ?>
                                            <td>
                                                <a href="<?= site_url(); ?>/wp-admin/admin.php?page=battle&battle=<?= $pending_battle->id; ?>"
                                                   class="button-primary">Battle</a>
                                            </td>
                                            <?php
                                        }
                                    } else {
                                        ?>
                                        <td>
                                            <a href="<?= site_url(); ?>/wp-admin/admin.php?page=battle&cancel=<?= $pending_battle->id; ?>" class="button-secondary">Cancel</a>
                                        </td>
                                        <?php
                                    }
                                ?>
                            </tr>
                            <?php
                        }
                    }
                ?>
            </tbody>

            <tfoot>
            <tr>
                <th scope="col" class="manage-column column-name">Id</th>
                <th scope="col" class="manage-column column-name">Opponent</th>
                <th scope="col" class="manage-column column-name">Strength</th>
                <th scope="col" class="manage-column column-name">Action</th>
            </tr>
            </tfoot>
        </table>
    </div
<?php else: ?>

<?php
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

?>
<div class="wrap">
    <h2 style="display:inline-block;">
        <?= strip_tags($_GET['view']) ?> Equipment | <a href="<?= site_url() ?>/wp-admin/admin.php?page=battle">Back</a>
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
                <td colspan="4">This user has no equipment or manpower.</td>
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