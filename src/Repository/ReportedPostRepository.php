<?php

namespace AGuardia\Repository;

class ReportedPostRepository
{
    private $filePath;

    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }

    public function get()
    {
        if(!file_exists($this->filePath)) {
            throw new \Exception('Posts file not found.');
        }

        return json_decode(file_get_contents($this->filePath), true);
    }

    public function save($postIds)
    {
        try {
            file_put_contents($this->filePath, json_encode($postIds));
        } catch(\Exception $e) {
            throw new \Exception('Error while writing on posts file.', 0, $e);
        }
    }
}
