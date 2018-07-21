<?php

class Battle{

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

    /**
     * Starts the battle, generates damage
     */
    public function startBattle(){

        $first_wins = 0;
        $second_wins = 0;
        for($i = 0;$i<=5;$i++){
            $start = $this->pickInitiator();
            if($start == 1){
                $this->user_first = $this->users[0];
            } else {
                $this->user_second = $this->users[1];
            }

            $first_equipment_damage = (int)(((float)((rand(90, 100))) / 100)*$this->getEquipmentDamage($this->user_first));
            $second_equipment_damage = (int)(((float)((rand(90, 100))) / 100)*(float)$this->getEquipmentDamage($this->user_second));

            $this->destroyEquipment($first_equipment_damage, $this->user_second, $this->user_first);
            $this->destroyEquipment($second_equipment_damage, $this->user_first, $this->user_second);

            if($first_equipment_damage > $second_equipment_damage){
                $first_wins++;
            } elseif($first_equipment_damage < $second_equipment_damage){
                $second_wins++;
            } else {
                $first_wins++;
                $second_wins++;
            }
            $this->nextRound();
        }

        $this->range = 5000;

        for($i = 0;$i<=5;$i++){
            $start = $this->pickInitiator();
            if($start == 1){
                $this->user_first = $this->users[0];
            } else {
                $this->user_second = $this->users[1];
            }

            $first_equipment_damage = (int)(rand(.9,1)*$this->getManpowerDamage($this->user_first));
            $second_equipment_damage = (int)(rand(.9,1)*$this->getManpowerDamage($this->user_second));

            $this->destroyManpower($first_equipment_damage, $this->user_second, $this->user_first);
            $this->destroyManpower($second_equipment_damage, $this->user_first, $this->user_second);

            if($first_equipment_damage > $second_equipment_damage){
                $first_wins++;
            } elseif($first_equipment_damage < $second_equipment_damage){
                $second_wins++;
            } else {
                $first_wins++;
                $second_wins++;
            }
            $this->nextRound();
        }

        if($first_wins > $second_wins){
            $this->finish($this->user_first, $this->user_second);
        } elseif($first_wins < $second_wins){
            $this->finish($this->user_second, $this->user_first);
        } else{
            $this->tie($this->user_first, $this->user_second);
        }
    }

    /**
     * Next round by moving the troops closer
     */
    public function nextRound(){
        $this->range -= 1000;
    }

    /**
     * Picks person to go first
     */
    public function pickInitiator()
    {
        $random = (mt_rand() / mt_getrandmax());

        if($random <= .5){
            return 1;
        }

        return 2;
    }

    /**
     * Inserts winner and loser into db
     */
    public function finish($winner, $loser){
        global $wpdb;

        $battles = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM $wpdb->vypsg_battles WHERE battle_id=%d", $this->battle_id )
        );

