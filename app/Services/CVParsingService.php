<?php

namespace App\Services;

use Smalot\PdfParser\Parser as PdfParser;
use PhpOffice\PhpWord\IOFactory;
use App\Models\Resume;
use App\Models\JobApplication;
use Illuminate\Support\Str;

class CVParsingService
{
    public function parseCVFromFile(string $filePath, string $mimeType): array
    {
        if (str_contains($mimeType, 'pdf')) {
            return $this->parsePdf($filePath);
        } elseif (str_contains($mimeType, 'word') || str_contains($mimeType, 'docx')) {
            return $this->parseDocx($filePath);
        } elseif (str_contains($mimeType, 'plain')) {
            return $this->parseText($filePath);
        }

        throw new \Exception('Unsupported file format');
    }

    private function parsePdf(string $filePath): array
    {
        $parser = new PdfParser();
        $pdf = $parser->parseFile($filePath);
        $text = '';

        foreach ($pdf->getPages() as $page) {
            $text .= $page->getText();
        }

        return $this->extractData($text);
    }

    private function parseDocx(string $filePath): array
    {
        $phpWord = IOFactory::load($filePath);
        $text = '';

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if (method_exists($element, 'getText')) {
                    $text .= $element->getText() . "\n";
                }
            }
        }

        return $this->extractData($text);
    }

    private function parseText(string $filePath): array
    {
        $text = file_get_contents($filePath);
        return $this->extractData($text);
    }

    private function extractData(string $text): array
    {
        return [
            'raw_text' => $text,
            'skills' => $this->extractSkills($text),
            'experience' => $this->extractExperience($text),
            'education' => $this->extractEducation($text),
            'certifications' => $this->extractCertifications($text),
            'contact' => $this->extractContact($text),
            'keywords' => $this->extractKeywords($text),
        ];
    }

    private function extractSkills(string $text): array
    {
        $commonSkills = [
            'PHP', 'Laravel', 'JavaScript', 'TypeScript', 'Python', 'Java', 'C++', 'C#', '.NET',
            'React', 'Vue', 'Angular', 'Node.js', 'Express', 'Django', 'Flask',
            'SQL', 'MySQL', 'PostgreSQL', 'MongoDB', 'Redis', 'Elasticsearch',
            'AWS', 'Azure', 'GCP', 'Docker', 'Kubernetes', 'CI/CD',
            'Git', 'Linux', 'Windows Server', 'Nginx', 'Apache',
            'HTML', 'CSS', 'Sass', 'Tailwind', 'Bootstrap',
            'REST API', 'GraphQL', 'WebSocket', 'SOAP',
            'Agile', 'Scrum', 'Kanban', 'DevOps',
            'Project Management', 'Leadership', 'Communication', 'Problem Solving',
            'Machine Learning', 'Data Science', 'Data Analysis', 'BI Tools',
            'Figma', 'Adobe XD', 'Photoshop', 'Illustrator',
            'Salesforce', 'SAP', 'Oracle', 'Tableau',
        ];

        $foundSkills = [];
        foreach ($commonSkills as $skill) {
            if (stripos($text, $skill) !== false) {
                $foundSkills[] = $skill;
            }
        }

        return array_unique($foundSkills);
    }

    private function extractExperience(string $text): array
    {
        $experiences = [];

        // Look for common experience patterns
        $patterns = [
            '/([0-9]{1,2})\s*(?:years?|yrs)\s*(?:of\s*)?(?:experience|exp)/',
            '/(?:worked|worked as|worked for)\s*([^.!?\n]{10,80})/i',
            '/(?:role|position):\s*([^\n]+)/i',
            '/(?:title):\s*([^\n]+)/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match_all($pattern, $text, $matches)) {
                $experiences = array_merge($experiences, $matches[1] ?? []);
            }
        }

        return array_unique(array_filter($experiences));
    }

    private function extractEducation(string $text): array
    {
        $education = [];
        $educationPatterns = [
            '/(?:bachelor|master|phd|diploma|degree|certification)\s*(?:of|in)?\s*([^\n.!?]{10,100})/i',
            '/([A-Z][^,\n.!?]{10,100})\s*(?:university|college|institute|school)/i',
        ];

        foreach ($educationPatterns as $pattern) {
            if (preg_match_all($pattern, $text, $matches)) {
                $education = array_merge($education, $matches[1] ?? []);
            }
        }

        return array_unique(array_filter($education));
    }

    private function extractCertifications(string $text): array
    {
        $certifications = [];
        $certPatterns = [
            '/(?:certified|certification|certified in):\s*([^\n.!?]+)/i',
            '/(?:AWS|Microsoft|Google|Cisco|Oracle|Kubernetes|Docker)\s*(?:Certified|Certification)\s*([^\n.!?]+)?/i',
        ];

        foreach ($certPatterns as $pattern) {
            if (preg_match_all($pattern, $text, $matches)) {
                $certifications = array_merge($certifications, $matches[0] ?? []);
            }
        }

        return array_unique(array_filter($certifications));
    }

    private function extractContact(string $text): array
    {
        $contact = [];

        // Extract emails
        if (preg_match_all('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $text, $matches)) {
            $contact['emails'] = array_unique($matches[0]);
        }

        // Extract phone numbers
        if (preg_match_all('/(?:\+?\d{1,3}[-.\s]?)?\(?(?:\d{3})\)?[-.\s]?\d{3}[-.\s]?\d{4}/', $text, $matches)) {
            $contact['phones'] = array_unique($matches[0]);
        }

        return $contact;
    }

    private function extractKeywords(string $text): array
    {
        // Extract important keywords (nouns, technical terms)
        $keywords = [];
        
        // Remove common words
        $commonWords = ['the', 'a', 'an', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for', 'of', 'with', 'by', 'from'];
        
        // Split text into words
        $words = preg_split('/\s+|[,;.!?()"\'-]+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        
        foreach ($words as $word) {
            if (strlen($word) > 4 && !in_array(strtolower($word), $commonWords)) {
                $keywords[] = $word;
            }
        }

        // Get most frequent keywords
        $frequency = array_count_values(array_map('strtolower', $keywords));
        arsort($frequency);
        
        return array_keys(array_slice($frequency, 0, 20));
    }

    public function scoreAgainstJob(Resume $resume, \App\Models\Job $job): array
    {
        $parsedData = $resume->parsed_data ?? [];
        $jobDescription = $job->description . ' ' . $job->requirements;

        $scores = [
            'keyword_match' => $this->scoreKeywordMatch($parsedData, $jobDescription),
            'skill_match' => $this->scoreSkillMatch($parsedData),
            'education_match' => $this->scoreEducationMatch($parsedData),
            'experience_match' => $this->scoreExperienceMatch($parsedData),
        ];

        // Calculate weighted average
        $weights = [
            'keyword_match' => 0.3,
            'skill_match' => 0.4,
            'education_match' => 0.15,
            'experience_match' => 0.15,
        ];

        $totalScore = 0;
        foreach ($scores as $metric => $score) {
            $totalScore += ($score * $weights[$metric]);
        }

        return [
            'total_score' => round($totalScore, 2),
            'breakdown' => $scores,
            'recommendation' => $this->getRecommendation($totalScore),
        ];
    }

    private function scoreKeywordMatch(array $parsedData, string $jobDescription): float
    {
        $cvKeywords = $parsedData['keywords'] ?? [];
        $cvText = $parsedData['raw_text'] ?? '';
        
        if (empty($cvKeywords)) {
            return 0;
        }

        $matches = 0;
        foreach ($cvKeywords as $keyword) {
            if (stripos($jobDescription, $keyword) !== false) {
                $matches++;
            }
        }

        return ($matches / count($cvKeywords)) * 100;
    }

    private function scoreSkillMatch(array $parsedData): float
    {
        $cvSkills = $parsedData['skills'] ?? [];
        
        if (empty($cvSkills)) {
            return 0;
        }

        // Basic scoring - in production, use NLP or more sophisticated matching
        return (count($cvSkills) > 0) ? 70 : 0;
    }

    private function scoreEducationMatch(array $parsedData): float
    {
        $education = $parsedData['education'] ?? [];
        
        $desiredDegrees = ['bachelor', 'master', 'phd', 'degree'];
        
        $matches = 0;
        foreach ($education as $edu) {
            foreach ($desiredDegrees as $degree) {
                if (stripos($edu, $degree) !== false) {
                    $matches++;
                    break;
                }
            }
        }

        return $matches > 0 ? 85 : 40;
    }

    private function scoreExperienceMatch(array $parsedData): float
    {
        $rawText = $parsedData['raw_text'] ?? '';
        
        // Look for years of experience
        if (preg_match('/(\d{1,2})\s*(?:years?|yrs)/', $rawText, $matches)) {
            $years = (int) $matches[1];
            
            if ($years >= 5) {
                return 95;
            } elseif ($years >= 3) {
                return 75;
            } elseif ($years >= 1) {
                return 50;
            }
        }

        return 30;
    }

    private function getRecommendation(float $score): string
    {
        if ($score >= 80) {
            return 'Highly Recommended';
        } elseif ($score >= 60) {
            return 'Recommended';
        } elseif ($score >= 40) {
            return 'Consider';
        } else {
            return 'Not Recommended';
        }
    }
}
