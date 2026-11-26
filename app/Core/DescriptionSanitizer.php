<?php

namespace App\Core;

class DescriptionSanitizer
{
    private const ALLOWED_TAGS = [
        'p', 'div', 'br', 'strong', 'b', 'em', 'i', 'u', 'ul', 'ol', 'li', 'h2', 'h3', 'blockquote',
    ];

    private const REMOVED_TAGS = [
        'script', 'style', 'iframe', 'object', 'embed', 'svg', 'math', 'template',
    ];

    public static function sanitize(string $content): string
    {
        $content = trim($content);
        if ($content === '') {
            return '';
        }

        if (strip_tags($content) === $content) {
            $paragraphs = preg_split('/\R{2,}/u', $content) ?: [$content];
            $html = '';
            foreach ($paragraphs as $paragraph) {
                $paragraph = trim($paragraph);
                if ($paragraph !== '') {
                    $html .= '<p>' . nl2br(htmlspecialchars($paragraph, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'), false) . '</p>';
                }
            }
            return $html;
        }

        if (!class_exists(\DOMDocument::class)) {
            return nl2br(htmlspecialchars(strip_tags($content), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'), false);
        }

        $document = new \DOMDocument('1.0', 'UTF-8');
        $previousErrorMode = libxml_use_internal_errors(true);
        try {
            $loaded = $document->loadHTML(
                '<?xml encoding="UTF-8"><div id="product-description-root">' . $content . '</div>',
                LIBXML_HTML_NODEFDTD | LIBXML_NONET
            );
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previousErrorMode);
        }

        if (!$loaded) {
            return nl2br(htmlspecialchars(strip_tags($content), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'), false);
        }

        $root = (new \DOMXPath($document))->query('//*[@id="product-description-root"]')->item(0);
        if (!$root) {
            return '';
        }

        self::cleanChildren($root);
        $html = '';
        foreach ($root->childNodes as $child) {
            $html .= $document->saveHTML($child);
        }

        return $html;
    }

    private static function cleanChildren(\DOMNode $parent): void
    {
        foreach (iterator_to_array($parent->childNodes) as $child) {
            if ($child instanceof \DOMComment) {
                $parent->removeChild($child);
                continue;
            }
            if (!$child instanceof \DOMElement) {
                continue;
            }

            $tag = strtolower($child->tagName);
            if (in_array($tag, self::REMOVED_TAGS, true)) {
                $parent->removeChild($child);
                continue;
            }

            self::cleanChildren($child);
            if (!in_array($tag, self::ALLOWED_TAGS, true)) {
                while ($child->firstChild) {
                    $parent->insertBefore($child->firstChild, $child);
                }
                $parent->removeChild($child);
                continue;
            }

            while ($child->attributes->length > 0) {
                $child->removeAttributeNode($child->attributes->item(0));
            }
        }
    }
}
