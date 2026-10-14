<?php

namespace App\Services;

class PdfExtractionService
{
    /**
     * Extract text and metadata from a PDF file.
     * 
     * @param string $filePath Absolute path to the PDF file.
     * @return array
     */
    public function extract($filePath)
    {
        if (!file_exists($filePath)) {
            return [
                'text' => '',
                'pages' => [],
                'page_count' => 0,
                'file_size' => '0 B',
            ];
        }

        $fileSize = $this->formatFileSize(filesize($filePath));
        $pageCount = $this->fastPageCount($filePath);

        // Primary: Use Smalot PdfParser
        try {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($filePath);
            $pages = [];
            $pageNum = 1;
            
            $allPages = $pdf->getPages();
            $pageCount = count($allPages) > 0 ? count($allPages) : $pageCount;
            
            $maxPagesToParse = (filesize($filePath) > 5 * 1024 * 1024) ? 8 : 500;
            
            \Log::info('PDF EXTRACTION DEBUG', [
                'file' => $filePath,
                'page_count' => count($allPages),
                'text_length' => strlen($pdf->getText()),
            ]);
            

            foreach ($allPages as $page) {
                if ($pageNum > $maxPagesToParse) {
                    break;
                }
                $pages[$pageNum] = $page->getText();
                $pageNum++;
            }
            
            $fullText = implode(' ', $pages);
            
            return [
                'text' => $fullText,
                'pages' => $pages,
                'page_count' => $pageCount,
                'file_size' => $fileSize,
            ];
        } catch (\Exception $e) {
            // Fallback: Use manual regex stream parser if parser library fails
            $pages = $this->extractPagesText($filePath);
            $fullText = implode(' ', $pages);

            return [
                'text' => $fullText,
                'pages' => $pages,
                'page_count' => $pageCount,
                'file_size' => $fileSize,
            ];
        }
    }

    /**
     * Hitung halaman PDF secara cepat tanpa memproses teks (untuk file besar).
     */
    private function fastPageCount($filePath)
    {
        $content = @file_get_contents($filePath);
        if (empty($content)) {
            return 1;
        }

        // Cari pola "/Type /Page"
        if (preg_match_all('/\/Type\s*\/Page\b/i', $content, $matches)) {
            return count($matches[0]);
        }

        // Cari pola "/Count [angka]"
        if (preg_match('/\/Count\s+(\d+)/i', $content, $matches)) {
            return (int) $matches[1];
        }

        return 1;
    }

    /**
     * Format byte sizes into human readable strings.
     * 
     * @param int $bytes
     * @return string
     */
    private function formatFileSize($bytes)
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }

    /**
     * Parse text content page-by-page from the PDF file.
     * 
     * @param string $filePath
     * @return array Array of page number => extracted text
     */
    private function extractPagesText($filePath)
    {
        $content = @file_get_contents($filePath);
        if (empty($content)) {
            return [];
        }

        $pagesText = [];

        // 1. Find all Page objects and their contents references
        // Pattern matches dictionary containing /Type /Page and captures /Contents object ID(s)
        preg_match_all('/<<[^>]*\/Type\s*\/Page\b[^>]*>>/isU', $content, $pageDictionaries);
        
        $pageIndex = 1;
        // Limit page parsing for large files to prevent timeout
        $maxPagesToParse = (filesize($filePath) > 5 * 1024 * 1024) ? 8 : 500;

        if (!empty($pageDictionaries[0])) {
            foreach ($pageDictionaries[0] as $dict) {
                if ($pageIndex > $maxPagesToParse) {
                    break;
                }

                $contentsIds = [];

                // Check for single contents object e.g. /Contents 5 0 R
                if (preg_match('/\/Contents\s+(\d+)\s+0\s+R/i', $dict, $match)) {
                    $contentsIds[] = (int) $match[1];
                }
                // Check for array of contents objects e.g. /Contents [5 0 R 6 0 R]
                elseif (preg_match('/\/Contents\s*\[([^\]]*)\]/i', $dict, $match)) {
                    preg_match_all('/(\d+)\s+0\s+R/i', $match[1], $arrayMatches);
                    if (!empty($arrayMatches[1])) {
                        foreach ($arrayMatches[1] as $id) {
                            $contentsIds[] = (int) $id;
                        }
                    }
                }

                $pageText = '';
                // 2. Extract and decompress stream for each contents ID
                foreach ($contentsIds as $id) {
                    $streamText = $this->extractStreamText($content, $id);
                    if ($streamText) {
                        $pageText .= $streamText . ' ';
                    }
                }

                $pageText = $this->cleanText($pageText);
                if (!empty($pageText) || $pageIndex === 1) {
                    $pagesText[$pageIndex] = $pageText;
                    $pageIndex++;
                }
            }
        }

        // Fallback: If page extraction didn't work (non-standard PDF), extract all streams as single page
        if (empty($pagesText)) {
            $fullText = '';
            preg_match_all('/<<[^>]*\/Filter\s*\/FlateDecode[^>]*>>\s*stream(.*)endstream/isU', $content, $streamMatches);
            if (!empty($streamMatches[1])) {
                foreach ($streamMatches[1] as $stream) {
                    $stream = trim($stream);
                    $decompressed = @gzuncompress($stream);
                    if ($decompressed !== false) {
                        $fullText .= $this->parseBtEtBlocks($decompressed) . ' ';
                    }
                }
            }
            $fullText = $this->cleanText($fullText);
            if ($fullText) {
                $pagesText[1] = $fullText;
            }
        }

        return $pagesText;
    }

    /**
     * Locate and extract text from a specific PDF object stream.
     * 
     * @param string $pdfContent
     * @param int $objectId
     * @return string
     */
    private function extractStreamText($pdfContent, $objectId)
    {
        // Match the object declaration and its stream
        // e.g. 5 0 obj ... stream ... endstream
        $pattern = '/' . $objectId . '\s+0\s+obj\s*<<(.*)>>\s*stream(.*)endstream/isU';
        if (preg_match($pattern, $pdfContent, $matches)) {
            $dict = $matches[1];
            $stream = trim($matches[2]);

            // Check if stream is compressed
            if (stripos($dict, '/FlateDecode') !== false) {
                $decompressed = @gzuncompress($stream);
                if ($decompressed !== false) {
                    return $this->parseBtEtBlocks($decompressed);
                }
            } else {
                // Uncompressed stream
                return $this->parseBtEtBlocks($stream);
            }
        }

        return '';
    }

    /**
     * Parse text blocks inside BT ... ET sections in PDF streams.
     * 
     * @param string $streamContent
     * @return string
     */
    private function parseBtEtBlocks($streamContent)
    {
        $text = '';
        preg_match_all('/BT(.*)ET/sU', $streamContent, $btMatches);
        if (!empty($btMatches[1])) {
            foreach ($btMatches[1] as $btBlock) {
                // Find all parenthesized strings e.g. (Hello World)
                preg_match_all('/\((.*?)\)/', $btBlock, $textMatches);
                if (!empty($textMatches[1])) {
                    foreach ($textMatches[1] as $txt) {
                        $text .= $txt . ' ';
                    }
                }
            }
        }
        return $text;
    }

    /**
     * Clean PDF text formatting.
     * 
     * @param string $text
     * @return string
     */
    private function cleanText($text)
    {
        $text = preg_replace('/\\\[0-9]{3}/', '', $text); // remove octal escapes
        $text = str_replace(['\\(', '\\)'], ['(', ')'], $text);
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
    }
}
