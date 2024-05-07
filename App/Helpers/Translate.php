<?php

function translate_key($input){

    $translate_arrays = [
        "name" => "نام",
        "phone_number" => "شماره تلفن",
        "username" => "نام کاربری",
        "password" => "رمز عبور"
    ];

    $isFind = false;

    foreach ($translate_arrays as $key => $value)
        if($input == $key) {
            $isFind = true;
            return $value;
        }


    if(!$isFind) return $input;
}