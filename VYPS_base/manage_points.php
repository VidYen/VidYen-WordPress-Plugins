<?php
$message = '';
$uploads = wp_upload_dir();
$upload_path = $uploads['url'];

if (isset($_GET['edituserpoints'])):
    global $wpdb;
    $user_id = $_GET['edituserpoints'];
    if (isset($_POST['update_user_point'])) {
        update_user_meta($user_id, 'points', $_POST['points']);
        $add_point = (!isset($_POST['addpoint'])) ? $_POST['addpoint'] = '' : $_POST['addpoint'];
        $sub_point = (!isset($_POST['subpoint'])) ? $_POST['subpoint'] = '' : $_POST['subpoint'];

        $is_user_log = $wpdb->get_row("select * from {$wpdb->prefix}vyps_points_log where user_id = '{$user_id}' and points = '{$_POST['points']}' order by id desc");

        if (empty($is_user_log)) {

            $point_amount = $wpdb->get_row("select * from {$wpdb->prefix}vyps_points where id = '{$_POST['points']}'");

            $amount = $point_amount->points;
            $adjustment = 'plus';
            if (!empty($add_point)) {
                $amount = $point_amount->points + $add_point;
                $adjustment = 'plus';
            }
			
/* Removing the sub point menus. Will remove permantlyu later.
            if (!empty($sub_point)) {
                $amount = -$point_amount->points - $sub_point;
                $adjustment = 'minus';
//                if ($amount < 0) {
//                    $amount = 0;
//                }
            }
*/
        } else {
            $amount = $is_user_log->points_amount;
            $adjustment = 'plus';
            if (!empty($add_point)) {
                $amount = $add_point;
                $adjustment = 'plus';
            }
/* I did not like the way this was coded -Felty
            if (!empty($sub_point)) {
                $amount = -$sub_point;
                $adjustment = 'minus';
//                if ($amount < 0) {
//                    $amount = 0;
//                }
            }
*/			

        }

        $table = $wpdb->prefix . 'vyps_points_log';
        $data = [
            'reason' => $_POST['reason'],
            'points' => $_POST['points'],
            'points_amount' => $amount,
            'user_id' => $user_id,
            'adjustment' => $adjustment,
            'time' => date('Y-m-d H:i:s')
        ];
        $wpdb->insert($table, $data);
    }


    $query = "select * from " . $wpdb->prefix . 'vyps_points';
    $data = $wpdb->get_results($query);

    $query2 = "select * from {$wpdb->prefix}vyps_points_log where user_id = '{$user_id}' order by time desc";
    $point_logs = $wpdb->get_results($query2);
    ?>

    <div class="wrap">
        <?php
        $userdata = get_userdata($user_id);
        ?>
        <h1 class="wp-heading-inline">Manage Points for <strong><?= $userdata->data->user_login ?></strong> - UID #<?= $user_id; ?></h1>
        <form method="post">
            <table class="form-table">
                <tr>
                    <th><label for="points">Points</label></th>
                    <td>
                        <?php
                        $user_points = get_the_author_meta('points', $user_id);
                        ?>
                        <select class="points" id="points" name="points">
                            <option value=''>Select Points</option>
                            <?php if (!empty($data)): ?>
                                <?php foreach ($data as $d): ?>
                                    <option <?php echo ($user_points == (string) $d->id) ? 'selected' : ''; ?> value='<?= $d->id ?>'><?= $d->name; ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>                
                        <span class="description">Chose point type</span>
                    </td>
                </tr>
                <tr>
                    <th><label for="addpoint">Adjust Points Manually</label></th>
                    <td>
                        <input type="number" name="addpoint" id="addpoint">
                    </td>                    
                </tr>
				
				<?php /* The Orion person thought the below was useful. I did not. Keeping the code around until making sure it can be removed safely.
				
                <tr>
                    <td colspan="2">--- Or ----</td>
                </tr>
                <tr>
                    <th><label for="subpoint">Subtract Point</label></th>
                    <td>
                        <input type="number" name="subpoint" id="subpoint">
                    </td>                    
                </tr>
				*/ ?>
				
                <tr>
                    <th><label for="reason">Reason</label></th>
                    <td>
                        <input type="text" name="reason" id="reason" maxlength="25" value="Manual Admin Adjustment" size="50">
                    </td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" name="update_user_point" id="update_user_point" class="button button-primary" value="Update">
            </p>
        </form>
        <h2>Points Log</h2>
        <table class="wp-list-table widefat fixed striped users">
            <tr>
                <th>Date</th>
                <th>Username - #UID</th>
                <th>Point type</th>
                <th>Amount +/-</th>
                <th>Adjustment Reason</th>
            </tr>
            <?php if (!empty($point_logs)): ?>
                <?php $i = 0; ?>
                <?php foreach ($point_logs as $logs): ?>
                    <tr>
                        <td><?= $logs->time; ?></td>
                        <td><?php $userdata = get_userdata($logs->user_id); echo $userdata->data->user_login; ?> - #UID <?= $logs->user_id; ?></td>
                        <td>
                            <?php
                            $points_name = $wpdb->get_row("select * from {$wpdb->prefix}vyps_points where id= '{$logs->points}'");
                            echo $points_name->name;
                            ?>
                        </td>
						<? /*I think I can safely remove the below. Will have to find all instances. 5.20.18 - Felty */ ?>
                        <td><? /*<?= ($logs->adjustment == "plus") ? '+': ''; ?> */?> <?= $logs->points_amount; ?></td>
                        <td><?= $logs->reason; ?></td>
                    </tr>
                    <?php $i++; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">No data found yet.</td>
                </tr>
            <?php endif; ?>
            <tr>
                <th>Date</th>
                <th>Username - #UID</th>
                <th>Point type</th>
                <th>Amount +/-</th>
                <th>Adjustment Reason</th>
            </tr>
        </table>
    </div>
    <script>
        document.getElementById('addpoint').onkeydown = function () {
            document.getElementById('subpoint').disabled = true;
        }

        document.getElementById('addpoint').onblur = function () {
            var addval = document.getElementById('addpoint').value
            if (addval == '')
                document.getElementById('subpoint').disabled = false;
        }

        document.getElementById('subpoint').onblur = function () {
            var addval = document.getElementById('subpoint').value
            if (addval == '')
                document.getElementById('addpoint').disabled = false;
        }

        document.getElementById('subpoint').onkeydown = function () {
            document.getElementById('addpoint').disabled = true;
        }
    </script>
    <?php
