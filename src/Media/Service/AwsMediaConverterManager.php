<?php

namespace App\Media\Service;

use App\Media\Dto\MediaConverterOutputDto;
use App\Media\Enumeration\MediaItemType;
use Aws\MediaConvert\MediaConvertClient;

class AwsMediaConverterManager
{
    public function __construct(
        private readonly MediaConvertClient $mediaConvertClient,
        private readonly string $awsMediaConvertQueueArn,
        private readonly string $awsMediaConvertRoleArn,
    ) {
    }

    public function createJob(array $inputs, array $outputGroups): void
    {
        $this->mediaConvertClient->createJob([
            "Role" => $this->awsMediaConvertRoleArn,
            "Queue" => $this->awsMediaConvertQueueArn,
            "Settings" => $this->prepareJobSettings($inputs, $outputGroups),
            "AccelerationSettings" => [
                "Mode" => "DISABLED",
            ],
            "StatusUpdateInterval" => "SECONDS_60",
            "Priority" => 0,
        ]);
    }

    public function prepareVideoInput(string $fileInput): array
    {
        return [
            "AudioSelectors" => [
                "Audio Selector 1" => [
                    "DefaultSelection" => "DEFAULT",
                ],
            ],
            "VideoSelector" => [
                "Rotate" => "AUTO",
            ],
            "TimecodeSource" => "ZEROBASED",
            "FileInput" => $fileInput,
        ];
    }

    public function prepareVideoOutput(MediaConverterOutputDto $mediaConverterOutputDto): array
    {
        return [
            'ContainerSettings' => [
                'Container' => 'MP4',
                'Mp4Settings' => [
                ],
            ],
            'VideoDescription' => [
                'Width' => $mediaConverterOutputDto->width,
                'CodecSettings' => [
                    'Codec' => 'H_264',
                    'H264Settings' => [
                        'MaxBitrate' => $mediaConverterOutputDto->maxBitrate,
                        'RateControlMode' => 'QVBR',
                        'QvbrSettings' => [
                            'QvbrQualityLevel' => 9,
                        ],
                        'SceneChangeDetect' => 'TRANSITION_DETECTION',
                        'QualityTuningLevel' => 'SINGLE_PASS',
                    ],
                ],
            ],
            'AudioDescriptions' => [
                0 => [
                    'AudioSourceName' => 'Audio Selector 1',
                    'CodecSettings' => [
                        'Codec' => 'AAC',
                        'AacSettings' => [
                            'Bitrate' => 96000,
                            'CodingMode' => 'CODING_MODE_2_0',
                            'SampleRate' => 48000,
                        ],
                    ],
                ],
            ],
            'Extension' => 'mp4',
            'NameModifier' => $mediaConverterOutputDto->nameModifier,
        ];
    }

    public function prepareImageOutput(MediaConverterOutputDto $mediaConverterOutputDto): array
    {
        return [
            'ContainerSettings' => [
                'Container' => 'RAW',
            ],
            'VideoDescription' => [
                'Width' => $mediaConverterOutputDto->width,
                'ScalingBehavior' => 'DEFAULT',
                'CodecSettings' => [
                    'Codec' => 'FRAME_CAPTURE',
                    'FrameCaptureSettings' => [
                        'MaxCaptures' => 1,
                        'Quality' => 80,
                    ],
                ],
            ],
            'NameModifier' => $mediaConverterOutputDto->nameModifier,
        ];
    }

    private function prepareJobSettings(array $inputs, array $outputGroups): array
    {
        return [
            'TimecodeConfig' => [
                'Source' => 'ZEROBASED',
            ],
            'Inputs' => $inputs,
            'OutputGroups' => $outputGroups,
        ];
    }

    public function prepareOutputGroup(
        array $mediaConverterOutputDtos,
        string $destination,
        ?string $groupName = null,
    ): array {
        $outputs = [];

        /** @var MediaConverterOutputDto $mediaConverterOutputDto */
        foreach ($mediaConverterOutputDtos as $mediaConverterOutputDto) {
            if (MediaItemType::isVideo($mediaConverterOutputDto->mediaItemType)) {
                $outputs[] = $this->prepareVideoOutput($mediaConverterOutputDto);

                continue;
            }

            if (MediaItemType::isImage($mediaConverterOutputDto->mediaItemType)) {
                $outputs[] = $this->prepareImageOutput($mediaConverterOutputDto);
            }
        }

        return [
            [
                'Name' => $groupName ?? pathinfo($destination, PATHINFO_BASENAME),
                'Outputs' => $outputs,
                'OutputGroupSettings' => [
                    'Type' => 'FILE_GROUP_SETTINGS',
                    'FileGroupSettings' => [
                        'Destination' => $destination,
                        'DestinationSettings' => [
                            'S3Settings' => [
                                'AccessControl' => [
                                    'CannedAcl' => 'BUCKET_OWNER_FULL_CONTROL',
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        ];
    }
}