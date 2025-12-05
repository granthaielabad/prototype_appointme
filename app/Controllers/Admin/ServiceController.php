<?php
namespace App\Controllers\Admin;

use App\Models\Service;
use App\Core\Session;

/**
 * Admin service CRUD management.
 */
class ServiceController extends AdminController
{
    public function index(): void
    {
        $services = (new Service())->getAllActive();
        $this->render('services/index', ['services' => $services]);
    }

    public function create(): void
    {
        $this->render('services/create');
    }

    public function store(): void
    {
        if (empty($_POST['service_name']) || empty($_POST['price'])) {
            Session::flash('error', 'Please fill in required fields.', 'danger');
            header('Location: /admin/services/create');
            return;
        }

        (new Service())->create([
            'service_name' => $_POST['service_name'],
            'category' => $_POST['category'] ?? 'General',
            'description' => $_POST['description'] ?? null,
            'price' => $_POST['price'],
        ]);

        Session::flash('success', 'Service added successfully.', 'success');
        header('Location: /admin/services');
        exit;
    }

    public function edit(): void
    {
        $id = $_GET['id'] ?? null;
        $service = (new Service())->find($id);
        if (!$service) {
            Session::flash('error', 'Service not found.', 'danger');
            header('Location: /admin/services');
            return;
        }

        $this->render('services/edit', ['service' => $service]);
    }

    public function update(): void
    {
        if (empty($_POST['id'])) {
            Session::flash('error', 'Invalid update request.', 'danger');
            header('Location: /admin/services');
            return;
        }

        (new Service())->update($_POST['id'], [
            'service_name' => $_POST['service_name'],
            'category' => $_POST['category'] ?? 'General',
            'description' => $_POST['description'] ?? null,
            'price' => $_POST['price'],
        ]);

        Session::flash('success', 'Service updated successfully.', 'success');
        header('Location: /admin/services');
        exit;
    }

    public function delete(): void
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            Session::flash('error', 'Invalid archive request.', 'danger');
            header('Location: /admin/services');
            return;
        }

        $adminId = \App\Core\Auth::user()['user_id'] ?? null;
        $success = (new Service())->archive($id, $adminId);
        
        if ($success) {
            Session::flash('success', 'Service archived successfully.', 'success');
        } else {
            Session::flash('error', 'Failed to archive service.', 'danger');
        }
        
        header('Location: /admin/services');
        exit;
    }
}
