<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Log;

class KeywordHarvesterService
{
    /**
     * Common English & Hindi filler words (Stopwords) that waste Amazon's 250-byte limit
     */
    protected array $stopwords = [
        'and', 'or', 'the', 'with', 'in', 'of', 'for', 'to', 'a', 'an', 'at', 'by', 'from',
        'is', 'on', 'this', 'that', 'these', 'those', 'as', 'it', 'its', 'be', 'are', 'was',
        'best', 'good', 'top', 'new', 'latest', 'brand', 'quality', 'great', 'high', 'super',
        'amazon', 'meesho', 'flipkart', 'buy', 'online', 'free', 'shipping', 'deal', 'offer',
        'price', 'cheap', 'cost', 'discount', 'ka', 'ki', 'ke', 'ko', 'me', 'se', 'par'
    ];

    /**
     * Morphological Plural-to-Singular Stemming Dictionary for Plumbing, Hardware & E-Commerce
     */
    protected array $stemmingMap = [
        'taps' => 'tap',
        'valves' => 'valve',
        'faucets' => 'faucet',
        'pipes' => 'pipe',
        'fittings' => 'fitting',
        'mixers' => 'mixer',
        'nozzles' => 'nozzle',
        'connectors' => 'connector',
        'couplers' => 'coupler',
        'elbows' => 'elbow',
        'tees' => 'tee',
        'unions' => 'union',
        'reducers' => 'reducer',
        'joints' => 'joint',
        'bathrooms' => 'bathroom',
        'kitchens' => 'kitchen',
        'sinks' => 'sink',
        'basins' => 'basin',
        'flanges' => 'flange',
        'threads' => 'thread',
        'handles' => 'handle',
        'switches' => 'switch',
        'plumbers' => 'plumber',
        'accessories' => 'accessory',
        'washers' => 'washer',
        'gaskets' => 'gasket',
        'totis' => 'toti',
        'totiya' => 'toti',
        'totiyan' => 'toti',
        'nals' => 'nal',
        'nalo' => 'nal',
        'tubings' => 'tubing',
        'hoses' => 'hose',
    ];

    /**
     * High-volume buyer search terms & Hinglish colloquialisms categorized by product intent
     */
    protected array $buyerSearchDictionary = [
        'faucet' => [
            'tap', 'toti', 'nal', 'faucet', 'bib', 'cock', 'basin', 'sink', 'wall', 'flange',
            '15mm', '1/2', 'ptmt', 'brass', 'bathroom', 'mixer', 'angle', 'pillar', 'long',
            'body', 'handle', 'leakproof', 'water', 'thread', 'totiya', 'wash', 'unthreaded'
        ],
        'valve' => [
            'valve', 'ball', 'tanki', 'stop', 'switch', 'upvc', 'cpvc', 'pipe', 'tank', 'water',
            '1inch', '25mm', '32mm', '50mm', 'handle', 'plumber', 'joint', 'pipeline', 'pressure',
            'control', 'brass', 'gate', 'unthreaded'
        ],
        'fitting' => [
            'fitting', 'elbow', 'tee', 'coupler', 'union', 'reducer', 'mta', 'fta', 'socket',
            'bend', 'solvent', 'cement', 'joint', 'upvc', 'cpvc', 'tank', 'connector', 'end',
            'cap', 'plumbing', 'pipeline', 'extension', 'nipple'
        ],
        'pipe' => [
            'pipe', 'hose', 'water', 'car', 'washing', 'garden', 'kisan', 'braided', '3layer',
            'flexible', 'nozzle', 'tubing', 'reel', 'outdoor', 'lawn', 'heavy', 'duty', 'cleaning'
        ],
        'general' => [
            'hardware', 'plumbing', 'home', 'improvement', 'replacement', 'sanitary', 'fittings',
            'leakproof', 'durability', 'commercial', 'grade', 'tool'
        ]
    ];

