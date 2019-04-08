<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | such as the size rules. Feel free to tweak each of these messages.
    |
    */

    'accepted'             => ':attribute ha de ser acceptat.',
    'active_url'           => ':attribute no és una URL válida.',
    'after'                => ':attribute ha de ser una data posterior a :date.',
    'after_or_equal'       => ':attribute ha de ser una data posterior o igual a :date.',
    'alpha'                => ':attribute solsament deu contenir lletras.',
    'alpha_dash'           => ':attribute solsament deu contenir lletras, números i guions.',
    'alpha_num'            => ':attribute solsament deu contenir lletras i números.',
    'array'                => ':attribute deu ser un conjunt.',
    'before'               => ':attribute deu ser una data anterior a :date.',
    'before_or_equal'      => ':attribute deu ser una data anterior o igual a :date.',
    'between'              => [
        'numeric' => ':attribute té que estar entre :min - :max.',
        'file'    => ':attribute deu pesar entre :min - :max kilobytes.',
        'string'  => ':attribute ha de tenir entre :min - :max caracteres.',
        'array'   => ':attribute ha de tenir entre :min - :max ítems.',
    ],
    'boolean'              => 'El camp :attribute ha de tenir un valor verdader o fals.',
    'confirmed'            => 'La confirmació de :attribute no coincideix.',
    'date'                 => ':attribute no es una data válida.',
    'date_format'          => ':attribute no es correspon al format :format.',
    'different'            => ':attribute i :other ha de ser diferents.',
    'digits'               => ':attribute ha de tenir :digits dígitos.',
    'digits_between'       => ':attribute ha de tenir entre :min y :max dígitos.',
    'dimensions'           => 'Les dimensions de la imatge :attribute no son válides.',
    'distinct'             => 'El camp :attribute conté un valor duplicat.',
    'email'                => ':attribute no es un correu válido',
    'exists'               => ':attribute es inválit.',
    'file'                 => 'El camp :attribute ha de ser un archiu.',
    'filled'               => 'El camp :attribute es obligatori.',
    'image'                => ':attribute ha de ser una imatge.',
    'in'                   => ':attribute es inválit.',
    'in_array'             => 'El camp :attribute no existeix a :other.',
    'integer'              => ':attribute ha de ser un número enter.',
    'ip'                   => ':attribute ha ser una direcció IP válida.',
    'json'                 => 'El camp :attribute ha de tenir una cadena JSON válida.',
    'max'                  => [
        'numeric' => ':attribute no ha de ser major a :max.',
        'file'    => ':attribute no ha de ser major que :max kilobytes.',
        'string'  => ':attribute no debe ser mayor que :max caracteres.',
        'array'   => ':attribute no debe tener más de :max elementos.',
    ],
    'mimes'                => ':attribute debe ser un archivo con formato: :values.',
    'mimetypes'            => ':attribute debe ser un archivo con formato: :values.',
    'min'                  => [
        'numeric' => 'El tamaño de :attribute debe ser de al menos :min.',
        'file'    => 'El tamaño de :attribute debe ser de al menos :min kilobytes.',
        'string'  => ':attribute debe contener al menos :min caracteres.',
        'array'   => ':attribute debe tener al menos :min elementos.',
    ],
    'not_in'               => ':attribute es inválido.',
    'numeric'              => ':attribute debe ser numérico.',
    'present'              => 'El campo :attribute debe estar presente.',
    'regex'                => 'El formato de :attribute es inválido.',
    'required'             => 'El campo :attribute es obligatorio.',
    'required_if'          => 'El campo :attribute es obligatorio cuando :other es :value.',
    'required_unless'      => 'El campo :attribute es obligatorio a menos que :other esté en :values.',
    'required_with'        => 'El campo :attribute es obligatorio cuando :values está presente.',
    'required_with_all'    => 'El campo :attribute es obligatorio cuando :values está presente.',
    'required_without'     => 'El campo :attribute es obligatorio cuando :values no está presente.',
    'required_without_all' => 'El campo :attribute es obligatorio cuando ninguno de :values estén presentes.',
    'same'                 => ':attribute y :other deben coincidir.',
    'size'                 => [
        'numeric' => 'El tamaño de :attribute debe ser :size.',
        'file'    => 'El tamaño de :attribute debe ser :size kilobytes.',
        'string'  => ':attribute debe contener :size caracteres.',
        'array'   => ':attribute debe contener :size elementos.',
    ],
    'string'               => 'El campo :attribute debe ser una cadena de caracteres.',
    'timezone'             => 'El :attribute debe ser una zona válida.',
    'unique'               => ':attribute ya ha sido registrado.',
    'uploaded'             => 'Subir :attribute ha fallado.',
    'url'                  => 'El formato :attribute es inválido.',

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

    'custom'               => [
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
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes'           => [
        'name'                  => 'nombre',
        'short_name'            => 'nombre corto',
        'username'              => 'usuario',
        'email'                 => 'correo electrónico',
        'first_name'            => 'nombre',
        'last_name'             => 'apellido',
        'password'              => 'contraseña',
        'password_confirmation' => 'confirmación de la contraseña',
        'city'                  => 'ciudad',
        'country'               => 'país',
        'address'               => 'dirección',
        'phone'                 => 'teléfono',
        'mobile'                => 'móvil',
        'age'                   => 'edad',
        'sex'                   => 'sexo',
        'gender'                => 'género',
        'year'                  => 'año',
        'month'                 => 'mes',
        'day'                   => 'día',
        'hour'                  => 'hora',
        'minute'                => 'minuto',
        'second'                => 'segundo',
        'title'                 => 'título',
        'body'                  => 'contenido',
        'description'           => 'descripción',
        'excerpt'               => 'extracto',
        'date'                  => 'fecha',
        'time'                  => 'hora',
        'subject'               => 'asunto',
        'message'               => 'mensaje',
        'stadium_name'          => 'nombre del estadio',
        'primary_color'         => 'color primario',
        'secondary_color'       => 'color secundario',
        'old_password'          => 'contraseña actual',
        'new_password'          => 'contraseña nueva',
        'strategy'              => 'estrategia',
        'formation'             => 'formación',
        'match_type'            => 'tipo de partido',
        'player_id'             => 'id de jugador'
    ],

];