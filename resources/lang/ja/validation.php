<?php

return [
    'required' => ':attribute は必須項目です。',
    'email' => ':attribute には有効なメールアドレスを入力してください。',
    'max' => [
        'string' => ':attribute は :max 文字以内で入力してください。',
    ],
    'unique' => ':attribute は既に使用されています。',
    'string' => ':attribute は文字列で指定してください。',

    'attributes' => [
        'name' => '顧客名',
        'abbreviation' => '略称',
        'email' => 'メールアドレス',
        'phone' => '電話番号',
        'address' => '住所',
        'notes' => '備考',
    ],
    'after_or_equal' => ':attribute には :date 以降の日付を指定してください。',

    'attributes' => [
        'start_date' => '開始日',
        'end_date' => '終了日',
    ],
];
