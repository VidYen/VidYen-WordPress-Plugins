<?php
/**
 * Shows battle options
 */

global $wpdb;

$pending_battles = $wpdb->get_results(
    "SELECT * FROM $wpdb->vypsg_pending_battles WHERE (user_one = '" . wp_get_current_user()->user_login . "' and user_two is null) or (user_one is null and user_two= '" . wp_get_current_user()->user_login . "')"
    );

if(isset($_POST['battle']) && count($pending_battles) == 0){

    $ongoing = $wpdb->get_results(
        $wpdb->prepare("SELECT * FROM $wpdb->vypsg_pending_battles WHERE user_one != %s and user_two = ''", wp_get_current_user()->user_login )
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
                'user_two' => $_POST['battle_user']
            ),
            array(
                '%s',
            )
        );
    }
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
                        <td colspan="3">No pending battles.</td>
                    </tr>
                    <?php
                } else {
                    foreach($pending_battles as $pending_battle){
                        ?>
                        <tr>
                            <td><?= $pending_battle->id ?></td>
                            <?php

                            $opponent = $pending_battle->user_one;
                            $user_one = true;

                            if($opponent != wp_get_current_user()->user_login){
                                $opponent = $pending_battle->user_two;
                                $user_one = false;
                            }

                            if($opponent == '' or is_null($opponent)){
                                $opponent = "Searching for opponent...";
                            }

                            ?>
                            <td><?= $opponent ?></td>

                            <?php
                                if($user_one){
                                    ?>
                                    <td><a class="button-secondary">View Opponent Army</a></td>
                                    <td><a class="button-primary">Ready</a></td>
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
