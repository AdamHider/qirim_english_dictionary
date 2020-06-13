<?php


$noun = [
    
    'isafet' => [
        'prefix_to_word' => '',
        'group' => 'case',
        'template' => 'noun_case',
        'hard' => [
            'sonorous' => [
                'single_syllable'=>[
                    ['*ı']
                ],
                'multi_syllable'=>[
                    ['*ı']
                ]
            ],
            'vowel' => [
                'single_syllable'=>[
                    ['s*ı']
                ],
                'multi_syllable'=>[
                    ['s*ı']
                ]
            ],
            'non_sonorous' => [
                'single_syllable'=>[
                    ['*ı']
                ],
                'multi_syllable'=>[
                    ['*ı']
                ]
            ]
        ],
        'soft' => [
            'sonorous' => [
                'single_syllable'=>[
                    ['*i']
                ],
                'multi_syllable'=>[
                    ['*i']
                ]
            ],
            'vowel' => [
                'single_syllable'=>[
                    ['s*i']
                ],
                'multi_syllable'=>[
                    ['s*i']
                ]
            ],
            'non_sonorous' => [
                'single_syllable'=>[
                    ['*i']
                ],
                'multi_syllable'=>[
                    ['*i']
                ]
            ]
        ]
    ],
    'nominative_possession' => [
        'group' => 'case',
        'template' => 'personilized',
        'hard' => [
            'sonorous' => [
                'single_syllable'=>[
                    ['*ım|*um', '*ıñ|*uñ', '*ı|*u' ],
                    ['ım*ız|um*ız', 'ıñ*ız|uñ*ız', '*ı|*u' ]
                ],
                'multi_syllable'=>[
                    ['*ım|*um', '*ıñ|*uñ', '*ı|*u' ],
                    ['ım*ız|um*ız', 'ıñ*ız|uñ*ız', '*ı|*u' ]
                ]
            ],
            'vowel' => [
                'single_syllable'=>[
                    ['m', 'ñ', 's*ı' ],
                    ['m*ız', 'ñ*ız', 's*ı' ]
                ],
                'multi_syllable'=>[
                    ['m', 'ñ', 's*ı' ],
                    ['m*ız', 'ñ*ız', 's*ı' ]
                ]
            ],
            'non_sonorous' => [
                'single_syllable'=>[
                    ['*ım|*um', '*ıñ|*uñ', '*ı|*u' ],
                    ['ım*ız|um*ız', 'ıñ*ız|uñ*ız', '*ı|*u' ]
                ],
                'multi_syllable'=>[
                    ['*ım|*um', '*ıñ|*uñ', '*ı|*u' ],
                    ['ım*ız|um*ız', 'ıñ*ız|uñ*ız', '*ı|*u' ]
                ]
            ]
        ],
        'soft' => [
            'sonorous' => [
                'single_syllable'=>[
                    ['*im|*üm', '*iñ|*üñ', '*i|*ü' ],
                    ['im*iz|üm*iz', 'iñ*iz|üñ*iz', '*i|*ü' ]
                ],
                'multi_syllable'=>[
                    ['*im|*üm', '*iñ|*üñ', '*i|*ü' ],
                    ['im*iz|üm*iz', 'iñ*iz|üñ*iz', '*i|*ü' ]
                ]
            ],
            'vowel' => [
                'single_syllable'=>[
                    ['m', 'ñ', 's*i' ],
                    ['m*iz', 'ñ*iz', 's*i' ]
                ],
                'multi_syllable'=>[
                    ['m', 'ñ', 's*i' ],
                    ['m*iz', 'ñ*iz', 's*i' ]
                ]
            ],
            'non_sonorous' => [
                'single_syllable'=>[
                    ['*im|*üm', '*iñ|*üñ', '*i|*ü' ],
                    ['im*iz|üm*iz', 'iñ*iz|üñ*iz', '*i|*ü' ]
                ],
                'multi_syllable'=>[
                    ['*im|*üm', '*iñ|*üñ', '*i|*ü' ],
                    ['im*iz|üm*iz', 'iñ*iz|üñ*iz', '*i|*ü' ]
                ]
            ]
        ]
    ],
    'genitive_possession' => [
        'group' => 'case',
        'template' => 'personilized',
        'hard' => [
            'sonorous' => [
                'single_syllable'=>[
                    ['ımn*ıñ|umn*ıñ', 'ıñn*ıñ|uñn*ıñ', 'ın*ıñ|un*ıñ' ],
                    ['ımızn*ıñ|umızn*ıñ', 'ıñızn*ıñ|uñızn*ıñ', 'ın*ıñ|un*ıñ' ]
                ],
                'multi_syllable'=>[
                    ['ımn*ıñ|umn*ıñ', 'ıñn*ıñ|uñn*ıñ', 'ın*ıñ|un*ıñ' ],
                    ['ımızn*ıñ|umızn*ıñ', 'ıñızn*ıñ|uñızn*ıñ', 'ın*ıñ|un*ıñ' ]
                ]
            ],
            'vowel' => [
                'single_syllable'=>[
                    ['mn*ıñ', 'ñn*ıñ', 'sın*ıñ' ],
                    ['mızn*ıñ', 'ñızn*ıñ', 'sın*ıñ' ]
                ],
                'multi_syllable'=>[
                    ['mn*ıñ', 'ñn*ıñ', 'sın*ıñ' ],
                    ['mızn*ıñ', 'ñızn*ıñ', 'sın*ıñ' ]
                ]
            ],
            'non_sonorous' => [
                'single_syllable'=>[
                    ['ımn*ıñ|umn*ıñ', 'ıñn*ıñ|uñn*ıñ', 'ın*ıñ|un*ıñ' ],
                    ['ımızn*ıñ|umızn*ıñ', 'ıñızn*ıñ|uñızn*ıñ', 'ın*ıñ|un*ıñ' ]
                ],
                'multi_syllable'=>[
                    ['ımn*ıñ|umn*ıñ', 'ıñn*ıñ|uñn*ıñ', 'ın*ıñ|un*ıñ' ],
                    ['ımızn*ıñ|umızn*ıñ', 'ıñızn*ıñ|uñızn*ıñ', 'ın*ıñ|un*ıñ' ]
                ]
            ]
        ],
        'soft' => [
            'sonorous' => [
                'single_syllable'=>[
                    ['imn*iñ|ümn*iñ', 'iñn*iñ|üñn*iñ', 'in*iñ|ün*iñ' ],
                    ['imizn*iñ|ümizn*iñ', 'iñizn*iñ|üñizn*iñ', 'in*iñ|ün*iñ' ]
                ],
                'multi_syllable'=>[
                    ['imn*iñ|ümn*iñ', 'iñn*iñ|üñn*iñ', 'in*iñ|ün*iñ' ],
                    ['imizn*iñ|ümizn*iñ', 'iñizn*iñ|üñizn*iñ', 'in*iñ|ün*iñ' ]
                ]
            ],
            'vowel' => [
                'single_syllable'=>[
                    ['mn*iñ', 'ñn*iñ', 'sin*iñ' ],
                    ['mizn*iñ', 'ñizn*iñ', 'sin*iñ' ]
                ],
                'multi_syllable'=>[
                    ['mn*iñ', 'ñn*iñ', 'sin*iñ' ],
                    ['mizn*iñ', 'ñizn*iñ', 'sin*iñ' ]
                ]
            ],
            'non_sonorous' => [
                'single_syllable'=>[
                    ['imn*iñ|ümn*iñ', 'iñn*iñ|üñn*iñ', 'in*iñ|ün*iñ' ],
                    ['imizn*iñ|ümizn*iñ', 'iñizn*iñ|üñizn*iñ', 'in*iñ|ün*iñ' ]
                ],
                'multi_syllable'=>[
                    ['imn*iñ|ümn*iñ', 'iñn*iñ|üñn*iñ', 'in*iñ|ün*iñ' ],
                    ['imizn*iñ|ümizn*iñ', 'iñizn*iñ|üñizn*iñ', 'in*iñ|ün*iñ' ]
                ]
            ]
        ]
    ],
    'dative_possession' => [
        'group' => 'case',
        'template' => 'personilized',
        'hard' => [
            'sonorous' => [
                'single_syllable'=>[
                    ['ım*a|um*a', 'ıñ*a|uñ*a', 'ın*a|un*a' ],
                    ['ımızğ*a|umızğ*a', 'ıñızğ*a|uñızğ*a', 'ın*a|un*a' ]
                ],
                'multi_syllable'=>[
                    ['ım*a|um*a', 'ıñ*a|uñ*a', 'ın*a|un*a' ],
                    ['ımızğ*a|umızğ*a', 'ıñızğ*a|uñızğ*a', 'ın*a|un*a' ]
                ]
            ],
            'vowel' => [
                'single_syllable'=>[
                    ['m*a', 'ñ*a', 'sın*a' ],
                    ['mızğ*a', 'ñızğ*a', 'sın*a' ]
                ],
                'multi_syllable'=>[
                    ['m*a', 'ñ*a', 'sın*a' ],
                    ['mızğ*a', 'ñızğ*a', 'sın*a' ]
                ]
            ],
            'non_sonorous' => [
                'single_syllable'=>[
                    ['ım*a|um*a', 'ıñ*a|uñ*a', 'ın*a|un*a' ],
                    ['ımızğ*a|umızğ*a', 'ıñızğ*a|uñızğ*a', 'ın*a|un*a' ]
                ],
                'multi_syllable'=>[
                    ['ım*a|um*a', 'ıñ*a|uñ*a', 'ın*a|un*a' ],
                    ['ımızğ*a|umızğ*a', 'ıñızğ*a|uñızğ*a', 'ın*a|un*a' ]
                ]
            ]
        ],
        'soft' => [
            'sonorous' => [
                'single_syllable'=>[
                    ['im*e|üm*e', 'iñ*e|üñ*e', 'in*e|ün*e' ],
                    ['imizg*e|ümizg*e', 'iñizg*e|üñizg*e', 'in*e|ün*e' ]
                ],
                'multi_syllable'=>[
                    ['im*e|üm*e', 'iñ*e|üñ*e', 'in*e|ün*e' ],
                    ['imizg*e|ümizg*e', 'iñizg*e|üñizg*e', 'in*e|ün*e' ]
                ]
            ],
            'vowel' => [
                'single_syllable'=>[
                    ['m*e', 'ñ*e', 'sin*e' ],
                    ['mizg*e', 'ñizg*e', 'sin*e' ]
                ],
                'multi_syllable'=>[
                    ['m*e', 'ñ*e', 'sin*e' ],
                    ['mizg*e', 'ñizg*e', 'sin*e' ]
                ]
            ],
            'non_sonorous' => [
                'single_syllable'=>[
                    ['im*e|üm*e', 'iñ*e|üñ*e', 'in*e|ün*e' ],
                    ['imizg*e|ümizg*e', 'iñizg*e|üñizg*e', 'in*e|ün*e' ]
                ],
                'multi_syllable'=>[
                    ['im*e|üm*e', 'iñ*e|üñ*e', 'in*e|ün*e' ],
                    ['imizg*e|ümizg*e', 'iñizg*e|üñizg*e', 'in*e|ün*e' ]
                ]
            ]
        ]
    ],
    'accusative_possession' => [
        'group' => 'case',
        'template' => 'personilized',
        'hard' => [
            'sonorous' => [
                'single_syllable'=>[
                    ['ımn*ı|umn*ı', 'ıñn*ı|uñn*ı', 'ın*ı|un*ı' ],
                    ['ımızn*ı|umızn*ı', 'ıñızn*ı|uñızn*ı', 'ın*ı|un*ı' ]
                ],
                'multi_syllable'=>[
                    ['ımn*ı|umn*ı', 'ıñn*ı|uñn*ı', 'ın*ı|un*ı' ],
                    ['ımızn*ı|umızn*ı', 'ıñızn*ı|uñızn*ı', 'ın*ı|un*ı' ]
                ]
            ],
            'vowel' => [
                'single_syllable'=>[
                    ['mn*ı', 'ñn*ı', 'sın*ı' ],
                    ['mızn*ı', 'ñızn*ı', 'sın*ı' ]
                ],
                'multi_syllable'=>[
                    ['mn*ı', 'ñn*ı', 'sın*ı' ],
                    ['mızn*ı', 'ñızn*ı', 'sın*ı' ]
                ]
            ],
            'non_sonorous' => [
                'single_syllable'=>[
                    ['ımn*ı|umn*ı', 'ıñn*ı|uñn*ı', 'ın*ı|un*ı' ],
                    ['ımızn*ı|umızn*ı', 'ıñızn*ı|uñızn*ı', 'ın*ı|un*ı' ]
                ],
                'multi_syllable'=>[
                    ['ımn*ı|umn*ı', 'ıñn*ı|uñn*ı', 'ın*ı|un*ı' ],
                    ['ımızn*ı|umızn*ı', 'ıñızn*ı|uñızn*ı', 'ın*ı|un*ı' ]
                ]
            ]
        ],
        'soft' => [
            'sonorous' => [
                'single_syllable'=>[
                    ['imn*i|ümn*i', 'iñn*i|üñn*i', 'in*i|ün*i' ],
                    ['imizn*i|ümizn*i', 'iñizn*i|üñizn*i', 'in*i|ün*i' ]
                ],
                'multi_syllable'=>[
                    ['im|üm', 'iñ|üñ', 'i|ü' ],
                    ['imiz|ümiz', 'iñiz|üñiz', 'i|ü' ]
                ]
            ],
            'vowel' => [
                'single_syllable'=>[
                    ['mn*i', 'ñn*i', 'sin*i' ],
                    ['mizn*i', 'ñizn*i', 'sin*i' ]
                ],
                'multi_syllable'=>[
                    ['mn*i', 'ñn*i', 'sin*i' ],
                    ['mizn*i', 'ñizn*i', 'sin*i' ]
                ]
            ],
            'non_sonorous' => [
                'single_syllable'=>[
                    ['imn*i|ümn*i', 'iñn*i|üñn*i', 'in*i|ün*i' ],
                    ['imizn*i|ümizn*i', 'iñizn*i|üñizn*i', 'in*i|ün*i' ]
                ],
                'multi_syllable'=>[
                    ['imn*i|ümn*i', 'iñn*i|üñn*i', 'in*i|ün*i' ],
                    ['imizn*i|ümizn*i', 'iñizn*i|üñizn*i', 'in*i|ün*i' ]
                ]
            ]
        ]
    ],
    'placive_possession' => [
        'group' => 'case',
        'template' => 'personilized',
        'hard' => [
            'sonorous' => [
                'single_syllable'=>[
                    ['ımd*a|umd*a', 'ıñd*a|uñd*a', 'ınd*a|und*a' ],
                    ['ımızd*a|umızd*a', 'ıñızd*a|uñızd*a', 'ınd*a|und*a' ]
                ],
                'multi_syllable'=>[
                    ['ımd*a|umd*a', 'ıñd*a|uñd*a', 'ınd*a|und*a' ],
                    ['ımızd*a|umızd*a', 'ıñızd*a|uñızd*a', 'ınd*a|und*a' ]
                ]
            ],
            'vowel' => [
                'single_syllable'=>[
                    ['md*a', 'ñd*a', 'sınd*a' ],
                    ['mızd*a', 'ñızd*a', 'sınd*a' ]
                ],
                'multi_syllable'=>[
                    ['md*a', 'ñd*a', 'sınd*a' ],
                    ['mızd*a', 'ñızd*a', 'sınd*a' ]
                ]
            ],
            'non_sonorous' => [
                'single_syllable'=>[
                    ['ımd*a|umd*a', 'ıñd*a|uñd*a', 'ınd*a|und*a' ],
                    ['ımızd*a|umızd*a', 'ıñızd*a|uñızd*a', 'ınd*a|und*a' ]
                ],
                'multi_syllable'=>[
                    ['ımd*a|umd*a', 'ıñd*a|uñd*a', 'ınd*a|und*a' ],
                    ['ımızd*a|umızd*a', 'ıñızd*a|uñızd*a', 'ınd*a|und*a' ]
                ]
            ]
        ],
        'soft' => [
            'sonorous' => [
                'single_syllable'=>[
                    ['imd*e|ümd*e', 'iñd*e|üñd*e', 'ind*e|ünd*e' ],
                    ['imizd*e|ümizd*e', 'iñizd*e|üñizd*e', 'ind*e|ünd*e' ]
                ],
                'multi_syllable'=>[
                    ['imd*e|ümd*e', 'iñd*e|üñd*e', 'ind*e|ünd*e' ],
                    ['imizd*e|ümizd*e', 'iñizd*e|üñizd*e', 'ind*e|ünd*e' ]
                ]
            ],
            'vowel' => [
                'single_syllable'=>[
                    ['md*e', 'ñd*e', 'sind*e' ],
                    ['mizd*e', 'ñizd*e', 'sind*e' ]
                ],
                'multi_syllable'=>[
                    ['md*e', 'ñd*e', 'sind*e' ],
                    ['mizd*e', 'ñizd*e', 'sind*e' ]
                ]
            ],
            'non_sonorous' => [
                'single_syllable'=>[
                    ['imd*e|ümd*e', 'iñd*e|üñd*e', 'ind*e|ünd*e' ],
                    ['imizd*e|ümizd*e', 'iñizd*e|üñizd*e', 'ind*e|ünd*e' ]
                ],
                'multi_syllable'=>[
                    ['imd*e|ümd*e', 'iñd*e|üñd*e', 'ind*e|ünd*e' ],
                    ['imizd*e|ümizd*e', 'iñizd*e|üñizd*e', 'ind*e|ünd*e' ]
                ]
            ]
        ]
    ],
    'exodive_possession' => [
        'group' => 'case',
        'template' => 'personilized',
        'hard' => [
            'sonorous' => [
                'single_syllable'=>[
                    ['ımd*an|umd*an', 'ıñd*an|uñd*an', 'ınd*an|ud*an' ],
                    ['ımızd*an|umızd*an', 'ıñızd*an|uñızd*an', 'ıd*an|ud*an' ]
                ],
                'multi_syllable'=>[
                    ['ımd*an|umd*an', 'ıñd*an|uñd*an', 'ınd*an|ud*an' ],
                    ['ımızd*an|umızd*an', 'ıñızd*an|uñızd*an', 'ıd*an|ud*an' ]
                ]
            ],
            'vowel' => [
                'single_syllable'=>[
                    ['md*an', 'ñd*an', 'sıd*an' ],
                    ['mızd*an', 'ñızd*an', 'sıd*an' ]
                ],
                'multi_syllable'=>[
                    ['md*an', 'ñd*an', 'sıd*an' ],
                    ['mızd*an', 'ñızd*an', 'sıd*an' ]
                ]
            ],
            'non_sonorous' => [
                'single_syllable'=>[
                    ['ımd*an|umd*an', 'ıñd*an|uñd*an', 'ınd*an|ud*an' ],
                    ['ımızd*an|umızd*an', 'ıñızd*an|uñızd*an', 'ıd*an|ud*an' ]
                ],
                'multi_syllable'=>[
                    ['ımd*an|umd*an', 'ıñd*an|uñd*an', 'ınd*an|ud*an' ],
                    ['ımızd*an|umızd*an', 'ıñızd*an|uñızd*an', 'ıd*an|ud*an' ]
                ]
            ]
        ],
        'soft' => [
            'sonorous' => [
                'single_syllable'=>[
                    ['imd*en|ümd*en', 'iñd*en|üñd*en', 'ind*en|ünd*en' ],
                    ['imizd*en|ümizd*en', 'iñizd*en|üñizd*en', 'ind*en|ünd*en' ]
                ],
                'multi_syllable'=>[
                    ['imd*en|ümd*en', 'iñd*en|üñd*en', 'ind*en|ünd*en' ],
                    ['imizd*en|ümizd*en', 'iñizd*en|üñizd*en', 'ind*en|ünd*en' ]
                ]
            ],
            'vowel' => [
                'single_syllable'=>[
                    ['md*en', 'ñd*en', 'sind*en' ],
                    ['mizd*en', 'ñizd*en', 'sind*en' ]
                ],
                'multi_syllable'=>[
                    ['md*en', 'ñd*en', 'sind*en' ],
                    ['mizd*en', 'ñizd*en', 'sind*en' ]
                ]
            ],
            'non_sonorous' => [
                'single_syllable'=>[
                    ['imd*en|ümd*en', 'iñd*en|üñd*en', 'ind*en|ünd*en' ],
                    ['imizd*en|ümizd*en', 'iñizd*en|üñizd*en', 'ind*en|ünd*en' ]
                ],
                'multi_syllable'=>[
                    ['imd*en|ümd*en', 'iñd*en|üñd*en', 'ind*en|ünd*en' ],
                    ['imizd*en|ümizd*en', 'iñizd*en|üñizd*en', 'ind*en|ünd*en' ]
                ]
            ]
        ]
    ],
    
    'nominative_plural_possession' => [
        'group' => 'case',
        'template' => 'personilized',
        'hard' => [
            'sonorous' => [
                'single_syllable'=>[
                    ['lar*ım', 'lar*ıñ', 'lar*ı' ],
                    ['larım*ız', 'larıñ*ız', 'lar*ı' ]
                ],
                'multi_syllable'=>[
                    ['lar*ım', 'lar*ıñ', 'lar*ı' ],
                    ['larım*ız', 'larıñ*ız', 'lar*ı' ]
                ]
            ],
            'vowel' => [
                'single_syllable'=>[
                    ['lar*ım', 'lar*ıñ', 'lar*ı' ],
                    ['larım*ız', 'larıñ*ız', 'lar*ı' ]
                ],
                'multi_syllable'=>[
                    ['lar*ım', 'lar*ıñ', 'lar*ı' ],
                    ['larım*ız', 'larıñ*ız', 'lar*ı' ]
                ]
            ],
            'non_sonorous' => [
                'single_syllable'=>[
                    ['lar*ım', 'lar*ıñ', 'lar*ı' ],
                    ['larım*ız', 'larıñ*ız', 'lar*ı' ]
                ],
                'multi_syllable'=>[
                    ['lar*ım', 'lar*ıñ', 'lar*ı' ],
                    ['larım*ız', 'larıñ*ız', 'lar*ı' ]
                ]
            ]
        ],
        'soft' => [
            'sonorous' => [
                'single_syllable'=>[
                    ['ler*im', 'ler*iñ', 'ler*i' ],
                    ['lerim*iz', 'leriñ*iz', 'ler*i' ]
                ],
                'multi_syllable'=>[
                    ['ler*im', 'ler*iñ', 'ler*i' ],
                    ['lerim*iz', 'leriñ*iz', 'ler*i' ]
                ]
            ],
            'vowel' => [
                'single_syllable'=>[
                    ['ler*im', 'ler*iñ', 'ler*i' ],
                    ['lerim*iz', 'leriñ*iz', 'ler*i' ]
                ],
                'multi_syllable'=>[
                    ['ler*im', 'ler*iñ', 'ler*i' ],
                    ['lerim*iz', 'leriñ*iz', 'ler*i' ]
                ]
            ],
            'non_sonorous' => [
                'single_syllable'=>[
                    ['ler*im', 'ler*iñ', 'ler*i' ],
                    ['lerim*iz', 'leriñ*iz', 'ler*i' ]
                ],
                'multi_syllable'=>[
                    ['ler*im', 'ler*iñ', 'ler*i' ],
                    ['lerim*iz', 'leriñ*iz', 'ler*i' ]
                ]
            ]
        ]
    ],
    'genitive_plural_possession' => [
        'group' => 'case',
        'template' => 'personilized',
        'hard' => [
            'sonorous' => [
                'single_syllable'=>[
                    ['larımn*ıñ', 'larıñn*ıñ', 'ların*ıñ' ],
                    ['larımizn*ıñ', 'larıñizn*ıñ', 'ların*ıñ' ]
                ],
                'multi_syllable'=>[
                    ['larımn*ıñ', 'larıñn*ıñ', 'ların*ıñ' ],
                    ['larımizn*ıñ', 'larıñizn*ıñ', 'ların*ıñ' ]
                ]
            ],
            'vowel' => [
                'single_syllable'=>[
                    ['larımn*ıñ', 'larıñn*ıñ', 'ların*ıñ' ],
                    ['larımizn*ıñ', 'larıñizn*ıñ', 'ların*ıñ' ]
                ],
                'multi_syllable'=>[
                    ['larımn*ıñ', 'larıñn*ıñ', 'ların*ıñ' ],
                    ['larımizn*ıñ', 'larıñizn*ıñ', 'ların*ıñ' ]
                ]
            ],
            'non_sonorous' => [
                'single_syllable'=>[
                    ['larımn*ıñ', 'larıñn*ıñ', 'ların*ıñ' ],
                    ['larımizn*ıñ', 'larıñizn*ıñ', 'ların*ıñ' ]
                ],
                'multi_syllable'=>[
                    ['larımn*ıñ', 'larıñn*ıñ', 'ların*ıñ' ],
                    ['larımizn*ıñ', 'larıñizn*ıñ', 'ların*ıñ' ]
                ]
            ]
        ],
        'soft' => [
            'sonorous' => [
                'single_syllable'=>[
                    ['lerimn*iñ', 'leriñn*iñ', 'lerin*iñ' ],
                    ['lerimizn*iñ', 'leriñizn*iñ', 'lerin*iñ' ]
                ],
                'multi_syllable'=>[
                    ['lerimn*iñ', 'leriñn*iñ', 'lerin*iñ' ],
                    ['lerimizn*iñ', 'leriñizn*iñ', 'lerin*iñ' ]
                ]
            ],
            'vowel' => [
                'single_syllable'=>[
                    ['lerimn*iñ', 'leriñn*iñ', 'lerin*iñ' ],
                    ['lerimizn*iñ', 'leriñizn*iñ', 'lerin*iñ' ]
                ],
                'multi_syllable'=>[
                    ['lerimn*iñ', 'leriñn*iñ', 'lerin*iñ' ],
                    ['lerimizn*iñ', 'leriñizn*iñ', 'lerin*iñ' ]
                ]
            ],
            'non_sonorous' => [
                'single_syllable'=>[
                    ['lerimn*iñ', 'leriñn*iñ', 'lerin*iñ' ],
                    ['lerimizn*iñ', 'leriñizn*iñ', 'lerin*iñ' ]
                ],
                'multi_syllable'=>[
                    ['lerimn*iñ', 'leriñn*iñ', 'lerin*iñ' ],
                    ['lerimizn*iñ', 'leriñizn*iñ', 'lerin*iñ' ]
                ]
            ]
        ]
    ],
    'dative_plural_possession' => [
        'group' => 'case',
        'template' => 'personilized',
        'hard' => [
            'sonorous' => [
                'single_syllable'=>[
                    ['larım*a', 'larıñ*a', 'ların*a' ],
                    ['larımızğ*a', 'larıñızğ*a', 'ların*a' ]
                ],
                'multi_syllable'=>[
                    ['larım*a', 'larıñ*a', 'ların*a' ],
                    ['larımızğ*a', 'larıñızğ*a', 'ların*a' ]
                ]
            ],
            'vowel' => [
                'single_syllable'=>[
                    ['larım*a', 'larıñ*a', 'ların*a' ],
                    ['larımızğ*a', 'larıñızğ*a', 'ların*a' ]
                ],
                'multi_syllable'=>[
                    ['larım*a', 'larıñ*a', 'ların*a' ],
                    ['larımızğ*a', 'larıñızğ*a', 'ların*a' ]
                ]
            ],
            'non_sonorous' => [
                'single_syllable'=>[
                    ['larım*a', 'larıñ*a', 'ların*a' ],
                    ['larımızğ*a', 'larıñızğ*a', 'ların*a' ]
                ],
                'multi_syllable'=>[
                    ['larım*a', 'larıñ*a', 'ların*a' ],
                    ['larımızğ*a', 'larıñızğ*a', 'ların*a' ]
                ]
            ]
        ],
        'soft' => [
            'sonorous' => [
                'single_syllable'=>[
                    ['lerim*e', 'leriñ*e', 'lerin*e'],
                    ['lerimizg*e', 'leriñizg*e', 'lerin*e']
                ],
                'multi_syllable'=>[
                    ['lerim*e', 'leriñ*e', 'lerin*e'],
                    ['lerimizg*e', 'leriñizg*e', 'lerin*e']
                ]
            ],
            'vowel' => [
                'single_syllable'=>[
                    ['lerim*e', 'leriñ*e', 'lerin*e'],
                    ['lerimizg*e', 'leriñizg*e', 'lerin*e']
                ],
                'multi_syllable'=>[
                    ['lerim*e', 'leriñ*e', 'lerin*e'],
                    ['lerimizg*e', 'leriñizg*e', 'lerin*e']
                ]
            ],
            'non_sonorous' => [
                'single_syllable'=>[
                    ['lerim*e', 'leriñ*e', 'lerin*e'],
                    ['lerimizg*e', 'leriñizg*e', 'lerin*e']
                ],
                'multi_syllable'=>[
                    ['lerim*e', 'leriñ*e', 'lerin*e'],
                    ['lerimizg*e', 'leriñizg*e', 'lerin*e']
                ]
            ]
        ]
    ],
    'accusative_plural_possession' => [
        'group' => 'case',
        'template' => 'personilized',
        'hard' => [
            'sonorous' => [
                'single_syllable'=>[
                    ['larımn*ı', 'larıñn*ı', 'ların*ı' ],
                    ['larımızn*ı', 'larıñızn*ı', 'ların*ı' ]
                ],
                'multi_syllable'=>[
                    ['larımn*ı', 'larıñn*ı', 'ların*ı' ],
                    ['larımızn*ı', 'larıñızn*ı', 'ların*ı' ]
                ]
            ],
            'vowel' => [
                'single_syllable'=>[
                    ['larımn*ı', 'larıñn*ı', 'ların*ı' ],
                    ['larımızn*ı', 'larıñızn*ı', 'ların*ı' ]
                ],
                'multi_syllable'=>[
                    ['larımn*ı', 'larıñn*ı', 'ların*ı' ],
                    ['larımızn*ı', 'larıñızn*ı', 'ların*ı' ]
                ]
            ],
            'non_sonorous' => [
                'single_syllable'=>[
                    ['larımn*ı', 'larıñn*ı', 'ların*ı' ],
                    ['larımızn*ı', 'larıñızn*ı', 'ların*ı' ]
                ],
                'multi_syllable'=>[
                    ['larımn*ı', 'larıñn*ı', 'ların*ı' ],
                    ['larımızn*ı', 'larıñızn*ı', 'ların*ı' ]
                ]
            ]
        ],
        'soft' => [
            'sonorous' => [
                'single_syllable'=>[
                    ['lerimn*i', 'leriñn*i', 'lerin*i' ],
                    ['lerimizn*i', 'leriñizn*i', 'lerin*i' ]
                ],
                'multi_syllable'=>[
                    ['lerimn*i', 'leriñn*i', 'lerin*i' ],
                    ['lerimizn*i', 'leriñizn*i', 'lerin*i' ]
                ]
            ],
            'vowel' => [
                'single_syllable'=>[
                    ['lerimn*i', 'leriñn*i', 'lerin*i' ],
                    ['lerimizn*i', 'leriñizn*i', 'lerin*i' ]
                ],
                'multi_syllable'=>[
                    ['lerimn*i', 'leriñn*i', 'lerin*i' ],
                    ['lerimizn*i', 'leriñizn*i', 'lerin*i' ]
                ]
            ],
            'non_sonorous' => [
                'single_syllable'=>[
                    ['lerimn*i', 'leriñn*i', 'lerin*i' ],
                    ['lerimizn*i', 'leriñizn*i', 'lerin*i' ]
                ],
                'multi_syllable'=>[
                    ['lerimn*i', 'leriñn*i', 'lerin*i' ],
                    ['lerimizn*i', 'leriñizn*i', 'lerin*i' ]
                ]
            ]
        ]
    ],
    'placive_plural_possession' => [
        'group' => 'case',
        'template' => 'personilized',
        'hard' => [
            'sonorous' => [
                'single_syllable'=>[
                    ['larımd*a', 'larıñd*a', 'larınd*a' ],
                    ['larımızd*a', 'larıñızd*a', 'larınd*a' ]
                ],
                'multi_syllable'=>[
                    ['larımd*a', 'larıñd*a', 'larınd*a' ],
                    ['larımızd*a', 'larıñızd*a', 'larınd*a' ]
                ]
            ],
            'vowel' => [
                'single_syllable'=>[
                    ['larımd*a', 'larıñd*a', 'larınd*a' ],
                    ['larımızd*a', 'larıñızd*a', 'larınd*a' ]
                ],
                'multi_syllable'=>[
                    ['larımd*a', 'larıñd*a', 'larınd*a' ],
                    ['larımızd*a', 'larıñızd*a', 'larınd*a' ]
                ]
            ],
            'non_sonorous' => [
                'single_syllable'=>[
                    ['larımd*a', 'larıñd*a', 'larınd*a' ],
                    ['larımızd*a', 'larıñızd*a', 'larınd*a' ]
                ],
                'multi_syllable'=>[
                    ['larımd*a', 'larıñd*a', 'larınd*a' ],
                    ['larımızd*a', 'larıñızd*a', 'larınd*a' ]
                ]
            ]
        ],
        'soft' => [
            'sonorous' => [
                'single_syllable'=>[
                    ['lerimd*e', 'leriñd*e', 'lerind*e' ],
                    ['lerimizd*e', 'leriñizd*e', 'lerind*e' ]
                ],
                'multi_syllable'=>[
                    ['lerimd*e', 'leriñd*e', 'lerind*e' ],
                    ['lerimizd*e', 'leriñizd*e', 'lerind*e' ]
                ]
            ],
            'vowel' => [
                'single_syllable'=>[
                    ['lerimd*e', 'leriñd*e', 'lerind*e' ],
                    ['lerimizd*e', 'leriñizd*e', 'lerind*e' ]
                ],
                'multi_syllable'=>[
                    ['lerimd*e', 'leriñd*e', 'lerind*e' ],
                    ['lerimizd*e', 'leriñizd*e', 'lerind*e' ]
                ]
            ],
            'non_sonorous' => [
                'single_syllable'=>[
                    ['lerimd*e', 'leriñd*e', 'lerind*e' ],
                    ['lerimizd*e', 'leriñizd*e', 'lerind*e' ]
                ],
                'multi_syllable'=>[
                    ['lerimd*e', 'leriñd*e', 'lerind*e' ],
                    ['lerimizd*e', 'leriñizd*e', 'lerind*e' ]
                ]
            ]
        ]
    ],
    'exodive_plural_possession' => [
        'group' => 'case',
        'template' => 'personilized',
        'hard' => [
            'sonorous' => [
                'single_syllable'=>[
                    ['larımd*an', 'larıñd*an', 'larınd*an' ],
                    ['larımızd*an', 'larıñızd*an', 'larıd*an' ]
                ],
                'multi_syllable'=>[
                    ['larımd*an', 'larıñd*an', 'larınd*an' ],
                    ['larımızd*an', 'larıñızd*an', 'larıd*an' ]
                ]
            ],
            'vowel' => [
                'single_syllable'=>[
                    ['larımd*an', 'larıñd*an', 'larınd*an' ],
                    ['larımızd*an', 'larıñızd*an', 'larıd*an' ]
                ],
                'multi_syllable'=>[
                    ['larımd*an', 'larıñd*an', 'larınd*an' ],
                    ['larımızd*an', 'larıñızd*an', 'larıd*an' ]
                ]
            ],
            'non_sonorous' => [
                'single_syllable'=>[
                    ['larımd*an', 'larıñd*an', 'larınd*an' ],
                    ['larımızd*an', 'larıñızd*an', 'larıd*an' ]
                ],
                'multi_syllable'=>[
                    ['larımd*an', 'larıñd*an', 'larınd*an' ],
                    ['larımızd*an', 'larıñızd*an', 'larıd*an' ]
                ]
            ]
        ],
        'soft' => [
            'sonorous' => [
                'single_syllable'=>[
                    ['lerimd*en', 'leriñd*en', 'lerind*en' ],
                    ['lerimizd*en', 'leriñizd*en', 'lerind*en' ]
                ],
                'multi_syllable'=>[
                    ['lerimd*en', 'leriñd*en', 'lerind*en' ],
                    ['lerimizd*en', 'leriñizd*en', 'lerind*en' ]
                ]
            ],
            'vowel' => [
                'single_syllable'=>[
                    ['lerimd*en', 'leriñd*en', 'lerind*en' ],
                    ['lerimizd*en', 'leriñizd*en', 'lerind*en' ]
                ],
                'multi_syllable'=>[
                    ['lerimd*en', 'leriñd*en', 'lerind*en' ],
                    ['lerimizd*en', 'leriñizd*en', 'lerind*en' ]
                ]
            ],
            'non_sonorous' => [
                'single_syllable'=>[
                    ['lerimd*en', 'leriñd*en', 'lerind*en' ],
                    ['lerimizd*en', 'leriñizd*en', 'lerind*en' ]
                ],
                'multi_syllable'=>[
                    ['lerimd*en', 'leriñd*en', 'lerind*en' ],
                    ['lerimizd*en', 'leriñizd*en', 'lerind*en' ]
                ]
            ]
        ]
    ]
];  


