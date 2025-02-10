<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Session;

class OnlyOfficeController extends Controller
{
    public function redirectToOAuth()
    {
        $clientId = config('services.onlyoffice.client_id');
        $redirectUri = config('services.onlyoffice.redirect_uri');
        $baseUrl = config('services.onlyoffice.base_url');

        $url = "$baseUrl/oauth/authorize?client_id=$clientId&redirect_uri=$redirectUri&response_type=code&scope=files:write";

        return redirect($url);
    }

    public function handleCallback(Request $request)
    {
        $code = $request->query('code');

        $client = new Client();
        $response = $client->post(config('services.onlyoffice.base_url') . '/oauth/token', [
            'form_params' => [
                'client_id' => config('services.onlyoffice.client_id'),
                'client_secret' => config('services.onlyoffice.client_secret'),
                'redirect_uri' => config('services.onlyoffice.redirect_uri'),
                'grant_type' => 'authorization_code',
                'code' => $code,
            ],
        ]);

        $tokenData = json_decode($response->getBody(), true);

        Session::put('onlyoffice_access_token', $tokenData['access_token']);

        return redirect('/files/upload-form');
    }

    public function showUploadForm()
    {
        return view('files.upload');
    }

    public function uploadFile(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:doc,docx,pdf',
        ]);

        $file = $request->file('file');
        $pathToFile = $file->getRealPath();
        $fileName = $file->getClientOriginalName();
        $accessToken = session('onlyoffice_access_token');

        $client = new Client();

        try {
            $response = $client->post(config('services.onlyoffice.base_url') . '/api/v2/storage/upload', [
                'headers' => [
                    'Authorization' => "Bearer {$accessToken}",
                ],
                'multipart' => [
                    [
                        'name' => 'file',
                        'contents' => fopen($pathToFile, 'r'),
                        'filename' => $fileName,
                    ],
                ],
            ]);

            $responseData = json_decode($response->getBody(), true);

            return response()->json(['message' => 'Fayl muvaffaqiyatli yuklandi!', 'data' => $responseData]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function getEditLink($fileId)
    {
        $accessToken = session('onlyoffice_access_token');

        $client = new Client();
        $response = $client->get(config('services.onlyoffice.base_url') . "/api/v1/files/{$fileId}/edit", [
            'headers' => [
                'Authorization' => "Bearer {$accessToken}",
            ],
        ]);

        $data = json_decode($response->getBody(), true);

        return view('files.edit', ['editLink' => $data['edit_link']]);
    }
}
