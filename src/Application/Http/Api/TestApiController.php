<?php

namespace App\Application\Http\Api;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestApiController
{
    #[Route('/')]
    public function number(): Response
    {
        $number = random_int(0, 100);

        return new Response(
            '<html><body>Api: '.$number.'</body></html>'
        );
    }
}