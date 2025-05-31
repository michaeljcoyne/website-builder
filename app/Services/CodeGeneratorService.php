<?php

namespace App\Services;

use App\Models\Project;

class CodeGeneratorService
{
    public function generate(Project $project, string $format = 'html'): string
    {
        return match($format) {
            'html' => $this->generateHTML($project),
            'vue' => $this->generateVue($project),
            'react' => $this->generateReact($project),
            default => throw new \InvalidArgumentException("Unsupported format: {$format}")
        };
    }

    private function generateHTML(Project $project): string
    {
        $elements = $project->elements ?? [];
        $styles = $this->generateCSS($elements);
        $body = $this->generateHTMLElements($elements);

        return "<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>{$project->name}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }
        .canvas { position: relative; min-height: 100vh; }
        {$styles}
    </style>
</head>
<body>
    <div class=\"canvas\">
        {$body}
    </div>
</body>
</html>";
    }

    private function generateCSS(array $elements): string
    {
        $css = '';
        foreach ($elements as $element) {
            $css .= $this->generateElementCSS($element);
        }
        return $css;
    }

    private function generateElementCSS(array $element): string
    {
        $id = $element['id'];
        $props = $element['properties'] ?? [];

        $styles = [
            "position: absolute",
            "left: {$element['x']}px",
            "top: {$element['y']}px",
            "width: {$element['width']}px",
            "height: {$element['height']}px"
        ];

        switch ($element['type']) {
            case 'text':
            case 'heading':
                if (isset($props['fontSize'])) $styles[] = "font-size: {$props['fontSize']}px";
                if (isset($props['color'])) $styles[] = "color: {$props['color']}";
                break;
            case 'button':
                if (isset($props['backgroundColor'])) $styles[] = "background-color: {$props['backgroundColor']}";
                if (isset($props['textColor'])) $styles[] = "color: {$props['textColor']}";
                $styles[] = "border: none; border-radius: 4px; cursor: pointer; padding: 10px 20px;";
                break;
        }

        return "#{$id} { " . implode('; ', $styles) . " }\n";
    }

    private function generateHTMLElements(array $elements): string
    {
        $html = '';
        foreach ($elements as $element) {
            $html .= $this->generateHTMLElement($element);
        }
        return $html;
    }

    private function generateHTMLElement(array $element): string
    {
        $id = $element['id'];
        $props = $element['properties'] ?? [];

        return match($element['type']) {
            'text' => "<div id=\"{$id}\">{$props['content']}</div>\n",
            'heading' => "<h{$props['level']} id=\"{$id}\">{$props['content']}</h{$props['level']}>\n",
            'button' => "<button id=\"{$id}\">{$props['text']}</button>\n",
            'image' => "<img id=\"{$id}\" src=\"{$props['src']}\" alt=\"{$props['alt']}\">\n",
            default => "<div id=\"{$id}\"><!-- {$element['type']} --></div>\n"
        };
    }

    private function generateVue(Project $project): string
    {
        $elements = $project->elements ?? [];
        $template = $this->generateVueTemplate($elements);

        return "<template>\n  <div class=\"canvas\">\n{$template}\n  </div>\n</template>\n\n<script setup>\n// Generated Vue component\n</script>";
    }

    private function generateVueTemplate(array $elements): string
    {
        $html = '';
        foreach ($elements as $element) {
            $html .= $this->generateVueElement($element);
        }
        return $html;
    }

    private function generateVueElement(array $element): string
    {
        $props = $element['properties'] ?? [];
        $style = "position: absolute; left: {$element['x']}px; top: {$element['y']}px; width: {$element['width']}px; height: {$element['height']}px;";

        return match($element['type']) {
            'text' => "    <div style=\"{$style}\">{$props['content']}</div>\n",
            'heading' => "    <h{$props['level']} style=\"{$style}\">{$props['content']}</h{$props['level']}>\n",
            'button' => "    <button style=\"{$style}\">{$props['text']}</button>\n",
            'image' => "    <img style=\"{$style}\" src=\"{$props['src']}\" alt=\"{$props['alt']}\">\n",
            default => "    <div style=\"{$style}\"><!-- {$element['type']} --></div>\n"
        };
    }

    private function generateReact(Project $project): string
    {
        return "import React from 'react';\n\nexport default function GeneratedComponent() {\n  return (\n    <div className=\"canvas\">\n      {/* Generated React component */}\n    </div>\n  );\n}";
    }
}
