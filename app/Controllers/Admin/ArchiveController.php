<?php
namespace App\Controllers\Admin;

use App\Core\Session;
use App\Models\Archive;

/**
 * Archive management for admin.
 */
class ArchiveController extends AdminController
{
    protected Archive $archiveModel;

    public function __construct()
    {
        parent::__construct();
        $this->archiveModel = new Archive();
    }

    public function index(): void
    {
        $items = $this->archiveModel->getAll();
        $this->render('archives/index', ['items' => $items]);
    }

    public function restore(): void
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            Session::flash('error', 'Missing archive id.', 'danger');
            header('Location: /admin/archives');
            exit;
        }
        $ok = $this->archiveModel->restore((int)$id);
        Session::flash($ok ? 'success' : 'error', $ok ? 'Item restored.' : 'Failed to restore.', $ok ? 'success' : 'danger');
        header('Location: /admin/archives');
        exit;
    }

    public function delete(): void
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            Session::flash('error', 'Missing archive id.', 'danger');
            header('Location: /admin/archives');
            exit;
        }
        $ok = $this->archiveModel->remove((int)$id);
        Session::flash($ok ? 'success' : 'error', $ok ? 'Item permanently deleted.' : 'Failed to delete.', $ok ? 'success' : 'danger');
        header('Location: /admin/archives');
        exit;
    }
}
