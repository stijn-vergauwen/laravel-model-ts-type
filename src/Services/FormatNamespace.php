<?php

namespace Scrumble\TypeGenerator\Services;

use Illuminate\Support\Str;

class FormatNamespace
{
    /**
     * @param $path
     * @throws Exception
     * @return string
     */
    public function get($path)
    {
        $namespace = $this->fromFileContent($path);

        if (null === $namespace) {
            $namespace = $this->fromFilePath($path);
        }

        if (null !== $namespace) {
            return $namespace;
        }

        throw new Exception("Could not make a valid namespace for path '{$path}'", 500);
    }

    /**
     * @param $path
     * @return null|string
     */
    protected function fromFileContent($path): ?string
    {
        $namespace = null;

        if (file_exists($path)) {
            $handle = fopen($path, 'r');

            if ($handle) {
                while (($line = fgets($handle)) !== false) {
                    if (0 === strpos($line, 'namespace')) {
                        $parts = explode(' ', $line);
                        $namespace = rtrim(trim($parts[1]), ';');

                        break;
                    }
                }

                fclose($handle);
            }

            $fileName = Str::ucfirst(pathinfo($path, PATHINFO_FILENAME));
            $namespace .= '\\' . $fileName;
        }

        return $namespace;
    }

    /**
     * @param $path
     * @return mixed
     */
    protected function fromFilePath($path)
    {
        if (!starts_with($path, '/')) {
            $path = '/' . $path;
        }

        $namespace = str_replace(base_path(), '', $path);
        $namespace = preg_replace('/\//', '\\', $namespace);
        $namespace = str_replace('.php', '', $namespace);

        return Str::ucfirst(substr($namespace, 2));
    }
}