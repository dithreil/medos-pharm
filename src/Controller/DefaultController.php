<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route(
     *     path="/{vueRouting}",
     *     requirements={"vueRouting"="^(?!(api|admin|download)|_(profiler|wdt)).*"},
     *     defaults={"vueRouting"=""},
     *     methods={"GET"},
     *     name="app_front_default_index"
     * )
     * @param Request $request
     * @param string $vueRouting
     * @return Response
     */
    public function index(Request $request, string $vueRouting): Response
    {
        return $this->render('front/index.html.twig');
    }
}
