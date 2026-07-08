<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DicomImagingService
{
    private string $orthancUrl;

    private string $username;

    private string $password;

    public function __construct()
    {
        $this->orthancUrl = config('services.orthanc.url', 'https://orthanc:8042');
        $this->username = config('services.orthanc.username', '');
        $this->password = config('services.orthanc.password', '');
    }

    /**
     * Query studies for a patient by MRN.
     */
    public function findStudiesByMrn(string $mrn): array
    {
        $response = Http::withBasicAuth($this->username, $this->password)
            ->timeout(30)
            ->post("{$this->orthancUrl}/tools/find", [
                'Level' => 'Study',
                'Query' => [
                    'PatientID' => $mrn,
                ],
                'Expand' => true,
            ]);

        if ($response->failed()) {
            Log::error('Orthanc DICOM query failed', [
                'mrn' => $mrn,
                'status' => $response->status(),
            ]);

            return ['success' => false, 'studies' => []];
        }

        $studies = $response->json();
        $result = [];

        foreach ((array) $studies as $study) {
            $result[] = [
                'study_uid' => $study['ID'] ?? '',
                'study_date' => $study['MainDicomTags']['StudyDate'] ?? '',
                'study_description' => $study['MainDicomTags']['StudyDescription'] ?? '',
                'modality' => $study['MainDicomTags']['ModalitiesInStudy'] ?? '',
                'instance_count' => $study['MainDicomTags']['NumberOfStudyRelatedInstances'] ?? 0,
                'series_count' => $study['MainDicomTags']['NumberOfStudyRelatedSeries'] ?? 0,
            ];
        }

        return ['success' => true, 'studies' => $result];
    }

    /**
     * Retrieve a DICOM study's metadata including series and instances.
     */
    public function getStudyDetail(string $studyId): array
    {
        $response = Http::withBasicAuth($this->username, $this->password)
            ->timeout(15)
            ->get("{$this->orthancUrl}/studies/{$studyId}");

        if ($response->failed()) {
            return ['success' => false, 'error' => 'Study not found'];
        }

        return ['success' => true, 'study' => $response->json()];
    }

    /**
     * Get a thumbnail preview for a DICOM series (first instance).
     */
    public function getSeriesThumbnail(string $seriesId): ?string
    {
        $response = Http::withBasicAuth($this->username, $this->password)
            ->timeout(30)
            ->get("{$this->orthancUrl}/series/{$seriesId}/preview");

        if ($response->failed()) {
            return null;
        }

        return base64_encode($response->body());
    }
}
