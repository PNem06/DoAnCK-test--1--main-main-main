<?php

// 🔥 LOAD MODEL TRƯỚC (QUAN TRỌNG NHẤT)
require_once 'App/Models/MonUkou/Account.php';

session_start();

// 🔥 CHẶN CHƯA LOGIN
if (!isset($_SESSION['user_obj'])) {
    $controller = $_GET['controller'] ?? '';

    if ($controller !== 'account') {
        header("Location: index.php?controller=account&action=login");
        exit;
    }
}

require_once 'Config/database.php';
require_once 'Config/config.php';

ob_start();

try {
    $controller = $_GET['controller'] ?? 'home';
    $action = $_GET['action'] ?? 'index';
    $page = $_GET['page'] ?? 1;
    $id = $_GET['id'] ?? 0;

    switch ($controller) {

        // ================= ADMIN =================
        case 'admin':
                require_once 'App/Controllers/MonUkou/AdminController.php';
                $ctrl = new \App\Controllers\MonUkou\AdminController();

                // 🔥 CHẶN KHÔNG PHẢI ADMIN
                if (!isset($_SESSION['user_obj']) || $_SESSION['user_obj']->getRole() != 1) {
                    header("Location: index.php");
                    exit;
                }

                switch ($action) {
                    case 'dashboard':
                        $ctrl->dashboard();
                        break;

                    case 'addpost':
                        require 'App/Views/Admin/addpost.php';
                        break;

                    case 'detailpost':
                        require 'App/Views/Admin/detailpost.php';
                        break;

                    case 'editpost':
                        require 'App/Views/Admin/editpost.php';
                        break;

                    default:
                        $ctrl->dashboard();
                        break;
                }
                break;

        // ================= ACCOUNT =================
        case 'account':
        require_once 'App/Controllers/MonUkou/AccountController.php';
        $ctrl = new \App\Controllers\MonUkou\AccountController();

        if ($action === 'login') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $ctrl->login(Database::getInstance()->getConnection());
            } else {
                $ctrl->showLogin();
            }
        }

        elseif ($action === 'profile') {
            $ctrl->profile();
        }

        elseif ($action === 'updateProfile') {   // 🔥 THÊM DÒNG NÀY
            $ctrl->updateProfile();
        }

        elseif ($action === 'logout') {
            $ctrl->logout();
        }

        break;

        // ================= HOME =================
        case 'home':
            require_once 'App/Controllers/PNem06/HomeController.php';
            $ctrl = new HomeController(Database::getInstance()->getConnection());
            $ctrl->index($page);
            break;

        // ================= DEFAULT =================
        default:
            require_once 'App/Controllers/PNem06/HomeController.php';
            $ctrl = new HomeController(Database::getInstance()->getConnection());
            $ctrl->index(1);
            break;
    }

} catch (Exception $e) {
    echo "<div class='alert alert-danger'>Lỗi: " . $e->getMessage() . "</div>";
}

$content = ob_get_clean();

// 🔥 KHÔNG LOAD LAYOUT KHI LOGIN
if (($controller ?? '') === 'account' && ($action ?? '') === 'login') {
    echo $content;
} else {
    include 'App/Views/Member/layouts/main.php';
}