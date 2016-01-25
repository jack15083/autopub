<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'username' => [
            'required' => '用户名必填！',
            'max'      => '用户名太长！',
            'unique'   => '用户名已使用过！'
        ],
            
        'email' => [
            'required' => '邮箱必填！',
            'email'    => '邮箱格式不合法！',
            'max'      => '用户名太长！',
            'unique'   => '邮箱已使用过！'
        ],
            
        'password' => [
            'required' => '密码必填！',
            'confirm'  => '两次输入的密码不一样！',
            'min'      => '密码太短不能小于六位！',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [],

];
