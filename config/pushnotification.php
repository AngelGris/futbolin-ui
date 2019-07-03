<?php

return [
  'gcm' => [
      'priority' => 'normal',
      'dry_run' => false,
      'apiKey' => 'My_ApiKey',
  ],
  'fcm' => [
        'priority' => 'normal',
        'dry_run' => false,
        'apiKey' => 'AAAAUNxRN-w:APA91bHqO42D1sXxqsboaluTsJQny3AV6WBNt5IVQd9PjFyZhOPNs0tWeIaSItWmvWElqJjD8S7dVGwm_qUjZH7o0gz2fQd79ici7wC4WWrCLZcwiFJL1DlBw9FFoq0CyJo5DE_cdsJa',
  ],
  'apn' => [
      'certificate' => __DIR__ . '/iosCertificates/apns-dev-cert.pem',
      'passPhrase' => '1234', //Optional
      'passFile' => __DIR__ . '/iosCertificates/yourKey.pem', //Optional
      'dry_run' => true
  ]
];