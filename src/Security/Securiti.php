<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Securiti
{
    private $security;
    private $urlGenerator;
    public function __construct(Security $security, UrlGeneratorInterface $urlGenerator)
    {
        $this->security = $security;
        $this->urlGenerator = $urlGenerator;


    }

    public function controlDroit(User $user)
    {
        $userConnect = $this->security->getUser();
        if ($userConnect != $user)
        {
            return new RedirectResponse($this->urlGenerator->generate('default_home'));
        }
    }
}