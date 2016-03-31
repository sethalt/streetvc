<?php
/**
 * Project: streetvc
 * User: dao
 * Date: 8/19/14
 * Time: 12:53 PM
 */

namespace StreetVC\BaseBundle\Controller;
use FOS\RestBundle\Controller\Annotations as FOS;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use StreetVC\BaseBundle\Document\Document;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Class DocumentsController
 * @FOS\RouteResource("Document")
 * @package StreetVC\BaseBundle\Controller
 */
class DocumentsController extends BaseController {

    /**
     * @param ParamFetcher $paramFetcher
     * @return array|View
     */
    public function cgetAction(ParamFetcher $paramFetcher)
    {
        $documents = $this->getRepository('StreetVC:Document')->findAll();
        return ['documents' => $documents ];
    }

    /**
     * @FOS\View()
     * @param Document $document
     * @return Document
     */
    public function getAction(Document $document)
    {
        return $document;
    }

    /**
     * @FOS\Get
     * @param Document $document
     * @return Response
     */
    public function downloadAction(Document $document)
    {
        return Response::create($document->getBytes(), 200, ['Content-Type'=>$document->getContentType()]);
    }

    /**
     * @FOS\Get
     * @param Document $document
     * @return StreamedResponse
     */
    public function streamAction(Document $document)
    {
        $stream = $document->getStream();
        $getBytes = function() use ($stream) { fpassthru($stream); };
        $headers = ['Content-Type'=>$document->getContentType()];
        return StreamedResponse::create($getBytes, 200, $headers);
    }

    /**
     * @FOS\Get
     * @return array
     */
    public function newAction()
    {
        $filename = 'sampleagreement.pdf';
        $filepath = '/mnt/projects/streetvc/web/'.$filename;
        if (!$document = $this->findByName($filename)) {
            $document = new Document();
            $document->setContentType('application/pdf');
            $document->setFile($filepath);
            $document->setFilename($filename);
//            $document->setUploadedFile($file);
            $this->get('odm')->persist($document);
            $this->get('odm')->flush();
        }
        return ['document' => $document ];
    }

    protected function findByName($name)
    {
        $document = $this->getRepository('StreetVC:Document')->findOneBy(['filename' => $name]);
        return $document;
    }

}