elseif (isset($_GET['edit_vyps'])):
    if (isset($_POST['update_point'])) {
        $point_name = $_POST['point_name'];
        if (!empty($_FILES['point_icon_url']['name'])) {
            $point_icon_url = media_handle_upload('point_icon_url', 0);
            $icon = wp_get_attachment_url($point_icon_url);
        } else {
            $prev_icon = $wpdb->get_row("select * from {$wpdb->prefix}vyps_points where id = '{$_GET['edit_vyps']}'");
            $icon = $prev_icon->icon;
        }
		
		
		/* Some cases below where the post of the point would be empty */
		if (isset($_POST['point'])){
			
			$point = $_POST['point'];
			
			$table = $wpdb->prefix . 'vyps_points';
			$data = [
				'name' => $point_name,
				'icon' => $icon,
				'points' => $point,
				'time' => date('Y-m-d H:i:s')
			];
		} else {

			$table = $wpdb->prefix . 'vyps_points';
			$data = [
				'name' => $point_name,
				'icon' => $icon,
				//'points' => $point, //see what i did here?
				'time' => date('Y-m-d H:i:s')
			];
		}
        $wpdb->update($table, $data, ['id' => $_GET['edit_vyps']]);

        $message = "Updated successfully.";
    }

    $query_row = "select * from {$wpdb->prefix}vyps_points where id= '{$_GET['edit_vyps']}'";
    $row_data = $wpdb->get_row($query_row);
    ?>
    <div class="wrap">
        <h1 id="add-new-user">Update Point</h1>
        <?php if (!empty($message)): ?>
            <div id="message" class="updated notice is-dismissible">
                <p><strong><?= $message; ?>.</strong></p>
                <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
            </div>
        <?php endif; ?>
        <p>Update this point.</p>
        <form method="post" name="createuser" id="createuser" class="validate" novalidate="novalidate" enctype="multipart/form-data">
            <table class="form-table">
                <tbody>
                    <tr class="form-field form-required">
                        <th scope="row">
                            <label for="point_name">Point Name<span class="description">(required)</span></label>
                        </th>
                        <td>
                            <input name="point_name" type="text" id="point_name" value="<?= $row_data->name ?>" aria-required="true" autocapitalize="none" autocorrect="off" maxlength="60" >
                        </td>
                    </tr>        
                    <tr class="form-field form-required">
                        <th scope="row">
                            <label for="point_icon_url">Point Icon url<span class="description">(required)</span></label>
                        </th>
                        <td>
                            <?php $url = wp_get_attachment_url($row_data->icon);
                            ?>
                            <img src="<?php echo $row_data->icon; ?>" class="img-responsive" width="50px">
                            <br>
                            <input name="point_icon_url" type="file" id="point_icon_url" value="<?= $row_data->icon ?>" aria-required="true" autocapitalize="none" autocorrect="off">
                        </td>
                    </tr>
					<? /* I want to remove the below. I have no idea why they created this field despite me asking twice to remove it. -Felty */ ?>
					<? /* Ok. This line of code was for update Coin, not the New Coin. I am leaving this here to back comment later.
                    <tr class="form-field form-required">
                        <th scope="row">
                            <label for="point">Users start with how many points ? <span class="description">(required)</span></label>
                        </th>
                        <td>
                            <input name="point" type="number" min="1" id="point" value="<?= $row_data->points; ?>" aria-required="true" autocapitalize="none" autocorrect="off" maxlength="10">
                        </td>
                    </tr>    
					*/ ?>
                </tbody>
            </table>
            <p class="submit">
                <input type="submit" name="update_point" id="update_point" class="button button-primary" value="Update Point">
            </p>
        </form>
    </div>
    <?php
