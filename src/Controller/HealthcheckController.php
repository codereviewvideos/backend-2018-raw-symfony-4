<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HealthcheckController extends Controller
{
    /**
     * @Route("/ping", name="healthcheck")
     */
    public function index()
    {
        return new JsonResponse('pong');
    }
}
