<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => ':attribute deve ser aceito.',
    'active_url'           => ':attribute não é um URL válido.',
    'after'                => ':attribute deve ser uma data após :date.',
    'after_or_equal'       => ':attribute deve ser uma data posterior ou igual a :date.',
    'alpha'                => ':attribute só pode conter letras.',
    'alpha_dash'           => ':attribute só pode conter letras, números e traços.',
    'alpha_num'            => ':attribute só pode conter letras e números.',
    'array'                => ':attribute deve ser um array.',
    'before'               => ':attribute deve ser uma data antes de :date.',
    'before_or_equal'      => ':attribute deve ser uma data anterior ou igual a :date.',
    'between'              => [
        'numeric' => ':attribute deve estar entre :min e :max.',
        'file'    => ':attribute deve estar entre :min e :max kilobytes.',
        'string'  => ':attribute deve estar entre :min e :max caracteres.',
        'array'   => ':attribute deve ter entre :min e :max itens.',
    ],
    'boolean'              => ':attribute deve ser verdadeiro ou falso.',
    'confirmed'            => 'A confirmação de :attribute não corresponde.',
    'date'                 => ':attribute não é uma data válida.',
    'date_format'          => ':attribute não corresponde ao formato :format.',
    'different'            => ':attribute e :other devem ser diferentes.',
    'digits'               => ':attribute deve ter :digits dígitos.',
    'digits_between'       => ':attribute deve ter entre :min e :max dígitos.',
    'dimensions'           => ':attribute tem dimensões de imagem inválidas.',
    'distinct'             => ':attribute tem um valor duplicado.',
    'email'                => ':attribute deve ser um endereço de email válido.',
    'exists'               => ':attribute selecionado é inválido.',
    'file'                 => ':attribute deve ser um arquivo.',
    'filled'               => ':attribute é obrigatório.',
    'image'                => ':attribute deve ser uma imagem.',
    'in'                   => ':attribute selecionado é inválido.',
    'in_array'             => ':attribute não existe em :other.',
    'integer'              => ':attribute deve ser um inteiro.',
    'ip'                   => ':attribute deve ser um endereço IP válido.',
    'json'                 => ':attribute deve ser uma cadeia JSON válida.',
    'max'                  => [
        'numeric' => ':attribute não pode ser maior que :max.',
        'file'    => ':attribute não pode ser maior que :max kilobytes.',
        'string'  => ':attribute não pode ser maior que :max caracteres.',
        'array'   => ':attribute pode não ter mais que :max itens.',
    ],
    'mimes'                => ':attribute deve ser um arquivo do tipo: :values.',
    'mimetypes'            => ':attribute deve ser um arquivo do tipo: :values.',
    'min'                  => [
        'numeric' => ':attribute deve ser pelo menos :min.',
        'file'    => ':attribute deve ter pelo menos :min kilobytes.',
        'string'  => ':attribute deve ter pelo menos :min caracteres.',
        'array'   => ':attribute deve ter pelo menos :min itens.',
    ],
    'not_in'               => ':attribute selecionado é inválido.',
    'numeric'              => ':attribute deve ser um número.',
    'present'              => ':attribute deve estar presente.',
    'regex'                => ':attribute tem um formato inválido.',
    'required'             => ':attribute é obrigatório.',
    'required_if'          => ':attribute é obrigatório quando :other é :value.',
    'required_unless'      => ':attribute é obrigatório, a menos que :other esteja em :values.',
    'required_with'        => ':attribute é obrigatório quando :values estão presentes.',
    'required_with_all'    => ':attribute é obrigatório quando :values estão presentes.',
    'required_without'     => ':attribute é obrigatório quando :values não estão presentes.',
    'required_without_all' => ':attribute é obrigatório quando nenhum dos :values estiver presente.',
    'same'                 => ':attribute é :other devem coincidir.',
    'size'                 => [
        'numeric' => ':attribute deve ser :size.',
        'file'    => ':attribute deve ter :size kilobytes.',
        'string'  => ':attribute deve ter :size caracteres.',
        'array'   => ':attribute deve conter :size itens.',
    ],
    'string'               => ':attribute deve ser uma cadeia.',
    'timezone'             => ':attribute deve ser uma zona válida.',
    'unique'               => ':attribute já foi tomado.',
    'uploaded'             => ':attribute falhou ao fazer upload.',
    'url'                  => ':attribute tem um formato inválido.',

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
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes'           => [
        'name'                  => 'nome',
        'short_name'            => 'nome curto',
        'username'              => 'nome de usuário',
        'email'                 => 'e-mail',
        'first_name'            => 'primeiro nome',
        'last_name'             => 'sobrenome',
        'password'              => 'senha',
        'password_confirmation' => 'confirmação de senha',
        'city'                  => 'cidade',
        'country'               => 'país',
        'address'               => 'endereço',
        'phone'                 => 'telefone',
        'mobile'                => 'celular',
        'age'                   => 'idade',
        'sex'                   => 'sexo',
        'gender'                => 'gênero',
        'year'                  => 'ano',
        'month'                 => 'mês',
        'day'                   => 'dia',
        'hour'                  => 'hora',
        'minute'                => 'minuto',
        'second'                => 'segundo',
        'title'                 => 'título',
        'body'                  => 'corpo',
        'description'           => 'descrição',
        'excerpt'               => 'excerto',
        'date'                  => 'data',
        'time'                  => 'hora',
        'subject'               => 'assunto',
        'message'               => 'mensagem',
        'stadium_name'          => 'nome do estádio',
        'primary_color'         => 'cor primária',
        'secondary_color'       => 'cor secundária',
        'old_password'          => 'senha antiga',
        'new_password'          => 'senha nova',
        'strategy'              => 'estratégia',
        'formation'             => 'formação',
        'match_type'            => 'tipo de partida',
        'player_id'             => 'id do jogador'
    ],

];
