<?php

namespace App\Services;

/** Server-side allow-list for editor HTML. The editor is a convenience, not a trust boundary. */
class ArticleContentSanitizer
{
    private const TAGS = ['p','br','h2','h3','h4','strong','b','em','i','u','s','ul','ol','li','blockquote','a','img','figure','figcaption','hr','table','thead','tbody','tr','th','td','span','div'];

    public function sanitize(string $html): string
    {
        $html = mb_substr($html, 0, 500000, 'UTF-8');
        if (!class_exists(\DOMDocument::class)) {
            $html = preg_replace('~<(script|style|iframe|object|embed|form)[^>]*>.*?</\\1\\s*>~is', '', $html) ?? '';
            $html = strip_tags($html, '<p><br><h2><h3><h4><strong><b><em><i><u><s><ul><ol><li><blockquote><a><img><figure><figcaption><hr><table><thead><tbody><tr><th><td><span><div>');
            return preg_replace('/\\s+on[a-z]+\\s*=\\s*("[^"]*"|\'[^\']*\'|[^\\s>]+)/i', '', $html) ?? '';
        }

        $doc = new \DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="utf-8" ?><div id="article-root">' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);
        $root = $doc->getElementById('article-root');
        if (!$root) return '';

        $walk = function (\DOMNode $node) use (&$walk): void {
            for ($child = $node->firstChild; $child; ) {
                $next = $child->nextSibling;
                if ($child instanceof \DOMElement) {
                    $tag = strtolower($child->tagName);
                    if (!in_array($tag, self::TAGS, true)) {
                        if (in_array($tag, ['script','style','iframe','object','embed','form','svg','math'], true)) {
                            $node->removeChild($child);
                        } else {
                            while ($child->firstChild) $node->insertBefore($child->firstChild, $child);
                            $node->removeChild($child);
                        }
                    } else {
                        $allowed = $tag === 'a' ? ['href','title','target'] : ($tag === 'img' ? ['src','alt','title','width','height'] : []);
                        foreach (iterator_to_array($child->attributes) as $attr) {
                            $name = strtolower($attr->name);
                            if (!in_array($name, $allowed, true)) { $child->removeAttribute($name); continue; }
                            if (in_array($name, ['href','src'], true) && !$this->safeUrl($attr->value, $tag === 'img')) $child->removeAttribute($name);
                        }
                        if ($tag === 'a' && strtolower($child->getAttribute('target')) === '_blank') $child->setAttribute('rel', 'noopener noreferrer');
                        $walk($child);
                    }
                }
                $child = $next;
            }
        };
        $walk($root);
        $result = '';
        foreach ($root->childNodes as $child) $result .= $doc->saveHTML($child);
        return $result;
    }

    private function safeUrl(string $url, bool $image): bool
    {
        $url = trim(html_entity_decode($url, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        if ($url === '' || preg_match('/[\\x00-\\x20]/', $url)) return false;
        if (str_starts_with($url, '/') && !str_starts_with($url, '//')) return true;
        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
        return $image ? in_array($scheme, ['https'], true) : in_array($scheme, ['https','http','mailto'], true);
    }
}
