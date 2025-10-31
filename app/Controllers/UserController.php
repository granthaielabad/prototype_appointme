<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class UserController extends Controller
{
    public function index(): void
    {
        $users = (new User())->findAll();
        $this->view('pages/users', ['users' => $users]);
    }

    public function show($id): void
    {
        $user = (new User())->find($id);
        $this->view('pages/user_show', ['user' => $user]);
    }
}
