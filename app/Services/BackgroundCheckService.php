<?php

namespace App\Services;

use App\Models\TenantScreening;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class BackgroundCheckService
{
    protected $apiBaseUrl;
    protected $apiKey;
    
    public function __construct()
    {
        $this->apiBaseUrl = config('services.background_check.url', 'https://api.backgroundcheck.example.com/v1');
        $this->apiKey = config('services.background_check.key', 'demo_key_for_development');
    }
    
    /**
     * Initiate a background check for a tenant screening
     *
     * @param TenantScreening $screening
     * @return array
     */
    public function initiateCheck(TenantScreening $screening)
    {
        try {
            $applicant = $screening->user;
            $profile = $applicant->profile;
            
            if (!$profile) {
                return [
                    'success' => false,
                    'message' => 'Applicant profile not found',
                    'reference_id' => null
                ];
            }
            
            $response = $this->makeApiCall('post', '/checks', [
                'first_name' => $applicant->name,
                'last_name' => $profile->surname ?? '',
                'email' => $applicant->email,
                'phone' => $profile->phone_number ?? '',
                'dob' => $profile->date_of_birth ?? '',
                'id_number' => $profile->id_number ?? '',
                'address' => $profile->address ?? '',
                'employer' => $profile->employer ?? '',
                'income' => $profile->income ?? '',
                'screening_id' => $screening->id,
                'check_types' => ['criminal', 'credit', 'eviction', 'employment', 'income_verification'],
            ]);
            
            if ($response['success']) {
                return [
                    'success' => true,
                    'message' => 'Background check initiated successfully',
                    'reference_id' => $response['data']['reference_id'] ?? null,
                    'check_url' => $response['data']['check_url'] ?? null
                ];
            }
            
            return [
                'success' => false,
                'message' => $response['message'] ?? 'Failed to initiate background check',
                'reference_id' => null
            ];
        } catch (Exception $e) {
            Log::error('Background check initiation failed: ' . $e->getMessage(), [
                'screening_id' => $screening->id,
                'exception' => $e
            ]);
            
            return [
                'success' => false,
                'message' => 'Background check service error: ' . $e->getMessage(),
                'reference_id' => null
            ];
        }
    }
    
    /**
     * Get the status of a background check
     *
     * @param string $referenceId
     * @return array
     */
    public function getCheckStatus(string $referenceId)
    {
        try {
            $response = $this->makeApiCall('get', "/checks/{$referenceId}");
            
            if ($response['success']) {
                return [
                    'success' => true,
                    'status' => $response['data']['status'] ?? 'pending',
                    'message' => $response['message'] ?? 'Check status retrieved successfully',
                    'report_data' => $response['data'] ?? []
                ];
            }
            
            return [
                'success' => false,
                'status' => 'error',
                'message' => $response['message'] ?? 'Failed to retrieve check status',
                'report_data' => []
            ];
        } catch (Exception $e) {
            Log::error('Background check status retrieval failed: ' . $e->getMessage(), [
                'reference_id' => $referenceId,
                'exception' => $e
            ]);
            
            return [
                'success' => false,
                'status' => 'error',
                'message' => 'Background check service error: ' . $e->getMessage(),
                'report_data' => []
            ];
        }
    }
    
    /**
     * Mock background check result for development purposes
     *
     * @param TenantScreening $screening
     * @return array
     */
    public function getMockResult(TenantScreening $screening)
    {
        return [
            'success' => true,
            'status' => 'completed',
            'reference_id' => 'mock_' . uniqid(),
            'report_data' => [
                'criminal_check' => [
                    'status' => 'passed',
                    'records' => []
                ],
                'credit_check' => [
                    'status' => 'passed',
                    'credit_score' => rand(650, 820),
                    'debt_to_income_ratio' => rand(15, 35) . '%',
                    'payment_history' => 'good'
                ],
                'eviction_check' => [
                    'status' => 'passed',
                    'history' => []
                ],
                'employment_verification' => [
                    'status' => 'verified',
                    'employer' => $screening->user->profile->employer ?? 'Unknown',
                    'position' => 'Verified',
                    'income' => 'Verified'
                ],
                'screening_recommendation' => 'approve',
                'overall_risk' => 'low'
            ]
        ];
    }
    
    /**
     * Make API call to the background check service
     *
     * @param string $method
     * @param string $endpoint
     * @param array $data
     * @return array
     */
    protected function makeApiCall(string $method, string $endpoint, array $data = [])
    {
        try {
            if (app()->environment('local', 'development', 'testing')) {
                // For development environments, use mock responses
                return $this->getMockApiResponse($endpoint, $data);
            }
            
            $url = $this->apiBaseUrl . $endpoint;
            
            /** @var Response $response */
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ])->{$method}($url, $data);
            
            if ($response->successful()) {
                return [
                    'success' => true,
                    'status_code' => $response->status(),
                    'message' => 'API call successful',
                    'data' => $response->json()
                ];
            }
            
            return [
                'success' => false,
                'status_code' => $response->status(),
                'message' => 'API call failed: ' . $response->body(),
                'data' => $response->json() ?? []
            ];
        } catch (Exception $e) {
            Log::error('API call failed: ' . $e->getMessage(), [
                'endpoint' => $endpoint,
                'method' => $method,
                'exception' => $e
            ]);
            
            return [
                'success' => false,
                'status_code' => 500,
                'message' => 'Exception: ' . $e->getMessage(),
                'data' => []
            ];
        }
    }
    
    /**
     * Get mock API responses for development environments
     *
     * @param string $endpoint
     * @param array $data
     * @return array
     */
    protected function getMockApiResponse(string $endpoint, array $data = [])
    {
        // Simulating a successful check initiation
        if (str_contains($endpoint, '/checks') && !str_contains($endpoint, '/')) {
            return [
                'success' => true,
                'status_code' => 201,
                'message' => 'Background check initiated successfully',
                'data' => [
                    'reference_id' => 'mock_' . uniqid(),
                    'check_url' => 'https://example.com/checks/mock_id',
                    'status' => 'initiated'
                ]
            ];
        }
        
        // Simulating a check status retrieval
        if (preg_match('/\/checks\/mock_[a-z0-9]+/', $endpoint)) {
            $statuses = ['pending', 'in_progress', 'completed'];
            $randomIndex = array_rand($statuses);
            
            return [
                'success' => true,
                'status_code' => 200,
                'message' => 'Check status retrieved successfully',
                'data' => [
                    'status' => $statuses[$randomIndex],
                    'reference_id' => substr($endpoint, strrpos($endpoint, '/') + 1),
                    'criminal_check' => [
                        'status' => 'passed',
                        'records' => []
                    ],
                    'credit_check' => [
                        'status' => 'passed',
                        'credit_score' => rand(650, 820),
                        'debt_to_income_ratio' => rand(15, 35) . '%',
                        'payment_history' => 'good'
                    ],
                    'eviction_check' => [
                        'status' => 'passed',
                        'history' => []
                    ],
                    'employment_verification' => [
                        'status' => 'verified',
                        'employer' => 'Verified',
                        'position' => 'Verified',
                        'income' => 'Verified'
                    ],
                    'screening_recommendation' => 'approve',
                    'overall_risk' => 'low'
                ]
            ];
        }
        
        // Default mock response
        return [
            'success' => false,
            'status_code' => 404,
            'message' => 'Mock endpoint not found',
            'data' => []
        ];
    }
}