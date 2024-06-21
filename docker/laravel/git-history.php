<?php

// For Laravel cmd
namespace App\Console\Commands;

use Illuminate\Console\Command;

class GitHistory extends Command
{

    public function handle()
    {
        $account = 'hezachary';
        $stagingBranch = 'staging';
        $source = 'source.txt';
        $target = 'target.csv';
        $limit = strtotime('-7 months');

        $handle = fopen(storage_path($source), "r");
        if ($handle) {
            $list = [];
            $previousLine = '';
            while (($line = fgets($handle)) !== false) {
                if (substr($line, 0, 7) === 'Merge: ') {
                    fgets($handle);
                    $date = fgets($handle);
                    $date = strtotime(trim(substr($date, strpos($date, ' ') + 1)));

                    if ($limit > $date) {
                        break;
                    }
                    $row = [
                        'release' => '',
                        'detail' => '',
                        'date' => date('Y-m-d H:i:s', $date),
                        'commit' => trim(substr($previousLine, strpos($previousLine, ' ') + 1)),
                    ];
                    continue;
                }

                if (!empty($row) && empty($row['detail']) && preg_match('/^\s+\S+/', $line)) {
                    if (
                        preg_match('/^\s+Merge remote-tracking branch/', $line)
                        || preg_match('/^\s+Merge branch .+ into /', $line)
                    ) {
                        $row = [];
                        continue;
                    }

                    if (preg_match('/^\s+Merge pull request #\d+ from ' . $account . '\/' . $stagingBranch . '$/', $line)) {
                        $row['release'] = 'Y';
                    }
                    if (!preg_match('/^\s+Merge pull request #\d+ from ' . $account . '/', $line)) {
                        $row['detail'] = trim($line);
                        $list[] = $row;
                        $row = [];
                        continue;
                    }
                }

                $previousLine = $line;
            }

            fclose($handle);
        }

        $columns = array_keys($list[0]);
        $default = array_fill_keys($columns, null);

        $fp = fopen(storage_path(date('Y-m-d', $limit) . '-' . date('Y-m-d') . '_' . $target), 'w');
        fputcsv($fp, $columns);

        foreach ($list as $row) {
            fputcsv($fp, array_values(array_merge($default, $row)));
        }

        fclose($fp);
    }
}
