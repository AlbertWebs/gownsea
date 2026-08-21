<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\CatalogueFeedBuilder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CatalogueFeedController extends Controller
{
    public function index(CatalogueFeedBuilder $builder): View
    {
        return view('admin.catalogue.feeds.index', [
            'productCount' => Product::query()->published()->count(),
            'googlePreview' => $builder->googleRows()->take(3),
            'facebookPreview' => $builder->facebookRows()->take(3),
        ]);
    }

    public function export(Request $request, CatalogueFeedBuilder $builder): StreamedResponse|Response
    {
        $data = $request->validate([
            'channel' => ['required', 'in:google,facebook'],
            'format' => ['required', 'in:csv,excel'],
        ]);

        $rows = $data['channel'] === 'google'
            ? $builder->googleRows()
            : $builder->facebookRows();

        $stamp = now()->format('Y-m-d');
        $base = $data['channel'] === 'google'
            ? "gownsea-google-merchant-{$stamp}"
            : "gownsea-facebook-catalog-{$stamp}";

        $headers = array_keys($rows->first() ?? [
            'id' => '',
            'title' => '',
            'description' => '',
            'link' => '',
            'image_link' => '',
            'availability' => '',
            'price' => '',
            'brand' => '',
        ]);

        if ($data['format'] === 'csv') {
            return $this->csvDownload($base.'.csv', $headers, $rows->all());
        }

        return $this->excelDownload($base.'.xls', $headers, $rows->all());
    }

    /**
     * @param  list<string>  $headers
     * @param  list<array<string, string>>  $rows
     */
    private function csvDownload(string $filename, array $headers, array $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rows) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, $headers);
            foreach ($rows as $row) {
                $line = [];
                foreach ($headers as $header) {
                    $line[] = $row[$header] ?? '';
                }
                fputcsv($out, $line);
            }
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * SpreadsheetML .xls — opens in Excel without extra PHP packages.
     *
     * @param  list<string>  $headers
     * @param  list<array<string, string>>  $rows
     */
    private function excelDownload(string $filename, array $headers, array $rows): Response
    {
        $escape = fn (string $value): string => htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<?mso-application progid="Excel.Sheet"?>'."\n";
        $xml .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" '
            .'xmlns:o="urn:schemas-microsoft-com:office:office" '
            .'xmlns:x="urn:schemas-microsoft-com:office:excel" '
            .'xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">'
            .'<Worksheet ss:Name="Catalogue"><Table>';

        $xml .= '<Row>';
        foreach ($headers as $header) {
            $xml .= '<Cell><Data ss:Type="String">'.$escape($header).'</Data></Cell>';
        }
        $xml .= '</Row>';

        foreach ($rows as $row) {
            $xml .= '<Row>';
            foreach ($headers as $header) {
                $xml .= '<Cell><Data ss:Type="String">'.$escape((string) ($row[$header] ?? '')).'</Data></Cell>';
            }
            $xml .= '</Row>';
        }

        $xml .= '</Table></Worksheet></Workbook>';

        return response($xml, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}
