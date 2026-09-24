<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExternalDataController extends Controller
{
    /**
     * The external API URL.
     */
    private const EXTERNAL_API_URL = 'https://bit.ly/48ejMhW';

    /**
     * Fetch and parse data from the external API.
     *
     * @return array|null
     */
    private function fetchExternalData(): ?array
    {
        try {
            $response = Http::timeout(30)->get(self::EXTERNAL_API_URL);

            if ($response->failed()) {
                Log::error('External API request failed', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return null;
            }

            $json = $response->json();

            if (!isset($json['DATA']) || $json['RC'] !== 200) {
                Log::error('External API returned unexpected format', ['response' => $json]);
                return null;
            }

            return $this->parseData($json['DATA']);
        } catch (\Exception $e) {
            Log::error('External API exception', ['message' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Parse the pipe-delimited data string into an array of records.
     *
     * @param string $dataString
     * @return array
     */
    private function parseData(string $dataString): array
    {
        $lines = explode("\n", $dataString);
        $records = [];

        if (count($lines) < 2) {
            return $records;
        }

        // Parse header dynamically to determine column positions
        $header = explode('|', trim($lines[0]));
        $columnMap = array_flip(array_map('trim', $header));

        // Validate required columns exist
        if (!isset($columnMap['NAMA'], $columnMap['YMD'], $columnMap['NIM'])) {
            return $records;
        }

        $namaIdx = $columnMap['NAMA'];
        $ymdIdx  = $columnMap['YMD'];
        $nimIdx  = $columnMap['NIM'];

        for ($i = 1; $i < count($lines); $i++) {
            $line = trim($lines[$i]);
            if (empty($line)) {
                continue;
            }

            $parts = explode('|', $line);
            if (count($parts) === 3) {
                $records[] = [
                    'YMD'  => trim($parts[$ymdIdx]),
                    'NIM'  => trim($parts[$nimIdx]),
                    'NAMA' => trim($parts[$namaIdx]),
                ];
            }
        }

        return $records;
    }

    /**
     * Search data by NAMA (name).
     * Endpoint: GET /api/external-data/search/nama?nama=Turner Mia
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function searchByNama(Request $request): JsonResponse
    {
        $request->validate([
            'nama' => 'required|string',
        ]);

        $nama = $request->query('nama');
        $data = $this->fetchExternalData();

        if ($data === null) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to fetch data from external API',
            ], 503);
        }

        $results = array_values(array_filter($data, function ($record) use ($nama) {
            return stripos($record['NAMA'], $nama) !== false;
        }));

        return response()->json([
            'status'       => 'success',
            'message'      => 'Search by NAMA',
            'query'        => $nama,
            'total_results' => count($results),
            'data'         => $results,
        ], 200);
    }

    /**
     * Search data by NIM (student ID).
     * Endpoint: GET /api/external-data/search/nim?nim=9352078461
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function searchByNim(Request $request): JsonResponse
    {
        $request->validate([
            'nim' => 'required|string',
        ]);

        $nim  = $request->query('nim');
        $data = $this->fetchExternalData();

        if ($data === null) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to fetch data from external API',
            ], 503);
        }

        $results = array_values(array_filter($data, function ($record) use ($nim) {
            return $record['NIM'] === $nim;
        }));

        return response()->json([
            'status'       => 'success',
            'message'      => 'Search by NIM',
            'query'        => $nim,
            'total_results' => count($results),
            'data'         => $results,
        ], 200);
    }

    /**
     * Search data by YMD (date).
     * Endpoint: GET /api/external-data/search/ymd?ymd=20230405
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function searchByYmd(Request $request): JsonResponse
    {
        $request->validate([
            'ymd' => 'required|string',
        ]);

        $ymd  = $request->query('ymd');
        $data = $this->fetchExternalData();

        if ($data === null) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to fetch data from external API',
            ], 503);
        }

        $results = array_values(array_filter($data, function ($record) use ($ymd) {
            return $record['YMD'] === $ymd;
        }));

        return response()->json([
            'status'       => 'success',
            'message'      => 'Search by YMD',
            'query'        => $ymd,
            'total_results' => count($results),
            'data'         => $results,
        ], 200);
    }

    /**
     * Get all external data.
     * Endpoint: GET /api/external-data
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $data = $this->fetchExternalData();

        if ($data === null) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to fetch data from external API',
            ], 503);
        }

        return response()->json([
            'status'      => 'success',
            'message'     => 'All external data fetched successfully',
            'total_records' => count($data),
            'data'        => $data,
        ], 200);
    }
}
