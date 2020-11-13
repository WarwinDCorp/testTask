<?php


namespace Classes;


class Response {
    public static function returnResponse($data){
        header('Content-Type: Application/Json');
        echo(json_encode($data));
        die;
    }
}