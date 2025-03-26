<?php

function checkTitleMeta($title, $description)
{
    $titleLength = strlen($title);
    $descLength = strlen($description);

    return [
        'title_length' => $titleLength,
        'description_length' => $descLength,
        'title_status' => ($titleLength < 50) ? 'Kurang Optimal' : (($titleLength > 60) ? 'Berlebihan' : 'Optimal'),
        'description_status' => ($descLength < 120) ? 'Kurang Optimal' : (($descLength > 160) ? 'Berlebihan' : 'Optimal'),
    ];
}

function checkKeywordDensity($content, $keywords)
{
    $textContent = strip_tags($content);
    $totalWords = str_word_count($textContent);
    $keywordArray = array_map('trim', explode(',', strtolower($keywords)));

    $keywordCounts = [];
    $keywordPercentages = [];
    $totalKeywordCount = 0;

    foreach ($keywordArray as $keyword) {
        $count = substr_count(strtolower($textContent), $keyword);
        $keywordCounts[$keyword] = $count;
        $totalKeywordCount += $count;
    }

    foreach ($keywordCounts as $keyword => $count) {
        $keywordPercentages[$keyword] = ($totalWords > 0) ? round(($count / $totalWords) * 100, 2) : 0;
    }

    $totalDensity = ($totalWords > 0) ? ($totalKeywordCount / $totalWords) * 100 : 0;
    $status = ($totalDensity < 1) ? 'Kurang Optimal' : (($totalDensity > 3) ? 'Berlebihan' : 'Optimal');

    return [
        'total_words' => $totalWords,
        'keyword_counts' => $keywordCounts,
        'keyword_percentages' => $keywordPercentages,
        'total_density' => round($totalDensity, 2),
        'status' => $status,
    ];
}

function checkImageAltTags($content)
{
    preg_match_all('/<img[^>]+>/i', $content, $imgTags);
    preg_match_all('/<img[^>]+alt=["\'][^"\']+["\']/i', $content, $imagesWithAlt);

    $totalImages = count($imgTags[0]);
    $altImages = count($imagesWithAlt[0]);
    $status = ($totalImages === 0) ? 'Kurang Optimal' : (($altImages === $totalImages) ? 'Optimal' : 'Berlebihan');

    return [
        'total_images' => $totalImages,
        'images_with_alt' => $altImages,
        'status' => $status,
    ];
}


function checkInternalExternalLinks($content, $baseUrl)
{
    preg_match_all('/<a[^>]+href=["\'](.*?)["\']/i', $content, $matches);
    $links = $matches[1];
    $internalLinks = 0;
    $externalLinks = 0;

    foreach ($links as $link) {
        if (strpos($link, $baseUrl) !== false || strpos($link, '/') === 0) {
            $internalLinks++;
        } else {
            $externalLinks++;
        }
    }

    $totalLinks = count($links);
    $status = ($totalLinks == 0) ? 'Kurang Optimal' : (($internalLinks > 0 && $externalLinks > 0) ? 'Optimal' : 'Berlebihan');

    return [
        'total_links' => $totalLinks,
        'internal_links' => $internalLinks,
        'external_links' => $externalLinks,
        'status' => $status,
    ];
}

function checkHeadingStructure($content)
{
    preg_match_all('/<h([1-6])>(.*?)<\/h[1-6]>/i', $content, $matches);
    $headings = array_count_values($matches[1]);

    $h1Count = isset($headings['1']) ? $headings['1'] : 0;
    $status = ($h1Count === 0) ? 'Kurang Optimal' : (($h1Count > 1) ? 'Berlebihan' : 'Optimal');

    return [
        'headings' => $headings,
        'status' => $status,
    ];
}

function readabilityScore($content)
{
    $textContent = strip_tags($content); // Hilangkan tag HTML
    $sentences = preg_split('/[.!?]/', $textContent);
    $words = str_word_count($textContent);
    $syllables = preg_match_all('/[aeiouy]{1,2}/i', $textContent);

    $fleschScore = 206.835 - (1.015 * ($words / max(1, count($sentences)))) - (84.6 * ($syllables / max(1, $words)));

    return [
        'score' => round($fleschScore, 2),
        'status' => ($fleschScore >= 60) ? 'Mudah Dibaca' : 'Sulit Dibaca',
    ];
}
