<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Goal;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class AiSuggestionController extends Controller
{
    public function generateSteps(Request $request, Goal $goal)
    {
        $this->authorize('update', $goal);
        
        $validatedData = $request->validate([
            'description' => 'required|string',
        ]);

        // Use OpenAI's API to generate steps
        try {
            $client = new Client();
            $response = $client->post('https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . Config::get('services.openai.api_key'),
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'gpt-4',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are a helpful assistant that generates actionable steps for achieving goals.'
                        ],
                        [
                            'role' => 'user',
                            'content' => "Generate 5-7 concrete, actionable steps to achieve this goal: {$validatedData['description']}. Format the response as a JSON array of objects with 'title' and 'description' properties."
                        ]
                    ],
                    'temperature' => 0.7,
                ],
            ]);

            $result = json_decode((string) $response->getBody(), true);
            $content = $result['choices'][0]['message']['content'];
            
            // Parse the JSON response
            $steps = json_decode($content, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                // If not proper JSON, try to extract steps from text
                preg_match_all('/\d+\.\s+(.*?)(?=\n\d+\.|\n\n|$)/s', $content, $matches);
                $steps = [];
                foreach ($matches[1] as $index => $match) {
                    $parts = explode(':', $match, 2);
                    $title = trim($parts[0]);
                    $description = isset($parts[1]) ? trim($parts[1]) : '';
                    $steps[] = [
                        'title' => $title,
                        'description' => $description
                    ];
                }
            }
            
            // Save steps to the goal
            $savedSteps = [];
            $order = $goal->steps()->count() + 1;
            
            foreach ($steps as $stepData) {
                if (empty($stepData['title'])) continue;
                
                $step = $goal->steps()->create([
                    'title' => $stepData['title'],
                    'description' => $stepData['description'] ?? '',
                    'order' => $order++
                ]);
                
                $savedSteps[] = $step;
            }
            
            return response()->json([
                'success' => true,
                'steps' => $savedSteps
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to generate steps: ' . $e->getMessage()
            ], 500);
        }
    }
}