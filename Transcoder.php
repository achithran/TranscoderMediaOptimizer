<?php
class Transcoder
{
    private $transcoderClient;

    public function __construct($transcoderClient)
    {
        $this->transcoderClient = $transcoderClient;
    }

    public function transcodeFile($file)
    {
        $inputFile = $file['name'];
        $outputPath = 'uploads/';

        $outputSettings = [
            [
                'PresetId' => '1351620000001-000010', // 144p
                'Suffix' => '_144p'
            ],
            [
                'PresetId' => '1351620000001-000020', // 240p
                'Suffix' => '_240p'
            ],
            [
                'PresetId' => '1351620000001-000030', // 360p
                'Suffix' => '_360p'
            ],
            [
                'PresetId' => '1351620000001-000040', // 480p
                'Suffix' => '_480p'
            ],
            [
                'PresetId' => '1351620000001-000050', // 720p
                'Suffix' => '_720p'
            ],
            [
                'PresetId' => '1351620000001-000060', // 1080p
                'Suffix' => '_1080p'
            ]
        ];

        $transcodedFiles = [];

        foreach ($outputSettings as $outputSetting) {
            $outputKey = basename($inputFile, '.mp4') . $outputSetting['Suffix'] . '.mp4';

            $params = [
                'PipelineId' => '1687113866248-zzgh39',
                'OutputKeyPrefix' => $outputPath,
                'Input' => [
                    'Key' => $inputFile,
                ],
                'Outputs' => [
                    [
                        'Key' => $outputKey,
                        'PresetId' => $outputSetting['PresetId'],
                    ]
                ]
            ];

            $result = $this->transcoderClient->createJob($params);

            if (isset($result['Job']['Id'])) {
                $transcodedFiles[] = $outputKey;
            }
        }

        return $transcodedFiles;
    }
}
