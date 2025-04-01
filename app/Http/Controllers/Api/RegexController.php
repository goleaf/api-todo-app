<?php

namespace App\Http\Controllers\Api;

use App\Facades\Regex;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Regex\CustomExtractionRequest;
use App\Http\Requests\Api\Regex\DataExtractionRequest;
use App\Http\Requests\Api\Regex\EmailValidationRequest;
use App\Http\Requests\Api\Regex\TransformationRequest;
use App\Http\Requests\Api\Regex\ValidationRequest;

class RegexController extends Controller
{
    /**
     * Validate email using regex
     *
     * @param EmailValidationRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateEmail(EmailValidationRequest $request)
    {
        $email = $request->input('email');
        
        $isValid = Regex::isEmail($email);
        
        return response()->json([
            'success' => true,
            'is_valid' => $isValid,
            'message' => $isValid ? 'Email is valid' : 'Email is invalid'
        ]);
    }
    
    /**
     * Extract data from text
     *
     * @param DataExtractionRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function extractData(DataExtractionRequest $request)
    {
        $text = $request->input('text');
        
        $data = [
            'emails' => Regex::extractEmails($text),
            'urls' => Regex::extractUrls($text),
            'hashtags' => Regex::extractHashtags($text),
            'mentions' => Regex::extractMentions($text)
        ];
        
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
    
    /**
     * Validate URL, UUID, IP address, etc.
     *
     * @param ValidationRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateValue(ValidationRequest $request)
    {
        $input = $request->input('value');
        $type = $request->input('type');
        
        $result = false;
        
        switch ($type) {
            case 'email':
                $result = Regex::isEmail($input);
                break;
            case 'url':
                $result = Regex::isUrl($input);
                break;
            case 'ip':
                $result = Regex::isIp($input);
                break;
            case 'ipv4':
                $result = Regex::isIpv4($input);
                break;
            case 'ipv6':
                $result = Regex::isIpv6($input);
                break;
            case 'uuid':
                $result = Regex::isUuid($input);
                break;
            case 'phone':
                $result = Regex::isPhone($input);
                break;
            case 'username':
                $result = Regex::isValidUsername($input);
                break;
            case 'password':
                $result = Regex::isStrongPassword($input);
                break;
            case 'hex_color':
                $result = Regex::isHexColor($input);
                break;
            default:
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid validation type'
                ], 400);
        }
        
        return response()->json([
            'success' => true,
            'is_valid' => $result,
            'message' => $result ? "The {$type} is valid" : "The {$type} is invalid"
        ]);
    }
    
    /**
     * Transform text using regex
     *
     * @param TransformationRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function transform(TransformationRequest $request)
    {
        $text = $request->input('text');
        $type = $request->input('type');
        
        $result = null;
        
        switch ($type) {
            case 'slug':
                $result = Regex::slugify($text);
                break;
            case 'strip_html':
                $result = Regex::stripHtml($text);
                break;
            default:
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid transformation type'
                ], 400);
        }
        
        return response()->json([
            'success' => true,
            'result' => $result
        ]);
    }
    
    /**
     * Extract data using a custom pattern
     *
     * @param CustomExtractionRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function extractCustom(CustomExtractionRequest $request)
    {
        $text = $request->input('text');
        $pattern = $request->input('pattern');
        
        try {
            $data = Regex::extractData($pattern, $text);
            
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid regex pattern: ' . $e->getMessage()
            ], 400);
        }
    }
} 