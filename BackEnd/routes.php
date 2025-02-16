
<?php

return [
    'GET' => [
        // Маршруты для SubstanceController
        '/substances' => ['SubstanceController', 'get'],

        // Маршруты для SubstanceTypeController
        '/substance-types' => ['SubstanceTypeController', 'get'],
    ],
    'POST' => [
        // Маршруты для SubstanceController
        '/substances/create' => ['SubstanceController', 'create'],

        // Маршруты для SubstanceTypeController
        '/substance-types/create' => ['SubstanceTypeController', 'create']
    ],
    'PUT' => [
        // Маршруты для SubstanceController
        '/substances/update' => ['SubstanceController', 'update'],

        // Маршруты для SubstanceTypeController
        '/substance-types/update' => ['SubstanceTypeController', 'update']
    ],
    'DELETE' => [
        '/substances/delete' => ['SubstanceController', 'delete'],
        '/substance-types/delete' => ['SubstanceTypeController', 'delete']
    ]
];
?>
