<?php
class BcSquareCommon extends AppModel
{
    public $validate = [
        'application_id' => [
            'notBlank' => [
                'rule' => ['notBlank'],
                'message' => '必須入力です。'
            ]
        ],
        'access_token' => [
            'notBlank' => [
                'rule' => ['notBlank'],
                'message' => '必須入力です。'
            ]
        ],
        'location_id' => [
            'notBlank' => [
                'rule' => ['notBlank'],
                'message' => '必須入力です。'
            ]
        ],
        'environment' => [
            'notBlank' => [
                'rule' => ['notBlank'],
                'message' => '必須入力です。'
            ],
            ['rule' => ['range',0,3],'message' => '1～2を入力してください'],
        ]
    ];
}
