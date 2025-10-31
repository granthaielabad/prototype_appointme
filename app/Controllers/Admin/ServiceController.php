<?php
namespace App\Controllers\Admin;

use App\Models\Service;
use App\Core\Session;

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
        (new Service())->create([
            'service_name' => $_POST['service_name'],
            'price' => $_POST['price'],
            'description' => $_POST['description']
        ]);
        Session::flash('success', 'Service added successfully!');
        header('Location: /admin/services');
    }

    public function edit(): void
    {
        $service = (new Service())->find($_GET['id']);
        $this->render('services/edit', ['service' => $service]);
    }

    public function update(): void
    {
        (new Service())->update($_POST['id'], [
            'service_name' => $_POST['service_name'],
            'description' => $_POST['description'],
            'price' => $_POST['price']
        ]);
        Session::flash('success', 'Service updated.');
        header('Location: /admin/services');
    }

    public function delete(): void
    {
        (new Service())->delete($_GET['id']);
        Session::flash('success', 'Service deleted.');
        header('Location: /admin/services');
    }
}
