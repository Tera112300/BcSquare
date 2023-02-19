<?php
class BcSquareConfig extends AppModel
{
    public $validate = [
        'content_id' => [
            'notBlank' => [
                'rule' => ['notBlank'],
                'message' => '必須入力です。'
            ],
            ['rule' => ['numeric'],'message' => '数値を入力してください'],
        ],
        'price' => [
            'notBlank' => [
                'rule' => ['notBlank'],
                'message' => '必須入力です。'
            ],
            ['rule' => ['numeric'],'message' => '数値を入力してください'],
            ['rule' => ['naturalNumber',true],'message' => '0以上の数値を入力してください'],
        ],
    ];
}
