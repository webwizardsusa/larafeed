<?php

namespace Webwizardsusa\Larafeed\Helpers;

class Utils
{
    public static function mimeFromFileName(string $filename): ?string
    {
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        if (! $extension) {
            return null;
        }

        $mimes = new \Symfony\Component\Mime\MimeTypes();
        $types = $mimes->getMimeTypes($extension);
        if (empty($types)) {
            return null;
        }

        return $types[0];

    }

    public static function formatUserAndEmail(?string $userEmail, ?string $userName): ?string
    {
        if (! $userEmail) {
            return null;
        }

        $final = $userEmail;
        if ($userName) {
            $final = $userName . ' (' . $userEmail . ')';
        }

        return $final;
    }

    public static function isAbsoluteUrl(string $url): bool
    {
        // Validate URL format
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            return false;
        }

        // Check if the URL has a valid scheme (http or https)
        $parsedUrl = parse_url($url);

        return isset($parsedUrl['scheme']) && in_array($parsedUrl['scheme'], ['http', 'https'], true);
    }
}
