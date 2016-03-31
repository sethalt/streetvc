<?php
namespace Sd\BaseBundle\Form\Transformer;

use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\MongoDB\GridFSFile;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class GridFSUploadTypeTransformer implements DataTransformerInterface
{
    private $dm;
    private $upload;

    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
    }

    public function transform($value)
    {
        return null;
    }

	public function reverseTransform($value) {
        if (!$value instanceof UploadedFile) {
            return null;
        }

        if (null !== $this->upload) {
            $this->dm->remove($this->upload);
            $this->dm->flush();
        }

        return $value;
	}

}