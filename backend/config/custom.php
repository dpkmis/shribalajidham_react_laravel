<?php 

return [
    'secret_sso_secret' => env('WAVESTUBE_SSO_SECRET'),
    'LOG_CONTROLLER' => [
        'AuthenticatedSessionController' => 'Login',
        'MasterCategoriesController' => 'Category',
        'MasterFaqCategoriesController' => 'Faq Category',
        'MasterFaqContentController' => 'Faq Content',
        'MasterGenreController' => 'Genres',
        'MasterLanguageController' => 'Language',
        'ProfileController' => 'User Profile'
    ]
];



?>