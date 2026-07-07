<?php

declare(strict_types=1);

/**
 * Application-level configuration.
 */
return [
    'name'            => 'Employee Manager',
    'base_url'        => 'http://localhost:8000',
    'pagination_limit' => 10,
    'timezone'        => 'UTC',
    'session_name'    => 'emp_mgr_session',

    /** Departments available throughout the application */
    'departments' => [
        'Engineering',
        'Marketing',
        'Sales',
        'Human Resources',
        'Finance',
        'Operations',
        'Customer Support',
        'Legal',
        'Design',
        'Product',
    ],
];