    /**
     * Core Engine: Harvest, Merge with AI, Stem, Deduplicate & Pack into <= 248 Bytes
     */
    public function harvestAndOptimizeKeywords(Product $product, ?string $aiGeneratedKeywords = null): array
    {
        $existingKeywords = $product->generic_keywords ?: '';
        $productName = $product->name ?: '';
        $categoryName = $product->category->name ?? '';
        $desc = $product->description ?: '';

        // 1. Gather Candidate Tokens from all sources
        $rawPool = [];

        // Source A: AI-Generated Keywords (if any)
        if (!empty($aiGeneratedKeywords)) {
            $rawPool[] = $aiGeneratedKeywords;
        }

        // Source B: Existing Product Keywords (if any)
        if (!empty($existingKeywords)) {
            $rawPool[] = $existingKeywords;
        }

        // Source C: High-Volume Search Terms mined for this Product Category & Name
        $minedTerms = $this->mineHighVolumeBuyerQueries($productName, $categoryName, $desc);
        $rawPool[] = implode(' ', $minedTerms);

        // 2. Tokenize & Clean into raw word list
        $combinedText = implode(' ', $rawPool);
        // Replace punctuation, slashes, dashes, commas with single space
        $cleanedText = preg_replace('/[^a-zA-Z0-9\/\-]/', ' ', strtolower($combinedText));
        $tokens = preg_split('/\s+/', $cleanedText, -1, PREG_SPLIT_NO_EMPTY);

        // 3. Normalize, Stem & Filter Stopwords
        $uniqueStemmedTokens = [];
        $tokenScores = [];

        foreach ($tokens as $t) {
            $cleanToken = trim($t, "-/ \t\n\r\0\x0B");

            // Ignore 1-letter words (unless '1' or '2')
            if (strlen($cleanToken) < 2 && !in_array($cleanToken, ['1', '2', '4', '6'])) {
                continue;
            }

            // Filter stopwords
            if (in_array($cleanToken, $this->stopwords)) {
                continue;
            }

            // Morphological Stemming: convert plurals/variations to root keyword
            $rootToken = $this->stemmingMap[$cleanToken] ?? $cleanToken;

            // Score token by relevance:
            // High search intent words get higher weight
            $score = 10;
            if (in_array($rootToken, ['tap', 'toti', 'nal', 'faucet', 'valve', 'pipe', 'fitting', 'basin', 'sink', 'tanki'])) {
                $score += 50;
            }
            if (in_array($rootToken, ['cpvc', 'upvc', 'ptmt', 'brass', '15mm', '1/2', '25mm', '1inch', 'flange', 'mixer'])) {
                $score += 30;
            }
            // Reward brevity: shorter high-impact words allow more keywords in 250 bytes
            $score += max(0, 15 - strlen($rootToken));

            if (!isset($uniqueStemmedTokens[$rootToken])) {
                $uniqueStemmedTokens[$rootToken] = $rootToken;
                $tokenScores[$rootToken] = $score;
            } else {
                // If repeated across AI + Harvester, boost confidence score
                $tokenScores[$rootToken] += 15;
            }
        }

        // 4. Sort tokens by Score (Highest Search-Volume & Relevance first)
        arsort($tokenScores);

        // 5. Strict Amazon 245-248 Byte Greedy Packing
        $finalKeywordsList = [];
        $currentByteLength = 0;
        $maxBytes = 248; // Safe ceiling below Amazon's 250-byte cliff

        foreach ($tokenScores as $word => $sc) {
            $candidateWord = $uniqueStemmedTokens[$word];
            $wordBytes = strlen($candidateWord);

            // Calculate needed bytes (word bytes + 1 space if not first word)
            $additionalBytes = (count($finalKeywordsList) === 0) ? $wordBytes : ($wordBytes + 1);

            if (($currentByteLength + $additionalBytes) <= $maxBytes) {
                $finalKeywordsList[] = $candidateWord;
                $currentByteLength += $additionalBytes;
            }
        }

        $optimizedString = implode(' ', $finalKeywordsList);
        $finalBytes = strlen($optimizedString);

        return [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'keywords' => $optimizedString,
            'byte_length' => $finalBytes,
            'words_count' => count($finalKeywordsList),
            'unique_words' => $finalKeywordsList,
            'status' => 'optimized_compliant',
            'safe_margin_bytes' => (250 - $finalBytes)
        ];
    }

