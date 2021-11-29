<?php

return [
    'http_timeout' => 60 * 60,
    'chunk_mins' => 10,
    'connect_timeout' => 60,
    'secret_key' => env('AWS_SECRET_ACCESS_KEY','GW3XivW7ViJcUZMqCxvnWkxLQ3UTsQFesANT71o4'),
    'access_key' => env('AWS_ACCESS_KEY_ID','AKIA3FRC4LLAC4NFZRJX'),
    'host' => env('AWS_SAGEMAKER_HOST','runtime.sagemaker.us-east-2.amazonaws.com'),
    'uri' => env('AWS_SAGEMAKER_URI','/endpoints/pytorch-inference-2021-09-10-18-21-32-194/invocations'),
    'region' => env('AWS_DEFAULT_REGION','us-east-2'),
    'service' => 'sagemaker',
    'lambda_url' => env('AWS_SAGEMAKER_URL','https://runtime.sagemaker.us-east-2.amazonaws.com/endpoints/pytorch-inference-2021-09-10-18-21-32-194/invocations')
];