<?php

class Battle_Backup
{
    //distance to start battle
    private $range;
    private $users;
    private $user_first;
    private $user_second;
    private $battle_id;
    public function __construct($range = 5000, $users, $battle_id)
    {
        $this->range = $range;
        $this->users = $users;
        $this->battle_id = $battle_id;
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
        $morale_first = 100;
        $morale_second = 100;

        $i = 0;
        while($i < 5){

            if($this->getEquipmentLeft($this->user_first) == 0 || $this->getEquipmentLeft($this->user_second) == 0){
                if($this->getEquipmentLeft($this->user_first) == $this->getEquipmentLeft($this->user_second)){
                    global $wpdb;
                    $wpdb->insert(
                        $wpdb->vypsg_battles,
                        array(
                            'winner' => '',
                            'loser' => '',
                        ),
                        array(
                            '%s',
                            '%s',
                        )
                    );

                    $data = array('battled' => 1,);
                    $wpdb->update($wpdb->vypsg_pending_battles, $data, ['id' => $this->battle_id]);

                    break;
                }
            }

            /** First user attacks equipment of second **/
            $first_equipment_damage = $this->generateEquipmentDamage($this->user_first, $this->range);
            $this->destroyEquipment($first_equipment_damage, $this->user_second);

            if($this->getEquipmentLeft($this->user_second) == 0){
                $this->finish($this->user_first, $this->user_second);
                $i = 5;
            }

            /** Second user attacks equipment of first **/
            $second_equipment_damage = $this->generateEquipmentDamage($this->user_second, $this->range);
            $this->destroyEquipment($second_equipment_damage, $this->user_first);

            if($this->getEquipmentLeft($this->user_first) == 0){
                $this->finish($this->user_second, $this->user_first);
                $i = 5;
            }


            /** First user attacks manpower of second **/
            $manpower_damage = $this->generateManpowerDamage($this->user_first, $this->range);
            $this->destroyManpower($manpower_damage, $this->user_second);

            if($this->getEquipmentLeft($this->user_second) == 0){
                $this->finish($this->user_first, $this->user_second);
                $i = 5;
            }

            /** Second user attacks manpower of first **/
            $manpower_damage = $this->generateManpowerDamage($this->user_second, $this->range);
            $this->destroyManpower($manpower_damage, $this->user_first);

            if($this->getEquipmentLeft($this->user_first) == 0){
                $this->finish($this->user_second, $this->user_first);
                $i = 5;
            }


            /** Second user morale check **/
            $morale_daamge = $this->generateMoralDamage($this->user_second, $this->range);
            $morale_second = $this->destroyMorale($morale_daamge, $this->user_second);

            if($morale_second < 60){
                $this->finish($this->user_first, $this->user_second);
                $i = 5;
            }

            /** First user morale check **/
            $morale_daamge = $this->generateMoralDamage($this->user_second, $this->range);
            $morale_first = $this->destroyMorale($morale_daamge, $this->user_second);

            if($morale_first < 60){
                $this->finish($this->user_second, $this->user_first);
                $i = 5;
            }


            $this->nextRound();
            $i++;
        }
    }

    public function nextRound(){
        $this->range -= 1000;
    }

    public function getEquipmentLeft($username)
    {
        global $wpdb;
        $user_equipment = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM $wpdb->vypsg_tracking WHERE username=%s and battle_id is null ORDER BY id DESC", $username )
        );

        return count($user_equipment);
    }

    public function finish($winner, $loser){
        global $wpdb;

        $wpdb->insert(
            $wpdb->vypsg_battles,
            array(
                'winner' => $winner,
                'loser' => $loser,
            ),
            array(
                '%s',
                '%s',
            )
        );

        $data = array('battled' => 1,);
        $wpdb->update($wpdb->vypsg_pending_battles, $data, ['id' => $this->battle_id]);
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
            $wpdb->prepare("SELECT * FROM $wpdb->vypsg_tracking WHERE username=%s and combat_range >= $this->range and battle_id is null ORDER BY id DESC", $username )
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
        global $wpdb;
        $user_equipment = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM $wpdb->vypsg_tracking WHERE username=%s and battle_id is null ORDER BY id DESC", $username )
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
                    $equipment[$indiv->item_id]['amount'] = 1;
                    $equipment[$indiv->item_id]['morale'] = $new[0]->morale;
                }

            }
        }

        $morale = 0;
        foreach($equipment as $indiv){
            $morale += $indiv['morale'] * $indiv['amount'];
        }

        return $morale;
    }

    public function generateManpowerDamage($username, $range)
    {
        global $wpdb;
        $user_equipment = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM $wpdb->vypsg_tracking WHERE username=%s and combat_range >= $this->range and battle_id is null ORDER BY id DESC", $username )
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

    public function destroyEquipment($damage, $username)
    {
        global $wpdb;

        while(true){

            if($damage < 0 || $this->getEquipmentLeft($username) == 0){
                break;
            }

            $user_equipment = $wpdb->get_results(
                $wpdb->prepare("SELECT * FROM $wpdb->vypsg_tracking WHERE username=%s and battle_id is null ORDER BY RAND()", $username )
            );

            echo($this->getEquipmentLeft($username));

            $damage -= ($user_equipment[0]->armor*$user_equipment[0]->entrenchment);

            $data = array('battle_id' => $this->battle_id);
            $wpdb->update($wpdb->vypsg_tracking, $data, ['id' => $user_equipment[0]->id]);
        }

    }

    public function destroyMorale($morale_damage, $username)
    {

        return $morale_damage;
    }

    public function destroyManpower($damage, $username)
    {
        global $wpdb;

        while(true){

            if($damage < 0 || $this->getEquipmentLeft($username) == 0){
                break;
            }

            $user_equipment = $wpdb->get_results(
                $wpdb->prepare("SELECT * FROM $wpdb->vypsg_tracking WHERE username=%s and battle_id is null ORDER BY RAND()", $username )
            );

            $damage -= ($user_equipment[0]->armor*$user_equipment[0]->entrenchment);

            $data = array('battle_id' => $this->battle_id);
            $wpdb->update($wpdb->vypsg_tracking, $data, ['id' => $user_equipment[0]->id]);
        }
    }
}