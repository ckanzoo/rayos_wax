<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class FileSessionHandler extends Session implements SessionHandlerInterface
{
    private $save_path;
    private $file_path;
    private $data;

    public function __construct()
    {
        // Use custom path from config or default to /tmp/sessions
        if (!empty(config_item('sess_save_path'))) {
            $this->save_path = rtrim(config_item('sess_save_path'), '/\\');
        } else {
            $this->save_path = '/tmp/sessions';
        }

        // Ensure session folder exists
        if (!is_dir($this->save_path)) {
            mkdir($this->save_path, 0700, true);
        }

        ini_set('session.save_path', $this->save_path);
    }

    public function open($save_path, $session_name): bool
    {
        $this->save_path = $save_path ?: $this->save_path;
        $this->file_path = $this->save_path . DIRECTORY_SEPARATOR . $session_name . '_';

        if (!is_dir($this->save_path)) {
            mkdir($this->save_path, 0700, true);
        }

        return true;
    }

    public function close(): bool
    {
        return true;
    }

    public function read($session_id): string
    {
        $this->data = '';
        $filename = $this->file_path . $session_id;

        if (file_exists($filename)) {
            $this->data = @file_get_contents($filename) ?: '';
        }

        return $this->data;
    }

    public function write($session_id, $session_data): bool
    {
        $filename = $this->file_path . $session_id;

        // Ensure folder exists
        $dir = dirname($filename);
        if (!is_dir($dir)) mkdir($dir, 0700, true);

        if ($session_data !== $this->data) {
            return @file_put_contents($filename, $session_data, LOCK_EX) !== false;
        } else {
            return @touch($filename);
        }
    }

    public function destroy($session_id): bool
    {
        $filename = $this->file_path . $session_id;
        if (file_exists($filename)) @unlink($filename);

        return true;
    }

    public function gc($maxlifetime): int
    {
        foreach (glob($this->file_path . '*') as $filename) {
            if (filemtime($filename) + $maxlifetime < time() && file_exists($filename)) {
                @unlink($filename);
            }
        }

        return true;
    }
}
?>
