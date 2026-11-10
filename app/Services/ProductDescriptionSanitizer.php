<?php

namespace App\Services;

/** Normalizes legacy HTML-escaped product copy and renders only a small safe markup set. */
final class ProductDescriptionSanitizer
{
    private const ALLOWED_TAGS = ['p', 'br', 'ul', 'ol', 'li', 'strong', 'b', 'em', 'i', 'u', 'h2', 'h3', 'h4', 'blockquote', 'a', 'div', 'span'];
    private const DROP_WITH_CONTENT = ['script', 'style', 'iframe', 'object', 'embed', 'svg', 'math'];

    public function sanitizeHtml(string $content): string
    {
        $content = $this->decodeEscapedMarkup($content);
        if (!class_exists(\DOMDocument::class)) {
            $plain = strip_tags($content);
            return nl2br(esc(html_entity_decode($plain, ENT_QUOTES | ENT_HTML5, 'UTF-8')));
        }

        $document = new \DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        $document->loadHTML('<?xml encoding="utf-8" ?><div id="ilena-product-copy-root">' . $content . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);
        $root = $document->getElementById('ilena-product-copy-root');
        if (!$root) {
            return nl2br(esc(html_entity_decode(strip_tags($content), ENT_QUOTES | ENT_HTML5, 'UTF-8')));
        }

        $render = function (\DOMNode $node) use (&$render): string {
            if ($node instanceof \DOMText) {
                return esc($node->nodeValue ?? '');
            }
            if (!$node instanceof \DOMElement) {
                return '';
            }

            $tag = strtolower($node->tagName);
            if (in_array($tag, self::DROP_WITH_CONTENT, true)) {
                return '';
            }

            $children = '';
            foreach ($node->childNodes as $child) {
                $children .= $render($child);
            }
            if (!in_array($tag, self::ALLOWED_TAGS, true)) {
                return $children;
            }
            if ($tag === 'br') {
                return '<br>';
            }

            $attributes = '';
            if ($tag === 'a') {
                $href = trim($node->getAttribute('href'));
                if ($href !== '' && preg_match('/^(https?:\/\/|mailto:|tel:|\/|#)/i', $href)) {
                    $attributes = ' href="' . esc($href, 'attr') . '" rel="noopener noreferrer"';
                }
            }
            return '<' . $tag . $attributes . '>' . $children . '</' . $tag . '>';
        };

        $output = '';
        foreach ($root->childNodes as $child) {
            $output .= $render($child);
        }
        return $output;
    }

    public function plainText(string $content): string
    {
        $content = $this->decodeEscapedMarkup($content);
        $content = preg_replace('~<\s*br\s*/?\s*>|</\s*(?:p|div|li|h[2-4]|blockquote)\s*>~i', "\n", $content) ?? $content;
        $text = html_entity_decode(strip_tags($content), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/[\t ]+/u', ' ', $text) ?? $text;
        $text = preg_replace('/\n[\t ]*/u', "\n", $text) ?? $text;
        return trim($text);
    }

    private function decodeEscapedMarkup(string $content): string
    {
        // Some older product rows stored editor markup as HTML entities. Decode only
        // when a complete encoded tag is present, and cap passes to avoid over-decoding.
        for ($pass = 0; $pass < 3 && preg_match('/&(?:amp;)*lt;\/?[a-z][\s\S]{0,500}?&(?:amp;)*gt;/i', $content); $pass++) {
            $decoded = html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            if ($decoded === $content) {
                break;
            }
            $content = $decoded;
        }
        return $content;
    }
}
