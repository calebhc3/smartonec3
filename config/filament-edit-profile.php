<?php

return [
    'avatar' => [
        'disk' => 'public', // Disco onde o avatar será armazenado
        'path' => 'avatars', // Caminho dentro do disco
    ],
    'fields' => [
        'name' => [
            'label' => 'Nome',
            'type' => 'text',
        ],
        'email' => [
            'label' => 'Email',
            'type' => 'email',
        ],
        'password' => [
            'label' => 'Senha',
            'type' => 'password',
        ],
    ],
    'sessions' => [
        'enabled' => true, // Habilitar gerenciamento de sessões
    ],
    
];