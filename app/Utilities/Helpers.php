<?php

use App\Models\Setting;
use JsonSchema\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Models\LightOfGuidance;
use Illuminate\Support\Facades\Log;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;

if (! function_exists('eThrowable')) {
    function eThrowable(string $_class, string $message, ?string $trace = null, array $data = []): object
    {
        $LOG = new LightOfGuidance;
        $LOG->uuid = (string) Str::uuid();
        $LOG->_class = $_class;
        $LOG->message = $message;
        $LOG->trace = $trace;
        $LOG->user_id = $data['user_id'] ?? null;
        $LOG->exception_type = $data['exception_type'] ?? null;
        $LOG->exception_code = $data['exception_code'] ?? null;
        $LOG->request_info = $data['request_info'] ?? null;
        $LOG->job_class = $data['job_class'] ?? null;
        $LOG->job_id = $data['job_id'] ?? null;
        $LOG->queue_name = $data['queue_name'] ?? null;
        $LOG->queue_connection = $data['queue_connection'] ?? null;
        $LOG->model_class = $data['model_class'] ?? null;
        $LOG->model_id = $data['model_id'] ?? null;
        $LOG->payload = $data['payload'] ?? null;
        $LOG->environment = $data['environment'] ?? null;
        $LOG->_status = $data['_status'] ?? LightOfGuidance::PENDING;

        $LOG->save();

        return $LOG->fresh();
    }
}

if (! function_exists('updateSetting')) {
    function updateSetting(string $item, string $data): object
    {
        $setting = Setting::whereItem($item)->first();
        $setting->current_value = trim($data);
        $setting->save();

        return $setting->fresh();
    }
}

if (! function_exists('getSetting')) {
    function getSetting(string $item): string
    {
        $setting = Setting::whereItem($item)->first();

        return optional($setting)->current_value ?? (optional($setting)->default_value ?? null);
    }
}

if (! function_exists('isUuid')) {
    function isUuid(string $id): bool
    {
        return Str::isUuid($id);
    }
}

if (!function_exists('generateUniqueNumber')) {
    function generateUniqueNumber(string $format, string $model, string $field, string $customCode = ''): string
    {
        // Parse the current timestamp
        $timestamp = Carbon::parse(REQUEST_TIMESTAMP);
        
        do {
            // Use max ID directly for sequential incrementation
            $sequence = $model::max('id') + 1;

            // Format number
            $number = sprintf(
                $customCode ? $format . "-%s-%s-%s" : $format . "-%s-%s",
                $timestamp->format('Ymd'), // Date element in YYYYMMDD format
                $sequence, // Sequential number
                $customCode ?? NULL // Custom code parameter number
            );

            // Only increment if this _uid already exists
            $sequence++;
        } while ($model::where($field, $number)->exists());

        return $number;
    }
}

/* generateUniqueNumber(
    "MWAK",
    \App\Models\Member::class,
    '_uid',
    '7544' // NULLABLE
); */

if (! function_exists('generateUID')) {
    function generateUID($model, $length = 5): string
    {
        $attempts = 0;
        $maxAttempts = 10 * $length;  // Arbitrary: Giving it 10 times the length to attempt a unique ID generation

        do {
            $attempts++;

            $_uid = strtoupper(str_pad(mt_rand(0, pow(10, $length) - 1), $length, "0", STR_PAD_LEFT));

            $existingUID = $model::where('_uid', $_uid)->withTrashed()->first();

            if ($attempts >= $maxAttempts) {
                throw new \Exception("Cannot generate a unique UID after {$maxAttempts} attempts.");
            }

        } while ($existingUID);

        return $_uid;
    }
}

if (! function_exists('extractFullName')) {
    function extractFullName(string $value): array
    {
        // Split the full name into an array by spaces
        $data = explode(' ', $value);

        // Extract and process the first and last names
        $result['first_name'] = strtolower(trim($data[0]));
        $result['last_name'] = strtolower(trim(end($data)));
        $result['middle_name'] = null; // Initialize middle name as null

        // If there are more than two parts, extract the middle name(s)
        if (count($data) > 2) {
            array_pop($data); // Remove the last element (last name)
            array_shift($data); // Remove the first element (first name)
            // Join the remaining elements as the middle name
            $result['middle_name'] = strtolower(trim(implode(' ', $data)));
        }

        // Return the associative array
        return $result;
    }
}