else:

    if (isset($_GET['delete_vyps'])):
        $table = $wpdb->prefix . 'vyps_points';
        $wpdb->delete($table, ['id' => $_GET['delete_vyps']]);
        $message = "deleted successfully.";
    endif;

    $query = "select * from " . $wpdb->prefix . 'vyps_points';
    $data = $wpdb->get_results($query);
    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline">Manage Points</h1>
        <?php if (!empty($message)): ?>
            <div id="message" class="updated notice is-dismissible">
                <p><strong><?= $message; ?>.</strong></p>
                <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
            </div>
        <?php endif; ?>
        <a href="<?= site_url(); ?>/wp-admin/admin.php?page=vyps_points_add" class="page-title-action">Add New</a>

        <hr class="wp-header-end">  

        <form method="get">                
            <h2 class="screen-reader-text">Points list</h2>
            <table class="wp-list-table widefat fixed striped users">
                <thead>
                    <tr>                    
                        <th scope="col" id="username" class="manage-column column-username column-primary sortable desc">
                            <a href="#">
                                <span>Name</span>
                                <span class="sorting-indicator"></span>
                            </a>
                        </th>
                        <th scope="col" id="name" class="manage-column column-name">Icon</th>
                        <th scope="col" id="email" class="manage-column column-email">Id</th>
                        <th scope="col" id="posts" class="manage-column column-posts num">Action</th>	
                    </tr>
                </thead>
                <tbody id="the-list" data-wp-lists="list:user">
                    <?php if (!empty($data)): ?>
                        <?php foreach ($data as $d):
                            ?>
                            <tr>
                                <td class="column-primary"><?= $d->name; ?></td>                
                                <td class="column-primary"><a href="<?php echo $d->icon; ?>" target="_blank"><img src="<?php echo $d->icon; ?>" width="42" hight="36"></a></td>
								<td class="column-primary"><?= $d->id; ?></td>
                                <td class="column-primary"><a href="<?= site_url(); ?>/wp-admin/admin.php?page=vyps_points_list&edit_vyps=<?= $d->id; ?>">Edit</a> | <a onclick="return confirm('Are you sure want to do this ?');" href="<?= site_url(); ?>/wp-admin/admin.php?page=vyps_points_list&edit_vyps=<?= $d->id; ?>">Rename</a></td> <? /* I'm being cute with this menu. *shades* -Felty*/ ?>
                            </tr>	

                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">No Point created yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>

                <tfoot>
                    <tr>                    
                        <th scope="col" id="username" class="manage-column column-username column-primary sortable desc">
                            <a href="#">
                                <span>Name</span>
                                <span class="sorting-indicator"></span>
                            </a>
                        </th>
                        <th scope="col" id="name" class="manage-column column-name">Icon</th>
                       <th scope="col" id="email" class="manage-column column-email">Id</th>
                        <th scope="col" id="posts" class="manage-column column-posts num">Action</th>	
                    </tr>
                </tfoot>
            </table>        
        </form>
        <br class="clear">
    </div>
<?php endif; ?>
