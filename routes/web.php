<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


use App\Http\Controllers\ImageController;



// Route::get('/image-crop', function () {
//     return view('image_crop');
// });

// Route::post('/extract-text', [ImageController::class, 'extractText'])->name('extract-text');

use Illuminate\Http\Request;

// Route::post('/extract-text', function (Request $request) {
//     if ($request->hasFile('image')) {
//         $image = $request->file('image');
//         $imagePath = $image->store('public/images'); // Store the image

//         // Here you can trigger your Python script for OCR
//         // For example, run the python script and pass the image path
//         $output = shell_exec("python D:\\fast\\calling\\python-api\\ocr_script.py " . storage_path('app/' . $imagePath));

//         // Return the LaTeX output in JSON format
//         return response()->json(['latex' => $output]);
//     }

//     return response()->json(['error' => 'No image uploaded.'], 400);
// })->name('extract-text');



// Route to display the image upload form
Route::get('/image', function () {
    return view('image_form');
});

// Route to handle form submission and trigger the Python script
Route::post('/image/run', [ImageController::class, 'runScript'])->name('image.runScript');



