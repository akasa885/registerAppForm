<?php

namespace App\Console\Commands;

use Illuminate\Foundation\Console\DownCommand;
use Illuminate\Foundation\Exceptions\RegisterErrorViewPaths;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class MaintenanceDownCommand extends DownCommand
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'maintenance:down
                            {--duration= : Lama maintenance dalam angka bulat positif}
                            {--unit= : Satuan estimasi: minutes, hours, atau days}
                            {--redirect= : The path that users should be redirected to}
                            {--render= : The view that should be prerendered for display during maintenance mode}
                            {--retry= : The number of seconds after which the request may be retried}
                            {--refresh= : The number of seconds after which the browser may refresh}
                            {--secret= : The secret phrase that may be used to bypass maintenance mode}
                            {--status=503 : The status code that should be used when returning the maintenance mode response}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Put the application into maintenance mode with a custom page and bypass secret';

    /**
     * @var int
     */
    protected $maintenanceDuration;

    /**
     * @var string
     */
    protected $maintenanceUnit;

    /**
     * @var string
     */
    protected $maintenanceSecret;

    /**
     * @var \Illuminate\Support\Carbon|null
     */
    protected $maintenanceEndsAt;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (is_file(storage_path('framework/down'))) {
            return parent::handle();
        }

        if (! $this->prepareMaintenanceOptions()) {
            return 1;
        }

        $result = parent::handle();

        if ($result !== 1) {
            $this->displayBypassInstructions();
        }

        return is_int($result) ? $result : 0;
    }

    /**
     * Get the payload to be placed in the down file.
     *
     * @return array
     */
    protected function getDownFilePayload()
    {
        return [
            'except' => $this->excludedPaths(),
            'redirect' => $this->redirectPath(),
            'retry' => $this->getRetryTime(),
            'refresh' => $this->option('refresh'),
            'secret' => $this->maintenanceSecret,
            'status' => (int) ($this->option('status') ?: 503),
            'template' => $this->resolveRenderView() ? $this->prerenderView() : null,
        ];
    }

    /**
     * Prerender the specified view so that it can be rendered even before loading Composer.
     *
     * @return string
     */
    protected function prerenderView()
    {
        (new RegisterErrorViewPaths)();

        return view($this->resolveRenderView(), [
            'appName' => config('app.name'),
            'maintenanceUnit' => $this->maintenanceUnit,
            'maintenanceDuration' => $this->maintenanceDuration,
            'maintenanceStartedAt' => now()->format('d/m/Y H:i'),
            'maintenanceEndsAt' => $this->maintenanceEndsAt
                ? $this->maintenanceEndsAt->format('d/m/Y H:i')
                : null,
            'maintenanceMessage' => $this->getMaintenanceMessage(),
            'retryAfter' => $this->getRetryTime(),
            'refreshAfter' => $this->option('refresh'),
        ])->render();
    }

    /**
     * Get the number of seconds the client should wait before retrying their request.
     *
     * @return int|null
     */
    protected function getRetryTime()
    {
        $retry = $this->option('retry');

        if (is_numeric($retry) && $retry > 0) {
            return (int) $retry;
        }

        if (! $this->maintenanceDuration || ! $this->maintenanceUnit) {
            return null;
        }

        if ($this->maintenanceUnit === 'minutes') {
            return $this->maintenanceDuration * 60;
        }

        if ($this->maintenanceUnit === 'hours') {
            return $this->maintenanceDuration * 3600;
        }

        return $this->maintenanceDuration * 86400;
    }

    /**
     * Prepare interactive or option-based maintenance settings.
     *
     * @return bool
     */
    protected function prepareMaintenanceOptions()
    {
        $this->maintenanceUnit = $this->resolveMaintenanceUnit();

        if (! $this->maintenanceUnit) {
            return false;
        }

        $this->maintenanceDuration = $this->resolveMaintenanceDuration();

        if (! $this->maintenanceDuration) {
            return false;
        }

        $this->maintenanceEndsAt = $this->resolveMaintenanceEndsAt();
        $this->maintenanceSecret = $this->sanitizeSecret($this->option('secret') ?: (string) Str::uuid());

        return true;
    }

    /**
     * Resolve the selected maintenance unit.
     *
     * @return string|null
     */
    protected function resolveMaintenanceUnit()
    {
        $unit = strtolower((string) $this->option('unit'));

        if (in_array($unit, ['minutes', 'hours', 'days'], true)) {
            return $unit;
        }

        if (! $this->input->isInteractive()) {
            $this->error('Option --unit wajib diisi dengan nilai minutes, hours, atau days saat mode non-interactive.');

            return null;
        }

        return $this->choice(
            'Estimasi maintenance berdasarkan satuan apa?',
            ['minutes', 'hours', 'days'],
            'minutes'
        );
    }

    /**
     * Resolve the maintenance duration.
     *
     * @return int|null
     */
    protected function resolveMaintenanceDuration()
    {
        $duration = $this->option('duration');

        if ($this->isValidPositiveNumber($duration)) {
            return (int) $duration;
        }

        if (! $this->input->isInteractive()) {
            $this->error('Option --duration wajib diisi dengan angka bulat positif saat mode non-interactive.');

            return null;
        }

        do {
            $duration = $this->ask($this->getDurationQuestion());

            if ($this->isValidPositiveNumber($duration)) {
                return (int) $duration;
            }

            $this->error('Masukkan angka bulat positif.');
        } while (true);
    }

    /**
     * Resolve the maintenance end time.
     *
     * @return \Illuminate\Support\Carbon|null
     */
    protected function resolveMaintenanceEndsAt()
    {
        $now = now();

        if ($this->maintenanceUnit === 'minutes') {
            return $now->copy()->addMinutes($this->maintenanceDuration);
        }

        if ($this->maintenanceUnit === 'hours') {
            return $now->copy()->addHours($this->maintenanceDuration);
        }

        return $now->copy()->addDays($this->maintenanceDuration);
    }

    /**
     * Resolve the render view name.
     *
     * @return string|null
     */
    protected function resolveRenderView()
    {
        return $this->option('render');
    }

    /**
     * Get the maintenance page message.
     *
     * @return string
     */
    protected function getMaintenanceMessage()
    {
        if ($this->maintenanceUnit === 'minutes') {
            return 'Kami sedang dalam perbaikan. Segera kami akan kembali secepatnya.';
        }

        return 'Kami sedang melakukan perbaikan sistem. Layanan diperkirakan kembali tersedia pada waktu berikut.';
    }

    /**
     * Get the duration question based on the selected unit.
     *
     * @return string
     */
    protected function getDurationQuestion()
    {
        if ($this->maintenanceUnit === 'hours') {
            return 'Perkiraan maintenance berapa jam?';
        }

        if ($this->maintenanceUnit === 'days') {
            return 'Perkiraan maintenance berapa hari?';
        }

        return 'Perkiraan maintenance berapa menit?';
    }

    /**
     * Determine if the provided value is a valid positive integer.
     *
     * @param  mixed  $value
     * @return bool
     */
    protected function isValidPositiveNumber($value)
    {
        return is_numeric($value) && (int) $value > 0 && (string) (int) $value === trim((string) $value);
    }

    /**
     * Sanitize the maintenance secret so it remains URL-safe.
     *
     * @param  string  $secret
     * @return string
     */
    protected function sanitizeSecret($secret)
    {
        $secret = trim((string) $secret);

        if ($secret === '') {
            return (string) Str::uuid();
        }

        return preg_replace('/[^A-Za-z0-9\-]/', '-', $secret);
    }

    /**
     * Display the bypass instructions after maintenance mode is enabled.
     *
     * @return void
     */
    protected function displayBypassInstructions()
    {
        $this->newLine();
        $this->info('Akses bypass maintenance Laravel:');
        $this->line($this->getBypassUrl());
        $this->comment('Buka URL tersebut sekali di browser untuk mendapatkan cookie bypass maintenance.');
        $this->comment('Cookie bypass berlaku 12 jam sesuai mekanisme bawaan Laravel.');

        if ($this->maintenanceUnit !== 'minutes' && $this->maintenanceEndsAt instanceof Carbon) {
            $this->line('Estimasi selesai: '.$this->maintenanceEndsAt->format('d/m/Y H:i'));
        }
    }

    /**
     * Build the bypass URL.
     *
     * @return string
     */
    protected function getBypassUrl()
    {
        return rtrim(config('app.url'), '/').'/'.$this->maintenanceSecret;
    }
}
