<?php

class Battle
{
    //distance to start battle
    private $range;
    private $users;
    private $user_first;
    private $user_second;
    public function __construct($range = 5000, $users)
    {
        $this->range = $range;
        $this->users = $users;
    }

    public function startBattle()
    {
        $start = $this->pickInitiator();
        if($start == 1){
            $this->user_first = $this->users[0];
        } else {
            $this->user_second = $this->users[1];
        }

        //rounds
        for($i=0;$i < 5;$i++){
            $first_equipment_damage = $this->generateEquipmentDamage($this->user_first, $this->range);
            $this->destroyEquipment($first_equipment_damage, $this->user_first);
            $second_equipment_damage = $this->generateEquipmentDamage($this->user_second, $this->range);
            $this->destroyEquipment($second_equipment_damage, $this->user_second);

            $manpower_damage = $this->generateManpowerDamage($this->user_first, $this->range);
            $this->destroyManpower($manpower_damage, $this->user_first, $first_equipment_damage);
            $manpower_damage = $this->generateManpowerDamage($this->user_second, $this->range);
            $this->destroyManpower($manpower_damage, $this->user_second, $second_equipment_damage);

            $this->nextRound();
        }
    }

    public function nextRound(){
        $this->range -= 1000;
    }


    public function pickInitiator()
    {
        $random = rand(0,1);

        if($random <= .5){
            return 1;
        }

        return 2;
    }

    public function generateEquipmentDamage($username, $range)
    {
        global $wpdb;
        $user_equipment = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM $wpdb->vypsg_tracking WHERE username=%s ORDER BY id DESC", $username )
        );

        //add counting
        $equipment = [];

        foreach($user_equipment as $indiv){

            if(array_key_exists($indiv->item_id, $equipment)){
                $equipment[$indiv->item_id]['amount'] += 1;
            } else {
                $new = $wpdb->get_results(
                    $wpdb->prepare("SELECT * FROM $wpdb->vypsg_equipment WHERE id=%d and combat_range >= %d", $indiv->item_id, $range )
                );

                if(!empty($new)){
                    $equipment[$indiv->item_id]['item'] = $indiv->item_id;
                    $equipment[$indiv->item_id]['amount'] = 1;
                    $equipment[$indiv->item_id]['name'] = $new[0]->name;
                    $equipment[$indiv->item_id]['combat_range'] = $new[0]->combat_range;
                    $equipment[$indiv->item_id]['hard_attack'] = $new[0]->hard_attack;
                    $equipment[$indiv->item_id]['entrenchment'] = $new[0]->entrenchment;
                    $equipment[$indiv->item_id]['armor'] = $new[0]->armor;
                }

            }
        }

        $damage_total = 0;
        foreach($equipment as $indiv){
            $damage_total += $indiv['hard_attack'] * $indiv['amount'];
            $damage_total -= ($indiv['entrenchment'] * $indiv['armor'] * $indiv['amount']);
        }

        if($damage_total < 0){
            $damage_total = 0;
        }

        return $damage_total;
    }

    public function generateMoralDamage($username, $range)
    {
        //compute moral
    }

    public function generateManpowerDamage($username, $range)
    {
        global $wpdb;
        $user_equipment = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM $wpdb->vypsg_tracking WHERE username=%s ORDER BY id DESC", $username )
        );

        //add counting
        $equipment = [];

        foreach($user_equipment as $indiv){

            if(array_key_exists($indiv->item_id, $equipment)){
                $equipment[$indiv->item_id]['amount'] += 1;
            } else {
                $new = $wpdb->get_results(
                    $wpdb->prepare("SELECT * FROM $wpdb->vypsg_equipment WHERE id=%d and combat_range >= %d", $indiv->item_id, $range )
                );

                if(!empty($new)){
                    $equipment[$indiv->item_id]['item'] = $indiv->item_id;
                    $equipment[$indiv->item_id]['amount'] = 1;
                    $equipment[$indiv->item_id]['name'] = $new[0]->name;
                    $equipment[$indiv->item_id]['combat_range'] = $new[0]->combat_range;
                    $equipment[$indiv->item_id]['soft_attack'] = $new[0]->hard_attack;
                    $equipment[$indiv->item_id]['entrenchment'] = $new[0]->entrenchment;
                    $equipment[$indiv->item_id]['armor'] = $new[0]->armor;
                }

            }
        }

        $damage_total = 0;
        foreach($equipment as $indiv){
            $damage_total += $indiv['soft_attack'] * $indiv['amount'];
            $damage_total -= ($indiv['entrenchment'] * $indiv['armor'] * $indiv['amount']);
        }

        if($damage_total < 0){
            $damage_total = 0;
        }

        return $damage_total;
    }

    public function getArmor($username)
    {
        global $wpdb;
        $user_equipment = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM $wpdb->vypsg_tracking WHERE username=%s ORDER BY id DESC", $username )
        );

        //add counting
        $equipment = [];

        foreach($user_equipment as $indiv){

            if(array_key_exists($indiv->item_id, $equipment)){
                $equipment[$indiv->item_id]['amount'] += 1;
            } else {
                $new = $wpdb->get_results(
                    $wpdb->prepare("SELECT * FROM $wpdb->vypsg_equipment WHERE id=%d and combat_range >= %d", $indiv->item_id, $range )
                );

                if(!empty($new)){
                    $equipment[$indiv->item_id]['item'] = $indiv->item_id;
                    $equipment[$indiv->item_id]['amount'] = 1;
                    $equipment[$indiv->item_id]['name'] = $new[0]->name;
                    $equipment[$indiv->item_id]['combat_range'] = $new[0]->combat_range;
                    $equipment[$indiv->item_id]['soft_attack'] = $new[0]->hard_attack;
                    $equipment[$indiv->item_id]['entrenchment'] = $new[0]->entrenchment;
                    $equipment[$indiv->item_id]['armor'] = $new[0]->armor;
                }

            }
        }

        $armor = 0;
        foreach($equipment as $indiv){
            $armor += $indiv['armor'] * $indiv['armor'];
        }

        if($armor < 0){
            $armor = 0;
        }

        return $armor;
    }

    public function destroyEquipment($damage, $username)
    {
        $damage = $damage - $this->getArmor($username);

        if($damage < 0){
            $damage = 0;
        }
    }

    public function destroyMoral()
    {

    }

    public function destroyManpower($damage, $username, $equipment_damage)
    {
        $armor = $this->getArmor($username);
        if($armor > $equipment_damage){
            $damage = $damage - ($armor - $equipment_damage);
        }
        
        if($armor < 0){
            $armor = 0;
        }
    }
}