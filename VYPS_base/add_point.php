<?php

$mesage = '';
$query = "select * from " . $wpdb->prefix . 'vyps_points';
    $data = $wpdb->get_results($query);

$points=count($data);
if(isset($_POST['nomore']))
{ $message = "You have already created the maximum of 3 point types."; }
else
{
if (isset($_POST['add_point'])) {

    $point_name = $_POST['point_name'];

    $point_icon_url = media_handle_upload('point_icon_url',0); 

//    $point = $_POST['point'];
// The below comment out was not done by me. The Above was. -Felty
//    $icon=$_FILES['point_icon_url']['name'];
    $icon = wp_get_attachment_url( $point_icon_url );
    $table = $wpdb->prefix . 'vyps_points';
    $data = [
        'name' => $point_name,
        'icon' => $icon,
        'time' => date('Y-m-d H:i:s')
    ];
    $data_id = $wpdb->insert($table, $data);

    //'points' => $point,
    $message = "Added successfully.";

?><script>window.location.href="admin.php?page=vyps_points_list";</script> <?php

    
}
}
?>
<div class="wrap">
    <h1 id="add-new-user">Add Point</h1>
    <?php if(!empty($message)): ?>
    <div id="message" class="updated notice is-dismissible">
        <p><strong><?= $message; ?>.</strong></p>
        <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
    </div>
    <?php endif; ?>
    <p>Create a new point.</p>
    <form method="post" name="createuser" id="createuser" class="validate" novalidate="novalidate" enctype="multipart/form-data">
        <table class="form-table">
        <tbody>
            <tr class="form-field form-required">
                <th scope="row">
                    <label for="point_name">Point Name<span class="description">(required)</span></label>
                </th>
                <td>
                    <input name="point_name" type="text" id="point_name" value="" aria-required="true" autocapitalize="none" autocorrect="off" maxlength="60">
                </td>
            </tr>        
            <tr class="form-field form-required">
                <th scope="row">
                    <label for="point_icon_url">Point Icon url<span class="description">(required)</span></label>
                </th>
                <td>
                    <input name="point_icon_url" type="file" id="point_icon_url" value="" aria-required="true" autocapitalize="none" autocorrect="off">
                </td>
            </tr>
			<? /* Commenting all this out as really asked a few times not to have this, yet they kept it. KISS -Felty */?>
            <? /* <tr class="form-field form-required">
                <th scope="row">
                    <label for="point">Users start with how many points ? <span class="description">(required)</span></label>
                </th>
                <td>
                    <input name="point" type="number" min="1" id="point" value="" aria-required="true" autocapitalize="none" autocorrect="off" maxlength="10">
                </td>
            </tr>
			*/ ?>
        </tbody>
        </table>
        <p class="submit">
<?php
if($points>=3)
{
?>
            <input type="submit" name="nomore" id="add_point" class="button button-primary" value="Add New Point"> <?php
}
else
{
?>
            <input type="submit" name="add_point" id="add_point" class="button button-primary" value="Add New Point"> <?php } ?>
        </p>
    </form>
</div>