        if(count($battles) == 0){
            $wpdb->insert(
                $wpdb->vypsg_battles,
                array(
                    'winner' => $winner,
                    'loser' => $loser,
                    'battle_id' => $this->battle_id,
                    'tie' => 0,
                ),
                array(
                    '%s',
                    '%s',
                    '%d',
                    '%d'
                )
            );

            $data = array('battled' => 1,);
            $wpdb->update($wpdb->vypsg_pending_battles, $data, ['id' => $this->battle_id]);
        }
    }

    /**
     * If the game is a tie, how to finalize
     */
    public function tie($first, $second)
    {
        global $wpdb;

        $battles = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM $wpdb->vypsg_battles WHERE battle_id=%d", $this->battle_id )
        );

        if(count($battles) == 0){
            $wpdb->insert(
                $wpdb->vypsg_battles,
                array(
                    'winner' => $first,
                    'loser' => $second,
                    'battle_id' => $this->battle_id,
                    'tie' => 1,
                ),
                array(
                    '%s',
                    '%s',
                    '%d',
                    '%d'
                )
            );

            $data = array('battled' => 1,);
            $wpdb->update($wpdb->vypsg_pending_battles, $data, ['id' => $this->battle_id]);
        }
    }

    /**
     * Generates user damage
     */
    public function getEquipmentDamage($username)
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
                    $wpdb->prepare("SELECT * FROM $wpdb->vypsg_equipment WHERE id=%d and combat_range >= %d", $indiv->item_id, $this->range )
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
        }

        if($damage_total < 0){
            $damage_total = 0;
        }

        return $damage_total;
    }

    /**
     * Generates manpower damage
     */
    public function getManpowerDamage($username)
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
                    $wpdb->prepare("SELECT * FROM $wpdb->vypsg_equipment WHERE id=%d and combat_range >= %d", $indiv->item_id, $this->range )
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
        }

        if($damage_total < 0){
            $damage_total = 0;
        }

        return $damage_total;
    }

    /**
     * How much equipment a user has left
     */
    public function getEquipmentLeft($username)
    {
        global $wpdb;
        $user_equipment = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM $wpdb->vypsg_tracking WHERE username=%s and battle_id is null", $username )
        );

        return count($user_equipment);
    }

    /**
     * Destroy equipment based on damage
     */
    public function destroyEquipment($damage, $username, $opposition)
    {
        global $wpdb;
        $count = 0;
        while(true){
            if($count == 10){
                break;
            }

            if($damage <= 0 || $this->getEquipmentLeft($username) == 0){
                break;
            }

            $user_equipment = $wpdb->get_results(
                $wpdb->prepare("SELECT * FROM $wpdb->vypsg_tracking WHERE username=%s and battle_id is null ORDER BY RAND()", $username )
            );

            $equipment = $wpdb->get_results(
                $wpdb->prepare("SELECT * FROM $wpdb->vypsg_equipment WHERE id=%d", $user_equipment[0]->item_id )
            );

            if(isset($equipment[0]->entrenchment)){
                if($equipment[0]->entrenchment == 0){
                    $equipment[0]->entrenchment = 1;
                }

                $damage -= ($equipment[0]->armor*$equipment[0]->entrenchment);

                $random = (mt_rand() / mt_getrandmax());

                if($random <= .5 || $equipment[0]->support){
                    $data = array('captured_from' => $user_equipment[0]->id, 'username' => $opposition, 'captured_id' => $this->battle_id);
                    $wpdb->update($wpdb->vypsg_tracking, $data, ['id' => $user_equipment[0]->id]);
                } else {
                    $data = array('battle_id' => $this->battle_id);
                    $wpdb->update($wpdb->vypsg_tracking, $data, ['id' => $user_equipment[0]->id]);
                }

                $count++;
            }
        }
    }

    /**
     * Destroy manpower based on damage
     */
    public function destroyManpower($damage, $username, $opposition)
    {
        global $wpdb;

        $count = 0;
        while(true){
            if($count == 100){
                break;
            }
            $count++;
            if($damage <= 0 || $this->getEquipmentLeft($username) == 0){
                break;
            }

            $user_equipment = $wpdb->get_results(
                $wpdb->prepare("SELECT * FROM $wpdb->vypsg_tracking WHERE username=%s and battle_id is null ORDER BY RAND()", $username )
            );

            $equipment = $wpdb->get_results(
                $wpdb->prepare("SELECT * FROM $wpdb->vypsg_equipment WHERE id=%d", $user_equipment[0]->item_id )
            );

            if(isset($equipment[0]->entrenchment)){
                if($equipment[0]->entrenchment == 0){
                    $equipment[0]->entrenchment = 1;
                }

                $damage -= ($equipment[0]->armor*$equipment[0]->entrenchment);

                if($equipment[0]->support){
                    $data = array('captured_from' => $user_equipment[0]->id, 'username' => $opposition, 'captured_id' => $this->battle_id);
                    $wpdb->update($wpdb->vypsg_tracking, $data, ['id' => $user_equipment[0]->id]);
                } else {
                    $data = array('battle_id' => $this->battle_id);
                    $wpdb->update($wpdb->vypsg_tracking, $data, ['id' => $user_equipment[0]->id]);
                }


            }
        }
    }
}