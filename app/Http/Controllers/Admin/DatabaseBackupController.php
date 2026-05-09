<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class DatabaseBackupController extends Controller
{
    /**
     * Menampilkan halaman backup & restore
     */
    public function index()
    {
        return view('admin.settings.backup.index');
    }

    /**
     * Alur Fitur Export (Backup)
     */
    public function export()
    {
        try {
            $filename = 'database_backup_' . date('Y_m_d_His') . '.sql.gz';
            $filepath = storage_path('app/' . $filename);
            
            // 4. Kompresi Langsung (On-the-fly) tanpa menyimpan .sql mentah
            $fp = gzopen($filepath, 'w9');

            $driver = DB::connection()->getDriverName();

            if ($driver === 'sqlite') {
                gzwrite($fp, "PRAGMA foreign_keys = OFF;\n\n");
                
                $tables = DB::select("SELECT name, type FROM sqlite_master WHERE type IN ('table', 'view') AND name NOT LIKE 'sqlite_%'");
                $views = [];

                foreach ($tables as $tableInfo) {
                    $tableName = $tableInfo->name;
                    $tableType = $tableInfo->type;

                    if ($tableType === 'view') {
                        $views[] = $tableName;
                        continue;
                    }

                    $createTable = DB::selectOne("SELECT sql FROM sqlite_master WHERE type='table' AND name=?", [$tableName]);
                    if ($createTable && $createTable->sql) {
                        gzwrite($fp, "-- Table structure for table `$tableName`\n");
                        gzwrite($fp, "DROP TABLE IF EXISTS `$tableName`;\n");
                        gzwrite($fp, $createTable->sql . ";\n\n");
                    }

                    gzwrite($fp, "-- Dumping data for table `$tableName`\n");
                    foreach (DB::table($tableName)->cursor() as $row) {
                        $rowArr = (array) $row;
                        $values = array_map(function ($val) {
                            if (is_null($val)) return 'NULL';
                            return DB::connection()->getPdo()->quote($val);
                        }, array_values($rowArr));

                        $line = "INSERT INTO `$tableName` VALUES (" . implode(",", $values) . ");\n";
                        gzwrite($fp, $line);
                    }
                    gzwrite($fp, "\n");
                }

                foreach ($views as $viewName) {
                    $createView = DB::selectOne("SELECT sql FROM sqlite_master WHERE type='view' AND name=?", [$viewName]);
                    if ($createView && $createView->sql) {
                        gzwrite($fp, "-- View structure for view `$viewName`\n");
                        gzwrite($fp, "DROP VIEW IF EXISTS `$viewName`;\n");
                        gzwrite($fp, $createView->sql . ";\n\n");
                    }
                }

                gzwrite($fp, "PRAGMA foreign_keys = ON;\n");
            } else {
                // MySQL / MariaDB Mode
                gzwrite($fp, "SET FOREIGN_KEY_CHECKS=0;\n");
                gzwrite($fp, "SET SESSION SQL_MODE='';\n\n");

                $tables = DB::select('SHOW FULL TABLES');
                $views = [];

                foreach ($tables as $tableInfo) {
                    $tableArr = (array) $tableInfo;
                    $tableName = array_values($tableArr)[0];
                    $tableType = array_values($tableArr)[1];

                    if ($tableType === 'VIEW') {
                        $views[] = $tableName;
                        continue;
                    }

                    $createTable = DB::select("SHOW CREATE TABLE `$tableName`");
                    $createStatement = ((array)$createTable[0])['Create Table'];
                    
                    gzwrite($fp, "-- Table structure for table `$tableName`\n");
                    gzwrite($fp, "DROP TABLE IF EXISTS `$tableName`;\n");
                    gzwrite($fp, $createStatement . ";\n\n");

                    gzwrite($fp, "-- Dumping data for table `$tableName`\n");
                    foreach (DB::table($tableName)->cursor() as $row) {
                        $rowArr = (array) $row;
                        $values = array_map(function ($val) {
                            if (is_null($val)) return 'NULL';
                            return DB::connection()->getPdo()->quote($val);
                        }, array_values($rowArr));

                        $line = "INSERT INTO `$tableName` VALUES (" . implode(",", $values) . ");\n";
                        gzwrite($fp, $line);
                    }
                    gzwrite($fp, "\n");
                }

                foreach ($views as $viewName) {
                    $createView = DB::select("SHOW CREATE VIEW `$viewName`");
                    $createStatement = ((array)$createView[0])['Create View'];
                    gzwrite($fp, "-- View structure for view `$viewName`\n");
                    gzwrite($fp, "DROP VIEW IF EXISTS `$viewName`;\n");
                    gzwrite($fp, $createStatement . ";\n\n");
                }

                gzwrite($fp, "SET FOREIGN_KEY_CHECKS=1;\n");
            }

            gzclose($fp);

            return response()->download($filepath)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membackup database: ' . $e->getMessage());
        }
    }

    /**
     * Alur Fitur Import (Restore)
     */
    public function import(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file',
        ]);

        try {
            // Set max execution time to infinite so it doesn't timeout
            set_time_limit(0);
            
            $file = $request->file('backup_file');
            $filepath = $file->getRealPath();
            $extension = $file->getClientOriginalExtension();
            
            // Validasi manual ekstensi
            if (!in_array(strtolower($extension), ['gz', 'sql'])) {
                return back()->with('error', 'Format file tidak didukung. Harap upload .sql atau .sql.gz');
            }
            
            $driver = DB::connection()->getDriverName();

            // 3. Simulasi Wipe Database (Pembersihan Table & View)
            if ($driver === 'sqlite') {
                DB::statement('PRAGMA foreign_keys = OFF;');
                $tables = DB::select("SELECT name, type FROM sqlite_master WHERE type IN ('table', 'view') AND name NOT LIKE 'sqlite_%'");
                foreach ($tables as $tableInfo) {
                    $tableName = $tableInfo->name;
                    $tableType = $tableInfo->type;
                    if ($tableType === 'view') {
                        DB::statement("DROP VIEW IF EXISTS `$tableName`");
                    } else {
                        DB::statement("DROP TABLE IF EXISTS `$tableName`");
                    }
                }
            } else {
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                $tables = DB::select('SHOW FULL TABLES');
                foreach ($tables as $tableInfo) {
                    $tableArr = (array) $tableInfo;
                    $tableName = array_values($tableArr)[0];
                    $tableType = array_values($tableArr)[1];

                    if ($tableType === 'VIEW') {
                        DB::statement("DROP VIEW IF EXISTS `$tableName`");
                    } else {
                        DB::statement("DROP TABLE IF EXISTS `$tableName`");
                    }
                }
            }

            // Membaca file
            $sql = '';
            $extension = $file->getClientOriginalExtension();
            
            if ($extension === 'gz') {
                // 1. Dekompresi On-The-Fly menggunakan gzopen/gzread
                $fp = gzopen($filepath, 'r');
                while (!gzeof($fp)) {
                    // Membaca isi file secara bertahap lalu digabungkan menjadi string SQL
                    $sql .= gzread($fp, 4096 * 1024);
                }
                gzclose($fp);
            } else {
                // Jika user upload format .sql langsung
                $sql = file_get_contents($filepath);
            }

            // 2. Sanitasi Data Mandiri (Anti Crash Zero Date MySQL)
            $sql = str_replace("'0000-00-00 00:00:00'", "'1970-01-01 00:00:00'", $sql);
            $sql = str_replace("'0000-00-00'", "'1970-01-01'", $sql);
            
            // 4. Eksekusi Raw SQL
            DB::unprepared($sql);
            
            if ($driver === 'sqlite') {
                DB::statement('PRAGMA foreign_keys = ON;');
            } else {
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            }

            return back()->with('success', 'Database berhasil di-restore!');

        } catch (\Exception $e) {
            $driver = DB::connection()->getDriverName();
            try { 
                if ($driver === 'sqlite') {
                    DB::statement('PRAGMA foreign_keys = ON;');
                } else {
                    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                }
            } catch (\Exception $ex) {}
            return back()->with('error', 'Terjadi kesalahan saat merestore database: ' . $e->getMessage());
        }
    }
}
