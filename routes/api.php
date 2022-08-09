<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/send-request', function (\App\Services\JiraService $jiraService, \App\Services\TelegramService $telegramService) {




    $response = $jiraService->sendRequest('search', [
        'jql' => 'assignee="firuzbekbakhadirov@77projects.com" AND created >= -2d ORDER BY created ASC'
    ]);

    return $response;








});
