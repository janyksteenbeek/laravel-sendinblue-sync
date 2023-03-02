<?php

return [
    /**
     * The model that should be synced with Sendinblue.
     * This model should use the IsSendinblueContact trait.
     *
     * @phpstan-ignore-next-line
     */
    'model' => \App\Models\User::class,

    /**
     * The field on the model that contains the Sendinblue ID.
     */
    'sendinblue_id_field' => env('SENDINBLUE_ID_FIELD', 'sendinblue_id'),

    /**
     * The field on the model that contains the Sendinblue ID.
     */
    'email_field' => env('SENDINBLUE_ID_FIELD', 'email'),

    /**
     * The mapping of the Sendinblue attributes on your model.
     * 'attribute_key' => 'model_field'
     *
     * @see https://my.sendinblue.com/lists/add-attributes
     */
    'attributes' => [
        'EMAIL' => env('SENDINBLUE_FIELD_EMAIL', 'email'),
        'FIRSTNAME' => env('SENDINBLUE_FIELD_FIRSTNAME', 'first_name'),
        'LASTNAME' => env('SENDINBLUE_FIELD_LASTNAME', 'last_name'),
        'SMS' => env('SENDINBLUE_FIELD_SMS', 'phone'),
    ],

    /**
     * The Sendinblue API key.
     *
     * @see https://app.sendinblue.com/settings/keys/api
     */
    'key' => env('SENDINBLUE_KEY'),
];