    /**
     * Mine High-Volume Buyer Search Queries for specific product type
     */
    protected function mineHighVolumeBuyerQueries(string $name, string $category, string $desc): array
    {
        $haystack = strtolower("{$name} {$category} {$desc}");
        $results = [];

        if (str_contains($haystack, 'cock') || str_contains($haystack, 'tap') || str_contains($haystack, 'faucet') || str_contains($haystack, 'ptmt')) {
            $results = array_merge($results, $this->buyerSearchDictionary['faucet']);
        }

        if (str_contains($haystack, 'valve') || str_contains($haystack, 'tank') || str_contains($haystack, 'ball')) {
            $results = array_merge($results, $this->buyerSearchDictionary['valve']);
        }

        if (str_contains($haystack, 'fitting') || str_contains($haystack, 'elbow') || str_contains($haystack, 'tee') || str_contains($haystack, 'union') || str_contains($haystack, 'reducer')) {
            $results = array_merge($results, $this->buyerSearchDictionary['fitting']);
        }

        if (str_contains($haystack, 'pipe') || str_contains($haystack, 'hose') || str_contains($haystack, 'garden') || str_contains($haystack, 'braided')) {
            $results = array_merge($results, $this->buyerSearchDictionary['pipe']);
        }

        $results = array_merge($results, $this->buyerSearchDictionary['general']);

        // Extract dimension numbers like 15mm, 1/2, 20mm, 25mm, 1inch
        if (preg_match_all('/(\d+\/?\d*\s*(?:mm|inch|in|\"))/i', $name, $matches)) {
            foreach ($matches[0] as $m) {
                $results[] = strtolower(str_replace(' ', '', $m));
            }
        }

        return $results;
    }

    /**
     * Harvest and automatically save directly into Product Database
     */
    public function harvestAndSaveProduct(Product $product, ?string $aiKeywords = null): array
    {
        $result = $this->harvestAndOptimizeKeywords($product, $aiKeywords);

        $product->generic_keywords = $result['keywords'];
        $attrs = $product->attributes ?? [];
        $attrs['keyword_harvested_at'] = now()->toIso8601String();
        $attrs['keyword_byte_length'] = $result['byte_length'];
        $product->attributes = $attrs;
        $product->save();

        return $result;
    }

    /**
     * Bulk Harvest & Auto-Update all given Products in 1 fast batch
     * @param iterable $products
     * @param bool $onlyPending If true, skip products that already have generic keywords
     */
    public function bulkHarvestAndSave($products, bool $onlyPending = true): array
    {
        $updated = [];
        $totalWords = 0;
        $skipped = 0;

        foreach ($products as $p) {
            $hasKeywords = !empty(trim($p->generic_keywords ?? ''));
            if ($onlyPending && $hasKeywords) {
                $skipped++;
                $words = count(array_filter(explode(' ', trim($p->generic_keywords))));
                $updated[] = [
                    'id' => $p->id,
                    'name' => $p->name,
                    'sku' => $p->sku ?: 'SKU-' . $p->id,
                    'keywords' => $p->generic_keywords,
                    'byte_length' => strlen($p->generic_keywords),
                    'words_count' => $words,
                    'skipped' => true,
                ];
                continue;
            }

            $res = $this->harvestAndSaveProduct($p);
            $updated[] = [
                'id' => $p->id,
                'name' => $p->name,
                'sku' => $p->sku ?: 'SKU-' . $p->id,
                'keywords' => $res['keywords'],
                'byte_length' => $res['byte_length'],
                'words_count' => $res['words_count'],
                'skipped' => false,
            ];
            $totalWords += $res['words_count'];
        }

        return [
            'total_products' => count($updated),
            'generated_count' => count($updated) - $skipped,
            'skipped_count' => $skipped,
            'total_words_harvested' => $totalWords,
            'items' => $updated
        ];
    }
}
