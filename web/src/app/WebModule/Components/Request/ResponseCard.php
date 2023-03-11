<?php

namespace Alfred\App\WebModule\Components\Request;

use Alfred\App\Model\Entity\RequestEntity;
use Alfred\App\Model\Entity\ResponseEntity;
use Doctrine\DBAL\Exception as DbalException;
use Nette\Application\UI\Control;
use Nettrine\ORM\EntityManagerDecorator;

/**
 * class ResponseCard
 *
 * @package Alfred\App\WebModule\Components\Request
 */
class ResponseCard extends Control
{
    public function __construct
    (
        private EntityManagerDecorator $em,
        private RequestEntity             $requestEntity,
    ) {

    }

    public function render() : void
    {
        $sep = DIRECTORY_SEPARATOR;

        $this->template->request = $this->requestEntity;
        $responses = $this->em->getRepository(ResponseEntity::class)->findAll();

        $assignedResponses = array_column($this->requestEntity->responses->getValues(), 'id');

        $this->template->responses = $responses;
        $this->template->assignedResponses = $assignedResponses;

        $this->template->setFile(__DIR__ . $sep . 'ResponseCard.latte');
        $this->template->render();
    }

    public function handleAdd(int $requestId, int $responseId) : void
    {
        if (!$this->presenter->isAjax()) {
            $this->presenter->redirect('Request:edit', $requestId);
        }

        /**
         * @var RequestEntity $request
         */
        $request = $this->em->getRepository(RequestEntity::class)->find($requestId);

        if (!$request) {
            $this->flashMessage('Požadavek nenalezen.', 'danger');
        }

        $response = $this->em->getRepository(ResponseEntity::class)->find($responseId);

        /**
         * @var ResponseEntity $response
         */
        if (!$response) {
            $this->flashMessage('Odpověď nenalezena.', 'danger');
        }

        try {
            $request->addResponse($response);

            $this->em->persist($request);
            $this->em->flush();

            $this->flashMessage('Odpověď byla přidána.', 'success');
        } catch (DbalException) {
            $this->flashMessage('Odpověď se nepodařilo přidat.', 'danger');
        }

        $this->redrawControl('responses');
        $this->redrawControl('flashes');
    }

    public function handleDelete(int $requestId, int $responseId) : void
    {
        if (!$this->presenter->isAjax()) {
            $this->presenter->redirect('Request:edit', $requestId);
        }

        /**
         * @var RequestEntity $request
         */
        $request = $this->em->getRepository(RequestEntity::class)->find($requestId);

        if (!$request) {
            $this->flashMessage('Požadavek nenalezen.', 'danger');
        }

        /**
         * @var ResponseEntity $response
         */
        $response = $this->em->getRepository(ResponseEntity::class)->find($responseId);

        if (!$response) {
            $this->flashMessage('Odpověď nenalezena.', 'danger');
        }

        try {
            $request->removeResponse($response);

            $this->em->persist($request);
            $this->em->flush();

            $this->flashMessage('Odpověď byla smazána.', 'success');
        } catch (DbalException $e) {
            $this->flashMessage('Odpověď se nepodařilo smazat.', 'danger');
        }

        $this->redrawControl('responses');
        $this->redrawControl('flashes');
    }
}
