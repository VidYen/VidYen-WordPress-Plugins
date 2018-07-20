<?php

function check_equipment_errors(
    $name,
    $description,
    $icon,
    $point_type,
    $point_cost,
    $point_sell,
    $manpower,
                                $manpower_use,
    $speed_modifier,
    $morale_modifier,
    $combat_range,
    $soft_attack,
    $hard_attack,
    $armor,
                                $entrenchment,
    $support,
    $faction,
    $model_year,
    $edit
) {
    global $wpdb;

    $error = "";

    switch (true) {
        case empty($name):
            $error = "Your equipment must have a title.";
            break;
        case empty($description):
            $error = "Your equipment must have a title.";
            break;
        case empty($icon):
            $error = "Your equipment must have an icon.";
            break;
        case empty($point_type):
            $error = "Your equipment must have a point type.";
            break;
        case empty($point_cost):
            $error = "Your equipment must have a point cost.";
            break;
        case empty($point_sell):
            $error = "Your equipment must have a point sell cost.";
            break;
        case empty($manpower):
            $error = "Your equipment must have a manpower number.";
            break;
        case empty($manpower_use):
            $error = "Your equipment must have a manpower use.";
            break;
        case empty($speed_modifier):
            $error = "Your equipment must have a speed modifier.";
            break;
        case empty($combat_range):
            $error = "Your equipment must have a combat range.";
            break;
        case empty($support) && $support != '0':
            $error = "Your equipment must have a support value.";
            break;
    }


    $duplicate = $wpdb->get_results(
        $wpdb->prepare("SELECT * FROM $wpdb->vypsg_equipment WHERE name=%s", $name)
    );

    if (!$edit && count($duplicate)) {
        $error = "You are trying to insert equipment that already exists.";
    }

    return $error;
}

function create_equipment(
    $name,
    $description,
    $icon,
    $point_type_id,
    $point_cost,
    $point_sell,
    $manpower,
    $manpower_use,
    $speed_modifier,
    $morale_modifier,
    $combat_range,
    $soft_attack,
    $hard_attack,
    $armor,
    $entrenchment,
    $support,
    $faction,
    $model_year,
    $edit = false,
    $old_id = 0
) {
    global $wpdb;
    $error = check_equipment_errors(
        $name,
        $description,
        $icon,
        $point_type_id,
        $point_cost,
        $point_sell,
        $manpower,
        $manpower_use,
        $speed_modifier,
        $morale_modifier,
        $combat_range,
        $soft_attack,
        $hard_attack,
        $armor,
        $entrenchment,
        $support,
        $faction,
        $model_year,
        $edit
    );

    if (! empty($error)) {
        return $error;
    }

    if (empty($armor)) {
        $armor = 1;
    }

    if (empty($entrenchment)) {
        $entrenchment = 1;
    }

    if (empty($model_year)) {
        $model_year = 1970;
    }

    $wpdb->insert(
        $wpdb->vypsg_equipment,
        array(
            'name' => $name,
            'description' => $description,
            'icon' => $icon,
            'point_type_id' => $point_type_id,
            'point_cost' => $point_cost,
            'point_sell' => $point_sell,
            'manpower' => $manpower,
            'manpower_use' => $manpower_use,
            'speed_modifier' => $speed_modifier,
            'morale_modifier' => $morale_modifier,
            'combat_range' => $combat_range,
            'soft_attack' => $soft_attack,
            'hard_attack' => $hard_attack,
            'armor' => $armor,
            'entrenchment' => $entrenchment,
            'support' => $support,
            'faction' => $faction,
            'model_year' => $model_year,
        ),
        array(
            '%s',
            '%s',
            '%s',
            '%d',
            '%d',
            '%d',
            '%s',
            '%d',
            '%d',
            '%d',
            '%d',
            '%d',
            '%d',
            '%d',
            '%d',
            '%d',
            '%s',
            '%d',
        )
    );

    $new_id = $wpdb->insert_id;
    if($edit == true){
        $data = array('item_id' => $new_id);
        $wpdb->update($wpdb->vypsg_tracking, $data, ['item_id' => $old_id]);
    }

    return $error;
}
