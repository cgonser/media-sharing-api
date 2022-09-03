<?php

namespace App\Media\Service;

use App\Media\Dto\MediaConverterInsertableImageDto;
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

    public function createJob(array $inputs, array $outputGroups, ?array $userMetadata = []): string
    {
        $return = $this->mediaConvertClient->createJob([
            'Role' => $this->awsMediaConvertRoleArn,
            'Queue' => $this->awsMediaConvertQueueArn,
            'Settings' => $this->prepareJobSettings($inputs, $outputGroups),
            'AccelerationSettings' => [
                'Mode' => 'DISABLED',
            ],
            'UserMetadata' => $userMetadata,
            'StatusUpdateInterval' => 'SECONDS_10',
            'Priority' => 0,
        ]);

        return $return->get('Job')['Id'];
    }

    public function prepareVideoInput(
        string $videoFileInput,
        ?string $audioSelector = '1',
        ?string $audioFileInput = null,
        ?string $audioOffset = null,
    ): array {
        $input = [
            'AudioSelectors' => [
                'Audio Selector '.$audioSelector => [
                    'DefaultSelection' => 'DEFAULT',
                ],
            ],
            'VideoSelector' => [
                'Rotate' => 'AUTO',
            ],
            'TimecodeSource' => 'ZEROBASED',
            'FileInput' => $videoFileInput,
        ];

        if (null !== $audioFileInput) {
            $input['AudioSelectors']['Audio Selector '.$audioSelector]['ExternalAudioFileInput'] = $audioFileInput;
        }
        if (null !== $audioOffset) {
            $input['AudioSelectors']['Audio Selector '.$audioSelector]['Offset'] = -1 * $audioOffset;
        }

        return $input;
    }

    public function prepareVideoOutput(MediaConverterOutputDto $mediaConverterOutputDto): array
    {
        $output = [
            'ContainerSettings' => [
                'Container' => 'MP4',
                'Mp4Settings' => [
                ],
            ],
            'VideoDescription' => [
                'Width' => $mediaConverterOutputDto->width,
                'VideoPreprocessors' => [],
                'CodecSettings' => [
                    'Codec' => 'H_264',
                    'H264Settings' => [
                        'FramerateDenominator' => 1,
                        'MaxBitrate' => $mediaConverterOutputDto->maxBitrate,
                        'FramerateControl' => 'SPECIFIED',
                        'RateControlMode' => 'QVBR',
                        'QvbrSettings' => [
                            'QvbrQualityLevel' => 9,
                        ],
                        'FramerateNumerator' => 15,
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

        if (!empty($mediaConverterOutputDto->insertableImages)) {
            $output['VideoDescription']['VideoPreprocessors']['ImageInserter'] = [
                'InsertableImages' => [],
            ];

            foreach ($mediaConverterOutputDto->insertableImages as $insertableImageDto) {
                $output['VideoDescription']['VideoPreprocessors']['ImageInserter']['InsertableImages'][] = $this->generateImageInserter(
                    $insertableImageDto
                );
            }
        }

        return $output;
    }

    private function generateImageInserter(MediaConverterInsertableImageDto $insertableImageDto): array
    {
        $imageInserter = [
            'Width' => $insertableImageDto->width,
            'Height' => $insertableImageDto->height,
            'ImageX' => $insertableImageDto->x,
            'ImageY' => $insertableImageDto->y,
            'Layer' => $insertableImageDto->layer,
            'ImageInserterInput' => $insertableImageDto->input,
            'StartTime' => $insertableImageDto->startTime,
            'Opacity' => $insertableImageDto->opacity,
        ];

        if (null !== $insertableImageDto->duration) {
            $imageInserter['Duration'] = $insertableImageDto->duration;
        }

        return $imageInserter;
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
            ],
        ];
    }
}
