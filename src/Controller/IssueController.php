<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\PaginationRequest;
use App\Entity\Issue;
use App\Form\Type\CreateIssueType;
use App\Form\Type\UpdateIssueStatusType;
use App\Issue\Command\CreateIssueCommand;
use App\Issue\Command\Handler\IssueCommandHandler;
use App\Issue\Command\RemoveIssueCommand;
use App\Issue\Command\UpdateIssueCommand;
use App\Issue\Query\Collection\GetPaginatedRecords;
use App\Issue\Requester\IssuesPaginatorRequesterInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/issues', name: 'app.issues.')]
final class IssueController extends AbstractController
{
    public function __construct(private IssueCommandHandler $handler)
    {
    }

    #[Route(path: '/{id}/view', name: 'show', methods: ['GET', 'POST'], requirements: ['id' => Requirement::POSITIVE_INT])]
    public function view(Issue $issue): Response
    {
        return $this->render('issue/view.html.twig', [
            'issue' => $issue,
        ]);
    }

    #[Route(path: '/new', name: 'add')]
    public function add(Request $request, CreateIssueCommand $command): Response
    {
        $issue = new Issue();

        $form = $this->createForm(CreateIssueType::class, $issue);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $isSuccess = $this->handler->handle($command, $issue);
            if (true === $isSuccess) {
                $this->addFlash('success', 'Issue created.');
            }

            return $this->redirectToRoute('app.issues.show', ['id' => $issue->getId()]);
        }

        return $this->render('issue/create.html.twig', [
            'form' => $form,
            'edit' => false,
        ]);
    }

    #[Route(path: '/{id}/edit', name: 'edit', requirements: ['id' => Requirement::POSITIVE_INT])]
    #[IsGranted(subject: 'issue', attribute: 'CAN_EDIT_ISSUE', message: 'You are not allowed to perform this action.')]
    public function edit(Issue $issue, Request $request, UpdateIssueCommand $command): Response
    {
        $form = $this->createForm(UpdateIssueStatusType::class, $issue);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $isSuccess = $this->handler->handle($command, $issue);
            if (true === $isSuccess) {
                $this->addFlash('success', 'Issue updated.');
            }

            return $this->redirectToRoute('app.issues.edit', ['id' => $issue->getId()]);
        }

        return $this->render('issue/create.html.twig', [
            'form' => $form,
            'edit' => true,
            'issue' => $issue,
        ]);
    }

    #[Route(path: '/{id}/remove', name: 'delete', requirements: ['id' => Requirement::POSITIVE_INT], methods: ['POST', 'GET'])]
    #[IsGranted(subject: 'issue', attribute: 'CAN_DELETE_ISSUE', message: 'You are not allowed to perform this action.')]
    public function delete(Issue $issue, Request $request, RemoveIssueCommand $command): Response
    {
        if ($request->isMethod(Request::METHOD_POST)) {
            $csrfToken = $request->request->get('_delete_token');

            if (!$this->isCsrfTokenValid(id: '__delete_'.$issue->getId(), token: $csrfToken)) {
                $this->addFlash('danger', 'Issue not removed.');
            } else {
                $this->handler->handle($command, $issue);

                $this->addFlash('success', 'Issue removed.');
            }

            return $this->redirectToRoute('app.issues.all');
        }

        return $this->render('issue/delete.html.twig', [
            'issue' => $issue,
        ]);
    }

    #[Route(path: '/all', name: 'all')]
    public function list(#[MapQueryString] PaginationRequest $paginationRequest, IssuesPaginatorRequesterInterface $issuesRequester): Response
    {
        $issues = $issuesRequester->getRecords(new GetPaginatedRecords($paginationRequest));

        return $this->render('issue/index.html.twig', [
            'pagination' => $issues,
        ]);
    }
}
