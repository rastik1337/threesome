<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ImageRepo;
use App\Services\ImageService;
use Exception;

class GalleryController extends Controller
{
    public function handleUpload(): void
    {
        $this->requireAuth();

        if (empty($_FILES) && empty($_POST) && isset($_SERVER['CONTENT_LENGTH']) && (int) $_SERVER['CONTENT_LENGTH'] > 0) {
            $this->setNotification('error', 'Nahraný soubor je příliš velký (překročil limit serveru).');
            $this->redirect('/');
        }

        $file = $_FILES['photo'] ?? $_FILES['image'] ?? null;
        if (!$file || !isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
            $this->setNotification('error', 'Vyberte fotografii k nahrání.');
            $this->redirect('/');
        }

        if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE) {
            $this->setNotification('error', 'Fotografie je příliš velká (překročila limit velikosti).');
            $this->redirect('/');
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->setNotification('error', 'Chyba při nahrávání souboru na server (kód chyby: ' . $file['error'] . ').');
            $this->redirect('/');
        }

        $description = trim($_POST['description'] ?? '');

        try {
            $path = ImageService::validateAndConvert($file, 'photos');
            ImageRepo::create(
                userId: $_SESSION['user']['id'],
                filePath: $path,
                description: $description !== '' ? $description : null
            );
            $this->setNotification('success', 'Fotografie byla úspěšně nahrána.');
        } catch (Exception $e) {
            $this->setNotification('error', $e->getMessage());
        }

        $this->redirect('/');
    }

    public function delete(): void
    {
        $this->requireAuth();

        $imageId = intval($_POST['image_id'] ?? 0);
        if ($imageId <= 0) {
            $this->redirect('/');
        }

        $image = ImageRepo::getById($imageId);
        if (!$image) {
            $this->setNotification('error', 'Fotografie nebyla nalezena.');
            $this->redirect('/');
        }

        $isOwner = $image->user_id === ($_SESSION['user']['id'] ?? null);
        $role = strtolower((string) ($_SESSION['user']['role'] ?? ''));
        $isAdmin = $role === 'admin';

        if (!$isOwner && !$isAdmin) {
            $this->setNotification('error', 'Nemáte oprávnění ke smazání této fotografie.');
            $this->redirect('/');
        }

        ImageService::deleteFile($image->file_path);
        ImageRepo::delete($imageId);

        $this->setNotification('success', 'Fotografie byla smazána.');
        $this->redirect('/');
    }
}
