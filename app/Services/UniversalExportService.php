<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\StreamedResponse;

class UniversalExportService
{
    /**
     * Export data to CSV with UTF-8 BOM for Excel compatibility.
     *
     * @param array $data
     * @param array $headers
     * @param string $filename
     * @return StreamedResponse
     */
    public static function exportCsv(array $data, array $headers, string $filename = 'export.csv')
    {
        $response = new StreamedResponse(function () use ($data, $headers) {
            $handle = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for Excel
            fputs($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            
            // Add headers
            fputcsv($handle, $headers, ';');
            
            // Add data rows
            foreach ($data as $row) {
                // Ensure all data is string for CSV
                $csvRow = array_map(function($value) {
                    return is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : (string)$value;
                }, array_values($row));
                fputcsv($handle, $csvRow, ';');
            }
            
            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

        return $response;
    }
}
