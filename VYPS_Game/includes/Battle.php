<?php

class Battle
{
    //distance to start battle
    private $distance;
    private $users;
    public function __construct($distance = 5000, $users)
    {
        $this->distance = $distance;
        $this->users = $users;
    }


    public function pickInitiator($user_one, $user_two)
    {
        $random = rand(0,1);

        if($random <= .5){
            return $user_one;
        }

        return $user_two;
    }

    public function generateEquipmentDamage($username, $round_range)
    {
        global $wpdb;
        $available_equipment = $wpdb->get_results(
            "SELECT * FROM $wpdb->vypsg_equipment"
        );

        $user_equipment = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM $wpdb->vypsg_tracking WHERE username=%s", $username )
        );

        $damage_total = 0;
        foreach($user_equipment as $equipment){
            foreach($available_equipment as $available){
                if(($equipment['name'] == $available['name']) && $available['combat_range'] >= $round_range){
                    $damage_total += $available['hard_attack'];
                }
                $damage_total -= ($available['entrenchment'] * $available['armor']);
            }
        }

        if($damage_total < 0){
            $damage_total = 0;
        }

        return $damage_total;
    }

    public function generateMoralDamage($username, $round_range)
    {

    }

    public function generateManpowerDamage()
    {
        global $wpdb;
        $available_equipment = $wpdb->get_results(
            "SELECT * FROM $wpdb->vypsg_equipment"
        );

        $user_equipment = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM $wpdb->vypsg_tracking WHERE username=%s", $username )
        );

        $damage_total = 0;
        foreach($user_equipment as $equipment){
            foreach($available_equipment as $available){
                if(($equipment['name'] == $available['name']) && $available['combat_range'] >= $round_range){
                    $damage_total += $available['hard_attack'];
                }
                $damage_total -= ($available['entrenchment'] * $available['armor']);
            }
        }

        if($damage_total < 0){
            $damage_total = 0;
        }

        return $damage_total;
    }


    public function destroyEquipment()
    {

    }

    public function destroyMoral()
    {

    }

    public function destroyManpower()
    {

    }

    public function startBattle()
    {

    }
}