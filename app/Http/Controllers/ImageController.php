<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class ImageController extends Controller
{
    public function runScript(Request $request)
    {
        // Check if the file exists in the request
        if (!$request->hasFile('image')) {
            return response()->json(['error' => 'No image file uploaded.']);
        }

        // Define the path to the Python script
        $pythonScriptPath = base_path('storage/python_script/ocr_script.py');

        // Store the uploaded image and get the path
        $image = $request->file('image');
        $imagePath = $image->store('uploads', 'public');

        // Notify user that image is being processed
        $process = new Process(['python3', $pythonScriptPath, public_path('storage/' . $imagePath)]);

        // Run the process and handle the output
        try {
            $process->mustRun();
            $output = $process->getOutput();

            // Notify success
            return response()->json(['output' => $output, 'status' => 'success']);
        } catch (ProcessFailedException $exception) {
            // Notify failure
            return response()->json(['error' => 'Python script failed: ' . $exception->getMessage(), 'status' => 'error']);
        }
    }
}
