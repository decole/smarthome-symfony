<?php

namespace App\Application\Service\SitePage;

final class ApiUriTranscribeService
{
    public function transcribeUri(string $uri): array
    {
        if ($uri === '') {
            return [];
        }

        $elements = explode(',', $uri);

        if (!is_array($elements)) {
            $elements = [$uri];
        }

        return $elements;
    }
}