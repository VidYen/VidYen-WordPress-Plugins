<?php
/**
 * This creates the admin view to create and delete equipment
 */
if (! defined('ABSPATH')) {
    die();
}

//check if user has permission
if (! current_user_can('manage_vidyen')) {
    die('Access Denied');
}

//delete equipment
if (! empty($_GET['delete_equipment'])) {
    $total = $wpdb->delete(
        $wpdb->vypsg_equipment,
        array(
            'id' => sanitize_key($_GET['delete_equipment']),
        )
    );

    if ($total) {
        echo "<div class=\"notice notice-error is-dismissible\">";
        echo    "<p><strong>Equipment has been deleted.</strong></p>";
        echo "</div>";
    }
}

$show_previous = false;
//if submitted
if (! empty($_POST['submit'])) {
    check_admin_referer('vyps_add-equipment');

    include_once __DIR__ . '/../includes/manage-equipment.php';

    if ($_POST['submit'] === "Create Equipment") {
        if (! function_exists('wp_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }

        //upload icon file
        $movefile = wp_handle_upload($_FILES['equipment_icon'], array( 'test_form' => false ));

        $error = "";
        if (isset($movefile['error'])) {
            $error = $movefile['error'];
        }

        //creates equipment
        if (empty($error)) {
            $error = create_equipment(
                $_POST['equipment_name'],
                $_POST['equipment_description'],
                $movefile['url'],
                $_POST['equipment_point_type_id'],
                $_POST['equipment_point_cost'],
                $_POST['equipment_point_sell'],
                $_POST['equipment_manpower'],
                $_POST['equipment_manpower_use'],
                $_POST['equipment_speed_modifier'],
                $_POST['equipment_combat_range'],
                $_POST['equipment_soft_attack'],
                $_POST['equipment_hard_attack'],
                $_POST['equipment_speed_modifier'],
                $_POST['equipment_armor'],
                $_POST['equipment_entrenchment'],
                $_POST['equipment_support'],
                $_POST['equipment_faction'],
                $_POST['equipment_model_year']
            );
        }

        if (empty($error)) {
            echo "<div class=\"notice notice-success is-dismissible\">";
            echo "<p><strong>Equipment successfully added.</strong></p>";
            echo "</div>";
        } else {
            echo "<div class=\"notice notice-error is-dismissible\">";
            echo "<p><strong>$error</strong></p>";
            echo "</div>";
            $show_previous = true;
        }

        //if not creating, then edit
    } elseif ($_POST['submit'] === "Finish Edit") {
        if (! function_exists('wp_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }

        //upload icon file
        $movefile = wp_handle_upload($_FILES['equipment_icon'], array( 'test_form' => false ));

        $error = "";
        if (isset($movefile['error'])) {
            $error = $movefile['error'];
        }

        //creates equipment
        if (empty($error)) {
            $error = create_equipment(
                $_POST['equipment_name'],
                $_POST['equipment_description'],
                $movefile['url'],
                $_POST['equipment_point_type_id'],
                $_POST['equipment_point_cost'],
                $_POST['equipment_point_sell'],
                $_POST['equipment_manpower'],
                $_POST['equipment_manpower_use'],
                $_POST['equipment_speed_modifier'],
                $_POST['equipment_combat_range'],
                $_POST['equipment_soft_attack'],
                $_POST['equipment_hard_attack'],
                $_POST['equipment_speed_modifier'],
                $_POST['equipment_armor'],
                $_POST['equipment_entrenchment'],
                $_POST['equipment_support'],
                $_POST['equipment_faction'],
                $_POST['equipment_model_year'],
                true,
                $_POST['equipment_id']
            );
        }

        if (empty($error)) {
            echo "<div class=\"notice notice-success is-dismissible\">";
            echo "<p><strong>Equipment successfully added.</strong></p>";
            echo "</div>";

            $total = $wpdb->delete(
                $wpdb->vypsg_equipment,
                array(
                    'id' => $_POST['edit_equipment'],
                ),
                array(
                    '%d'
                )
            );
        } else {
            echo "<div class=\"notice notice-error is-dismissible\">";
            echo "<p><strong>$error</strong></p>";
            echo "</div>";
        }
    }
}

//select old data if editing
if (! empty($_GET['edit_equipment'])) {
    $edit_equipment = $wpdb->get_results(
        $wpdb->prepare("SELECT * FROM $wpdb->vypsg_equipment WHERE id=%d", $_GET['edit_equipment'])
    );
}

$systems = $wpdb->get_results(
   "SELECT * FROM $wpdb->vyps_points"
);

//has css for card
wp_enqueue_style('vidyen-admin', plugins_url('VYPS_cg/admin.css'), '', '1.0.5');

?>
<?php if (!empty($text)) {
    echo '<!-- Last Action --><div id="message" class="updated fade">'.removeslashes($text).'</div>';
} ?>
<form method="post" id="equipment" enctype="multipart/form-data" action="<?php echo admin_url('admin.php?page='.plugin_basename(__FILE__)); ?>">
    <?php wp_nonce_field('vyps_add-equipment'); ?>
    <?php if(!empty($_GET['edit_equipment'])) { ?> <input type="hidden" value="<?= $_GET['edit_equipment'] ?>" name="equipment_id"/> <?php } ?>
    <div class="wrap">
        <h2><?php _e('Create Equipment', 'vidyen'); ?></h2>
        <table class="form-table">
            <tr>
                <!-- Equipment name -->
                <th width="10%" scope="row" valign="top" title="This will be the name of the equipment."><?php _e('Name', 'vidyen') ?></th>
                <td width="40%"><input type="text" value="<?= !empty($_POST['equipment_name']) && $show_previous ? $_POST['equipment_name'] : ''; ?><?= !empty($_GET['edit_equipment']) ? $edit_equipment[0]->name : ''; ?>" autocomplete="off" required placeholder="Name of equipment" size="70" maxlength="32" name="equipment_name" /></td>
                <!-- Equipment description -->
                <th width="10%" scope="row" valign="top" title="Simple description of what the equipment is, and how it functions in the game."><?php _e('Description', 'vidyen') ?></th>
                <td width="40%"><input type="text" value="<?= !empty($_POST['equipment_description']) && $show_previous ? $_POST['equipment_description'] : ''; ?><?= !empty($_GET['edit_equipment']) ? $edit_equipment[0]->description : ''; ?>" autocomplete="off" required placeholder="Description of equipment" size="70" maxlength="32" name="equipment_description" /></td>
            </tr>
        </table>
        <table class="form-table">
            <tr>
                <!-- equipment icon -->
                <th width="10%" scope="row" valign="top" title="The picture that represents the equipment."><?php _e('Icon', 'vidyen') ?></th>
                <td width="40%">
                    <?php if (!empty($_GET['edit_equipment'])) {
    ?>
                        <input type="hidden" name="edit_equipment" value="<?= $_GET['edit_equipment'] ?>"/>
                        <img height="16" width="16" src="<?= $edit_equipment[0]->icon ?>"/>
                    <?php
} ?>
                    <input type="file" required name="equipment_icon" accept="image/*"/>
                </td>
                <!-- morale number -->
                <th width="10%" scope="row" valign="top"><?php _e('Morale Modifier', 'vidyen') ?></th>
                <td width="40%"><input type="number" value="<?= !empty($_POST['equipment_morale_modifier']) && $show_previous ? $_POST['equipment_morale_modifier'] : ''; ?><?= !empty($_GET['edit_equipment']) ? $edit_equipment[0]->morale_modifier : ''; ?>" autocomplete="off" required placeholder="Morale modifier of equipment" size="70" maxlength="32" name="equipment_morale_modifier" /></td>
            </tr>
        </table>
        <table class="form-table">
            <tr>
                <!-- Point type -->
                <th width="10%" scope="row" valign="top" title="The point system you want the user to be able to buy with."><?php _e('Point Type', 'vidyen') ?></th>
                <td width="40%">
                        <?php if (!empty($systems)): ?>
                            <select form="equipment" name="equipment_point_type_id">
                                <?php foreach ($systems as $system): ?>
                                    <option value="<?= $system->id ?>"><?= $system->name ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php else: ?>
                            There are no available point systems.
                        <?php endif; ?>
                </td>
                <!-- Point cost -->
                <th width="10%" scope="row" valign="top" title="How many points the equipment costs."><?php _e('Point Cost', 'vidyen') ?></th>
                <td width="40%"><input type="number" value="<?= !empty($_POST['equipment_point_cost']) && $show_previous ? $_POST['equipment_point_cost'] : ''; ?><?= !empty($_GET['edit_equipment']) ? $edit_equipment[0]->point_cost : ''; ?>" autocomplete="off" required placeholder="Point cost of equipment" size="70" maxlength="32" name="equipment_point_cost" /></td>
            </tr>
        </table>
        <table class="form-table">
            <tr>
                <!-- Point sell -->
                <th width="10%" scope="row" valign="top" title="How much the equipment sells for."><?php _e('Point Sell', 'vidyen') ?></th>
                <td width="40%"><input type="number" value="<?= !empty($_POST['equipment_point_sell']) && $show_previous ? $_POST['equipment_point_sell'] : ''; ?><?= !empty($_GET['edit_equipment']) ? $edit_equipment[0]->point_sell : ''; ?>" autocomplete="off" required placeholder="Point sell of equipment" size="70" maxlength="32" name="equipment_point_sell" /></td>
                <!-- Manpower -->
                <th width="10%" scope="row" valign="top" title="ex: tankCrew, infantry. The type of manpower."><?php _e('Manpower', 'vidyen') ?></th>
                <td width="40%"><input type="text" value="<?= !empty($_POST['equipment_manpower']) && $show_previous ? $_POST['equipment_manpower'] : ''; ?><?= !empty($_GET['edit_equipment']) ? $edit_equipment[0]->manpower : ''; ?>" autocomplete="off" required placeholder="Manpower of equipment" size="70" maxlength="32" name="equipment_manpower" /></td>
            </tr>
        </table>
        <table class="form-table">
            <tr>
                <!-- Manpower use -->
                <th width="10%" scope="row" valign="top" title="How much manpower is used."><?php _e('Manpower Use', 'vidyen') ?></th>
                <td width="40%"><input type="number" value="<?= !empty($_POST['equipment_manpower_use']) && $show_previous ? $_POST['equipment_manpower_use'] : ''; ?><?= !empty($_GET['edit_equipment']) ? $edit_equipment[0]->manpower_use : ''; ?>" autocomplete="off" required placeholder="Manpower use of equipment" size="70" maxlength="32" name="equipment_manpower_use" /></td>
                <!-- Speed modifier -->
                <th width="10%" scope="row" valign="top"><?php _e('Speed Modifier', 'vidyen') ?></th>
                <td width="40%"><input type="number" value="<?= !empty($_POST['equipment_speed_modifier']) && $show_previous ? $_POST['equipment_speed_modifier'] : ''; ?><?= !empty($_GET['edit_equipment']) ? $edit_equipment[0]->speed_modifier : ''; ?>" autocomplete="off" required placeholder="Speed modifier of equipment" size="70" maxlength="32" name="equipment_speed_modifier" /></td>
            </tr>
        </table>
        <table class="form-table">
            <tr>
                <!-- Combat range -->
                <th width="10%" scope="row" valign="top" title="How far the equipment can attack."><?php _e('Combat Range', 'vidyen') ?></th>
                <td width="40%"><input type="number" value="<?= !empty($_POST['equipment_combat_range']) && $show_previous ? $_POST['equipment_combat_range'] : ''; ?><?= !empty($_GET['edit_equipment']) ? $edit_equipment[0]->combat_range : ''; ?>" autocomplete="off" required placeholder="Combat range of equipment" size="70" maxlength="32" name="equipment_combat_range" /></td>
                <!-- Soft attack -->
                <th width="10%" scope="row" valign="top" title="Attack to the man power."><?php _e('Soft Attack', 'vidyen') ?></th>
                <td width="40%"><input type="number" value="<?= !empty($_POST['equipment_soft_attack']) && $show_previous ? $_POST['equipment_soft_attack'] : ''; ?><?= !empty($_GET['edit_equipment']) ? $edit_equipment[0]->soft_attack : ''; ?>" autocomplete="off" required placeholder="Soft attack of equipment" size="70" maxlength="32" name="equipment_soft_attack" /></td>
            </tr>
        </table>
        <table class="form-table">
            <tr>
                <!-- Hard attack -->
                <th width="10%" scope="row" valign="top" title="Attack to the equipment."><?php _e('Hard Attack', 'vidyen') ?></th>
                <td width="40%"><input type="number" value="<?= !empty($_POST['equipment_hard_attack']) && $show_previous ? $_POST['equipment_hard_attack'] : ''; ?><?= !empty($_GET['edit_equipment']) ? $edit_equipment[0]->hard_attack : ''; ?>" autocomplete="off" required placeholder="Hard attack of equipment" size="70" maxlength="32" name="equipment_hard_attack" /></td>
                <!-- Armor -->
                <th width="10%" scope="row" valign="top"><?php _e('Armor', 'vidyen') ?></th>
                <td width="40%"><input type="number" value="<?= !empty($_POST['equipment_armor']) && $show_previous ? $_POST['equipment_armor'] : ''; ?><?= !empty($_GET['edit_equipment']) ? $edit_equipment[0]->armor : ''; ?>" autocomplete="off" placeholder="Armor of equipment" size="70" maxlength="32" name="equipment_armor" /></td>
            </tr>
        </table>
        <table class="form-table">
            <tr>
                <!-- Entrenchment -->
                <th width="10%" scope="row" valign="top" title="How hard it is to destroy equipment. The higher number, harder to kill."><?php _e('Entrenchment', 'vidyen') ?></th>
                <td width="40%"><input type="number" value="<?= !empty($_GET['edit_equipment']) && $show_previous ? $edit_equipment[0]->entrenchment : ''; ?>" autocomplete="off" placeholder="Entrenchment of equipment" size="70" maxlength="32" name="equipment_entrenchment" /></td>
                <!-- Support -->
                <th width="10%" scope="row" valign="top"><?php _e('Support', 'vidyen') ?></th>
                <td width="40%">
                    <?php
                        if (!empty($_GET['edit_equipment'])) {
                            ?>
                            <select name="equipment_support">
                                <option value="1" <?php if ($edit_equipment[0]->support == '1') {
                                ?> selected="selected" <?php
                            } ?>>Yes</option>
                                <option value="0" <?php if ($edit_equipment[0]->support == '0') {
                                ?> selected="selected" <?php
                            } ?>>No</option>
                            </select>
                            <?php
                        } else {
                            ?>
                            <select name="equipment_support">
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                            <?php
                        }
                    ?>
                </td>
            </tr>
        </table>
        <table class="form-table">
            <tr>
                <!-- Faction -->
                <th width="10%" scope="row" valign="top"><?php _e('Faction', 'vidyen') ?></th>
                <td width="40%"><input type="text" value="<?= !empty($_POST['equipment_faction']) && $show_previous ? $_POST['equipment_faction'] : ''; ?><?= !empty($_GET['edit_equipment']) ? $edit_equipment[0]->faction : ''; ?>" autocomplete="off" placeholder="Faction of equipment" size="70" maxlength="32" name="equipment_faction" /></td>
                <!-- Model year -->
                <th width="10%" scope="row" valign="top"><?php _e('Model Year', 'vidyen') ?></th>
                <td width="40%"><input type="number" value="<?= !empty($_POST['equipment_model_year']) && $show_previous ? $_POST['equipment_model_year'] : ''; ?><?= !empty($_GET['year']) ? $edit_equipment[0]->model_year : ''; ?>" autocomplete="off" placeholder="Model year of equipment" size="70" maxlength="32" name="equipment_model_year" /></td>
            </tr>
        </table>
        <p>
            <input type="submit" name="submit" value="<?php empty($_GET['edit_equipment']) ? _e('Create Equipment', 'vidyen') : _e('Finish Edit', 'vidyen'); ?>"  class="button-primary" />
            &nbsp;&nbsp;
            <?php if (!empty($_GET['edit_equipment'])) : ?>
                <input type="hidden" name="edit" value="<?= $_GET['edit_equipment'] ?>"/>
                <a type="button" value="<?php _e('Cancel', 'vidyen'); ?>" class="button-secondary" href="?page=<?= $_GET['page']?>">Cancel</a>
            <?php endif; ?>
        </p>
    </div>
</form>

<hr />

<?php
    //shows existing equipment
    $data = $wpdb->get_results("SELECT * FROM $wpdb->vypsg_equipment ORDER BY id DESC");
?>
<div class="wrap">
    <h2><?php _e('Manage Equipment', 'vidyen'); ?></h2>
    <h2 class="screen-reader-text">Equipment list</h2>
    <table class="wp-list-table widefat fixed striped users">
        <thead>
        <tr>
            <th scope="col" class="manage-column column-name">Name</th>
            <th scope="col" class="manage-column column-name">Description</th>
            <th scope="col" class="manage-column column-name">Icon</th>
            <th scope="col" class="manage-column column-name">Point Type</th>
            <th scope="col" class="manage-column column-name">Point Cost</th>
            <th scope="col" class="manage-column column-name">Point Sell Cost</th>
            <th scope="col" class="manage-column column-name">Manpower</th>
            <th scope="col" class="manage-column column-name">Manpower Use</th>
            <th scope="col" class="manage-column column-name">Speed/Morale Modifier</th>
            <th scope="col" class="manage-column column-name">Combat Range</th>
            <th scope="col" class="manage-column column-name">Soft Attack</th>
            <th scope="col" class="manage-column column-name">Hard Attack</th>
            <th scope="col" class="manage-column column-name">Armor</th>
            <th scope="col" class="manage-column column-name">Entrenchment</th>
            <th scope="col" class="manage-column column-name">Support</th>
            <th scope="col" class="manage-column column-name">Faction</th>
            <th scope="col" class="manage-column column-name">Model Year</th>
            <th scope="col" class="manage-column column-name">Action</th>
        </tr>
        </thead>
        <tbody id="the-list" data-wp-lists="list:equipment">
        <?php if (!empty($data)): ?>
            <?php foreach ($data as $d):
                    $point_system = $wpdb->get_results(
                        $wpdb->prepare("SELECT * FROM $wpdb->vyps_points WHERE id=%d", $d->point_type_id)
                    );

                    if ($d->support == 1) {
                        $d->support = 'Yes';
                    } else {
                        $d->support = 'No';
                    }
                ?>
                <tr>
                    <td class="column-primary"><?= $d->name; ?></td>
                    <td class="column-primary"><?= $d->description; ?></td>
                    <td class="column-primary"><img width="42" src="<?= $d->icon; ?>"/></td>
                    <td class="column-primary"><?= $point_system[0]->name; ?></td>
                    <td class="column-primary"><?= (float)$d->point_cost; ?></td>
                    <td class="column-primary"><?= (float)$d->point_sell; ?></td>
                    <td class="column-primary"><?= $d->manpower; ?></td>
                    <td class="column-primary"><?= $d->manpower_use; ?></td>
                    <td class="column-primary"><?= $d->speed_modifier; ?>/<?= $d->morale_modifier ?></td>
                    <td class="column-primary"><?= $d->combat_range; ?></td>
                    <td class="column-primary"><?= $d->soft_attack; ?></td>
                    <td class="column-primary"><?= $d->hard_attack; ?></td>
                    <td class="column-primary"><?= $d->armor; ?></td>
                    <td class="column-primary"><?= $d->entrenchment; ?></td>
                    <td class="column-primary"><?= $d->support; ?></td>
                    <td class="column-primary"><?= $d->faction; ?></td>
                    <td class="column-primary"><?= $d->model_year; ?></td>
                    <td class="column-primary"><a href="<?= site_url(); ?>/wp-admin/admin.php?page=VYPS_cg%2Fpages%2Fmanage-equipment.php&edit_equipment=<?= $d->id; ?>">Edit</a> | <a onclick="return confirm('Are you sure want to do this ?');" href="<?= site_url(); ?>/wp-admin/admin.php?page=VYPS_cg%2Fpages%2Fmanage-equipment.php&delete_equipment=<?= $d->id; ?>">Delete</a></td>
                </tr>

            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="18">No equipment created yet.</td>
            </tr>
        <?php endif; ?>
        </tbody>

        <tfoot>
        <tr>
            <th scope="col" class="manage-column column-name">Name</th>
            <th scope="col" class="manage-column column-name">Description</th>
            <th scope="col" class="manage-column column-name">Icon</th>
            <th scope="col" class="manage-column column-name">Point Type</th>
            <th scope="col" class="manage-column column-name">Point Cost</th>
            <th scope="col" class="manage-column column-name">Point Sell Cost</th>
            <th scope="col" class="manage-column column-name">Manpower</th>
            <th scope="col" class="manage-column column-name">Manpower Use</th>
            <th scope="col" class="manage-column column-name">Speed/Morale Modifier</th>
            <th scope="col" class="manage-column column-name">Combat Range</th>
            <th scope="col" class="manage-column column-name">Soft Attack</th>
            <th scope="col" class="manage-column column-name">Hard Attack</th>
            <th scope="col" class="manage-column column-name">Armor</th>
            <th scope="col" class="manage-column column-name">Entrenchment</th>
            <th scope="col" class="manage-column column-name">Support</th>
            <th scope="col" class="manage-column column-name">Faction</th>
            <th scope="col" class="manage-column column-name">Model Year</th>
            <th scope="col" class="manage-column column-name">Action</th>
        </tr>
        </tfoot>
    </table>
</div