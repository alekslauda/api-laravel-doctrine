<?php

return [
    'user_register' => [
        'validation_rules' => [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required'
        ]
    ],
    'user_login' => [
        'validation_rules' => [
            'email' => 'required|email',
            'password' => 'required'
        ]
    ],
    'user_forgot_password' => [
        'validation_rules' => [
            'email' => 'required|email'
        ]
    ],
    'user_reset_password' => [
        'validation_rules' => [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed'
        ]
    ],

    'social_create_record' => [
        'validation_rules' => [
            'email' => 'required|email',
            'provider_id' => 'required',
            'provider' => 'required',
        ]
    ],

];