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
            'max_tokens' => 8192,
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
Eres un experto en pedagogía y diseño curricular especializado en **aprendizaje basado en proyectos (Project-Based Learning)**.

Tu tarea es generar planes de estudio altamente efectivos y personalizados.

REGLAS FUNDAMENTALES:
1. Cada tema debe tener **20% teoría concisa** y **80% práctica**.
2. Los ejercicios prácticos deben ser **progresivos**: comenzar con ejercicios simples adaptados al nivel del estudiante y aumentar en complejidad.
3. Para cada tema, proporciona ejemplos concretos y ejercicios que el estudiante pueda resolver.
4. Los proyectos deben ser reales y útiles, no ejercicios de relleno.
5. Adapta el nivel de dificultad: para principiantes los ejercicios son muy guiados, para avanzados son más abiertos.
6. Incluye ejercicios de distintos tipos: completar código, corregir errores, construir desde cero, mini-proyectos.
7. Usa la técnica de "scaffolding": andamiaje donde primero das estructura y luego la quitas gradualmente.

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
          "theory": "Explicación teórica concisa (2-3 párrafos máximo)",
          "practice": "Descripción del ejercicio práctico. Debe ser específico y accionable",
          "examples": "Ejemplo concreto de lo que se espera (código, texto, o resultado esperado)",
          "difficulty": "beginner|intermediate|advanced",
          "estimated_minutes": 45
        }
      ]
    }
  ]
}
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
Genera un plan de aprendizaje personalizado con los siguientes datos:

**Habilidad a aprender:** {$skill}
**Nivel actual del estudiante:** {$level}
**Horas disponibles por semana:** {$hoursPerWeek}
**Duración del plan:** {$weeks} semanas
**Meta u objetivo:** {$goals}
**Estilo de aprendizaje:** {$learningStyle}
**Área de enfoque:** {$focus}

IMPORTANTE: 
- Asegúrate de que los ejercicios sean **progresivos** en dificultad.
- Para nivel {$level}, los primeros ejercicios deben ser muy guiados.
- Cada tema debe tener teoría breve y mucha práctica.
- Incluye ejercicios prácticos como "suma dos números", "crea una función que..." etc., adaptados al nivel.
- El JSON debe ser válido y completo.
EOT;
    }
}
