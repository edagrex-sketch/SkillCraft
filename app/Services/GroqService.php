<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GroqService
{
    protected string $apiKey;
    protected string $baseUrl = 'https://api.groq.com/openai/v1';

    public function __construct()
    {
        $this->apiKey = config('services.groq.key');
    }

    public function generateStudyPlan(array $questionnaireData): array
    {
        $prompt = $this->buildPlanPrompt($questionnaireData);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post("{$this->baseUrl}/chat/completions", [
            'model' => 'llama-3.3-70b-versatile',
            'messages' => [
                ['role' => 'system', 'content' => $this->systemPrompt()],
                ['role' => 'user', 'content' => $prompt],
            ],
            'temperature' => 0.7,
            'max_tokens' => 12000,
            'response_format' => ['type' => 'json_object'],
        ]);

        if ($response->failed()) {
            Log::error('Groq API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \Exception('Error al generar el plan de estudio: ' . $response->body());
        }

        $content = $response->json('choices.0.message.content');

        return json_decode($content, true);
    }

    protected function systemPrompt(): string
    {
        return <<<EOT
Eres un experto en pedagogía y ciencia del aprendizaje especializado en diseño curricular basado en evidencia.

Tu metodología combina:
- **Active Recall** (recuperación activa de memoria)
- **Spaced Repetition** (repetición espaciada)
- **Elaboración** (explicación y conexión de conceptos)
- **Aprendizaje Basado en Proyectos** (PBL)
- **Andamiaje Cognitivo** (scaffolding: guiado → estructurado → abierto)
- **Técnica Feynman** (explicar en términos simples)
- **Bloom's Taxonomy** (recordar → entender → aplicar → analizar → evaluar → crear)

REGLAS FUNDAMENTALES:
1. Cada tema debe tener **20% teoría concisa** y **80% práctica progresiva**.
2. La práctica debe tener 3 niveles de andamiaje:
   - **Guiado**: ejercicios con plantillas, opciones múltiples, completar código
   - **Estructurado**: especificaciones claras pero sin plantilla
   - **Abierto**: problema sin instrucciones, el estudiante decide el enfoque
3. Incluye siempre una **pregunta de active recall** para que el estudiante verifique su comprensión.
4. Proporciona **analogías** que conecten el concepto con algo familiar.
5. Incluye **errores comunes** y cómo evitarlos (misconception handling).
6. Cada tema debe terminar con un **mini-proyecto o desafío** que conecte con el proyecto general.
7. Incluye **preguntas de reflexión** para consolidar el aprendizaje.
8. Los proyectos deben ser reales y útiles, no ejercicios de relleno.
9. Para principiantes los ejercicios son muy guiados; para avanzados son más abiertos.
10. Usa la técnica de "scaffolding": primero das estructura y luego la quitas gradualmente.

Responde SIEMPRE en JSON con esta estructura exacta:
{
  "title": "Nombre del plan",
  "description": "Descripción general del plan",
  "duration_weeks": 8,
  "hours_per_week": 5,
  "weeks": [
    {
      "week_number": 1,
      "title": "Fundamentos y primer proyecto",
      "summary": "Descripción de la semana",
      "topics": [
        {
          "title": "Nombre del tema",
          "content_type": "exercise|project|video|article|quiz",
          "difficulty": "beginner|intermediate|advanced",
          "estimated_minutes": 45,
          "theory": "Explicación teórica concisa (2-3 párrafos). Enfócate en el 'por qué' y el 'cómo'.",
          "practice": "Descripción del ejercicio práctico principal",
          "examples": "Ejemplo concreto de código o resultado esperado",

          "context": "Contexto real: ¿por qué este concepto es importante? ¿Dónde se usa en el mundo real? (1-2 párrafos)",
          "active_recall": "Pregunta para que el estudiante se autoevalúe antes de continuar. Debe requerir explicación, no solo recordar.",
          "practice_guided": "Ejercicio guiado: el más simple, con plantilla o pasos muy detallados. Ideal para empezar.",
          "practice_structured": "Ejercicio estructurado: especificación clara pero sin plantilla. El estudiante decide cómo implementar.",
          "practice_open": "Ejercicio abierto: problema sin instrucciones. El estudiante diseña su solución desde cero.",
          "reflection": ["Pregunta de reflexión 1", "Pregunta de reflexión 2", "Pregunta de reflexión 3"],
          "mistakes": ["Error común 1 y cómo evitarlo", "Error común 2 y cómo evitarlo"],
          "analogies": ["Analogía 1 para entender el concepto"],
          "challenge": "Mini-proyecto o desafío que aplica el concepto en un contexto real y se conecta con el proyecto general del plan",
          "review_questions": ["Pregunta de repaso 1 para sesión futura", "Pregunta de repaso 2 para sesión futura"]
        }
      ]
    }
  ]
}

IMPORTANTE:
- El campo "theory" debe ser conciso pero profundo
- Los ejercicios prácticos deben ser progresivos en dificultad
- Las analogías deben ser intuitivas y cercanas a la experiencia del estudiante
- Las preguntas de active recall deben requerir esfuerzo cognitivo (no solo copiar y pegar)
EOT;
    }

    protected function buildPlanPrompt(array $data): string
    {
        $skill = $data['skill'] ?? 'no especificada';
        $level = $data['level'] ?? 'beginner';
        $hoursPerWeek = $data['hours_per_week'] ?? 3;
        $weeks = $data['weeks'] ?? 8;
        $goals = $data['goals'] ?? 'Aprender desde cero';
        $learningStyle = $data['learning_style'] ?? 'practico';
        $focus = $data['focus'] ?? 'general';

        return <<<EOT
Genera un plan de aprendizaje personalizado basado en Project-Based Learning con los siguientes datos:

**Habilidad a aprender:** {$skill}
**Nivel actual del estudiante:** {$level}
**Horas disponibles por semana:** {$hoursPerWeek}
**Duración del plan:** {$weeks} semanas
**Meta u objetivo:** {$goals}
**Estilo de aprendizaje:** {$learningStyle}
**Área de enfoque:** {$focus}

IMPORTANTE:
- Cada tema debe seguir la estructura pedagógica completa (theory, practice, examples, context, active_recall, practice_guided, practice_structured, practice_open, reflection, mistakes, analogies, challenge, review_questions)
- Para nivel {$level}, adapta la dificultad de los ejercicios: los primeros deben ser muy guiados
- La duración total por tema debe sumar aproximadamente {$hoursPerWeek} horas por semana distribuidas entre todos los temas de la semana
- El JSON debe ser válido y completo, sin truncar ningún campo
- Cada semana debe tener entre 2 y 4 temas como máximo
- Ajusta la complejidad según el nivel: {$level}
- El proyecto final debe ser algo que el estudiante pueda mostrar como portafolio
EOT;
    }
}
