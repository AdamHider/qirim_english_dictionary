<?php


$participles_active_present = [
    
    'nominative_present' => [
        'group' => 'case',
        'template' => 'noun_case',
        'hard' => [
            'sonorous' => [
                'single_syllable'=>[
                    ['*ar', 'arl*ar']
                ],
                'multi_syllable'=>[
                    ['*ır', 'ırl*ar' ]
                ]
            ],
            'vowel' => [
                'single_syllable'=>[
                    ['r', 'rl*ar' ]
                ],
                'multi_syllable'=>[
                    ['r', 'rl*ar' ]
                ]
            ],
            'non_sonorous' => [
                'single_syllable'=>[
                    ['*ar', 'arl*ar']
                ],
                'multi_syllable'=>[
                    ['*ır', 'ırl*ar' ]
                ]
            ]
        ],
        'soft' => [
            'sonorous' => [
                'single_syllable'=>[
                    ['*er','erl*er'],
                ],
                'multi_syllable'=>[
                    ['*ir','irl*er']
                ]
            ],
            'vowel' => [
                'single_syllable'=>[
                    ['r', 'rl*er' ]
                ],
                'multi_syllable'=>[
                    ['r', 'rl*er' ]
                ]
            ],
            'non_sonorous' => [
                'single_syllable'=>[
                    ['*er','erl*er'],
                ],
                'multi_syllable'=>[
                    ['*ir','irl*er']
                ]
            ]
        ]
    ],
    
    'genitive_present' => [
        'group' => 'case',
        'template' => 'noun_case',
        'hard' => [
            'sonorous' => [
                'single_syllable'=>[
                    ['arn*ıñ', 'arlarn*ıñ']
                ],
                'multi_syllable'=>[
                    ['ırn*ıñ', 'ırlarn*ıñ' ]
                ]
            ],
            'vowel' => [
                'single_syllable'=>[
                    ['rn*ıñ', 'rlarn*ıñ' ]
                ],
                'multi_syllable'=>[
                    ['rn*ıñ', 'rlarn*ıñ' ]
                ]
            ],
            'non_sonorous' => [
                'single_syllable'=>[
                    ['arn*ıñ', 'arlarn*ıñ']
                ],
                'multi_syllable'=>[
                    ['ırn*ıñ', 'ırlarn*ıñ' ]
                ]
            ]
        ],
        'soft' => [
            'sonorous' => [
                'single_syllable'=>[
                    ['ern*iñ','erlern*iñ'],
                ],
                'multi_syllable'=>[
                    ['irn*iñ','irlern*iñ']
                ]
            ],
            'vowel' => [
                'single_syllable'=>[
                    ['rn*iñ', 'rlern*iñ' ]
                ],
                'multi_syllable'=>[
                    ['rn*iñ', 'rlern*iñ' ]
                ]
            ],
            'non_sonorous' => [
                'single_syllable'=>[
                    ['ern*iñ','erlern*iñ'],
                ],
                'multi_syllable'=>[
                    ['irn*iñ','irlern*iñ']
                ]
            ]
        ]
    ],
    'dative_present' => [
        'group' => 'case',
        'template' => 'noun_case',
        'hard' => [
            'sonorous' => [
                'single_syllable'=>[
                    ['arğ*a', 'arlarğ*a']
                ],
                'multi_syllable'=>[
                    ['ırğ*a', 'ırlarğ*a' ]
                ]
            ],
            'vowel' => [
                'single_syllable'=>[
                    ['rğ*a', 'rlarğ*a' ]
                ],
                'multi_syllable'=>[
                    ['rğ*a', 'rlarğ*a' ]
                ]
            ],
            'non_sonorous' => [
                'single_syllable'=>[
                    ['arğ*a', 'arlarğ*a']
                ],
                'multi_syllable'=>[
                    ['ırğ*a', 'ırlarğ*a' ]
                ]
            ]
        ],
        'soft' => [
            'sonorous' => [
                'single_syllable'=>[
                    ['erg*e','erlerg*e'],
                ],
                'multi_syllable'=>[
                    ['irg*e','irlerg*e']
                ]
            ],
            'vowel' => [
                'single_syllable'=>[
                    ['rg*e', 'rlerg*e' ]
                ],
                'multi_syllable'=>[
                    ['rg*e', 'rlerg*e' ]
                ]
            ],
            'non_sonorous' => [
                'single_syllable'=>[
                    ['erg*e','erlerg*e'],
                ],
                'multi_syllable'=>[
                    ['irg*e','irlerg*e']
                ]
            ]
        ]
    ],
    
    'accusative_present' => [
        'group' => 'case',
        'template' => 'noun_case',
        'hard' => [
            'sonorous' => [
                'single_syllable'=>[
                    ['arn*ı', 'arlarn*ı']
                ],
                'multi_syllable'=>[
                    ['ırn*ı', 'ırlarn*ı' ]
                ]
            ],
            'vowel' => [
                'single_syllable'=>[
                    ['rn*ı', 'rlarn*ı' ]
                ],
                'multi_syllable'=>[
                    ['rn*ı', 'rlarn*ı' ]
                ]
            ],
            'non_sonorous' => [
                'single_syllable'=>[
                    ['arn*ı', 'arlarn*ı']
                ],
                'multi_syllable'=>[
                    ['ırn*ı', 'ırlarn*ı' ]
                ]
            ]
        ],
        'soft' => [
            'sonorous' => [
                'single_syllable'=>[
                    ['ern*i','erlern*i'],
                ],
                'multi_syllable'=>[
                    ['irn*i','irlern*i']
                ]
            ],
            'vowel' => [
                'single_syllable'=>[
                    ['rn*i', 'rlern*i' ]
                ],
                'multi_syllable'=>[
                    ['rn*i', 'rlern*i' ]
                ]
            ],
            'non_sonorous' => [
                'single_syllable'=>[
                    ['ern*i','erlern*i'],
                ],
                'multi_syllable'=>[
                    ['irn*i','irlern*i']
                ]
            ]
        ]
    ],
    
    'placive_present' => [
        'group' => 'case',
        'template' => 'noun_case',
        'hard' => [
            'sonorous' => [
                'single_syllable'=>[
                    ['ard*a', 'arlard*a']
                ],
                'multi_syllable'=>[
                    ['ırd*a', 'ırlard*a' ]
                ]
            ],
            'vowel' => [
                'single_syllable'=>[
                    ['rd*a', 'rlard*a' ]
                ],
                'multi_syllable'=>[
                    ['rd*a', 'rlard*a' ]
                ]
            ],
            'non_sonorous' => [
                'single_syllable'=>[
                    ['ard*a', 'arlard*a']
                ],
                'multi_syllable'=>[
                    ['ırd*a', 'ırlard*a' ]
                ]
            ]
        ],
        'soft' => [
            'sonorous' => [
                'single_syllable'=>[
                    ['erd*e','erlerd*e'],
                ],
                'multi_syllable'=>[
                    ['ird*e','irlerd*e']
                ]
            ],
            'vowel' => [
                'single_syllable'=>[
                    ['rd*e', 'rlerd*e' ]
                ],
                'multi_syllable'=>[
                    ['rd*e', 'rlerd*e' ]
                ]
            ],
            'non_sonorous' => [
                'single_syllable'=>[
                    ['erd*e','erlerd*e'],
                ],
                'multi_syllable'=>[
                    ['ird*e','irlerd*e']
                ]
            ]
        ]
    ],
    
    'exodive_present' => [
        'group' => 'case',
        'template' => 'noun_case',
        'hard' => [
            'sonorous' => [
                'single_syllable'=>[
                    ['ard*an', 'arlard*an']
                ],
                'multi_syllable'=>[
                    ['ırd*an', 'ırlard*an' ]
                ]
            ],
            'vowel' => [
                'single_syllable'=>[
                    ['rd*an', 'rlard*an' ]
                ],
                'multi_syllable'=>[
                    ['rd*an', 'rlard*an' ]
                ]
            ],
            'non_sonorous' => [
                'single_syllable'=>[
                    ['ard*an', 'arlard*an']
                ],
                'multi_syllable'=>[
                    ['ırd*an', 'ırlard*an' ]
                ]
            ]
        ],
        'soft' => [
            'sonorous' => [
                'single_syllable'=>[
                    ['erd*en','erlerd*en'],
                ],
                'multi_syllable'=>[
                    ['ird*en','irlerd*en']
                ]
            ],
            'vowel' => [
                'single_syllable'=>[
                    ['rd*en', 'rlerd*en' ]
                ],
                'multi_syllable'=>[
                    ['rd*en', 'rlerd*en' ]
                ]
            ],
            'non_sonorous' => [
                'single_syllable'=>[
                    ['erd*en','erlerd*en'],
                ],
                'multi_syllable'=>[
                    ['ird*en','irlerd*en']
                ]
            ]
        ]
    ]
    
];    
