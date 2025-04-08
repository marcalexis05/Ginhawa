<?php
header('Content-Type: application/json');
include("../connection.php");

// Sample symptom-to-advice mapping (expand as needed)
$symptomAdvice = [
    'anxiety' => 'Practice deep breathing exercises and consider cognitive behavioral therapy.',
    'depression' => 'Engage in regular physical activity and consider speaking to a therapist.',
    'insomnia' => 'Establish a consistent sleep schedule and avoid caffeine before bed.',
    'stress' => 'Try mindfulness meditation and time management techniques.',
    'fatigue' => 'Ensure adequate rest and check for underlying medical conditions.',
    'panic' => 'Learn grounding techniques and consult a specialist for panic disorder.',
    'mood_swings' => 'Monitor your mood changes and consider therapy for emotional regulation.'
];

// Symptom-to-specialty mapping
$symptomSpecialty = [
    'anxiety' => 'Psychiatry',
    'depression' => 'Psychiatry',
    'insomnia' => 'Sleep Medicine',
    'stress' => 'Psychology',
    'fatigue' => 'General Medicine',
    'panic' => 'Psychiatry',
    'mood_swings' => 'Psychology'
];

$input = json_decode(file_get_contents('php://input'), true);
$symptoms = $input['symptoms'] ?? [];

if (empty($symptoms)) {
    echo json_encode(['error' => 'No symptoms provided']);
    exit;
}

// Generate advice
$advice = [];
foreach ($symptoms as $symptom) {
    if (isset($symptomAdvice[$symptom])) {
        $advice[] = $symptomAdvice[$symptom];
    }
}
$adviceText = implode(' ', array_unique($advice));

// Find matching doctors
$specialties = array_map(function($symptom) use ($symptomSpecialty) {
    return $symptomSpecialty[$symptom] ?? '';
}, $symptoms);
$specialties = array_unique(array_filter($specialties));

$doctors = [];
if (!empty($specialties)) {
    $placeholders = implode(',', array_fill(0, count($specialties), '?'));
    $query = "SELECT d.docname, s.sname FROM doctor d 
              JOIN specialties s ON d.specialties = s.id 
              WHERE s.sname IN ($placeholders)";
    $stmt = $database->prepare($query);
    $stmt->bind_param(str_repeat('s', count($specialties)), ...$specialties);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $doctors[] = ['name' => $row['docname'], 'specialty' => $row['sname']];
    }
}

echo json_encode([
    'advice' => $adviceText ?: 'Consult a professional for personalized advice.',
    'doctors' => $doctors
]);
?>