if (! function_exists('excelDateToPhpDate')) {
    function excelDateToPhpDate(string $date): datetime
    {
        return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(trim($date));
    }
}

if (! function_exists('greetings')) {
    function greetings(): string
    {
        $hour = date('H');

        $greetings = [
            'morning' => ['Good morning'],
            'afternoon' => ['Good afternoon'],
            'evening' => ['Good evening']
        ];

        switch ($hour) {
            case 6:
            case 7:
            case 8:
            case 9:
            case 10:
                return $greetings['morning'][0];
            case 11:
            case 12:
            case 13:
            case 14:
            case 15:
            case 16:
                return $greetings['afternoon'][0];
            case 17:
            case 18:
            case 19:
            case 20:
            case 21:
            case 22:
            case 23:
            case 0:
                return $greetings['evening'][0];
            default:
                return 'Greetings';
        }
    }
}

if (! function_exists('pluralSentence')) {
    function pluralSentence(string $sentence): string
    {
        $words = explode(' ', $sentence);

        $pluralWords = array_map(function ($word) {
            return Str::plural($word);
        }, $words);

        return implode(' ', $pluralWords);
    }
}

if (! function_exists('singularSentence')) {
    function singularSentence(string $sentence): string
    {
        $words = explode(' ', $sentence);

        $singularWords = array_map(function($word) {
            return Str::singular($word);
        }, $words);

        return implode(' ', $singularWords);
    }
}

if (! function_exists('phoneNumberPrefix')) {
    function phoneNumberPrefix(string $telephone, string $code = 'KE'): string
    {
        $phoneUtil = PhoneNumberUtil::getInstance();
        $parsedNumber = $phoneUtil->parse($telephone, $code);

        return $phoneUtil->format($parsedNumber, PhoneNumberFormat::E164);
    }
}

if (! function_exists('ensureNumericString')) {
    function ensureNumericString(string $value): ?string
    {
        if (ctype_digit($value)) {
            return $value;
        }

        return null;
    }
}

if (! function_exists('substrReplace')) {
    function substrReplace(string $string, int $length): string
    {
        return  substr_replace($string, '...', $length);
    }
}

if (! function_exists('arrayKeyWalk')) {
    function arrayKeyWalk(array $item, array $keyReplacements): array
    {
        array_walk($item, function ($value, $key) use ($keyReplacements, &$item) {
            $newkey = array_key_exists($key, $keyReplacements) ? $keyReplacements[$key] : false;
            if ($newkey !== false) {
                $item[$newkey] = $value;
                unset($item[$key]);
            }
        });

        return $item;
    }
}

if (! function_exists('objectToArray')) {
    function objectToArray($obj) {
        if (is_object($obj)) {
            $obj = get_object_vars($obj);
        }
        if (is_array($obj)) {
            return array_map('objectToArray', $obj);
        }
        return $obj;
    }
}

if (! function_exists('validateConfiguration')) {
    function validateConfiguration($data, $schema) {
        $validator = new Validator();
        $validator->validate($data, (object)$schema);
    
        if ($validator->isValid()) {
            return true;
        } else {
            // Log the errors or handle them as needed
            foreach ($validator->getErrors() as $error) {
                Log::error(sprintf("[%s] %s\n", $error['property'], $error['message']));
            }
            return false;
        }
    }
}

if (! function_exists('explodeUppercase')) {
    function explodeUppercase($string)
    {
        return preg_replace('/([a-z])([A-Z])/', '$1 $2', $string);
    }
}

if (! function_exists('getOnlyNumbers')) {
    function getOnlyNumbers($alphaNumeric): string
    {
        return preg_replace("/[^-0-9\.]/", '', $alphaNumeric);
    }
}

if (! function_exists('getOnlyAlphabets')) {
    function getOnlyAlphabets($alphaNumeric): string
    {
        return preg_replace("/[^-_aA-zZ\.]/", '', $alphaNumeric);
    }
}

if (! function_exists('getPercentOfNumber')) {
    function getPercentOfNumber($percent, $number): int
    {
        return abs(($percent / 100) * $number);
    }
}

if (! function_exists('getValueFromJsonArray')) {
    function getValueFromJsonArray($jsonArray, $key) {
        $keys = explode('.', $key);
        $currentValue = $jsonArray;

        foreach ($keys as $k) {
            if (!isset($currentValue[$k])) {
                return null; // Key does not exist
            }
            $currentValue = $currentValue[$k];
        }

        return $currentValue;
    }
}
