<?php

return [
    'accepted' => ':attributeは承認されなければなりません。',
    'active_url' => ':attributeは有効なURLではありません。',
    'after' => ':attributeは:dateより後の日付でなければなりません。',
    'after_or_equal' => ':attributeは:date以降の日付でなければなりません。',
    'alpha' => ':attributeは英字のみが許されます。',
    'alpha_dash' => ':attributeは英数字とハイフン、アンダースコアのみが許されます。',
    'alpha_num' => ':attributeは英数字のみが許されます。',
    'array' => ':attributeは配列でなければなりません。',
    'before' => ':attributeは:dateより前の日付でなければなりません。',
    'before_or_equal' => ':attributeは:date以前の日付でなければなりません。',
    'between' => [
        'numeric' => ':attributeは:minから:maxの間でなければなりません。',
        'file' => ':attributeは:minから:maxキロバイトの間でなければなりません。',
        'string' => ':attributeは:minから:max文字の間でなければなりません。',
        'array' => ':attributeは:minから:max個の項目を持たなければなりません。',
    ],
    'boolean' => ':attributeは真か偽でなければなりません。',
    'confirmed' => ':attributeの確認が一致しません。',
    'date' => ':attributeは有効な日付ではありません。',
    'date_format' => ':attributeは:format形式と一致しません。',
    'different' => ':attributeと:otherは異なっている必要があります。',
    'digits' => ':attributeは:digits桁でなければなりません。',
    'digits_between' => ':attributeは:minから:max桁の間でなければなりません。',
    'dimensions' => ':attributeは不正な画像サイズです。',
    'distinct' => ':attributeフィールドは重複する値を持っています。',
    'email' => ':attributeは有効なメールアドレスでなければなりません。',
    'exists' => '選択された:attributeは無効です。',
    'file' => ':attributeはファイルでなければなりません。',
    'filled' => ':attributeフィールドは値が必須です。',
    'gt' => [
        'numeric' => ':attributeは:valueより大きくなければなりません。',
        'file' => ':attributeは:valueキロバイトより大きくなければなりません。',
        'string' => ':attributeは:value文字より大きくなければなりません。',
        'array' => ':attributeは:value個より多くの項目を持たなければなりません。',
    ],
    'gte' => [
        'numeric' => ':attributeは:value以上でなければなりません。',
        'file' => ':attributeは:valueキロバイト以上でなければなりません。',
        'string' => ':attributeは:value文字以上でなければなりません。',
        'array' => ':attributeは:value個以上の項目を持たなければなりません。',
    ],
    'image' => ':attributeは画像でなければなりません。',
    'in' => '選択された:attributeは無効です。',
    'in_array' => ':attributeフィールドは:otherに存在しません。',
    'integer' => ':attributeは整数でなければなりません。',
    'ip' => ':attributeは有効なIPアドレスでなければなりません。',
    'ipv4' => ':attributeは有効なIPv4アドレスでなければなりません。',
    'ipv6' => ':attributeは有効なIPv6アドレスでなければなりません。',
    'json' => ':attributeは有効なJSON文字列でなければなりません。',
    'lt' => [
        'numeric' => ':attributeは:valueより小さくなければなりません。',
        'file' => ':attributeは:valueキロバイトより小さくなければなりません。',
        'string' => ':attributeは:value文字より小さくなければなりません。',
        'array' => ':attributeは:value個より少ない項目を持たなければなりません。',
    ],
    'lte' => [
        'numeric' => ':attributeは:value以下でなければなりません。',
        'file' => ':attributeは:valueキロバイト以下でなければなりません。',
        'string' => ':attributeは:value文字以下でなければなりません。',
        'array' => ':attributeは:value個以下の項目を持たなければなりません。',
    ],
    'max' => [
        'numeric' => ':attributeは:maxより大きくすることはできません。',
        'file' => ':attributeは:maxキロバイトより大きくすることはできません。',
        'string' => ':attributeは:max文字より大きくすることはできません。',
        'array' => ':attributeは:max個より多くの項目を持つことはできません。',
    ],
    'mimes' => ':attributeは:valuesタイプのファイルでなければなりません。',
    'mimetypes' => ':attributeは:valuesタイプのファイルでなければなりません。',
    'min' => [
        'numeric' => ':attributeは:minより小さくすることはできません。',
        'file' => ':attributeは:minキロバイトより小さくすることはできません。',
        'string' => ':attributeは:min文字より小さくすることはできません。',
        'array' => ':attributeは:min個より少ない項目を持つことはできません。',
    ],
    'not_in' => '選択された:attributeは無効です。',
    'not_regex' => ':attributeの形式は無効です。',
    'numeric' => ':attributeは数値でなければなりません。',
    'present' => ':attributeフィールドが存在する必要があります。',
    'regex' => ':attributeの形式は無効です。',
    'required' => ':attributeフィールドは必須です。',
    'required_if' => ':otherが:valueの場合、:attributeフィールドは必須です。',
    'required_unless' => ':otherが:valuesに含まれていない場合、:attributeフィールドは必須です。',
    'required_with' => ':valuesが存在する場合、:attributeフィールドは必須です。',
    'required_with_all' => ':valuesが存在する場合、:attributeフィールドは必須です。',
    'required_without' => ':valuesが存在しない場合、:attributeフィールドは必須です。',
    'required_without_all' => ':valuesのいずれも存在しない場合、:attributeフィールドは必須です。',
    'same' => ':attributeと:otherは一致しなければなりません。',
    'size' => [
        'numeric' => ':attributeは:sizeでなければなりません。',
        'file' => ':attributeは:sizeキロバイトでなければなりません。',
        'string' => ':attributeは:size文字でなければなりません。',
        'array' => ':attributeは:size個の項目を含まなければなりません。',
    ],
    'string' => ':attributeは文字列でなければなりません。',
    'timezone' => ':attributeは有効なタイムゾーンでなければなりません。',
    'unique' => ':attributeは既に存在します。',
    'uploaded' => ':attributeのアップロードに失敗しました。',
    'url' => ':attributeは有効なURL形式でなければなりません。',

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
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "Email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [
        'name' => '名前',
        'email' => 'メールアドレス',
        'password' => 'パスワード',
    ],
];
