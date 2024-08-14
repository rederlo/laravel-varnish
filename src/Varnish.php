<?php

namespace Spatie\Varnish;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class Varnish
{
    /**
     * @param string|array $host
     * @param string $url
     *
     * @return \Symfony\Component\Process\Process
     */
    public function flush($host = null, string $url = null): Process
    {
        $host = $this->getHosts($host);

        $command = $this->generateBanCommand($host, $url);

        return $this->executeCommand($command);
    }

    /**
     * @param array|string $host
     *
     * @return array
     */
    protected function getHosts($host = null): array
    {
        $host = $host ?? config('varnish.host');

        if (!is_array($host)) {
            $host = [$host];
        }

        return $host;
    }

    /**
     * @return string
     */
    protected function getSecretPath(): string
    {
        $path = Storage::drive('local')->path('varnish_secret');

        if (!File::exists($path)) {
            $secret = config('varnish.administrative_secret');
            File::put($path, $secret);
        }

        return $path;
    }


    /**
     * @param array $hosts
     * @param string $url
     * @return string
     */
    public function generateBanCommand(array $hosts, string $url = null): string
    {
        $hostsRegex = collect($hosts)
            ->map(function (string $host) {
                return "(^{$host}$)";
            })
            ->implode('|');

        $config = config('varnish');

        $urlRegex = '';
        if (!empty($url)) {
            $urlRegex = " && req.url ~ {$url}";
        }

        $secretPath = $this->getSecretPath();

        return "varnishadm -S {$secretPath} -T {$config['administrative_host']}:{$config['administrative_port']} 'ban req.http.host ~ {$hostsRegex}{$urlRegex}'";
    }


    protected function executeCommand(string $command): Process
    {
        $process = new Process([$command]);

        $process->run();

        return $process;
    }
}
