<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class ScraperTableController extends BaseController
{
    public function scrape()
    {
        $url = 'https://afd.calpoly.edu/web/sample-tables';

        $client = new Client();
        try {
            // Send HTTP request to fetch the HTML content from the URL
            $response = $client->get($url);
            $htmlContent = (string) $response->getBody();
                
            // Initialize the DOM Crawler to parse the HTML content
            $crawler = new Crawler($htmlContent);

            $tablesData = [];
            
            // Loop through each table and extract data
            $crawler->filter('table')->each(function (Crawler $node, $index) use (&$tablesData) {
                $tableKey = 'table' . ($index + 1); 
                $tableData = [];
                
                // Get the rows of the table
                $node->filter('tr')->each(function (Crawler $row) use (&$tableData) {
                    $rowData = [];
                    $row->filter('td')->each(function (Crawler $cell) use (&$rowData) {
                        $rowData[] = $cell->text();  // Extract text from each table cell
                    });
                    // Only add non-empty rows to the table data
                    if (!empty($rowData)) {
                        $tableData[] = $rowData;
                    }
                });

                // Store the table data using the table key
                $tablesData[$tableKey] = $tableData;
            });

            // Return the data in the required format, only table values (no captions)
            return response()->json($tablesData);

        } catch (\Exception $e) {
            // Return error message if scraping fails
            return response()->json(['error' => 'Failed to scrape data', 'message' => $e->getMessage()], 500);
        }
    }
}

