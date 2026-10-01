<?php

declare(strict_types=1);

namespace Modules\Mobile\Native\Screens;

use Native\Mobile\Edge\Components\Native\NativeComponent;
use Native\Mobile\Edge\NativeComponent as EdgeNativeComponent;

class MapScreen extends NativeComponent
{
    public string $title = 'Mappa Segnalazioni';

    public array $filters = [
        'types' => [],
        'statuses' => [],
        'date_range' => null,
    ];

    public int $markerCount = 0;

    public bool $isLoading = true;

    public string $error = '';

    public function mount(): void
    {
        $this->loadMarkers();
    }

    public function loadMarkers(): void
    {
        $this->isLoading = true;
        $this->error = '';

        // In produzione: usa nativephp_call per fetch nativo o HTTP client
        // Per ora simuliamo il caricamento
        $this->markerCount = 27; // Dal seed demo

        $this->isLoading = false;
    }

    public function applyFilters(array $filters): void
    {
        $this->filters = array_merge($this->filters, $filters);
        $this->loadMarkers();
    }

    public function clearFilters(): void
    {
        $this->filters = [
            'types' => [],
            'statuses' => [],
            'date_range' => null,
        ];
        $this->loadMarkers();
    }

    public function render(): EdgeNativeComponent
    {
        return view('mobile::native.screens.map', [
            'title' => $this->title,
            'markerCount' => $this->markerCount,
            'isLoading' => $this->isLoading,
            'error' => $this->error,
            'filters' => $this->filters,
        ]);
    }
}