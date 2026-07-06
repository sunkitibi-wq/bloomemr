<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>{{ $title ?? config('app.name', 'Laravel') }}</title>
        
        <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
        <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;600;700&family=Inter:wght@400;600&family=JetBrains+Mono&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
        
        <script id="tailwind-config">
          window.tailwind = window.tailwind || {};
          window.tailwind.config = {
            darkMode: "class",
            theme: {
              extend: {
                "colors": {
                    "outline": "#76777d",
                    "on-primary": "#ffffff",
                    "primary-fixed": "#dae2fd",
                    "on-secondary-fixed-variant": "#0e5138",
                    "outline-variant": "#c6c6cd",
                    "status-warning": "#F59E0B",
                    "secondary-fixed": "#b1f0ce",
                    "surface-tint": "#565e74",
                    "inverse-primary": "#bec6e0",
                    "primary-container": "#131b2e",
                    "on-error-container": "#93000a",
                    "on-tertiary-fixed": "#002113",
                    "on-secondary": "#ffffff",
                    "on-tertiary-fixed-variant": "#005235",
                    "watermark-text": "#CBD5E1",
                    "on-tertiary-container": "#2a9569",
                    "primary-fixed-dim": "#bec6e0",
                    "on-error": "#ffffff",
                    "on-secondary-container": "#316e52",
                    "surface-container-highest": "#e0e3e5",
                    "status-critical": "#DC2626",
                    "surface-container-low": "#f2f4f6",
                    "surface-container-high": "#e6e8ea",
                    "surface-bright": "#f7f9fb",
                    "primary": "#000000",
                    "on-background": "#191c1e",
                    "trust-navy": "#0F172A",
                    "surface-container": "#eceef0",
                    "inverse-on-surface": "#eff1f3",
                    "inverse-surface": "#2d3133",
                    "surface-variant": "#e0e3e5",
                    "on-primary-fixed": "#131b2e",
                    "secondary-fixed-dim": "#95d4b3",
                    "on-primary-container": "#7c839b",
                    "surface-container-lowest": "#ffffff",
                    "on-surface-variant": "#45464d",
                    "surface-dim": "#d8dadc",
                    "on-tertiary": "#ffffff",
                    "background": "#f7f9fb",
                    "surface": "#f7f9fb",
                    "growth-sage": "#74A57F",
                    "on-primary-fixed-variant": "#3f465c",
                    "tertiary-fixed-dim": "#75daa8",
                    "error-container": "#ffdad6",
                    "on-secondary-fixed": "#002114",
                    "tertiary-fixed": "#92f7c3",
                    "status-waiting": "#64748B",
                    "tertiary": "#000000",
                    "tertiary-container": "#002113",
                    "secondary": "#2c694e",
                    "error": "#ba1a1a",
                    "on-surface": "#191c1e",
                    "secondary-container": "#aeeecb"
                },
                "borderRadius": {
                    "DEFAULT": "0.125rem",
                    "lg": "0.25rem",
                    "xl": "0.5rem",
                    "full": "0.75rem"
                },
                "spacing": {
                    "margin-page": "32px",
                    "gutter": "24px",
                    "unit": "4px",
                    "widget-gap": "16px",
                    "form-row-gap": "20px"
                },
                "fontFamily": {
                    "headline-md": ["Hanken Grotesk"],
                    "body-lg": ["Inter"],
                    "label-md": ["Inter"],
                    "data-mono": ["JetBrains Mono"],
                    "body-sm": ["Inter"],
                    "headline-lg": ["Hanken Grotesk"],
                    "headline-lg-mobile": ["Hanken Grotesk"],
                    "headline-xl": ["Hanken Grotesk"],
                    "body-md": ["Inter"]
                }
              }
            }
          };
        </script>
        
        <style>
            .material-symbols-outlined {
                font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
                vertical-align: middle;
            }
        </style>

        @fluxAppearance
    </head>
    <body class="min-h-screen bg-[#f7f9fb] dark:bg-zinc-950 font-sans antialiased text-zinc-900 dark:text-zinc-100">
        {{ $slot }}

        @fluxScripts
    </body>
</html>
