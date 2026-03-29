<?php

namespace App\Services;

use Aws\Sqs\SqsClient;
use Aws\Exception\AwsException;
use Illuminate\Support\Facades\Log;

class SqsService
{
    protected SqsClient $client;
    protected string $queueUrl;

    public function __construct()
    {       
        $this->client = new SqsClient([
            'version' => 'latest',
            'region'  => config('aws.region', env('AWS_DEFAULT_REGION', 'ap-south-1')),
            'credentials' => [
                'key'    => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);
        $this->queueUrl = rtrim(env('AWS_SQS_PREFIX'), '/') . '/' . env('AWS_SQS_QUEUE');
    }

    /**
     * Send a message to AWS SQS with full error handling and logging.
     *
     * @param  array  $payload
     * @param  array  $options
     * @return array|null
     */
    public function sendMessage(array $payload, array $options = []): ?array
    {       
        // ✅ Validate payload before sending
        if (empty($payload)) {
            Log::warning('SQSService: Empty payload, skipping message send.');
            return null;
        }

        try {
            $params = array_merge([
                'QueueUrl'    => $this->queueUrl,
                'MessageBody' => json_encode($payload, JSON_THROW_ON_ERROR),
            ], $options);

            $response = $this->client->sendMessage($params);

            Log::info('✅ SQS message sent successfully', [
                'queue'      => $this->queueUrl,
                'MessageId'  => $response['MessageId'] ?? null,
                'payload'    => $payload,
            ]);

            return [
                'status' => 'success',
                'message_id' => $response['MessageId'] ?? null,
                'payload' => $payload,
            ];
        } catch (AwsException $e) {
            // Catch AWS SDK related exceptions
            Log::error('❌ AWS SQS error', [
                'error' => $e->getAwsErrorMessage(),
                'code'  => $e->getAwsErrorCode(),
                'type'  => $e->getAwsErrorType(),
                'trace' => $e->getTraceAsString(),
            ]);
        } catch (\JsonException $e) {
            // Handle bad payload JSON encoding
            Log::error('❌ JSON encoding failed for SQS payload', [
                'payload' => $payload,
                'error'   => $e->getMessage(),
            ]);
        } catch (\Throwable $e) {
            // Catch any other runtime error
            Log::error('❌ Unexpected error while sending message to SQS', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        return [
            'status' => 'failed',
            'payload' => $payload,
        ];
    }
}
