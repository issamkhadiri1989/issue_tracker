<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route('/', name: 'app.default')]
final class DefaultController extends AbstractController
{
    public function __construct(private Security $security)
    {
    }

    public function __invoke(AuthenticationUtils $utils): Response
    {
        if ($this->security->isGranted('IS_AUTHENTICATED')) {
            return $this->redirectToRoute('app.issues.all');
        }

        $lastError = $utils->getLastAuthenticationError();
        $lastUsername = $utils->getLastUsername();

        return $this->render('default/index.html.twig', [
            'last_error' => $lastError,
            'last_username' => $lastUsername,
        ]);
    }
}
