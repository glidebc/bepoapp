<?php

return array(

    'appNameIOS'     => array(
        'environment' =>'development',
        //Save to app path
        'certificate' =>app_path().'certificate.pem',
        'passPhrase'  =>'password',
        'service'     =>'apns'
    ),
    'appNameAndroid' => array(
        'environment' =>'production',
        'apiKey'      =>'yourAPIKey',
        'service'     =>'gcm'
    )

);
