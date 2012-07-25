<?php

require_once 'erSreality.class.php';

$api_key = 'xaxaxa';
$api_id = 1;
$api_password = 'xaxaxa';

$handle = fopen('test.jpg', 'r');
$img_data = fread($handle, filesize('test.jpg'));
fclose($handle);

$sreality = new erSreality($api_id, $api_key, $api_password);

$seller_params = array(
    'client_login' => 'test@test.cz',
    'client_name' => 'šJosef Novák',
    'contact_gsm' => '132456',
    'photo' => base64_encode($img_data),
);
$seller_id = $sreality->saveSeller(null, 66, $seller_params);
var_dump($seller_id);

//$sreality->delSeller($seller_id);

$sellers = $sreality->listSeller();
var_dump($sellers);


$params = array(
    'advert_rkid' => 69,
    'advert_function' => 1, // prodej
    'advert_lifetime' => 4, // 90 dni
    'advert_price' => 10000000,
    'advert_price_currency' => 1, //CZK
    'advert_price_unit' => 1, //za nemovitost
    'advert_type' => 1, //Byty
    'advert_subtype' => 1, //garosiniera
    'description' => 'Testovací inzerát pres sreality API',
//    'locality_city' => 'Městec králové',
//    'locality_street' => 'Dvořákova 879',
    'locality_inaccuracy_level' => 1,
    'locality_uir' => 318264,
    'locality_uir_level' => 7,
    'estate_area' => 200,
    'usable_area' => 150,
    'floor_number' => 2,
    'ownership' => 1, //osobní
    'building_type' => 1, //drevena
    'building_condition' => 1, //velmidobry
    'object_type' => 2, //patrovy
    'balcony' => false,
    'loggia' => false,
    'cellar' => false,
    'terrace' => false,
    'garage' => false,
    'parking_lots' => false,
    'user_status' => true,
    //'seller_id' => 17951,
);

$advert_id = $sreality->saveAdvert($params);
var_dump($advert_id);

//$advert_id = $sreality->delAdvert($advert_id);

$adverts = $sreality->listAdvert();
var_dump($adverts);

$photo_params = array(
    'data' => base64_encode($img_data),
    'main' => 1,
    'alt' => "Popis",
    'photo_rkid' => 66,
);
$photo_id = $sreality->savePhoto($advert_id, null, $photo_params);
var_dump($photo_id);

//$sreality->delPhoto($photo_id);

$photos = $sreality->listPhoto($advert_id);
var_dump($photos);

