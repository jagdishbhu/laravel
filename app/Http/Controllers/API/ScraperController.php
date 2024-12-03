<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class ScraperController extends BaseController
{
    public function scrape()
    {
        $url = 'https://afd.calpoly.edu/web/sample-tables';

        $client = new Client();
        try {
            $response = $client->get($url);
            $htmlContent = (string) $response->getBody();
                
            // Initialize the DOM Crawler to parse the HTML content
            $crawler = new Crawler($htmlContent);

            $tablesData = [];
            
            $crawler->filter('table')->each(function (Crawler $node, $index) use (&$tablesData) {
                $tableKey = 'table' . ($index + 1); 
                $tableData = [];
                
                // Get the table caption/title
                $caption = $node->filter('caption')->text();
                
                // Get the rows of the table
                $rows = [];
                $node->filter('tr')->each(function (Crawler $row) use (&$rows) {
                    $rowData = [];
                    $row->filter('td')->each(function (Crawler $cell) use (&$rowData) {
                        $rowData[] = $cell->text();
                    });
                    if (!empty($rowData)) {
                        $rows[] = $rowData;
                    }
                });

                $tablesData[$tableKey] = [
                    'title' => $caption,
                    'rows' => $rows
                ];
            });

            // Return the data in the required format
            return response()->json($tablesData);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to scrape data', 'message' => $e->getMessage()], 500);
        }
    }
}

