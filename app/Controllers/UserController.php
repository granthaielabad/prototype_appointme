<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Session;
use App\Core\CSRF;
use App\Models\User;

class UserController extends Controller
{
    /**
     * PUBLIC PAGES (still optional)
     */
    public function index(): void
    {
        $users = (new User())->findAll();

        // moved out of /pages/users
        $this->renderPublic("Home/users", [
            "users" => $users,
            "pageTitle" => "Users"
        ]);
    }

    public function show($id): void
    {
        $user = (new User())->find($id);

        // moved out of /pages/user_show
        $this->renderPublic("Home/user_show", [
            "user" => $user,
            "pageTitle" => "User"
        ]);
    }

    /**
     * CUSTOMER PROFILE PAGE
     * route: /profile
     */
    public function profile(): void
    {
        Auth::requireRole(2); // customers only

        $user = Auth::user();

        $this->renderCustomer("Customer/profile", [
            "user" => $user,
            "pageTitle" => "My Profile"
        ]);
    }

    /**
     * Update customer profile
     */
    public function updateProfile(): void
    {
        Auth::requireRole(2);

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            header("Location: /profile");
            exit();
        }

        $token = $_POST["_csrf"] ?? "";
        if (!CSRF::verify($token)) {
            Session::flash("error", "Invalid form submission", "danger");
            header("Location: /profile");
            exit();
        }

        $m = new User();

        $updates = [
            "first_name"      => trim($_POST["first_name"] ?? ""),
            "last_name"       => trim($_POST["last_name"] ?? ""),
            "contact_number"  => trim($_POST["contact_number"] ?? "")
        ];

        $m->update(Auth::user()["user_id"], $updates);

        Session::flash("success", "Profile updated successfully.", "success");
        header("Location: /profile");
        exit();
    }
}