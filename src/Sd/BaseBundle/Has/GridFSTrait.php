<?php
namespace Sd\BaseBundle\Has;

use Symfony\Component\HttpFoundation\File\UploadedFile;
trait GridFSTrait
{
    /** @MongoDB\Id */
    private $id;

    /** @MongoDB\String */
    private $filename;

    /** @MongoDB\File */
    private $file;

    /** @MongoDB\String */
    private $contentType;

    /** @MongoDB\Date */
    private $uploadDate;

    /** @MongoDB\Int */
    private $length;

    /** @MongoDB\Int */
    private $chunkSize;

    /** @MongoDB\String */
    private $md5;

    public function setUploadedFile(UploadedFile $upload)
    {
        if (!$upload->isValid()) {
            throw new \InvalidArgumentException("invalid upload");
        }
        $this->setContentType($upload->getClientMimeType());
        $this->setFile($upload->getPathname());
        $this->setFilename($upload->getClientOriginalName());
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \MongoGridFSFile
     */
    public function getFile()
    {
        return $this->file;
    }

    public function setFile($file)
    {
        $this->file = $file;
        return $this;
    }

    public function setFilename($filename)
    {
        $this->filename = $filename;
        return $this;
    }

    public function getFilename()
    {
        return $this->filename;
    }

    public function getContentType()
    {
        return $this->contentType;
    }

    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
    }

    public function getChunkSize()
    {
        return $this->chunkSize;
    }

    public function getLength()
    {
        return $this->length;
    }

    public function getMd5()
    {
        return $this->md5;
    }

    public function getUploadDate()
    {
        return $this->uploadDate;
    }

    public function getBytes()
    {
        return $this->getFile()->getBytes();
    }

    public function getStream()
    {
        return $this->getFile()->getMongoGridFSFile()->getResource();
    }
}
