<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Employee;

/**
 * DashboardController – displays summary statistics on the home page.
 */
class DashboardController extends BaseController
{
    public function index(): void
    {
        $employeeModel = new Employee();
        $stats         = $employeeModel->getStats();

        $this->render('dashboard.index', [
            'title' => 'Dashboard',
            'stats' => $stats,
        ]);
    }
}
