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
        $services = (new Service())->findAll();
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
            Session::flash('error', 'Invalid delete request.', 'danger');
            header('Location: /admin/services');
            return;
        }

        (new Service())->delete($id);
        Session::flash('success', 'Service deleted successfully.', 'success');
        header('Location: /admin/services');
        exit;
    }
}
