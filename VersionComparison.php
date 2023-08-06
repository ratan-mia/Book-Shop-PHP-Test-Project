<?php
class VersionComparison
{
    public static function compareVersions($version1, $version2)
    {
        return version_compare($version1, $version2, '>');
    }
}

class SalesDataProcessor
{
    private $salesData;

    public function __construct($salesData)
    {
        $this->salesData = $salesData;
    }

    public function convertSalesDateToUTC($version)
    {
        if (VersionComparison::compareVersions($version, '1.0.17+60')) {
            foreach ($this->salesData as &$sale) {
                $saleDate = DateTime::createFromFormat('Y-m-d H:i:s', $sale['sale_date'], new DateTimeZone('Europe/Berlin'));
                $saleDate->setTimeZone(new DateTimeZone('UTC'));
                $sale['sale_date'] = $saleDate->format('Y-m-d H:i:s');
            }
        }
        return $this->salesData;
    }
}

// Load JSON data from sales_data.json
$jsonData = file_get_contents('sales_data.json');
$salesData = json_decode($jsonData, true);

// Create an instance of SalesDataProcessor
$processor = new SalesDataProcessor($salesData);

// Convert sales dates to UTC based on version
$convertedSalesData = $processor->convertSalesDateToUTC("1.0.17+59");

// Print the converted sales data
print_r($convertedSalesData);
