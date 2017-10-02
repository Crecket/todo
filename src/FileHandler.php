<?php

namespace Greg\ToDo;

use Greg\ToDo\Exceptions\Http\PageNotFoundException;
use Greg\ToDo\Http\Header;
use Greg\ToDo\Http\Response;
use Greg\ToDo\Models\File;
use Greg\ToDo\ORM\Model;
use Greg\ToDo\Repositories\FileRepository;

class FileHandler
{
    /** @var string */
    private $uploadPath;
    /** @var FileRepository */
    private $fileRepository;

    /**
     * Database constructor.
     * @param string $uploadPath
     * @param FileRepository $fileRepository
     */
    public function __construct(string $uploadPath, FileRepository $fileRepository)
    {
        $this->uploadPath = ROOT.$uploadPath;
        $this->fileRepository = $fileRepository;
    }

    /**
     * @param string $fileKey
     * @return File
     * @throws \Exception
     */
    public function storeFileUpload(string $fileKey): File
    {
        if (empty($_FILES[$fileKey])) {
            return false;
        }
        $fileInfo = $_FILES[$fileKey];

        $fileName = basename($fileInfo["name"]);
        // generate unique hash for this file
        $fileHash = $this->generateHash($fileName);

        $targetPath = $this->uploadPath.$fileHash;

        // attempt to store the file on the server
        if (!move_uploaded_file($fileInfo["tmp_name"], $targetPath)) {
            throw new \Exception("Failed to store uploaded file");
        }

        $file = new File();
        $file->id = $fileHash;
        $file->file_name = $fileName;
        $file->size = $fileInfo['size'];
        $file->type = $fileInfo['type'];

        if ($this->fileRepository->insert($file) === 0) {
            throw new \Exception("Failed to store uploaded file");
        }

        // return the file name
        return $file;
    }

    /**
     * Returns a valid Response object containing the file contents and headers
     * @param string $fileHash
     * @return Response
     * @throws PageNotFoundException
     */
    public function outputFile(string $fileHash): Response
    {
        /** @var File $file */
        $file = $this->fileRepository->find($fileHash);

        if (!$file instanceof File) {
            throw new PageNotFoundException();
        }

        $targetPath = $this->uploadPath.$fileHash;
        if (!file_exists($targetPath)) {
            throw new PageNotFoundException();
        }

        $fileContents = file_get_contents($targetPath);

        return new Response($fileContents, 200, [
            new Header("Content-Type", $file->type),
            new Header("Content-Disposition", "attachment; filename=\"$file->file_name\";"),
        ]);
    }

    /**
     * @param string $string
     * @return string
     */
    private function generateHash(string $string): string
    {
        return hash("sha256", $string.random_int(100, 1000000).time());
    }
}